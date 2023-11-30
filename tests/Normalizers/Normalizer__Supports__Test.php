<?php /** @noinspection IdentifierGrammar */

namespace Vendi\VendiAlgoliaWordpressBase\Tests\Normalizers;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\ArrayNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\BackedEnumNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\BooleanNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\DateTimeInterfaceNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\FloatNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\IntegerNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\JsonSerializableNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\NullNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\StringNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\UnitEnumNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Tests\includes\MyBackedEnumForTesting;
use Vendi\VendiAlgoliaWordpressBase\Tests\includes\MyNonBackedEnumForTesting;

class Normalizer__Supports__Test extends TestCase
{
    public static function dataForStringNormalizer(): array
    {
        return self::makeSupports(supportsString: true);
    }

    public static function dataForIntegerNormalizer(): array
    {
        return self::makeSupports(supportsInteger: true);
    }

    public static function dataForBooleanNormalizer(): array
    {
        return self::makeSupports(supportsBoolean: true);
    }

    public static function dataForFloatNormalizer(): array
    {
        return self::makeSupports(supportsInteger: true, supportsFloat: true);
    }

    public static function dataForArrayNormalizer(): array
    {
        return self::makeSupports(supportsArray: true);
    }

    public static function dateForDateTimeInterfaceNormalizer(): array
    {
        return self::makeSupports(supportsDateTimeInterface: true);
    }

    public static function dataForJsonSerializableNormalizer(): array
    {
        return self::makeSupports(supportsJsonSerializable: true);
    }

    public static function dataForNullNormalizer(): array
    {
        return self::makeSupports(supportsNull: true);
    }

    public static function dataForBackedEnumNormalizer(): array
    {
        return self::makeSupports(supportsBackedEnum: true);
    }

    public static function dataForUnitEnumNormalizer(): array
    {
        return self::makeSupports(supportsUnitEnum: true);
    }

    #[DataProvider('dataForArrayNormalizer')]
    public function testArrayNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new ArrayNormalizer())->supports($type));
    }

    #[DataProvider('dataForNullNormalizer')]
    public function testNullNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new NullNormalizer())->supports($type));
    }

    #[DataProvider('dataForStringNormalizer')]
    public function testStringNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new StringNormalizer())->supports($type));
    }

    #[DataProvider('dataForIntegerNormalizer')]
    public function testIntegerNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new IntegerNormalizer())->supports($type));
    }

    #[DataProvider('dataForFloatNormalizer')]
    public function testFloatNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new FloatNormalizer())->supports($type));
    }

    #[DataProvider('dataForBooleanNormalizer')]
    public function testBooleanNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new BooleanNormalizer())->supports($type));
    }

    #[DataProvider('dataForBackedEnumNormalizer')]
    public function testBackedEnumNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new BackedEnumNormalizer())->supports($type));
    }

    #[DataProvider('dataForUnitEnumNormalizer')]
    public function testUnitEnumNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new UnitEnumNormalizer())->supports($type));
    }

    #[DataProvider('dateForDateTimeInterfaceNormalizer')]
    public function testDateTimeInterfaceNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new DateTimeInterfaceNormalizer())->supports($type));
    }

    #[DataProvider('dataForJsonSerializableNormalizer')]
    public function testJsonSerializableNormalizer(bool $supports, mixed $type): void
    {
        $this->assertSame($supports, (new JsonSerializableNormalizer())->supports($type));
    }


    private static function makeSupports(
        bool $supportsBoolean = false,
        bool $supportsString = false,
        bool $supportsInteger = false,
        bool $supportsFloat = false,
        bool $supportsNull = false,
        bool $supportsArray = false,
        bool $supportsBackedEnum = false,
        bool $supportsUnitEnum = false,
        bool $supportsDateTimeInterface = false,
        bool $supportsJsonSerializable = false,
    ): array {
        return [
            'Null' => [$supportsNull, null],
            'Boolean' => [$supportsBoolean, true],
            'Int' => [$supportsInteger, 1],
            'Float' => [$supportsFloat, 1.1],
            'String' => [$supportsString, ''],
            'DateTimeInterface' => [$supportsDateTimeInterface, new \DateTime()],
            'JsonSerializable' => [
                $supportsJsonSerializable,
                new class implements \JsonSerializable {
                    public function jsonSerialize(): array
                    {
                        return [];
                    }
                },
            ],
            'DateTimeImmutable' => [$supportsDateTimeInterface, new DateTimeImmutable()],
            'BackedEnum' => [$supportsBackedEnum, MyBackedEnumForTesting::FOO],
            'UnitEnum' => [$supportsUnitEnum, MyNonBackedEnumForTesting::FOO],
            'Array' => [$supportsArray, []],
        ];
    }
}