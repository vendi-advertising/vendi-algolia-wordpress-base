<?php

namespace Vendi\VendiAlgoliaWordpressBase;

use Vendi\VendiAlgoliaWordpressBase\Exception\MissingEnvironmentVariableException;

abstract class AlgoliaEnvironmentVariables
{
    public const string ALGOLIA_APPLICATION_ID = 'ALGOLIA_APPLICATION_ID';
    public const string ALGOLIA_WRITE_API_KEY  = 'ALGOLIA_WRITE_API_KEY';
    public const string ALGOLIA_INDEX_NAME     = 'ALGOLIA_INDEX_NAME';

    public const string ALGOLIA_INDEX_NAME_ENV           = 'ALGOLIA_INDEX_NAME_ENV';
    public const string ALGOLIA_SEARCH_API_KEY           = 'ALGOLIA_SEARCH_API_KEY';
    public const string ALGOLIA_MAX_RECORD_SIZE_IN_BYTES = 'ALGOLIA_MAX_RECORD_SIZE_IN_BYTES';
    public const string ALGOLIA_SEARCH_PAGE_URL          = 'ALGOLIA_SEARCH_PAGE_URL';
    public const string ALGOLIA_UTILITY_CLASS            = 'ALGOLIA_UTILITY_CLASS';

    /**
     * @throws MissingEnvironmentVariableException
     */
    private static function getAlgoliaEnvironmentVariableOrThrow(string $name): string
    {
        if ( ! $value = getenv($name)) {
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

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function getSearchPageUrl(): string
    {
        return self::getAlgoliaEnvironmentVariableOrThrow(self::ALGOLIA_SEARCH_PAGE_URL);
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    public static function getAlgoliaUtilityClassName(): string
    {
        $class = self::getAlgoliaEnvironmentVariableOrThrow(self::ALGOLIA_UTILITY_CLASS);
        if ( ! class_exists($class)) {
            throw new MissingEnvironmentVariableException(sprintf('Class %s does not exist', $class));
        }

        return $class;
    }
}
