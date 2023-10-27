<?php

namespace Vendi\VendiAlgoliaWordpressBase\Attribute;

use Attribute;
use BackedEnum;
use Vendi\VendiAlgoliaWordpressBase\Enum\DateTimeSerializationFormatEnum;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class SerializeDateTimeAttribute extends SerializeAttribute
{
    public function __construct(
        public ?string $serializationFieldName = null,
        public string|BackedEnum|null $serializationGroupName = null,
        public bool $keepEmptyValues = false,
        public string|DateTimeSerializationFormatEnum $format = DateTimeSerializationFormatEnum::Timestamp,
    ) {
        parent::__construct($serializationFieldName, $serializationGroupName, $keepEmptyValues);
    }
}