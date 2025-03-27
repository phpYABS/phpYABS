<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\ORM\Mapping as ORM;
use PhpYabs\Repository\PurchaseRepository;

#[ORM\Table(name: 'purchases')]
#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    private ?Book $book;

    #[ORM\Id]
    #[ORM\Column(name: 'purchase_id', options: ['default' => 0])]
    private ?int $purchaseId;

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
}
