<?php

use PhpYabs\DB\Book;

function fullisbn(string $isbn): string
{
    $book = new Book();
    $book->setFields([
        'ISBN' => $isbn,
        'Titolo' => 'Dummy',
    ]);

    return $book->getFullIsbn();
}