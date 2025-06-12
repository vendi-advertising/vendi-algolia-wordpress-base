<?php

namespace Vendi\VendiAlgoliaWordpressBase\Commands;

abstract class CommandBase
{
    /**
     * This method is taken directly from WP-CLI Utils.php in order to avoid a hard-dependency on
     * that library for production.
     *
     * @param array $assoc_args
     * @param string $flag
     * @param null $default
     *
     * @return mixed|null
     */
    final protected function get_flag_value(array $assoc_args, string $flag, $default = null): mixed
    {
        return $assoc_args[$flag] ?? $default;
    }
}
