<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

use DateTimeInterface;

class DateTimeInterfaceNormalizer extends ObjectNormalizer
{
    public function supports(mixed $value): bool
    {
        return $value instanceof DateTimeInterface;
    }

    public function normalize(mixed $value): mixed
    {
        return $value->getTimestamp();
    }
}