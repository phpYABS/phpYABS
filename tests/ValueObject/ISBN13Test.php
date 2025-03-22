<?php

namespace PhpYabs\Tests\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PhpYabs\ValueObject\ISBN13;

#[CoversClass(ISBN13::class)]
class ISBN13Test extends TestCase
{
    /**
     * @return iterable<string,string[]>
     */
    public static function invalidInputProvider(): iterable
    {
        return [
            'Empty string' => [''],
            'Too short' => ['123'],
            'Too long' => ['97801234567890'],
            'Invalid characters' => ['978-0-12-345678-X'],
            'Invalid ISBN-10 check digit' => ['0123456788'],
            'Invalid ISBN-13 check digit' => ['9780123456788'],
            'Non-digit characters' => ['978-0-12-345678-X'],
            'Letters in ISBN' => ['978012345678X'],
            'Too many dashes' => ['978-3-16-148410--0'],
        ];
    }

    #[DataProvider('invalidInputProvider')]
    public function testInvalidISBN13(string $invalidInput): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ISBN13::fromString($invalidInput);
    }

    /**
     * @return iterable<string,string[]>
     */
    public static function validISBNProvider(): iterable
    {
        return [
            'Valid ISBN-10' => ['316148410X', '316148410X'],
            'Valid ISBN-13' => ['978-3-16-148410-0', '9783161484100'],
        ];
    }

    #[DataProvider('validISBNProvider')]
    public function testValidISBN(string $validInput, string $expectedString): void
    {
        $isbn = ISBN13::fromString($validInput);
        $this->assertEquals($expectedString, (string) $isbn);
    }
}
