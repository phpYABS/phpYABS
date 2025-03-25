<?php

declare(strict_types=1);

// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

namespace PhpYabs\DB;

use Doctrine\DBAL\Connection;
/**
 * $Id: file-header.php 299 2009-11-21 17:09:54Z dvbellet $.
 *
 * phpYABS - Web-based book management
 * Copyright (C) 2009 Davide Bellettini
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @author Davide Bellettini <dvbellet@users.sourceforge.net>
 * @license GNU General Public License
 */

use Doctrine\ORM\EntityManagerInterface;
use PhpYabs\Entity\Book;
use PhpYabs\Repository\BookRepository;

class Acquisto extends ActiveRecord
{
    private int $ID;
    private BookRepository $bookRepository;

    public function __construct(Connection $dbal, private EntityManagerInterface $em)
    {
        parent::__construct($dbal);

        $this->bookRepository = $em->getRepository(Book::class);

        $purchase_id = $dbal->fetchOne('SELECT MAX(purchase_id) FROM purchases') ?? 0;
        $this->ID = $purchase_id + 1;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function setID(int $ID): bool
    {
        $dbal = $this->getDbalConnection();

        if ($ID === $this->ID) {
            return true;
        }

        $exists = $dbal->fetchOne(
            'SELECT 1 FROM purchases WHERE purchase_id = ?',
            [$ID],
        );

        if ($exists) {
            $this->ID = $ID;

            return true;
        }

        return false;
    }

    public function addBook(string $ISBN): string
    {
        $dbal = $this->getDbalConnection();

        $book = $this->bookRepository->findOneBy(['isbn' => $ISBN]);

        if ($book) {
            $dbal->insert('purchases', [
                'purchase_id' => $this->ID,
                'book_id' => $book->getId(),
            ]);

            return 'si';
        }

        return 'no';
    }

    public function delBook(string $bookId): bool
    {
        $dbal = $this->getDbalConnection();

        if (is_numeric($bookId)) {
            $dbal->delete('purchases', [
                'book_id' => $bookId,
                'purchase_id' => $this->ID,
            ]);

            return true;
        }

        return false;
    }

    public function numBook(): int
    {
        $dbal = $this->getDbalConnection();

        return (int) $dbal->fetchOne(
            'SELECT COUNT(*) FROM purchases WHERE purchase_id = ?',
            [$this->ID],
        );
    }

    public function getAcquisti(): iterable
    {
        $dbal = $this->getDbalConnection();

        $books = $dbal->fetchAllAssociative(
            'SELECT purchase_id, book_id FROM purchases WHERE purchase_id = ?',
            [$this->ID],
        );

        if (!$books) {
            return;
        }

        $numero = 1;
        foreach ($books as $row) {
            $book = $this->bookRepository->find($row['book_id']);

            if ($book instanceof Book) {
                $fields['bookId'] = $row['book_id'];
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
        $dbal = $this->getDbalConnection();

        $totaleb = $totalec = $totaler = 0.0;

        $sql = <<<SQL
        SELECT b.rate, b.price
        FROM purchases p
        INNER JOIN books b ON p.book_id = b.id
        WHERE p.purchase_id = ?
        SQL;

        $books = $dbal->executeQuery($sql, [$this->ID]);

        while (false !== ($book = $books->fetchAssociative())) {
            switch ($book['rate']) {
                case 'rotmed':
                    $totaler += 0.5;
                    break;
                case 'rotsup':
                    $totaler += 1.0;
                    break;
                case 'buono':
                    $prezzo = $book['price'];
                    $totaleb += round($prezzo / 3, 2);
                    $totalec += round($prezzo / 4, 2);
                    break;
            }
        }

        return ['totaleb' => $totaleb, 'totalec' => $totalec, 'totaler' => $totaler];
    }
}
