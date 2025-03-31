<?php

declare(strict_types=1);

namespace PhpYabs\DB;

use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Hit;
use PhpYabs\Entity\Purchase;
use PhpYabs\Repository\BookRepository;
use PhpYabs\Repository\HitRepository;
use PhpYabs\Repository\PurchaseLineRepository;
use PhpYabs\Repository\PurchaseRepository;

class Acquisto
{
    private ?Purchase $purchase;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PurchaseRepository $purchaseRepository,
        private readonly PurchaseLineRepository $purchaseLineRepository,
        private readonly BookRepository $bookRepository,
        private readonly HitRepository $hitRepository,
    ) {
        $this->purchase = $this->purchaseRepository->getLatest();
        if (!$this->purchase || !$this->purchase->getLines()->isEmpty()) {
            $this->purchase = new Purchase();
        }
    }

    public function getId(): int
    {
        if (!$this->purchase->getId()) {
            $this->em->persist($this->purchase);
            $this->em->flush();
        }

        return $this->purchase->getId();
    }

    public function setId(int $id): bool
    {
        if ($id === $this->purchase->getId()) {
            return true;
        }

        $entity = $this->purchaseRepository->find($id);

        if ($entity) {
            $this->purchase = $entity;

            return true;
        }

        return false;
    }

    public function addBook(string $ISBN): bool
    {
        $book = $this->bookRepository->findOneBy(['isbn' => $ISBN]);
        $hit = $this->hitRepository->findOneBy(['isbn' => $ISBN]);
        if (!$hit) {
            $hit = new Hit()->setIsbn($ISBN);
            $this->em->persist($hit);
        }

        if ($book) {
            $this->getId(); // ensure persist of purchase
            $this->purchase->addBook($book);

            $hit->matched();
            $this->em->flush();

            return true;
        }

        $hit->missed();
        $this->em->flush();

        return false;
    }

    public function delBook(string $bookId): bool
    {
        $purchase = $this->purchaseLineRepository->findOneBy(['purchase' => $this->purchase, 'book' => $bookId]);

        if ($purchase) {
            $this->em->remove($purchase);
            $this->em->flush();

            return true;
        }

        return false;
    }

    public function numBook(): int
    {
        return $this->purchaseLineRepository->count(['purchase' => $this->purchase]);
    }

    public function getAcquisti(): iterable
    {
        $purchases = $this->purchaseLineRepository->findBy(['purchase' => $this->purchase]);

        $numero = 0;
        foreach ($purchases as $purchase) {
            $book = $purchase->getBook();

            if ($book instanceof Book) {
                $fields['bookId'] = $book->getId();
                $fields['title'] = $book->getTitle();
                $fields['author'] = $book->getAuthor();
                $fields['publisher'] = $book->getPublisher();
                $fields['price'] = $book->getPriceObject();
                $fields['ISBN'] = $book->getFullIsbn();
                $fields['rate'] = $book->getRate()->value;
                $fields['storeCredit'] = $book->getStoreCredit();
                $fields['cashValue'] = $book->getCashValue();
                $fields['dest'] = $book
                    ->getDestinations()
                    ->map(fn ($d) => $d->getDestination())
                    ->reduce(function ($a, $b) {
                        if (strlen((string) $a) > 0) {
                            return $a . ', ' . $b;
                        }

                        return $b;
                    })
                ;

                $fields['sequence'] = ++$numero;

                yield $fields;
            }
        }
    }

    /**
     * @return array<string,Money>
     */
    public function getBill(): array
    {
        $purchases = $this->purchaseLineRepository->findBy(['purchase' => $this->purchase]);

        $totaleb = Money::EUR(0);
        $totalec = Money::EUR(0);

        foreach ($purchases as $purchase) {
            $book = $purchase->getBook();
            $totaleb = $totaleb->add($book->getStoreCredit());
            $totalec = $totalec->add($book->getCashValue());
        }

        return ['totaleb' => $totaleb, 'totalec' => $totalec];
    }
}
