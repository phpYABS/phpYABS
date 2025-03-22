<?php

// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

namespace PhpYabs\DB;

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
use Doctrine\DBAL\Connection;

class Acquisto extends ActiveRecord
{
    private int $ID;

    public function __construct(?Connection $dbalConnection = null)
    {
        parent::__construct(null, $dbalConnection);

        $prefix = $this->getPrefix();
        $dbal = $this->getDbalConnection();

        $IdAcquisto = $dbal->fetchOne('SELECT MAX(IdAcquisto) FROM ' . $prefix . '_acquisti') ?? 0;
        $this->ID = $IdAcquisto + 1;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function setID(int $ID): bool
    {
        $prefix = $this->getPrefix();
        $dbal = $this->getDbalConnection();

        if ($ID === $this->ID) {
            return true;
        }

        $exists = $dbal->fetchOne(
            'SELECT 1 FROM ' . $prefix . '_acquisti WHERE IdAcquisto = ?',
            [$ID]
        );

        if ($exists) {
            $this->ID = $ID;

            return true;
        }

        return false;
    }

    public function addBook(string $ISBN): string
    {
        $prefix = $this->getPrefix();
        $dbal = $this->getDbalConnection();

        $book = new Book($dbal);

        if ($book->getFromDB($ISBN)) {
            $dbal->insert($prefix . '_acquisti', [
                'IdAcquisto' => $this->ID,
                'ISBN' => $ISBN,
            ]);

            return 'si';
        }

        return 'no';
    }

    public function delBook(string $IdLibro): bool
    {
        $prefix = $this->getPrefix();
        $dbal = $this->getDbalConnection();

        if (is_numeric($IdLibro)) {
            $dbal->delete($prefix . '_acquisti', [
                'IdLibro' => $IdLibro,
                'IdAcquisto' => $this->ID,
            ]);

            return true;
        }

        return false;
    }

    public function numBook(): int
    {
        $prefix = $this->getPrefix();
        $dbal = $this->getDbalConnection();

        return (int) $dbal->fetchOne(
            'SELECT COUNT(*) FROM ' . $prefix . '_acquisti WHERE IdAcquisto = ?',
            [$this->ID]
        );
    }

    public function printAcquisto(): void
    {
        $prefix = $this->getPrefix();
        $dbal = $this->getDbalConnection();

        $books = $dbal->fetchAllAssociative(
            'SELECT IdLibro, ISBN FROM ' . $prefix . '_acquisti WHERE IdAcquisto = ?',
            [$this->ID]
        );

        if (!$books) {
            return;
        }

        $numero = 1;
        foreach ($books as $row) {
            $book = new Book($dbal);

            if ($book->getFromDB($row['ISBN']) && is_array($fields = $book->getFields())) {
                [$ISBN, $Titolo, $Autore, $Editore, $Prezzo] = $fields;
                $sISBN = $ISBN;
                $ISBN = $book->getFullIsbn();
                $Valutazione = $book->getValutazione();
                $Buono = $book->getBuono();
                $Contanti = $book->getContanti();

                include \PATH_TEMPLATES . '/oldones/acquisti/tabview.php';
                ++$numero;
            }
        }
    }

    /**
     * @return array<string,float>
     */
    public function getBill(): array
    {
        $prefix = $this->getPrefix();
        $dbal = $this->getDbalConnection();

        $totaleb = $totalec = $totaler = 0.0;

        $books = $dbal->fetchAllAssociative(
            'SELECT ISBN FROM ' . $prefix . '_acquisti WHERE IdAcquisto = ?',
            [$this->ID]
        );

        foreach ($books as $fields) {
            $book = new Book($dbal);

            if ($book->getFromDB($fields['ISBN'])) {
                switch ($book->getValutazione()) {
                    case 'rotmed':
                        $totaler += 0.5;
                        break;
                    case 'rotsup':
                        $totaler += 1.0;
                        break;
                    case 'buono':
                        $prezzo = $book->getPrezzo();
                        $totaleb += round($prezzo / 3, 2);
                        $totalec += round($prezzo / 4, 2);
                        break;
                }
            }
        }

        return ['totaleb' => $totaleb, 'totalec' => $totalec, 'totaler' => $totaler];
    }
}
