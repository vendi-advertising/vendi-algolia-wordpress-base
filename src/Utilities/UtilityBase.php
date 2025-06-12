<?php

namespace Vendi\VendiAlgoliaWordpressBase\Utilities;

use InvalidArgumentException;

abstract class UtilityBase
{
    /**
     * @var UtilityBase[]
     */
    protected static array $instances = [];

    private function __construct()
    {
        // NOOP
    }

    /**
     * Normally we'd use DI for this, but to keep things simpler
     * we'll just use a singleton.
     *
     * @return static
     */
    final public static function getInstance(): UtilityBase
    {
        $static_class = static::class;
        if (!isset(self::$instances[$static_class])) {
            $obj = new $static_class();
            $obj->init();
            self::$instances[$static_class] = $obj;
        }

        return self::$instances[$static_class];
    }

    protected function init(): void
    {
        //NOOP
    }
}