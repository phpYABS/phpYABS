<?php

declare(strict_types=1);

namespace PhpYabs\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Purchase;

#[CoversClass(Purchase::class)]
class PurchaseTest extends TestCase
{
    public function testAddBookToEmptyPurchase(): void
    {
        $purchase = new Purchase();
        $purchase->addBook($this->makeBook(1));

        $this->assertCount(1, $purchase->getLines());
    }

    public function testAddSecondBookCreatesSecondLine(): void
    {
        $purchase = new Purchase();
        $purchase->addBook($this->makeBook(1));
        $purchase->addBook($this->makeBook(2));

        $this->assertCount(2, $purchase->getLines());
    }

    public function testAddSameBookTwiceKeepsSingleLine(): void
    {
        $book = $this->makeBook(1);

        $purchase = new Purchase();
        $purchase->addBook($book);
        $purchase->addBook($book);

        $this->assertCount(1, $purchase->getLines());
    }

    public function testRemoveBook(): void
    {
        $bookToRemove = $this->makeBook(1);

        $purchase = new Purchase();
        $purchase->addBook($bookToRemove);
        $purchase->addBook($this->makeBook(2));
        $purchase->removeBook($bookToRemove);

        $this->assertCount(1, $purchase->getLines());
    }

    private function makeBook(int $id): Book
    {
        return new Book()
            ->setId($id)
            ->setIsbn('123456789')
            ->setTitle('Test Book')
            ->setAuthor('Test Author')
            ->setPublisher('Test Publisher')
        ;
    }
}
