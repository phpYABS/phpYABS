<?php

declare(strict_types=1);

namespace PhpYabs\DB;

use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Purchase;
use PhpYabs\Entity\Rate;
use PhpYabs\Repository\BookRepository;
use PhpYabs\Repository\PurchaseRepository;

class Acquisto
{
    private int $ID;

    public function __construct(
        private EntityManagerInterface $em,
        private readonly PurchaseRepository $purchaseRepository,
        private readonly BookRepository $bookRepository,
    ) {
        $this->ID = $this->purchaseRepository->getCurrentId() + 1;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function setID(int $ID): bool
    {
        if ($ID === $this->ID) {
            return true;
        }

        $entity = $this->purchaseRepository->findOneBy(['purchaseId' => $ID]);

        if ($entity) {
            $this->ID = $ID;

            return true;
        }

        return false;
    }

    public function addBook(string $ISBN): bool
    {
        $book = $this->bookRepository->findOneBy(['isbn' => $ISBN]);

        if ($book) {
            $purchase = new Purchase();
            $purchase->setBook($book)
                ->setPurchaseId($this->ID)
            ;

            $this->em->persist($purchase);
            $this->em->flush();

            return true;
        }

        return false;
    }

    public function delBook(string $bookId): bool
    {
        $purchase = $this->purchaseRepository->findOneBy(['purchaseId' => $this->ID, 'book' => $bookId]);

        if ($purchase) {
            $this->em->remove($purchase);
            $this->em->flush();

            return true;
        }

        return false;
    }

    public function numBook(): int
    {
        return $this->purchaseRepository->count(['purchaseId' => $this->ID]);
    }

    public function getAcquisti(): iterable
    {
        $purchases = $this->purchaseRepository->findBy(['purchaseId' => $this->ID]);

        $numero = 0;
        foreach ($purchases as $purchase) {
            $book = $purchase->getBook();

            if ($book instanceof Book) {
                $fields['bookId'] = $book->getId();
                $fields['title'] = $book->getTitle();
                $fields['author'] = $book->getAuthor();
                $fields['publisher'] = $book->getPublisher();
                $fields['price'] = $book->getPrice();
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
     * @return array<string,float>
     */
    public function getBill(): array
    {
        $purchases = $this->purchaseRepository->findBy(['purchaseId' => $this->ID]);

        $totaleb = Money::EUR(0);
        $totalec = Money::EUR(0);
        $totaler = Money::EUR(0);

        foreach ($purchases as $purchase) {
            $book = $purchase->getBook();
            switch ($book->getRate()) {
                case Rate::ROTMED:
                    $totaler = $totaler->add(Money::EUR(50));
                    break;
                case Rate::ROTSUP:
                    $totaler = $totaler->add(Money::EUR(100));
                    break;
                case Rate::BUONO:
                    $prezzo = Money::EUR($book->getPrice());
                    $totaleb = $totaleb->add($prezzo->divide(3));
                    $totalec = $totalec->add($prezzo->divide(4));
                    break;
                case Rate::ZERO:
                    // do nothing
                    break;
            }
        }

        return ['totaleb' => $totaleb, 'totalec' => $totalec, 'totaler' => $totaler];
    }
}
