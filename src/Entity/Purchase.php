<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PhpYabs\Repository\PurchaseRepository;

#[ORM\Table(name: 'purchases')]
#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    /**
     * @var Collection<PurchaseLine>
     */
    #[ORM\OneToMany(
        targetEntity: PurchaseLine::class,
        mappedBy: 'purchase',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function setLines(Collection $lines): self
    {
        $this->lines = $lines;

        return $this;
    }

    public function addBook(Book $book): self
    {
        $line = $this->lines->findFirst(
            fn (PurchaseLine $line) => $line->getBook()->getId() === $book->getId(),
        );

        if (!$line) {
            $line = new PurchaseLine();
            $line->setBook($book);
            $line->setPurchase($this);
            $this->lines->add($line);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        $line = $this->lines->findFirst(
            fn ($id, PurchaseLine $line) => $line->getBook()->getId() === $book->getId(),
        );

        if ($line) {
            $this->lines->removeElement($line);
        }

        return $this;
    }
}
