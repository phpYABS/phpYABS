<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\ORM\Mapping as ORM;
use PhpYabs\Repository\PurchaseLineRepository;

#[ORM\Table(name: 'purchase_lines')]
#[ORM\Entity(repositoryClass: PurchaseLineRepository::class)]
class PurchaseLine
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    private ?Book $book;

    #[ORM\Id]
    #[ORM\Column(name: 'purchase_id', options: ['default' => 0])]
    private ?int $purchaseId;

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

    public function getPurchaseId(): ?int
    {
        return $this->purchaseId;
    }

    public function setPurchaseId(?int $purchaseId): self
    {
        $this->purchaseId = $purchaseId;

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
