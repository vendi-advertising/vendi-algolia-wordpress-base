<?php

namespace Vendi\VendiAlgoliaWordpressBase\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SerializeFunctionAttribute
{
    public function __construct(
        public string $serializeFunction
    ) {
    }
}