<?php

namespace PhpYabs\Entity;

use PhpYabs\Repository\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'purchases')]
#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Column(name: "book_id")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $bookId = null;

    #[ORM\Column(name: "purchase_id", options: ["default" => 0])]
    private ?int $purchaseId = 0;

    #[ORM\Column(name: "ISBN", length: 9, options: ["fixed" => true])]
    private ?string $isbn = null;

    public function getBookId(): ?int
    {
        return $this->bookId;
    }

    public function getPurchaseId(): ?int
    {
        return $this->purchaseId;
    }

    public function setPurchaseId(int $purchaseId): static
    {
        $this->purchaseId = $purchaseId;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }
}
