<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

use BackedEnum;

class BackedEnumNormalizer implements NormalizerInterface
{
    public function supports(mixed $value): bool
    {
        return $value instanceof BackedEnum;
    }

    public function normalize(mixed $value): mixed
    {
        return $value->getBackedValue();
    }
}
