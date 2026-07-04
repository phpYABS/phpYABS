<?php

declare(strict_types=1);

namespace PhpYabs\Tests\Integration\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PhpYabs\Controller\BookController;
use PhpYabs\Entity\Book;
use PhpYabs\Repository\BookRepository;
use PhpYabs\ValueObject\ISBN;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversClass(BookController::class)]
class BookControllerTest extends WebTestCase
{
    public function testDeleteConfirmationPageRendersBookDetails(): void
    {
        $client = static::createClient();

        $isbn = $this->bakeBook('316148410');

        $client->request('GET', "/books/$isbn/delete");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.book-delete', 'Test Book');
    }

    private function bakeBook(string $isbn): string
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $bookRepository = static::getContainer()->get(BookRepository::class);

        $stored = (string) ISBN::fromString($isbn)->version13;

        if (!$bookRepository->findOneBy(['isbn' => $stored])) {
            $book = new Book()
                ->setIsbn($isbn)
                ->setTitle('Test Book')
                ->setAuthor('Test Author')
                ->setPublisher('Test Publisher')
                ->setPrice(Money::EUR(1000))
            ;
            $entityManager->persist($book);
            $entityManager->flush();
        }

        return $stored;
    }
}
