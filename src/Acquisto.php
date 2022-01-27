<?php

// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

namespace PhpYabs;

use ADOConnection;

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
class Acquisto extends Db
{
    public $books;
    public $ID;

    public function __construct(ADOConnection $connection = null)
    {
        parent::__construct($connection);

        $this->books = [];
        $prefix = $this->getPrefix();
        $conn = $this->_db;

        $rset = $conn->Execute('SELECT MAX(IdAcquisto) FROM ' . $prefix . '_acquisti');
        list($IdAcquisto) = $rset->fields;
        $rset->Close();

        $this->ID = $IdAcquisto + 1;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function setID($ID)
    {
        global $prefix;

        if ($ID != $this->ID) {
            $rset = $this->_db->Execute('SELECT * FROM ' . $prefix . "_acquisti WHERE IdAcquisto='$ID'");

            if (!$rset->EOF) {
                $this->ID = $ID;
                $ok = true;
            } else {
                $ok = false;
            }
            $rset->Close();
        } else {
            $ok = true;
        }

        return $ok;
    }

    public function addBook($ISBN)
    {
        global $prefix;

        $book = new Book();

        if ($book->getFromDB($ISBN)) {
            $this->_db->Execute('INSERT INTO ' . $prefix . "_acquisti (IdAcquisto,ISBN) VALUES ('" . $this->ID . "','$ISBN')");
            $trovato = 'si';
        } else {
            $trovato = 'no';
        }
    }

    public function delBook($IdLibro)
    {
        global $prefix;

        if (is_numeric($IdLibro)) {
            $this->_db->Execute('DELETE FROM ' . $prefix . "_acquisti WHERE IdLibro='$IdLibro' AND IdAcquisto='" . $this->ID . "'");

            return true;
        } else {
            return false;
        }
    }

    public function numBook()
    {
        global $prefix;
        $rset = $this->_db->Execute('SELECT * FROM ' . $prefix . "_acquisti WHERE IdAcquisto='" . $this->ID . "'");

        return $rset->RecordCount();
    }

    public function printAcquisto()
    {
        global $prefix;
        $rset = $this->_db->Execute('SELECT IdLibro, ISBN FROM ' . $prefix . "_acquisti WHERE IdAcquisto='" . $this->ID . "'");
        $numero = 1;

        while (!$rset->EOF) {
            list($IdLibro, $ISBN) = $rset->fields;
            $book = new Book();

            if ($book->getFromDB($ISBN)) {
                list($ISBN, $Titolo, $Autore, $Editore, $Prezzo) = $book->GetFields();
                $sISBN = $ISBN;
                $ISBN = $book->GetFullISBN();
                $Valutazione = $book->GetValutazione();
                $Buono = $book->GetBuono();
                $Contanti = $book->GetContanti();

                include PATH_TEMPLATES . '/oldones/acquisti/tabview.php';
                ++$numero;
            }

            $rset->MoveNext();
        }
    }

    public function getBill()
    {
        global $prefix;

        $totaleb = '0.00';
        $totalec = '0.00';
        $totaler = '0.00';

        $rset = $this->_db->Execute('SELECT ISBN From ' . $prefix . "_acquisti WHERE IdAcquisto ='" . $this->ID . "'");

        while (!$rset->EOF) {
            $book = new Book();

            if ($book->GetFromDB($rset->fields['ISBN'])) {
                switch ($book->getValutazione()) {
                    case 'rotmed':
                        $totaler += 0.5;
                        break;
                    case 'rotsup':
                        $totaler += 1.00;
                        break;
                    case 'buono':
                        $totaleb += round($book->fields['Prezzo'] / 3, 2);
                        $totalec += round($book->fields['Prezzo'] / 4, 2);
                        break;
                }
            }

            $rset->MoveNext();
        }

        return ['totaleb' => $totaleb, 'totalec' => $totalec, 'totaler' => $totaler];
    }
}
