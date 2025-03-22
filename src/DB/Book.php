<?php

// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

namespace PhpYabs\DB;

use Doctrine\DBAL\Connection;
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
    public string|false $_condition;

    /** @var string[] */
    private array $fields;

    public function __construct(?Connection $dbalConnection = null)
    {
        parent::__construct(null, $dbalConnection);

        $this->fields = [];
        $this->setCondition(false);
    }

    /**
     * @param string[] $fields
     */
    public function checkFields(array $fields): bool
    {
        return static::isValidISBN($fields['ISBN']) && strlen($fields['title']) > 0;
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

    public function getPrice(): ?float
    {
        $fields = $this->getFields();
        if (!is_array($fields)) {
            return null;
        }

        return (float) ($fields['price'] ?? 0.0);
    }

    public function setCondition(string|false $condition): void
    {
        switch ($condition) {
            case 'zero':
            case 'rotmed':
            case 'rotsup':
            case 'buono':
                break;
            default:
                $condition = false;
        }

        $this->_condition = $condition;
    }

    public function getCondition(): string|false
    {
        return $this->_condition;
    }

    public function getBuono(): float
    {
        return match ($this->getCondition()) {
            'rotmed' => 0.5,
            'rotsup' => 1.0,
            'buono' => round($this->fields['price'] / 3, 2),
            default => 0.0,
        };
    }

    public function getContanti(): float
    {
        return match ($this->getCondition()) {
            'rotmed' => 0.5,
            'rotsup' => 1.0,
            'buono' => round($this->fields['price'] / 4, 2),
            default => 0.0,
        };
    }

    public function saveToDB(): bool
    {
        $dbal = $this->getDbalConnection();

        if ($this->checkFields($this->fields)) {
            $existingBook = $dbal->fetchAssociative(
                'SELECT * FROM books WHERE ISBN = ?',
                [$this->fields['ISBN']]
            );

            if ($existingBook) {
                // Update existing book
                $dbal->update(
                    'books',
                    $this->fields,
                    ['ISBN' => $this->fields['ISBN']]
                );
            } else {
                // Insert new book
                $dbal->insert('books', $this->fields);
            }

            if ($this->_condition) {
                $buybackFields = [
                    'ISBN' => $this->fields['ISBN'],
                    'condition' => $this->_condition,
                ];

                $existingBuyback = $dbal->fetchAssociative(
                    'SELECT * FROM buyback_rates WHERE ISBN = ?',
                    [$this->fields['ISBN']]
                );

                if ($existingBuyback) {
                    $dbal->update(
                        'buyback_rates',
                        $buybackFields,
                        ['ISBN' => $this->fields['ISBN']]
                    );
                } else {
                    $dbal->insert('buyback_rates', $buybackFields);
                }
            } else {
                $dbal->delete(
                    'buyback_rates',
                    ['ISBN' => $this->fields['ISBN']]
                );
            }
        }

        $result = $dbal->fetchOne(
            'SELECT ISBN FROM books WHERE ISBN = ?',
            [$this->fields['ISBN']]
        );

        return false !== $result;
    }

    public function getFromDB(string $ISBN): bool
    {
        $dbal = $this->getDbalConnection();

        $ISBN = static::getShortISBN($ISBN);

        if ($ISBN && static::isValidISBN($ISBN)) {
            $fields = $dbal->fetchAssociative(
                'SELECT ISBN, title, author, publisher, price FROM books WHERE ISBN = ?',
                [$ISBN]
            );

            if (!$fields) {
                return false;
            }

            $this->setFields($fields);

            $condition = $dbal->fetchOne(
                'SELECT condition FROM buyback_rates WHERE ISBN = ?',
                [$ISBN]
            );

            $this->setCondition($condition ?: false);

            return false !== $condition;
        }

        return false;
    }

    public function delete(): bool
    {
        $dbal = $this->getDbalConnection();

        $ISBN = $this->fields['ISBN'];
        if (static::isValidISBN($ISBN)) {
            $dbal->delete('books', ['ISBN' => $ISBN]);
            $dbal->delete('buyback_rates', ['ISBN' => $ISBN]);
            $dbal->delete('destinations', ['ISBN' => $ISBN]);

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
        try {
            return ISBN::fromString($ISBN)->version10->withoutChecksum;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    public function getFullIsbn(): ?string
    {
        try {
            return (string) ISBN::fromString($this->fields['ISBN'])->version10;
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
}
