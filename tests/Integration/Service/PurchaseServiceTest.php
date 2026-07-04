<?php

declare(strict_types=1);

namespace PhpYabs\Tests\Integration\Service;

use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PhpYabs\Entity\Book;
use PhpYabs\Repository\BookRepository;
use PhpYabs\Repository\HitRepository;
use PhpYabs\Repository\PurchaseRepository;
use PhpYabs\Service\PurchaseService;
use PhpYabs\ValueObject\ISBN;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(PurchaseService::class)]
class PurchaseServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private PurchaseRepository $purchaseRepository;
    private BookRepository $bookRepository;
    private HitRepository $hitRepository;

    protected function setUp(): void
    {
        $container = self::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->purchaseRepository = $container->get(PurchaseRepository::class);
        $this->bookRepository = $container->get(BookRepository::class);
        $this->hitRepository = $container->get(HitRepository::class);
    }

    public function testAddTwoBooksAndGetLines(): void
    {
        $isbn1 = $this->bakeBook('123456789');
        $isbn2 = $this->bakeBook('316148410');
        $this->entityManager->clear();

        $service = $this->makeService();

        $this->assertTrue($service->addBook($isbn1));
        $this->assertTrue($service->addBook($isbn2));
        $this->assertSame(2, $service->count());

        // scanning the ISBN-10 printed on an older book must find the ISBN-13 stored one
        $this->assertTrue($service->addBook((string) ISBN::fromString($isbn2)->version10));
        $this->assertSame(2, $service->count());

        $this->assertFalse($service->addBook('not-an-isbn'));

        $lines = iterator_to_array($service->getLines(), false);

        $this->assertCount(2, $lines);
        $this->assertTrue(Money::EUR(1000)->equals($lines[0]->price));
        $this->assertSame(1, $lines[0]->sequence);
        $this->assertSame(2, $lines[1]->sequence);
    }

    private function makeService(): PurchaseService
    {
        return new PurchaseService(
            $this->entityManager,
            $this->purchaseRepository,
            $this->bookRepository,
            $this->hitRepository,
        );
    }

    /**
     * Returns the ISBN-13 under which the book is stored.
     */
    private function bakeBook(string $isbn): string
    {
        $stored = (string) ISBN::fromString($isbn)->version13;

        if (!$this->bookRepository->findOneBy(['isbn' => $stored])) {
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

        return $stored;
    }
}
