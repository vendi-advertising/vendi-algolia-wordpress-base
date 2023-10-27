<?php

namespace Vendi\VendiAlgoliaWordpressBase\Enum;

enum DateTimeSerializationFormatEnum: string
{
    case Timestamp = 'timestamp';
    case Iso8601 = 'iso8601';
}
