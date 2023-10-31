<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

use Vendi\VendiAlgoliaWordpressBase\Exception\UnsupportedNormalizerTypeException;

abstract class PassThruNormalizer implements NormalizerInterface
{
    final public function normalize(mixed $value): mixed
    {
        if (!$this->supports($value)) {
            throw new UnsupportedNormalizerTypeException(get_debug_type($value));
        }

        return $value;
    }
}