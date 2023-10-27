<?php

namespace Vendi\VendiAlgoliaWordpressBase\Normalizers;

interface NormalizerInterface
{
    public function supports(mixed $value): bool;

    public function normalize(mixed $value): mixed;
}