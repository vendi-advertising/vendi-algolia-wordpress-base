<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

use BackedEnum;

final class BackedEnumNormalizer implements NormalizerInterface
{
    public function supports(mixed $value): bool
    {
        return $value instanceof BackedEnum;
    }

    /**
     * @param BackedEnum $value
     * @return string|int
     */
    public function normalize(mixed $value): string|int
    {
        return $value->value;
    }
}
