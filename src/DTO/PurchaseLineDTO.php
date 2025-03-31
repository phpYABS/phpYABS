<?php

declare(strict_types=1);

namespace PhpYabs\DTO;

use Money\Money;

readonly class PurchaseLineDTO
{
    public function __construct(
        public int $bookId,
        public int $quantity,
        public string $title,
        public string $author,
        public string $publisher,
        public Money $price,
        public string $fullISBN,
        public string $ISBN,
        public string $rate,
        public Money $storeCredit,
        public Money $cashValue,
        public string $dest,
        public int $sequence,
    ) {
    }

    public function toArray(): array
    {
        return [
            'bookId' => $this->bookId,
            'quantity' => $this->quantity,
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'price' => $this->price,
            'fullISBN' => $this->fullISBN,
            'ISBN' => $this->ISBN,
            'rate' => $this->rate,
            'storeCredit' => $this->storeCredit,
            'cashValue' => $this->cashValue,
            'dest' => $this->dest,
            'sequence' => $this->sequence,
        ];
    }
}
