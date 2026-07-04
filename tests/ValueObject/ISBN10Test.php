<?php

declare(strict_types=1);

namespace PhpYabs\Tests\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PhpYabs\ValueObject\ISBN;
use PhpYabs\ValueObject\ISBN10;

#[CoversClass(ISBN10::class)]
class ISBN10Test extends TestCase
{
    public function testWithoutChecksum(): void
    {
        $isbn10 = ISBN::fromString('978-3-16-148410-0')->version10;

        $this->assertSame('316148410', $isbn10->withoutChecksum);
    }

    public function testFromNineDigitsComputesXCheckDigit(): void
    {
        $this->assertSame('123456789X', (string) ISBN10::fromNineDigits('123456789'));
    }

    public function test979PrefixedIsbn13HasNoIsbn10Equivalent(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ISBN::fromString('9791234567896')->version10;
    }
}
