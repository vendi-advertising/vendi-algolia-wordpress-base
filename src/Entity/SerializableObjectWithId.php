<?php

namespace Vendi\VendiAlgoliaWordpressBase\Entity;

abstract class SerializableObjectWithId extends BaseObject
{
    public function __construct(
        public int $id,

        #[Serialize('objectType')]
        public string $objectType,
    ) {
    }

    #[Serialize('objectID')]
    public function getObjectId(): string
    {
        return implode(self::ATTRIBUTE_DELIM, [$this->objectType, $this->id]);
    }
}