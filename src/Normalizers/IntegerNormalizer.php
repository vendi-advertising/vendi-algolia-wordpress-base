<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

final class IntegerNormalizer extends PassThruNormalizer
{
    public function supports(mixed $value): bool
    {
        return is_int($value);
    }
}