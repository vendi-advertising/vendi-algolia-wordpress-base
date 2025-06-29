<?php

namespace Vendi\VendiAlgoliaWordpressBase\Utilities;

use Algolia\AlgoliaSearch\Api\SearchClient;
use Algolia\AlgoliaSearch\Model\Search\SaveObjectResponse;
use DateTimeImmutable;
use Exception;
use JsonException;
use Vendi\VendiAlgoliaWordpressBase\AlgoliaEnvironmentVariables;
use Vendi\VendiAlgoliaWordpressBase\Entity\BaseObject;
use Vendi\VendiAlgoliaWordpressBase\Entity\CommonWpPost;
use Vendi\VendiAlgoliaWordpressBase\Exception\MissingEnvironmentVariableException;
use WP_Post;

use function get_permalink;
use function get_post_meta;
use function get_post_thumbnail_id;
use function get_the_post_thumbnail_url;

class AlgoliaUtility
{
    protected static AlgoliaUtility|null $instance = null;

    protected function objectBuildContent($page): ?string
    {
        return 'You must implement the objectBuildContent() method in your subclass.';
    }

    private function __construct()
    {
        // NOOP
    }

    /**
     * Normally we'd use DI for this, but to keep things simpler
     * we'll just use a singleton.
     *
     * @return static
     * @throws MissingEnvironmentVariableException
     */
    final public static function getInstance(): self
    {
        if ( ! self::$instance) {
            $className      = AlgoliaEnvironmentVariables::getAlgoliaUtilityClassName();
            self::$instance = new $className;
        }

        return self::$instance;
    }

    public function getAlgoliaCPTSlugsForIndexing(): array
    {
        return ['page'];
    }

    protected function assignEntityUrl(WP_Post $page): ?string
    {
        return get_permalink($page);
    }

    protected function createCommonWpPostFromObject(WP_Post $post): CommonWpPost
    {
        return new CommonWpPost($post->ID, $post->post_type);
    }

    protected function setCommonWpPostProperties(CommonWpPost $obj, WP_Post $post): void
    {
        $obj->title = $post->post_title;

        $obj->entityUrl    = $this->assignEntityUrl($post);
        $obj->dateCreated  = new DateTimeImmutable($post->post_date);
        $obj->dateModified = new DateTimeImmutable($post->post_modified);

        $obj->taxonomies = [];

        if ($url = get_the_post_thumbnail_url($post)) {
            $obj->imageUrl = $url;
            if ($attachmentId = get_post_thumbnail_id($post)) {
                $obj->imageAlt = get_post_meta($attachmentId, '_wp_attachment_image_alt', true);
            }
        }
    }

    protected function isCommonWpPostValid(CommonWpPost $obj): bool
    {
        //assignEntityUrl() can return null. If it does, don't index the post
        if ( ! $obj->entityUrl) {
            return false;
        }

        return true;
    }

