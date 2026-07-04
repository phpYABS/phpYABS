<?php

declare(strict_types=1);

namespace PhpYabs\Tests\Integration\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PhpYabs\Controller\PurchaseController;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Purchase;
use PhpYabs\Repository\BookRepository;
use PhpYabs\ValueObject\ISBN;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversClass(PurchaseController::class)]
class PurchaseControllerTest extends WebTestCase
{
    public function testViewingHistoricalPurchaseDoesNotHijackCurrentCart(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $historicalId = $this->bakePurchaseWithBook($entityManager);
        $entityManager->clear();

        $currentId = $this->requestPurchaseId($client, '/purchases/current');
        $this->assertNotSame($historicalId, $currentId);

        $viewedId = $this->requestPurchaseId($client, "/purchases/$historicalId");
        $this->assertSame($historicalId, $viewedId);

        $this->assertSame($currentId, $this->requestPurchaseId($client, '/purchases/current'));
    }

    private function requestPurchaseId(KernelBrowser $client, string $uri): int
    {
        $crawler = $client->request('GET', $uri);
        $this->assertResponseIsSuccessful();

        $this->assertSame(1, preg_match('/\\d+/', $crawler->filter('h2')->text(), $matches));

        return (int) $matches[0];
    }

    private function bakePurchaseWithBook(EntityManagerInterface $entityManager): int
    {
        $bookRepository = static::getContainer()->get(BookRepository::class);

        $stored = (string) ISBN::fromString('123456789')->version13;
        $book = $bookRepository->findOneBy(['isbn' => $stored]);
        if (!$book) {
            $book = new Book()
                ->setIsbn('123456789')
                ->setTitle('Test Book')
                ->setAuthor('Test Author')
                ->setPublisher('Test Publisher')
                ->setPrice(Money::EUR(1000))
            ;
            $entityManager->persist($book);
        }

        $purchase = new Purchase();
        $purchase->addBook($book);
        $entityManager->persist($purchase);
        $entityManager->flush();

        return (int) $purchase->getId();
    }
}
