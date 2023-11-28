<?php

namespace Vendi\VendiAlgoliaWordpressBase\Entity;

use JsonSerializable;
use Vendi\VendiAlgoliaWordpressBase\Service\VendiObjectSerializer;

abstract class BaseObject implements JsonSerializable
{
    public const ATTRIBUTE_DELIM = '-';

    public function jsonSerialize(): array
    {
        return (new VendiObjectSerializer())->getAttributes($this);
    }
}