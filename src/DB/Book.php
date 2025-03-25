<?php

declare(strict_types=1);

// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

namespace PhpYabs\DB;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use PhpYabs\ValueObject\ISBN;

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
    public string|false $_rate;

    /** @var string[] */
    private array $fields;

    public function __construct(Connection $dbalConnection)
    {
        parent::__construct($dbalConnection);

        $this->fields = [];
        $this->setRate(false);
    }

    /**
     * @param string[] $fields
     */
    public function checkFields(array $fields): bool
    {
        return static::isValidISBN($fields['ISBN'] ?? '') && strlen($fields['title'] ?? '') > 0;
    }

    /**
     * @param string[] $fields
     */
    public function setFields(array $fields): bool
    {
        $fields['ISBN'] = (string) static::getShortISBN($fields['ISBN']);

        if ($this->checkFields($fields)) {
            foreach ($fields as $key => $value) {
                $this->fields[$key] = strtoupper($value);
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
        if ($this->checkFields($this->fields)) {
            return $this->fields;
        } else {
            return false;
        }
    }

    public function getPrice(): ?float
    {
        $fields = $this->getFields();
        if (!is_array($fields)) {
            return null;
        }

        return (float) ($fields['price'] ?? 0.0);
    }

    public function setRate(mixed $rate): void
    {
        switch ($rate) {
            case 'zero':
            case 'rotmed':
            case 'rotsup':
            case 'buono':
                break;
            default:
                $rate = false;
        }

        $this->_rate = $rate;
    }

    public function getRate(): string|false
    {
        return $this->_rate;
    }


    public function saveToDB(): bool
    {
        $dbal = $this->getDbalConnection();

        if ($this->checkFields($this->fields)) {
            $existingBook = $dbal->fetchAssociative(
                'SELECT * FROM books WHERE ISBN = ?',
                [$this->fields['ISBN']],
            );

            if ($existingBook) {
                // Update existing book
                $dbal->update(
                    'books',
                    $this->fields,
                    ['ISBN' => $this->fields['ISBN']],
                );
            } else {
                // Insert new book
                $dbal->insert('books', $this->fields);
            }

            if ($this->_rate) {
                $buybackFields = [
                    'ISBN' => $this->fields['ISBN'],
                    'rate' => $this->_rate,
                ];

                $types = [
                    Types::STRING,
                    Types::STRING,
                ];

                $existingBuyback = $dbal->fetchAssociative(
                    'SELECT * FROM buyback_rates WHERE ISBN = ?',
                    [$this->fields['ISBN']],
                );

                if ($existingBuyback) {
                    $dbal->update(
                        'buyback_rates',
                        $buybackFields,
                        ['ISBN' => $this->fields['ISBN']],
                        $types,
                    );
                } else {
                    $dbal->insert('buyback_rates', $buybackFields, $types);
                }
            } else {
                $dbal->delete(
                    'buyback_rates',
                    ['ISBN' => $this->fields['ISBN']],
                );
            }
        }

        $result = $dbal->fetchOne(
            'SELECT ISBN FROM books WHERE ISBN = ?',
            [$this->fields['ISBN']],
        );

        return false !== $result;
    }

    public function getFromDB(string $ISBN): bool
    {
        $dbal = $this->getDbalConnection();

        $ISBN = static::getShortISBN($ISBN);

        if ($ISBN && static::isValidISBN($ISBN)) {
            $fields = $dbal->fetchAssociative(
                'SELECT ISBN, title, author, publisher, price, rate FROM books WHERE ISBN = ?',
                [$ISBN],
            );

            if (!$fields) {
                return false;
            }

            $this->setFields($fields);

            return true;
        }

        return false;
    }

    public function delete(): bool
    {
        $dbal = $this->getDbalConnection();

        $ISBN = $this->fields['ISBN'] ?? '';
        if (static::isValidISBN($ISBN)) {
            $dbal->delete('books', ['ISBN' => $ISBN]);
            $dbal->delete('buyback_rates', ['ISBN' => $ISBN]);
            $dbal->delete('destinations', ['ISBN' => $ISBN]);

            return true;
        }

        return false;
    }

    public static function isValidISBN(mixed $ISBN): bool
    {
        return is_numeric($ISBN) && 9 == strlen($ISBN) && 0 != $ISBN;
    }

    public static function getShortISBN(string $ISBN): string|false
    {
        try {
            return ISBN::fromString($ISBN)->version10->withoutChecksum;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    public function getFullIsbn(): ?string
    {
        try {
            return (string) ISBN::fromString($this->fields['ISBN'])->version10;
        } catch (\InvalidArgumentException) {
            return null;
        }
    }
}
