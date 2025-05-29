<?php

namespace Vendi\VendiAlgoliaWordpressBase\Exception;
use Exception;

class MissingEnvironmentVariableException extends Exception
{
    public function __construct(string $environmentVariableName)
    {
        parent::__construct(sprintf('Missing environment variable "%1$s"', $environmentVariableName));
    }

}
