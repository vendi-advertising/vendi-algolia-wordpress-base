<?php

namespace Vendi\VendiAlgoliaWordpressBase\Exception;

use Exception;

class UnsupportedNormalizerTypeException extends Exception
{
    public function __construct(string $type)
    {
        parent::__construct("Unsupported normalizer type: {$type}");
    }
}