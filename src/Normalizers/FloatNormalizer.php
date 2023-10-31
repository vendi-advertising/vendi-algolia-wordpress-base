<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

final class FloatNormalizer extends PassThruNormalizer
{
    public function supports(mixed $value): bool
    {
        return is_float($value) || is_int($value);
    }
}