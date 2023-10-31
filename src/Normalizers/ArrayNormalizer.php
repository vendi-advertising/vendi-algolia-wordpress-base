<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

use Vendi\VendiAlgoliaWordpressBase\Service\VendiObjectSerializer;

final class ArrayNormalizer implements NormalizerInterface
{
    public function supports(mixed $value): bool
    {
        return is_array($value);
    }

    public function normalize(mixed $value): array
    {
        return (new VendiObjectSerializer)->normalizeAttributes($value);
    }
}