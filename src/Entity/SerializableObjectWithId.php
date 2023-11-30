<?php

namespace Vendi\VendiAlgoliaWordpressBase\Entity;

use Vendi\VendiAlgoliaWordpressBase\Attribute\SerializeAttribute;

abstract class SerializableObjectWithId extends BaseObject
{
    public function __construct(
        public int $id,

        #[SerializeAttribute('objectType')]
        public string $objectType,
    ) {
    }

    #[SerializeAttribute('objectID')]
    public function getObjectId(): string
    {
        return implode(self::ATTRIBUTE_DELIM, [$this->objectType, $this->id]);
    }
}