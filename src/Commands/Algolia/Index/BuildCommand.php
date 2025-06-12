<?php

namespace Vendi\VendiAlgoliaWordpressBase\Commands\Algolia\Index;

use cli\progress\Bar;
use Exception;
use JsonException;
use Vendi\VendiAlgoliaWordpressBase\Commands\Algolia\AlgoliaBaseCommand;
use Vendi\VendiAlgoliaWordpressBase\Enum\CommandRunModeEnum;
use Vendi\VendiAlgoliaWordpressBase\Utilities\AlgoliaUtility;
use WP_CLI;
use WP_CLI\ExitException;

use function get_posts;
use function post_type_exists;

class BuildCommand extends AlgoliaBaseCommand
{
    /**
     * Index content for Algolia
     *
     * ## OPTIONS
     *
     * [<post-type>...]
     * : One or more post types to index
     *
     * [--limit=<limit>]
     * : Limit the number of posts to index
     *
     * [--all]
     * : Index all supported post types
     *
     * [--exclude-pdfs]
     * : Do not index PDFs
     *
     * [--skip-taxonomies]
     * : Do not index taxonomy terms
     *
     * [--run]
     * : If not supplied, this command will run in dry-run mode
     *
     * ## EXAMPLES
     *
     *     wp algolia index build news --run
     *
     *     wp algolia index build --all --run
     *
     *     wp algolia index build news --limit=10 --run
     *
     *
     * @param array $args
     *
     * @param array $assoc_args
     * @throws JsonException
     * @throws ExitException
     * @throws Exception
     */
    public function __invoke(array $args = [], array $assoc_args = []): void
    {
        $algoliaUtility = AlgoliaUtility::getInstance();

        $client = $algoliaUtility->getAlgoliaClient();


        $this->setConfig($args, $assoc_args);

        WP_CLI::log('Indexing content for Algolia');


        $postTypes = $this->determinePostTypes($args, $assoc_args);

        $limit = $this->getConfigAssocArgInt('limit', -1);


        $this->addKeyValueSetting('Limit', $limit);

        $this->addKeyValueSetting('Entities', implode(', ', $postTypes));

        $this->showSettings();

        $entities = get_posts(
            [
                'post_type'      => $postTypes,
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
            ],
        );


        $objects = $this->createObjects($entities);


        if ($this->getRunMode() === CommandRunModeEnum::DRY_RUN) {
            WP_CLI::line('Dry run, skipping indexing');
        } else {
            WP_CLI::line('Submitting objects to Algolia...');

            foreach ($objects as $object) {
                $client->saveObjects($algoliaUtility->getAlgoliaIndexName(), $object);
            }
        }

        WP_CLI::success('Submission to Algolia complete');
    }

    /**
     * @throws ExitException
     * @throws JsonException
     * @throws Exception
     */
    private function createObjects(array $entities): array
    {
        $progress = \WP_CLI\Utils\make_progress_bar('Building objects', count($entities));

        $objects = [];
        foreach ($entities as $page) {
            WP_CLI::debug('Processing ' . $page->post_title);
            //
            if ($obj = AlgoliaUtility::getInstance()->convertPostToJsonObjectForAlgolia($page, isWpCli: true)) {
                foreach ($obj as $objArray) {
                    if ( ! $objArray) {
                        WP_CLI::warning('No object found for ' . $page->post_title);
                        continue;
                    }
                    if ( ! array_key_exists('objectID', $objArray)) {
                        WP_CLI::warning('No objectID found for ' . $page->post_title);
                        continue;
                    }
                }


                $objects[] = $obj;
            }

            /** @noinspection DisconnectedForeachInstructionInspection */
            if ($progress instanceof Bar) {
                $progress->tick();
            }
        }
        $progress->finish();

        return $objects;
    }

    /**
     * @throws ExitException
     */
    private function determinePostTypes(array $args = [], array $assoc_args = []): array
    {
        $entitySlugsThatShouldBeIndexed = AlgoliaUtility::getInstance()->getAlgoliaCPTSlugsForIndexing();

        $postTypes = $args;

        $all = $assoc_args['all'] ?? false;

        if ($all && count($postTypes)) {
            WP_CLI::error('You must specify either --all or one or more post types, but not both');
        }

        if ($all) {
            $postTypes = $entitySlugsThatShouldBeIndexed;
        }

        if ( ! count($postTypes)) {
            WP_CLI::error('No post types specified');
        }

        foreach ($postTypes as $postType) {
            if ( ! post_type_exists($postType)) {
                WP_CLI::warning(sprintf('Post type "%1$s" does not exist', $postType));
            }

            if ( ! in_array($postType, $entitySlugsThatShouldBeIndexed, true)) {
                WP_CLI::error(sprintf('Post type "%1$s" is not configured to be indexed', $postType));
            }
        }

        return $postTypes;
    }
}
