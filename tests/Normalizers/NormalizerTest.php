<?php

namespace Vendi\VendiAlgoliaWordpressBase\Tests\Normalizers;

use PHPUnit\Framework\TestCase;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\ArrayNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\NullNormalizer;
use Vendi\VendiAlgoliaWordpressBase\Normalizers\ObjectNormalizer;

class NormalizerTest extends TestCase
{
    public function testArrayNormalizer()
    {
        $normalizer = new ArrayNormalizer();
        $this->assertTrue($normalizer->supports([]));
        $this->assertFalse($normalizer->supports(''));
        $this->assertFalse($normalizer->supports(1));
        $this->assertFalse($normalizer->supports(null));
    }

    public function testNullNormalizer()
    {
        $normalizer = new NullNormalizer();
        $this->assertTrue($normalizer->supports(null));
        $this->assertFalse($normalizer->supports(''));
        $this->assertFalse($normalizer->supports(1));
        $this->assertFalse($normalizer->supports([]));
    }

    public function testObjectNormalizer()
    {
        $normalizer = new ObjectNormalizer();
        $this->assertTrue($normalizer->supports(new \DateTimeImmutable()));
        $this->assertTrue($normalizer->supports(new \DateTime()));
        $this->assertTrue($normalizer->supports(new \stdClass()));
        $this->assertFalse($normalizer->supports(''));
        $this->assertFalse($normalizer->supports(1));
    }
}