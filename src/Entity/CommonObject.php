<?php

namespace Vendi\VendiAlgoliaWordpressBase\Entity;

use BackedEnum;
use Vendi\VendiAlgoliaWordpressBase\Attribute\SerializeAttribute as Serialize;
use Vendi\VendiAlgoliaWordpressBase\Entity\BaseObject;

abstract class CommonObject extends BaseObject
{
    public function __construct(
        public int|string $id,
        #[Serialize('objectType')]
        public string|BackedEnum $objectType,
    ) {
    }

    #[Serialize('objectID')]
    final public function getObjectId(): string
    {
        return implode(self::ATTRIBUTE_DELIM, [$this->objectType instanceof BackedEnum ? $this->objectType->value : $this->objectType, $this->id]);
    }
}
