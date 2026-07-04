<?php

declare(strict_types=1);

namespace PhpYabs\Tests\Integration\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Purchase;
use PhpYabs\Repository\BookRepository;
use PhpYabs\Repository\StatisticsRepository;
use PhpYabs\ValueObject\ISBN;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(StatisticsRepository::class)]
class StatisticsRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private StatisticsRepository $statisticsRepository;
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        $container = self::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->statisticsRepository = $container->get(StatisticsRepository::class);
        $this->bookRepository = $container->get(BookRepository::class);
    }

    public function testBooksPurchasedCountsBooksIncludingQuantities(): void
    {
        // the database persists between runs, so assert on the delta
        $before = $this->statisticsRepository->getStatistics();

        $purchase = new Purchase();
        $purchase->addBook($this->bakeBook('316148410'));

        $twoCopies = $this->bakeBook('045122474');
        $purchase->addBook($twoCopies);
        $purchase->addBook($twoCopies);

        $this->entityManager->persist($purchase);
        $this->entityManager->flush();

        $after = $this->statisticsRepository->getStatistics();

        $this->assertSame(3, (int) $after['books_purchased'] - (int) $before['books_purchased']);
        $this->assertSame(1, (int) $after['purchases_count'] - (int) $before['purchases_count']);
    }

    private function bakeBook(string $isbn): Book
    {
        $stored = (string) ISBN::fromString($isbn)->version13;
        $book = $this->bookRepository->findOneBy(['isbn' => $stored]);
        if (!$book) {
            $book = new Book()
                ->setIsbn($isbn)
                ->setTitle('Test Book')
                ->setAuthor('Test Author')
                ->setPublisher('Test Publisher')
                ->setPrice(Money::EUR(1000))
            ;
            $this->entityManager->persist($book);
            $this->entityManager->flush();
        }

        return $book;
    }
}
