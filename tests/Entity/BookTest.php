<?php

declare(strict_types=1);

namespace PhpYabs\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PhpYabs\Entity\Book;

#[CoversClass(Book::class)]
class BookTest extends TestCase
{
    public function testFullIsbnIsTheIsbn10For978Books(): void
    {
        $book = new Book()->setIsbn('9783161484100');

        $this->assertSame('316148410X', $book->getFullIsbn());
    }

    public function testFullIsbnFallsBackToIsbn13For979Books(): void
    {
        $book = new Book()->setIsbn('9791234567896');

        $this->assertSame('9791234567896', $book->getFullIsbn());
    }

    public function testIsbnWithoutChecksum(): void
    {
        $this->assertSame('316148410', new Book()->setIsbn('9783161484100')->getIsbnWithoutChecksum());
        $this->assertSame('123456789', new Book()->setIsbn('9791234567896')->getIsbnWithoutChecksum());
    }

    public function testDestinationsAreEmptyOnANewBook(): void
    {
        $this->assertSame('', new Book()->getDestinations());
    }
}
