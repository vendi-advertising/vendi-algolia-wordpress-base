<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

final class BooleanNormalizer extends PassThruNormalizer
{
    public function supports($value): bool
    {
        return is_bool($value);
    }
}