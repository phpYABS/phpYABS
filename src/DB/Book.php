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
class Book extends ActiveRecord
{
    public $_fields;
    public $_valutazione;

    public function __construct()
    {
        parent::__construct();

        $this->fields = [];
        $this->setValutazione(false);
    }

    public function _getDb()
    {
        return $this->_db;
    }

    public function checkFields($fields)
    {
        if ($this->isValidISBN($fields['ISBN']) && strlen($fields['Titolo']) > 0) {
            return true;
        } else {
            return false;
        }
    }

    //imposta i campi del libro
    public function setFields(array $fields)
    {
        $fields['ISBN'] = $this->GetShortISBN($fields['ISBN']);

        if ($this->CheckFields($fields)) {
            foreach ($fields as $key => $value) {
                $this->fields[$key] = strtoupper(addslashes($value));
            }

            return true;
        } else {
            return false;
        }
    }

    public function getFields()
    {
        if ($this->CheckFields($this->fields)) {
            return $this->fields;
        } else {
            return false;
        }
    }

    public function setValutazione($valutazione)
    {
        switch ($valutazione) {
            case 'zero':
            case 'rotmed':
            case 'rotsup':
            case 'buono':
                break;
            default:
                $valutazione = false;
        }

        $this->_valutazione = $valutazione;
    }

    public function getValutazione()
    {
        return $this->_valutazione;
    }

    public function getBuono()
    {
        switch ($this->getValutazione()) {
            default:
            case 'zero':
                $prezzoa = '0.00';
                break;
            case 'rotmed':
                $prezzoa = '0.50';
                break;
            case 'rotsup':
                $prezzoa = '1.00';
                // no break
            case 'buono':
                $prezzoa = round($this->fields['Prezzo'] / 3, 2);
                break;
        }

        return $prezzoa;
    }

    public function getContanti()
    {
        switch ($this->getValutazione()) {
            default:
            case 'zero':
                $prezzoa = '0.00';
                break;
            case 'rotmed':
                $prezzoa = '0.50';
                break;
            case 'rotsup':
                $prezzoa = '1.00';
                break;
            case 'buono':
                $prezzoa = round($this->fields['Prezzo'] / 4, 2);
                break;
        }

        return $prezzoa;
    }

    public function saveToDB()
    {
        $prefix = $this->getPrefix();

        if ($this->checkFields($this->fields)) {
            $rset = $this->_db->Execute('SELECT * FROM ' . $prefix . "_libri WHERE ISBN='" . $this->fields['ISBN'] . "'");

            if (!$rset->EOF) {
                $updateSQL = $this->_db->GetUpdateSQL($rset, $this->fields);
                if ($updateSQL) {
                    $this->_db->Execute($updateSQL);
                }
            } else {
                $insertSQL = $this->_db->GetInsertSQL($rset, $this->fields);

                if ($insertSQL) {
                    $this->_db->Execute($insertSQL);
                }
            }

            $rset->Close();

            if ($this->_valutazione) {
                $valfields = ['ISBN' => $this->fields['ISBN'], 'Valutazione' => $this->_valutazione];

                $rset = $this->_db->Execute('SELECT * FROM ' . $prefix . "_valutazioni WHERE ISBN='" . $this->fields['ISBN'] . "'");

                if ($rset->RecordCount()) {
                    $updateSQL = $this->_db->GetUpdateSQL($rset, $valfields);
                    if ($updateSQL) {
                        $this->_db->Execute($updateSQL);
                    }
                } else {
                    $insertSQL = $this->_db->GetInsertSQL($rset, $valfields);
                    if ($insertSQL) {
                        $this->_db->Execute($insertSQL);
                    }
                }
                $rset->Close();
            } else {
                $this->_db->Execute('DELETE FROM ' . $prefix . "_valutazioni WHERE ISBN='" . $this->fields['ISBN'] . "'");
            }
        }

        $rset = $this->_db->Execute('SELECT ISBN FROM ' . $prefix . "_libri WHERE ISBN='" . $this->fields['ISBN'] . "'");
        $esiste = $rset->RecordCount();
        $rset->Close();

        return $esiste > 0;
    }

    //carica i dati dal database, specificato l'isbn
    public function getFromDB($ISBN)
    {
        global $prefix;

        $ISBN = $this->getShortISBN($ISBN);

        if ($this->isValidISBN($ISBN)) {
            $rset = $this->_db->Execute('SELECT ISBN, Titolo, Autore, Editore, Prezzo FROM ' . $prefix . "_libri WHERE ISBN='$ISBN'");
            $this->SetFields($rset->fields);

            if ($rset->RecordCount()) {
                $rset->Close();

                $rset = $this->_db->Execute('SELECT valutazione FROM ' . $prefix . "_valutazioni WHERE ISBN='$ISBN'");

                [$Valutazione] = $rset->fields;
                $rset->Close();
                $this->setValutazione($Valutazione);

                return true;
            } else {
                $rset->Close();

                return false;
            }
        } else {
            return false;
        }
    }

    public function delete()
    {
        global $prefix;

        $ISBN = $this->fields['ISBN'];
        if ($this->IsValidISBN($ISBN)) {
            $this->_db->Execute('DELETE FROM ' . $prefix . "_libri WHERE ISBN = '$ISBN'");
            $this->_db->Execute('DELETE FROM ' . $prefix . "_valutazioni WHERE ISBN = '$ISBN'");
            $this->_db->Execute('DELETE FROM ' . $prefix . "_destinazioni WHERE ISBN = '$ISBN'");

            return true;
        }

        return false;
    }

    public function isValidISBN($ISBN)
    {
        return is_numeric($ISBN) && 9 == strlen($ISBN) && 0 != $ISBN;
    }

    public function getShortISBN($ISBN)
    {
        if (strlen($ISBN) > 9) {
            $ISBN = substr($ISBN, 0, strlen($ISBN) - 1);
        }

        while (strlen($ISBN) > 9) {
            $ISBN = substr($ISBN, 1, strlen($ISBN) - 1);
        }

        if ($this->isValidISBN($ISBN)) {
            return $ISBN;
        } else {
            return false;
        }
    }

    public function getFullIsbn()
    {
        $ISBN = $this->fields['ISBN'];
        $ISBN .= $this->ISBNCheck($ISBN);

        return $ISBN;
    }

    public function isbnCheck($ISBN)
    {
        $checksum = 0;

        for ($i = 0; $i < 9; ++$i) {
            $checksum += ($i + 1) * (int) $ISBN[$i];
        }

        $checksum %= 11;
        if (10 == $checksum) {
            $checksum = 'X';
        }

        return $checksum;
    }

    public function getEAN($ISBN = -1)
    {
        if (-1 == $ISBN) {
            $ISBN = $this->fields['ISBN'];
        }

        if ($this->GetShortISBN($ISBN)) {
            $EAN = '978' . $ISBN;
            $EAN .= $this->EANCheck($EAN);
        } else {
            $EAN = false;
        }

        return $EAN;
    }

    public function EANCheck(string $ean): int
    {
        $checksum = 0;

        for ($i = 2; $i <= 12; $i += 2) {
            $checksum += (int)substr($ean, $i - 1, 1);
        }

        $checksum *= 3;

        for ($i = 1; $i <= 11; $i += 2) {
            $checksum += (int)substr($ean, $i - 1, 1);
        }

        return 10 - ($checksum % 10);
    }
}
