<?php

namespace Vendi\VendiAlgoliaWordpressBase;

use Vendi\VendiAlgoliaWordpressBase\Exception\MissingEnvironmentVariableException;

final class AlgoliaEnvironmentVariables
{
    public const ALGOLIA_APPLICATION_ID = 'ALGOLIA_APPLICATION_ID';
    public const ALGOLIA_WRITE_API_KEY = 'ALGOLIA_WRITE_API_KEY';
    public const ALGOLIA_INDEX_NAME = 'ALGOLIA_INDEX_NAME';

    public const ALGOLIA_INDEX_NAME_ENV = 'ALGOLIA_INDEX_NAME_ENV';
    public const ALGOLIA_SEARCH_API_KEY = 'ALGOLIA_SEARCH_API_KEY';
    public const ALGOLIA_MAX_RECORD_SIZE_IN_BYTES = 'ALGOLIA_MAX_RECORD_SIZE_IN_BYTES';

    private function __construct()
    {
        // NOOP
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    private static function getAlgoliaEnvironmentVariableOrThrow(string $name): string
    {
        if (!$value = getenv($name)) {
            throw new MissingEnvironmentVariableException($name);
        }

        return $value;
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function getApplicationId(): string
    {
        return self::getAlgoliaEnvironmentVariableOrThrow(self::ALGOLIA_APPLICATION_ID);
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function getWriteApiKey(): string
    {
        return self::getAlgoliaEnvironmentVariableOrThrow(self::ALGOLIA_WRITE_API_KEY);
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function getIndexName(): string
    {
        return self::getAlgoliaEnvironmentVariableOrThrow(self::ALGOLIA_INDEX_NAME);
    }


    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function getIndexNameEnv(): string
    {
        return self::getAlgoliaEnvironmentVariableOrThrow(self::ALGOLIA_INDEX_NAME_ENV);
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function getSearchApiKey(): string
    {
        return self::getAlgoliaEnvironmentVariableOrThrow(self::ALGOLIA_SEARCH_API_KEY);
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function getMaxRecordSizeInBytes(): int
    {
        return (int)self::getAlgoliaEnvironmentVariableOrThrow(self::ALGOLIA_MAX_RECORD_SIZE_IN_BYTES);
    }
}
