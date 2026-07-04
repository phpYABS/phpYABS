<?php

declare(strict_types=1);

namespace PhpYabs\Service;

use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use PhpYabs\DTO\PurchaseLineDTO;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Hit;
use PhpYabs\Entity\Purchase;
use PhpYabs\Repository\BookRepository;
use PhpYabs\Repository\HitRepository;
use PhpYabs\Repository\PurchaseRepository;
use PhpYabs\ValueObject\ISBN;

class PurchaseService
{
    private ?Purchase $purchase;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PurchaseRepository $purchaseRepository,
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
        try {
            $isbn = ISBN::fromString($ISBN);
        } catch (\InvalidArgumentException) {
            return false;
        }

        $isbn13 = (string) $isbn->version13;
        $book = $this->bookRepository->findOneBy(['isbn' => $isbn13]);

        // the hits table is keyed on the 9-digit ISBN core (CHAR(9) column);
        // derive it from the ISBN-13 to avoid the lossy 13-to-10 conversion
        $hitKey = substr($isbn13, 3, 9);
        $hit = $this->hitRepository->findOneBy(['isbn' => $hitKey]);
        if (!$hit) {
            $hit = new Hit()->setIsbn($hitKey);
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

    public function delBook(string $bookId): void
    {
        $book = $this->bookRepository->find($bookId);
        if (!$book) {
            return;
        }

        $this->purchase->removeBook($book);
        $this->em->flush();
    }

    public function count(): int
    {
        return $this->purchase->getLines()->count();
    }

    /**
     * @return iterable<PurchaseLineDTO>
     */
    public function getLines(): iterable
    {
        foreach ($this->purchase->getLines() as $i => $line) {
            $book = $line->getBook();

            if ($book instanceof Book) {
                yield new PurchaseLineDTO(
                    bookId: $book->getId(),
                    quantity: $line->getQuantity(),
                    title: $book->getTitle(),
                    author: $book->getAuthor(),
                    publisher: $book->getPublisher(),
                    price: $book->getPrice(),
                    fullISBN: $book->getFullIsbn(),
                    ISBN: $book->getIsbnWithoutChecksum(),
                    rate: $book->getRate()->value,
                    storeCredit: $book->getStoreCredit(),
                    cashValue: $book->getCashValue(),
                    dest: $book->getDestinations(),
                    sequence: $i + 1,
                );
            }
        }
    }

    /**
     * @return array<string,Money>
     */
    public function getBill(): array
    {
        $this->getId(); // ensure persist of purchase

        return $this->purchase->getBill();
    }
}
