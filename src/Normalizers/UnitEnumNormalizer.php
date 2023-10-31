<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

use BackedEnum;
use RuntimeException;
use UnitEnum;

final class UnitEnumNormalizer implements NormalizerInterface
{
    public function supports(mixed $value): bool
    {
        return $value instanceof UnitEnum && !$value instanceof BackedEnum;
    }

    public function normalize(mixed $value): mixed
    {
        throw new RuntimeException('Cannot serialize UnitEnum of type '.get_class($value));
    }
}
