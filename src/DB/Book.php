<?php

// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

namespace PhpYabs\DB;

use ADOConnection;
use ADORecordSet;

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
    public string|false $_valutazione;

    /** @var string[] */
    private array $fields;

    public function __construct(ADOConnection $connection = null)
    {
        parent::__construct($connection);

        $this->fields = [];
        $this->setValutazione(false);
    }

    /**
     * @param string[] $fields
     */
    public function checkFields(array $fields): bool
    {
        return static::isValidISBN($fields['ISBN']) && strlen($fields['Titolo']) > 0;
    }

    /**
     * @param string[] $fields
     */
    public function setFields(array $fields): bool
    {
        $fields['ISBN'] = (string) static::GetShortISBN($fields['ISBN']);

        if ($this->CheckFields($fields)) {
            foreach ($fields as $key => $value) {
                $this->fields[$key] = strtoupper(addslashes($value));
            }

            return true;
        }

        return false;
    }

    /**
     * @return string[]|false
     */
    public function getFields(): array|false
    {
        if ($this->CheckFields($this->fields)) {
            return $this->fields;
        } else {
            return false;
        }
    }

    public function getPrezzo(): ?float
    {
        $fields = $this->getFields();
        if (!is_array($fields)) {
            return null;
        }

        return (float) ($fields['Prezzo'] ?? 0.0);
    }

    public function setValutazione(string|false $valutazione): void
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

    public function getValutazione(): string|false
    {
        return $this->_valutazione;
    }

    public function getBuono(): float
    {
        return match ($this->getValutazione()) {
            'rotmed' => 0.5,
            'rotsup' => 1.0,
            'buono' => round($this->fields['Prezzo'] / 3, 2),
            default => 0.0,
        };
    }

    public function getContanti(): float
    {
        return match ($this->getValutazione()) {
            'rotmed' => 0.5,
            'rotsup' => 1.0,
            'buono' => round($this->fields['Prezzo'] / 4, 2),
            default => 0.0,
        };
    }

    public function saveToDB(): bool
    {
        $prefix = $this->getPrefix();

        if ($this->checkFields($this->fields)) {
            $rset = $this->_db->Execute('SELECT * FROM ' . $prefix . "_libri WHERE ISBN='" . $this->fields['ISBN'] . "'");

            if ($rset instanceof ADORecordSet && !$rset->EOF) {
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

            if ($rset instanceof ADORecordSet) {
                $rset->Close();
            }

            if ($this->_valutazione) {
                $valfields = ['ISBN' => $this->fields['ISBN'], 'Valutazione' => $this->_valutazione];

                $rset = $this->_db->Execute('SELECT * FROM ' . $prefix . "_valutazioni WHERE ISBN='" . $this->fields['ISBN'] . "'");

                if ($rset instanceof ADORecordSet && !$rset->EOF) {
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
        if ($rset instanceof ADORecordSet) {
            $rset->Close();

            return !$rset->EOF;
        }

        return false;
    }

    //carica i dati dal database, specificato l'isbn
    public function getFromDB(string $ISBN): bool
    {
        global $prefix;

        $ISBN = static::getShortISBN($ISBN);

        if ($ISBN && static::isValidISBN($ISBN)) {
            $rset = $this->_db->Execute('SELECT ISBN, Titolo, Autore, Editore, Prezzo FROM ' . $prefix . "_libri WHERE ISBN='$ISBN'");
            if (!$rset instanceof ADORecordSet) {
                return false;
            }
            $fields = $rset->fields;

            if (!is_array($fields) || 0 === $rset->NumRows()) {
                $rset->Close();

                return false;
            }

            $this->SetFields($fields);

            $rset = $this->_db->Execute('SELECT valutazione FROM ' . $prefix . "_valutazioni WHERE ISBN='$ISBN'");
            $Valutazione = $this->fetchStringColumn($rset) ?: false;
            $this->setValutazione($Valutazione);

            if ($rset instanceof ADORecordSet) {
                $rset->Close();
            }

            return is_string($Valutazione);
        } else {
            return false;
        }
    }

    public function delete(): bool
    {
        global $prefix;

        $ISBN = $this->fields['ISBN'];
        if (static::IsValidISBN($ISBN)) {
            $this->_db->Execute('DELETE FROM ' . $prefix . "_libri WHERE ISBN = '$ISBN'");
            $this->_db->Execute('DELETE FROM ' . $prefix . "_valutazioni WHERE ISBN = '$ISBN'");
            $this->_db->Execute('DELETE FROM ' . $prefix . "_destinazioni WHERE ISBN = '$ISBN'");

            return true;
        }

        return false;
    }

    public static function isValidISBN(string $ISBN): bool
    {
        return is_numeric($ISBN) && 9 == strlen($ISBN) && 0 != $ISBN;
    }

    public static function getShortISBN(string $ISBN): string|false
    {
        if (strlen($ISBN) > 9) {
            $ISBN = substr($ISBN, 0, strlen($ISBN) - 1);
        }

        while (strlen($ISBN) > 9) {
            $ISBN = substr($ISBN, 1, strlen($ISBN) - 1);
        }

        if (self::isValidISBN($ISBN)) {
            return $ISBN;
        } else {
            return false;
        }
    }

    public function getFullIsbn(): string
    {
        $ISBN = $this->fields['ISBN'];
        $ISBN .= self::ISBNCheck($ISBN);

        return $ISBN;
    }

    public static function isbnCheck(string $ISBN): string
    {
        $checksum = 0;

        for ($i = 0; $i < 9; ++$i) {
            $checksum += ($i + 1) * (int) $ISBN[$i];
        }

        $checksum %= 11;
        if (10 == $checksum) {
            $checksum = 'X';
        }

        return (string) $checksum;
    }

    public function getEAN(string $ISBN = null): string|false
    {
        if (null === $ISBN) {
            $ISBN = $this->fields['ISBN'];
        }

        if (static::GetShortISBN($ISBN)) {
            $EAN = '978' . $ISBN;
            $EAN .= static::EANCheck($EAN);
        } else {
            $EAN = false;
        }

        return $EAN;
    }

    public static function EANCheck(string $ean): int
    {
        $checksum = 0;

        for ($i = 2; $i <= 12; $i += 2) {
            $checksum += (int) substr($ean, $i - 1, 1);
        }

        $checksum *= 3;

        for ($i = 1; $i <= 11; $i += 2) {
            $checksum += (int) substr($ean, $i - 1, 1);
        }

        return 10 - ($checksum % 10);
    }
}
