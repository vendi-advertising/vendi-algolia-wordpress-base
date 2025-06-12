<?php

namespace Vendi\VendiAlgoliaWordpressBase\Commands;

use Exception;
use Vendi\VendiAlgoliaWordpressBase\Commands\Algolia\AlgoliaCommandNamespace;
use Vendi\VendiAlgoliaWordpressBase\Commands\Algolia\Index\BuildCommand;
use Vendi\VendiAlgoliaWordpressBase\Commands\Algolia\Index\IndexCommandNamespace;
use Vendi\VendiAlgoliaWordpressBase\Commands\Algolia\Index\PurgeCommand;
use WP_CLI;

class CommandLoader
{
    /**
     * @throws Exception
     */
    public static function registerAllCommands(): void
    {
        //Bail early if wp cli isn't running
        if ( ! defined('WP_CLI') || ! class_exists(WP_CLI::class)) {
            return;
        }

        WP_CLI::add_command('algolia', AlgoliaCommandNamespace::class);
        WP_CLI::add_command('algolia index', IndexCommandNamespace::class);
        WP_CLI::add_command('algolia index build', BuildCommand::class);
        WP_CLI::add_command('algolia index purge', PurgeCommand::class);
    }
}
