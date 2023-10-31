<?php

namespace Vendi\VendiAlgoliaWordpressBase\Service;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Vendi\VendiAlgoliaWordpressBase\Attribute\SerializeAttribute;
use Vendi\VendiAlgoliaWordpressBase\Attribute\SerializeFunctionAttribute;
use Vendi\VendiAlgoliaWordpressBase\Entity\BaseObject;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\ArrayNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\BackedEnumNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\BooleanNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\FloatNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\IntegerNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\NormalizerInterface;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\NullNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\ObjectNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\StringNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\UnitEnumNormalizer;

class VendiObjectSerializer
{
    /**
     * @throws ReflectionException
     */
    public function getAttributes($obj): array
    {
        $chain = [];

        $ref = new ReflectionClass($obj);
        $chain[] = $ref;
        while ($parent = $ref->getParentClass()) {
            $chain[] = $parent;
            $ref = $parent;
        }

        $chain = array_reverse($chain);
        $attributes = [];

        foreach ($chain as $ref) {
            $this->getAttributesFromReflectionClass($ref, $attributes, $obj);
        }

        $ret = $this->normalizeAttributes($attributes);

        // Sort, mostly for debugging purposes
        ksort($ret);

        return $ret;
    }

    /**
     * @return NormalizerInterface[]
     */
    private function getNormalizers(): array
    {
        static $normalizers = [
            new NullNormalizer,
            new BooleanNormalizer,
            new IntegerNormalizer,
            new FloatNormalizer,
            new StringNormalizer,
            new BackedEnumNormalizer,
            new UnitEnumNormalizer,
            new ObjectNormalizer,
            new ArrayNormalizer,
        ];

        return $normalizers;
    }

    public function normalizeAttributes(array $attributes): array
    {
        $ret = [];
        foreach ($attributes as $key => $value) {
            foreach ($this->getNormalizers() as $normalizer) {
                if ($normalizer->supports($value)) {
                    $ret[$key] = $normalizer->normalize($value);
                    continue 2;
                }
            }

            throw new RuntimeException('No normalizer found for value: '.get_debug_type($value));
        }

        return $ret;
    }

    private function getSingleSerializeAttribute(ReflectionProperty|ReflectionMethod $propertyOrMethod): ?SerializeAttribute
    {
        $attr = $propertyOrMethod->getAttributes(SerializeAttribute::class);

        if (empty($attr)) {
            return null;
        }

        if (1 !== count($attr)) {
            throw new RuntimeException('Only one Serialize attribute is allowed per property or method');
        }

        return $attr[0]->newInstance();
    }


    public function getAttributesFromReflectionClass(ReflectionClass $ref, &$attributes, $obj): void
    {
        foreach ($ref->getProperties() as $prop) {
            if (!$serializeAttribute = $this->getSingleSerializeAttribute($prop)) {
                continue;
            }

            if (!$serializeAttribute->serializationFieldName) {
                $serializeAttribute->serializationFieldName = $prop->getName();
            }

            if ($serializeAttribute->serializationGroupName) {
                $serializedName = implode(BaseObject::ATTRIBUTE_DELIM, [$serializeAttribute->serializationGroupName, $serializeAttribute->serializationFieldName]);
            } else {
                $serializedName = $serializeAttribute->serializationFieldName;
            }

            $func = $prop->getAttributes(SerializeFunctionAttribute::class);
            if (!empty($func)) {
                /** @var SerializeFunctionAttribute $func */
                $func = $func[0]->newInstance();
                $funcName = $func->serializeFunction;
                $attributes[$serializedName] = $obj->$funcName($prop->getValue($obj));
            } else {
                $attributes[$serializedName] = $prop->getValue($obj);
            }
        }

        foreach ($ref->getMethods() as $method) {
            if (!$serializeAttribute = $this->getSingleSerializeAttribute($method)) {
                continue;
            }

            if (!$serializeAttribute->serializationFieldName) {
                $serializeAttribute->serializationFieldName = $method->getName();
            }

            if ($serializeAttribute->serializationGroupName) {
                $serializedName = implode(BaseObject::ATTRIBUTE_DELIM, [$serializeAttribute->serializationGroupName, $serializeAttribute->serializationFieldName]);
            } else {
                $serializedName = $serializeAttribute->serializationFieldName;
            }

            $methodName = $method->getName();

            $attributes[$serializedName] = $obj->$methodName();
        }
    }
}