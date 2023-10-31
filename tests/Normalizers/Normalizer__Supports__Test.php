<?php /** @noinspection IdentifierGrammar */

namespace Vendi\VendiAlgoliaWordpressBase\Tests\Normalizers;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\ArrayNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\BackedEnumNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\BooleanNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\FloatNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\IntegerNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\NullNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\ObjectNormalizer;
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
        return self::makeSupports(supportsFloat: true);
    }

    public static function dataForArrayNormalizer(): array
    {
        return self::makeSupports(supportsArray: true);
    }

    public static function dataForObjectNormalizer(): array
    {
        return self::makeSupports(supportsObject: true);
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
        $normalizer = new ArrayNormalizer();
        $this->assertSame($supports, $normalizer->supports($type));
    }

    #[DataProvider('dataForNullNormalizer')]
    public function testNullNormalizer(bool $supports, mixed $type): void
    {
        $normalizer = new NullNormalizer();
        $this->assertSame($supports, $normalizer->supports($type));
    }

    #[DataProvider('dataForObjectNormalizer')]
    public function testObjectNormalizer(bool $supports, mixed $type): void
    {
        $normalizer = new ObjectNormalizer();
        $this->assertSame($supports, $normalizer->supports($type));
    }

    #[DataProvider('dataForStringNormalizer')]
    public function testStringNormalizer(bool $supports, mixed $type): void
    {
        $normalizer = new StringNormalizer();
        $this->assertSame($supports, $normalizer->supports($type));
    }

    #[DataProvider('dataForIntegerNormalizer')]
    public function testIntegerNormalizer(bool $supports, mixed $type): void
    {
        $normalizer = new IntegerNormalizer();
        $this->assertSame($supports, $normalizer->supports($type));
    }

    #[DataProvider('dataForFloatNormalizer')]
    public function testFloatNormalizer(bool $supports, mixed $type): void
    {
        $normalizer = new FloatNormalizer();
        $this->assertSame($supports, $normalizer->supports($type));
    }

    #[DataProvider('dataForBooleanNormalizer')]
    public function testBooleanNormalizer(bool $supports, mixed $type): void
    {
        $normalizer = new BooleanNormalizer();
        $this->assertSame($supports, $normalizer->supports($type));
    }

    #[DataProvider('dataForBackedEnumNormalizer')]
    public function testBackedEnumNormalizer(bool $supports, mixed $type): void
    {
        $normalizer = new BackedEnumNormalizer();
        $this->assertSame($supports, $normalizer->supports($type));
    }

    #[DataProvider('dataForUnitEnumNormalizer')]
    public function testUnitEnumNormalizer(bool $supports, mixed $type): void
    {
        $normalizer = new UnitEnumNormalizer();
        $this->assertSame($supports, $normalizer->supports($type));
    }

    private static function makeSupports(
        bool $supportsBoolean = false,
        bool $supportsObject = false,
        bool $supportsString = false,
        bool $supportsInteger = false,
        bool $supportsFloat = false,
        bool $supportsNull = false,
        bool $supportsArray = false,
        bool $supportsBackedEnum = false,
        bool $supportsUnitEnum = false,
    ): array {
        return [
            'Null' => [$supportsNull, null],
            'Boolean' => [$supportsBoolean, true],
            'Int' => [$supportsInteger, 1],
            'Float' => [$supportsFloat, 1.1],
            'String' => [$supportsString, ''],
            'stdClass' => [$supportsObject, new stdClass()],
            'DateTimeImmutable' => [$supportsObject, new DateTimeImmutable()],
            'BackedEnum' => [$supportsBackedEnum, MyBackedEnumForTesting::FOO],
            'UnitEnum' => [$supportsUnitEnum, MyNonBackedEnumForTesting::FOO],
            'Array' => [$supportsArray, []],
        ];
    }
}