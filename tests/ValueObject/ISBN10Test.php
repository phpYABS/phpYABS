<?php

namespace PhpYabs\Tests\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PhpYabs\ValueObject\ISBN;
use PhpYabs\ValueObject\ISBN10;

#[CoversClass(ISBN10::class)]
class ISBN10Test extends TestCase
{
    public function testWithoutChecksum()
    {
        $isbn10 = ISBN::fromString('978-3-16-148410-0')->version10;

        $this->assertSame('316148410', $isbn10->withoutChecksum);
    }
}