    protected function objectBuildStart($page): ?object
    {
        if ( ! $page instanceof WP_Post) {
            throw new Exception('Expected WP_Post');
        }

        if ( ! $page->post_title) {
            return null;
        }

        $obj = $this->createCommonWpPostFromObject($page);
        $this->setCommonWpPostProperties($obj, $page);

        if ( ! $this->isCommonWpPostValid($obj)) {
            return null;
        }


        return $obj;
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public function getAlgoliaClient(): SearchClient
    {
        return SearchClient::create(
            AlgoliaEnvironmentVariables::getApplicationId(),
            AlgoliaEnvironmentVariables::getWriteApiKey(),
        );
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public function getAlgoliaIndexName(): string
    {
        return implode('_', [AlgoliaEnvironmentVariables::getIndexNameEnv(), AlgoliaEnvironmentVariables::getIndexName()]);
    }

    protected function stripAllHtmlFromText(?string $text): ?string
    {
        if ( ! $text) {
            return null;
        }

        // Ideally a DOMDocument would be used here, however we don't fully trust the content that could come through
        // this, it could be HTML, text, or something else. As such, we're just going to _try_ to remove script,
        // style, form tags and comments which should be sufficient for 99.9% of cases.
        $ret = preg_replace('#<script.*?>.*?</script>#is', '', $text);
        $ret = preg_replace('#<style.*?>.*?</style>#is', '', $ret);
        $ret = preg_replace('#<form.*?>.*?</form>#is', '', $ret);
        $ret = preg_replace('#<!--.*?-->#s', '', $ret);

        // Remove all HTML
        $ret = strip_tags($ret);

        $ret = html_entity_decode($ret, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Collapse all whitespace
        $ret = preg_replace('/\s+/', ' ', $ret);

        return trim($ret);
    }

    protected function splitStringByMaxLength($string, $maxLength = 80000, $encoding = 'UTF-8'): array
    {
        $result    = [];
        $strLength = mb_strlen($string, $encoding);

        for ($i = 0; $i < $strLength; $i += $maxLength) {
            $result[] = mb_substr($string, $i, $maxLength, $encoding);
        }

        return $result;
    }

    /**
     * @throws JsonException
     */
    protected function encodeJson($obj): string
    {
        return json_encode($obj, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_IGNORE);
    }

    /**
     * @throws Exception
     */
    protected function maybeSplitObjectIntoMultipleRecords($obj, $page): array
    {
        $objArray = [];
        $size     = mb_strlen(json_encode($obj));

        //Algolia has a max record size of 100KB. If the content is larger than 95KB, split it into multiple records
        //Make sure the index configuration in the Algolia dashboard has the "distinct" and "attributesForDistinct" properties set to avoid duplicate results
        //attributesForDistinct should be set to "entityUrl".
        if ($size > AlgoliaEnvironmentVariables::getMaxRecordSizeInBytes()) {
            $splitObjArray = $this->splitStringByMaxLength($obj->content);
            $idx           = 0;
            foreach ($splitObjArray as $splitObjContent) {
                if ( ! $newObj = $this->objectBuildStart($page)) {
                    if (class_exists(\WP_CLI::class)) {
                        \WP_CLI::warning('Skipping object build start for ' . $page->ID);
                    }
                    continue;
                }


                $newObj->id      .= '-' . $idx;
                $newObj->content = $splitObjContent;


                $message = 'Splitting content for ' . $page->post_title . ' into ' . count($splitObjArray) . ' parts';
                if (class_exists(\WP_CLI::class)) {
                    \WP_CLI::line($message);
                }

                $objArray[] = $this->convertObjectToJson($newObj);
                $idx++;
            }
        } else {
            $objArray[] = $this->convertObjectToJson($obj);
        }

        return $objArray;
    }

    /**
     * @throws JsonException
     * @throws MissingEnvironmentVariableException
     */
    protected function convertObjectToJson(BaseObject $obj): ?array
    {
        $encoded = $this->encodeJson($obj);

        if (strlen($encoded) >= AlgoliaEnvironmentVariables::getMaxRecordSizeInBytes()) {
            return null;
        }

        return json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws MissingEnvironmentVariableException
     * @throws Exception
     */
    public function removePostFromAlgolia(WP_Post $post, bool $isWpCli = false): void
    {
        $client  = $this->getAlgoliaClient();
        $objects = $this->convertPostToJsonObjectForAlgolia($post, isWpCli: $isWpCli);

        foreach ($objects as $object) {
            try {
                $client->deleteObject($this->getAlgoliaIndexName(), $object['objectID']);
            } catch (Exception $e) {
                // Do nothing
            }
        }
    }

    /**
     * @throws MissingEnvironmentVariableException
     * @throws Exception
     */
    public function indexSinglePost(WP_Post $post, bool $isWpCli = false): void
    {
        $entitySlugsThatShouldBeIndexed = $this->getAlgoliaCPTSlugsForIndexing();

        if ( ! in_array($post->post_type, $entitySlugsThatShouldBeIndexed, true)) {
            return;
        }

        $client  = $this->getAlgoliaClient();
        $objects = $this->convertPostToJsonObjectForAlgolia($post, isWpCli: $isWpCli);
        try {
            foreach ($objects as $object) {
                $client->saveObject($this->getAlgoliaIndexName(), $object);
            }
        } catch (Exception $e) {
            // Do nothing
        }
    }


    /**
     * @throws Exception
     */
    public function convertPostToJsonObjectForAlgolia($page, bool $isWpCli = false): array|null
    {
        $obj = $this->objectBuildStart($page);

        if ( ! $obj) {
            return null;
        }

        //Page content

        $ret          = $this->objectBuildContent($page);
        $obj->content = $this->stripAllHtmlFromText($ret) ?? null;

        //This returns an array no matter what
        return $this->maybeSplitObjectIntoMultipleRecords($obj, $page);
    }
}
