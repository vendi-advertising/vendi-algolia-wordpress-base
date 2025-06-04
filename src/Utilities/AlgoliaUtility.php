<?php

namespace Vendi\VendiAlgoliaWordpressBase\Utilities;

use Algolia\AlgoliaSearch\Api\SearchClient;
use Exception;
use JsonException;
use Vendi\VendiAlgoliaWordpressBase\AlgoliaEnvironmentVariables;
use Vendi\VendiAlgoliaWordpressBase\Entity\BaseObject;
use Vendi\VendiAlgoliaWordpressBase\Exception\MissingEnvironmentVariableException;

abstract class AlgoliaUtility extends UtilityBase
{
    public function objectBuildStart($page): ?object
    {
        if ( ! $page instanceof WP_Post) {
            throw new Exception('Expected WP_Post');
        }

        if ( ! $page->post_title) {
            return null;
        }

        $obj = new CommonWpPost($page->ID, $page->post_type);

        $obj->title = $page->post_title;

        $obj->entityUrl  = $this->assignEntityUrl($page);
        $obj->taxonomies = [];


        //assignEntityUrl() can return null. If it does, don't index the post
        if ( ! $obj->entityUrl) {
            return null;
        }

        $obj->dateCreated  = new DateTimeImmutable($page->post_date);
        $obj->dateModified = new DateTimeImmutable($page->post_modified);


        if ($url = get_the_post_thumbnail_url($page)) {
            $obj->imageUrl = $url;
            if ($attachmentId = get_post_thumbnail_id($page)) {
                $obj->imageAlt = get_post_meta($attachmentId, '_wp_attachment_image_alt', true);
            }
        }

        return $obj;
    }
    
    public function getAlgoliaClient(): SearchClient
    {
        return SearchClient::create(
            AlgoliaEnvironmentVariables::getApplicationId(),
            AlgoliaEnvironmentVariables::getWriteApiKey(),
        );
    }

    public function getAlgoliaIndexName(): string
    {
        return implode('_', [AlgoliaEnvironmentVariables::getIndexNameEnv(), AlgoliaEnvironmentVariables::getIndexName()]);
    }

    protected function stripAllHtmlFromText(?string $text): ?string
    {

        if (!$text) {
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
        $result = [];
        $strLength = mb_strlen($string, $encoding);

        for ($i = 0; $i < $strLength; $i += $maxLength) {
            $result[] = mb_substr($string, $i, $maxLength, $encoding);
        }

        return $result;
    }

    /**
     * @throws JsonException
     */
    protected
    function encodeJson($obj): string
    {
        return json_encode($obj, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_IGNORE);
    }

    /**
     * @throws Exception
     */
    protected function maybeSplitObjectIntoMultipleRecords($obj, $page): array
    {

        $objArray = [];
        $size = mb_strlen(json_encode($obj));

        //Algolia has a max record size of 100KB. If the content is larger than 95KB, split it into multiple records
        //Make sure the index configuration in the Algolia dashboard has the "distinct" and "attributesForDistinct" properties set to avoid duplicate results
        //attributesForDistinct should be set to "entityUrl".
        if ($size > 92000) {
            $splitObjArray = $this->splitStringByMaxLength($obj->content, 80000);
            $idx = 0;
            foreach ($splitObjArray as $splitObjContent) {
                $newObj = $this->objectBuildStart($page);


                $newObj->id = $newObj->id . '-' . $idx;
                $newObj->content = $splitObjContent;


                $message = 'Splitting content for ' . $page->post_title . ' into ' . count($splitObjArray) . ' parts';
                WP_CLI::line($message);

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


}
