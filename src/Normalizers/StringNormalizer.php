<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

final class StringNormalizer extends PassThruNormalizer
{
    public function supports(mixed $value): bool
    {
        return is_string($value);
    }
}