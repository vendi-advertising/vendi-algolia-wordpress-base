<?php

namespace Vendi\VendiAlgoliaWordpressBase\Commands\Algolia\Index;

use Exception;
use Vendi\VendiAlgoliaWordpressBase\Commands\Algolia\AlgoliaBaseCommand;
use Vendi\VendiAlgoliaWordpressBase\Enum\CommandRunModeEnum;
use Vendi\VendiAlgoliaWordpressBase\Utilities\AlgoliaUtility;
use WP_CLI;
use WP_CLI\ExitException;

use function post_type_exists;

class PurgeCommand extends AlgoliaBaseCommand
{
    /**
     * Purge content from the Algolia index
     *
     * ## OPTIONS
     *
     * [<post-type>...]
     * : One or more post types to index
     *
     * [--all]
     * : Index all supported post types
     *
     * [--exclude-pdfs]
     * : Do not index PDFs
     *
     * [--run]
     * : If not supplied, this command will run in dry-run mode
     *
     * ## EXAMPLES
     *
     *     wp algolia purge news
     *
     *     wp algolia index --all --run
     *
     *
     * @param array $args
     *
     * @param array $assoc_args
     * @throws ExitException
     * @throws Exception
     */
    public function __invoke(array $args = [], array $assoc_args = []): void
    {
        $this->setConfig($args, $assoc_args);
        WP_CLI::log('Purging site content for Algolia');

        $postTypes = $this->determinePostTypes($args, $assoc_args);
        $client    = AlgoliaUtility::getInstance()->getAlgoliaClient();

        $this->showSettings();

        WP_CLI::log('The following post types will be purged:');
        foreach ($postTypes as $postType) {
            WP_CLI::log(' * ' . $postType);
        }


        if ($this->getRunMode() === CommandRunModeEnum::LIVE) {
            $postTypeFilters = implode(
                ' OR ',
                array_filter(
                    array_map(
                        static function (string $postType) {
                            if ($postType === 'pdf') {
                                return null;
                            }

                            return 'postType:' . $postType;
                        },
                        $postTypes,
                    ),
                ),
            );

            if ($postTypeFilters) {
                $client->deleteBy(
                    AlgoliaUtility::getInstance()->getAlgoliaIndexName(),
                    [
                        'filters' => $postTypeFilters,
                    ],
                );
            }
        }


        WP_CLI::success('Done');
    }

    /**
     * @throws ExitException
     */
    private function determinePostTypes(array $args = [], array $assoc_args = []): array
    {
        $entityObjects = AlgoliaUtility::getInstance()->getAlgoliaCPTSlugsForIndexing();
        $postTypes     = $args;

        $all = $assoc_args['all'] ?? false;

        if ($all && count($postTypes)) {
            WP_CLI::error('You must specify either --all or one or more post types, but not both');
        }

        if ($all) {
            $postTypes = $entityObjects;
        }

        if ( ! count($postTypes)) {
            WP_CLI::error('No post types specified');
        }

        foreach ($postTypes as $postType) {
            if ( ! post_type_exists($postType)) {
                WP_CLI::warning(sprintf('Post type "%1$s" does not exist', $postType));
            }

            if ( ! in_array($postType, $entityObjects, true)) {
                WP_CLI::error(sprintf('Post type "%1$s" is not configured to be indexed', $postType));
            }
        }

        return $postTypes;
    }
}
