<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

use RuntimeException;
use UnitEnum;

class UnitEnumNormalizer implements NormalizerInterface
{
    public function supports(mixed $value): bool
    {
        return $value instanceof UnitEnum;
    }

    public function normalize(mixed $value): mixed
    {
        throw new RuntimeException('Cannot serialize UnitEnum of type '.get_class($value));
    }
}
