<?php

namespace Vendi\VendiAlgoliaWordpressBase\Attribute;

use Attribute;
use BackedEnum;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class SerializeAttribute
{
    public function __construct(
        public ?string $serializationFieldName = null,
        public string|BackedEnum|null $serializationGroupName = null,
        public bool $keepEmptyValues = false,
    ) {
    }
}