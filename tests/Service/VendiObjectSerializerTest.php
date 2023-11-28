<?php

namespace Vendi\VendiAlgoliaWordpressBase\Tests\Service;

use PHPUnit\Framework\TestCase;
use Vendi\VendiAlgoliaWordpressBase\Attribute\SerializeAttribute;
use Vendi\VendiAlgoliaWordpressBase\Attribute\SerializeDateTimeAttribute;
use Vendi\VendiAlgoliaWordpressBase\Attribute\SerializeFunctionAttribute;
use Vendi\VendiAlgoliaWordpressBase\Enum\DateTimeSerializationFormatEnum;
use Vendi\VendiAlgoliaWordpressBase\Service\VendiObjectSerializer;
use Vendi\VendiAlgoliaWordpressBase\Tests\includes\MyBackedEnumForTesting;

class VendiObjectSerializerTest extends TestCase
{
    public function testInheritance(): void
    {
        // Test double class to test inheritance
        $obj = new class(1) {
            public function __construct(
                #[SerializeAttribute]
                public int $a,
            ) {
            }
        };

        // Alias so that we can extend it
        class_alias($obj::class, __NAMESPACE__.'\foo');

        // Child class from the alias
        $b = new class(1, 'test') extends foo {
            public function __construct(
                int $a,

                #[SerializeAttribute]
                public string $b,
            ) {
                parent::__construct($a);
            }
        };

        $this->assertSame(['a' => 1, 'b' => 'test'], (new VendiObjectSerializer)->getAttributes($b));
    }

    public function testAllNormalizers(): void
    {
        $obj = new class(1, 'test', true, 1.1, null, new \DateTimeImmutable('2021-01-01'), ['a' => 1, 'b' => 2], MyBackedEnumForTesting::FOO) {
            public function __construct(
                #[SerializeAttribute]
                public int $a,

                #[SerializeAttribute]
                public string $b,

                #[SerializeAttribute]
                public bool $c,

                #[SerializeAttribute]
                public float $d,

                #[SerializeAttribute]
                public ?int $e,

                #[SerializeAttribute]
                public \DateTimeInterface $f,

                #[SerializeAttribute]
                public array $g,

                #[SerializeAttribute]
                public MyBackedEnumForTesting $h,
            ) {
            }
        };

        $this->assertSame(
            [
                'a' => 1,
                'b' => 'test',
                'c' => true,
                'd' => 1.1,
                'e' => null,
                'f' => 1609459200,
                'g' => ['a' => 1, 'b' => 2],
                'h' => 'foo',
            ],
            (new VendiObjectSerializer)->getAttributes($obj)
        );
    }

    public function testSkipped(): void
    {
        $obj = new class(1, 'test', 'test2') {
            public function __construct(
                #[SerializeAttribute]
                public int $a,

                public string $b,

                #[SerializeAttribute]
                public string $c,
            ) {
            }
        };

        $this->assertSame(['a' => 1, 'c' => 'test2'], (new VendiObjectSerializer)->getAttributes($obj));
    }

    public function testSimpleRename(): void
    {
        $obj = new class('test', 1) {
            public function __construct(
                #[SerializeAttribute('a')]
                public string $b,

                #[SerializeAttribute('b')]
                public int $a,
            ) {
            }
        };

        $this->assertSame(['a' => 'test', 'b' => 1], (new VendiObjectSerializer)->getAttributes($obj));
    }

    public function testSimpleOrder(): void
    {
        $obj = new class('test', 1) {
            public function __construct(
                #[SerializeAttribute]
                public string $b,

                #[SerializeAttribute]
                public int $a,
            ) {
            }
        };

        $this->assertSame(['a' => 1, 'b' => 'test'], (new VendiObjectSerializer)->getAttributes($obj));
    }

    public function testGetAttributes(): void
    {
        $obj = new class(1, 'test') {
            public function __construct(
                #[SerializeAttribute]
                public int $a,

                #[SerializeAttribute]
                public string $b,
            ) {
            }
        };

        $this->assertSame(['a' => 1, 'b' => 'test'], (new VendiObjectSerializer)->getAttributes($obj));
    }

    public function testFunctionSerializer(): void
    {
        $obj = new class() {

            #[SerializeAttribute]
            #[SerializeFunctionAttribute('getA')]
            public int $a = 5;

            public function __construct()
            {
            }

            public function getA(): string
            {
                return 'test2';
            }
        };

        $this->assertSame(['a' => 'test2'], (new VendiObjectSerializer)->getAttributes($obj));
    }
}
