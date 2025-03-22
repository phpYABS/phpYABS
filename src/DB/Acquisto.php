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
class Acquisto extends ActiveRecord
{
    private int $ID;

    public function __construct(?\ADOConnection $connection = null)
    {
        parent::__construct($connection);

        $prefix = $this->getPrefix();
        $conn = $this->_db;

        $rset = $conn->Execute('SELECT MAX(IdAcquisto) FROM ' . $prefix . '_acquisti');
        $IdAcquisto = $this->fetchColumn($rset) ?? 0;

        $this->ID = $IdAcquisto + 1;
    }

    public function getID(): int
    {
        return $this->ID;
    }

    public function setID(int $ID): bool
    {
        global $prefix;

        if ($ID === $this->ID) {
            return true;
        }

        $rset = $this->_db->Execute('SELECT * FROM ' . $prefix . "_acquisti WHERE IdAcquisto='$ID'");
        if ($rset instanceof \ADORecordSet) {
            if ($ok = !$rset->EOF) {
                $this->ID = $ID;
            }
            $rset->Close();

            return $ok;
        }

        return false;
    }

    public function addBook(string $ISBN): string
    {
        global $prefix;

        $book = new Book();

        if ($book->getFromDB($ISBN)) {
            $this->_db->Execute('INSERT INTO ' . $prefix . "_acquisti (IdAcquisto,ISBN) VALUES ('" . $this->ID . "','$ISBN')");

            return 'si';
        }

        return 'no';
    }

    public function delBook(string $IdLibro): bool
    {
        global $prefix;

        if (is_numeric($IdLibro)) {
            $this->_db->Execute('DELETE FROM ' . $prefix . "_acquisti WHERE IdLibro='$IdLibro' AND IdAcquisto='" . $this->ID . "'");

            return true;
        } else {
            return false;
        }
    }

    public function numBook(): int
    {
        global $prefix;
        $rset = $this->_db->Execute('SELECT * FROM ' . $prefix . "_acquisti WHERE IdAcquisto='" . $this->ID . "'");

        return $rset instanceof \ADORecordSet ? $rset->RecordCount() : 0;
    }

    public function printAcquisto(): void
    {
        global $prefix;
        $rset = $this->_db->Execute('SELECT IdLibro, ISBN FROM ' . $prefix . "_acquisti WHERE IdAcquisto='" . $this->ID . "'");

        if (!$rset instanceof \ADORecordSet) {
            return;
        }

        $numero = 1;
        foreach ($rset as [$IdLibro, $ISBN]) {
            $book = new Book();

            if ($book->getFromDB($ISBN) && is_array($fields = $book->getFields())) {
                [$ISBN, $Titolo, $Autore, $Editore, $Prezzo] = $fields;
                $sISBN = $ISBN;
                $ISBN = $book->GetFullISBN();
                $Valutazione = $book->GetValutazione();
                $Buono = $book->GetBuono();
                $Contanti = $book->GetContanti();

                include \PATH_TEMPLATES . '/oldones/acquisti/tabview.php';
                ++$numero;
            }

            $rset->MoveNext();
        }
    }

    /**
     * @return array<string,float>
     */
    public function getBill(): array
    {
        global $prefix;

        $totaleb = $totalec = $totaler = 0.0;

        $rset = $this->_db->Execute('SELECT ISBN From ' . $prefix . "_acquisti WHERE IdAcquisto ='" . $this->ID . "'");
        if (!$rset instanceof \ADORecordSet) {
            $rset = [];
        }

        foreach ($rset as $fields) {
            $book = new Book();

            if ($book->GetFromDB($fields['ISBN'])) {
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
