<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

use JsonSerializable;

final class JsonSerializableNormalizer extends ObjectNormalizer
{
    public function supports(mixed $value): bool
    {
        return $value instanceof JsonSerializable;
    }

    public function normalize(mixed $value): mixed
    {
        return $value->jsonSerialize();
    }
}