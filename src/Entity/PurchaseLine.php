<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'purchase_lines')]
#[ORM\Entity]
class PurchaseLine
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    private ?Book $book;

    #[ORM\Id]
    #[ORM\ManyToOne]
    private ?Purchase $purchase;

    #[ORM\Column(name: 'quantity', options: ['default' => 1])]
    private int $quantity = 1;

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getPurchase(): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): self
    {
        $this->purchase = $purchase;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
