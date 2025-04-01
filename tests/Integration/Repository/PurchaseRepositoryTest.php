<?php

declare(strict_types=1);

namespace PhpYabs\Tests\Integration\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PhpYabs\DTO\PurchaseListDTO;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Purchase;
use PhpYabs\Repository\BookRepository;
use PhpYabs\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(PurchaseRepository::class)]
class PurchaseRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private PurchaseRepository $purchaseRepository;

    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        $container = self::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->purchaseRepository = $container->get(PurchaseRepository::class);
        $this->bookRepository = $container->get(BookRepository::class);
    }

    public function testLatest(): void
    {
        $purchase = $this->bakePurchase();

        $this->assertSame($purchase->getId(), $this->purchaseRepository->getLatest()->getId());
    }

    public function testLatestWithTwoPurchases(): void
    {
        $purchase = $this->bakePurchase();
        $purchase = $this->bakePurchase();
        $this->assertSame($purchase->getId(), $this->purchaseRepository->getLatest()->getId());
    }

    public function testList(): void
    {
        $this->bakePurchaseWithBooks();

        $list = $this->purchaseRepository->list();
        $this->assertNotEmpty($list);

        $this->assertContainsOnlyInstancesOf(PurchaseListDTO::class, $list);
    }

    private function bakePurchaseWithBooks(): void
    {
        $purchase = $this->bakePurchase();

        $book = $this->bakeBook('123456789');
        $purchase->addBook($book);

        $this->entityManager->flush();
    }

    private function bakePurchase(): Purchase
    {
        $purchase = new Purchase();
        $this->entityManager->persist($purchase);
        $this->entityManager->flush();

        return $purchase;
    }

    private function bakeBook(string $isbn): ?Book
    {
        $book = $this->bookRepository->findOneBy(['isbn' => $isbn]);
        if (!$book) {
            $book = new Book()
                ->setIsbn($isbn)
                ->setTitle('Test Book')
                ->setAuthor('Test Author')
                ->setPublisher('Test Publisher')
                ->setPrice(Money::EUR(1000))
            ;
            $this->entityManager->persist($book);
        }

        return $book;
    }
}
