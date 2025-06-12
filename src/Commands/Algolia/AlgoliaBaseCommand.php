<?php

namespace Vendi\VendiAlgoliaWordpressBase\Commands\Algolia;

use Vendi\VendiAlgoliaWordpressBase\AlgoliaEnvironmentVariables;
use Vendi\VendiAlgoliaWordpressBase\Enum\CommandRunModeEnum;
use Vendi\VendiAlgoliaWordpressBase\Exception\MissingEnvironmentVariableException;
use WP_CLI;
use WP_CLI\ExitException;

abstract class AlgoliaBaseCommand
{
    private array $args;
    private array $assoc_args;

    protected function setConfig(array $args = [], array $assoc_args = []): void
    {
        $this->args       = $args;
        $this->assoc_args = $assoc_args;
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    protected function getCommonSettingsArray(): array
    {
        $settings[] = ['Name' => 'Index Name', 'Value' => AlgoliaEnvironmentVariables::getIndexName()];
        $settings[] = ['Name' => 'Index Environment', 'Value' => AlgoliaEnvironmentVariables::getIndexNameEnv()];
        $settings[] = ['Name' => 'Algolia Utility', 'Value' => AlgoliaEnvironmentVariables::getAlgoliaUtilityClassName()];
        $settings[] = ['Name' => 'Application ID', 'Value' => AlgoliaEnvironmentVariables::getApplicationId()];
        $settings[] = ['Name' => 'Run Mode', 'Value' => $this->getRunMode()->value];

        return $settings;
    }

    private array $settings = [];

    protected function addKeyValueSetting(string $key, string $value): void
    {
        $this->settings[] = ['Name' => $key, 'Value' => $value];
    }

    /**
     * @throws MissingEnvironmentVariableException
     */
    protected function showSettings(bool $includeCommon = true): void
    {
        if ($includeCommon) {
            $settings = array_merge($this->getCommonSettingsArray(), $this->settings);
        } else {
            $settings = $this->settings;
        }

        \WP_CLI\Utils\format_items(
            'table',
            $settings,
            ['Name', 'Value'],
        );
    }

    public function getConfigArg(string $key, $default = null): mixed
    {
        return $this->args[$key] ?? $default;
    }

    /**
     * @throws ExitException
     */
    public function getConfigAssocArgInt(string $key, int $default): int
    {
        $ret = $this->getConfigAssocArg($key, $default);
        if ( ! is_numeric($ret)) {
            WP_CLI::error(sprintf('%1$s must be numeric, found %2$s', $key, get_debug_type($ret)));
        }

        return $ret;
    }

    public function getConfigAssocArg(string $key, $default = null): mixed
    {
        return $this->assoc_args[$key] ?? $default;
    }

    protected function getRunMode(): CommandRunModeEnum
    {
        return match ($this->getConfigAssocArg('run', false)) {
            true => CommandRunModeEnum::LIVE,
            false => CommandRunModeEnum::DRY_RUN,
        };
    }
}
