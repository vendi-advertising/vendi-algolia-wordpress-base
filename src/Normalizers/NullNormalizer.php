<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

final class NullNormalizer implements NormalizerInterface
{
    public function supports(mixed $value): bool
    {
        return null === $value;
    }

    public function normalize(mixed $value): mixed
    {
        return null;
    }
}