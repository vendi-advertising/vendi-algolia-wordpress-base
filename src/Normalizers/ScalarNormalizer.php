<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

class ScalarNormalizer implements NormalizerInterface
{
    public function supports(mixed $value): bool
    {
        return is_scalar($value);
    }

    public function normalize(mixed $value): mixed
    {
        return $value;
    }
}