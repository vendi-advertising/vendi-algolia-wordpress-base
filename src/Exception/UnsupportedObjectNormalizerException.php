<?php

namespace Vendi\VendiAlgoliaWordpressBase\Exception;

use Exception;

class UnsupportedObjectNormalizerException extends Exception
{
    public function __construct(string $objectType)
    {
        parent::__construct(sprintf('Object of type %1$s does not support normalization for serialization purposes', $objectType));
    }
}