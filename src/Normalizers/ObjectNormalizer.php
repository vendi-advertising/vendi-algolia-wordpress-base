<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

use DateTimeInterface;
use JsonSerializable;
use RuntimeException;
use Vendi\VendiAlgoliaWordpressBase\Exception\UnsupportedObjectNormalizerException;

class ObjectNormalizer implements NormalizerInterface
{
    public function supports(mixed $value): bool
    {
        return is_object($value);
    }

    public function normalize(mixed $value): mixed
    {
        return match (true) {
            $value instanceof DateTimeInterface => $value->getTimestamp(),
            $value instanceof JsonSerializable => $value->jsonSerialize(),
            default => throw new UnsupportedObjectNormalizerException(get_class($value)),
        };
    }
}