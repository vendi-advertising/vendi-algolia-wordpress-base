<?php

namespace Vendi\VendiAlgoliaWordpressBase\Tests\Normalizers;

use PHPUnit\Framework\TestCase;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\ArrayNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\BooleanNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\FloatNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\IntegerNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\StringNormalizer;

class Normalizer__Normalize__Test extends TestCase
{
    public function testArrayNormalizerNormalize(): void
    {
        $normalizer = new ArrayNormalizer();
        $this->assertSame([], $normalizer->normalize([]));
    }

    public function testStringNormalizerNormalize(): void
    {
        $normalizer = new StringNormalizer();
        $this->assertSame('foo', $normalizer->normalize('foo'));
        $this->assertSame('01', $normalizer->normalize('01'));
    }

    public function testIntegerNormalizerNormalize(): void
    {
        $normalizer = new IntegerNormalizer();
        $this->assertSame(1, $normalizer->normalize(001));

        $this->expectExceptionMessage('Unsupported normalizer type: float');
        $this->assertSame(1, $normalizer->normalize(1.1));
    }

    public function testFloatNormalizerNormalize(): void
    {
        $normalizer = new FloatNormalizer();
        $this->assertSame(1.1, $normalizer->normalize(1.100));
        $this->assertSame(1, $normalizer->normalize(1));
    }

    public function testBoolNormalizerNormalize(): void
    {
        $normalizer = new BooleanNormalizer();
        $this->assertTrue($normalizer->normalize(true));
        $this->assertFalse($normalizer->normalize(false));

        $this->expectExceptionMessage('Unsupported normalizer type: int');
        $this->assertFalse($normalizer->normalize(1));

        $this->expectExceptionMessage('Unsupported normalizer type: string');
        $this->assertFalse($normalizer->normalize('true'));
    }
}