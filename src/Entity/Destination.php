<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\ORM\Mapping as ORM;
use PhpYabs\Repository\DestinationsRepository;

#[ORM\Table(name: 'destinations')]
#[ORM\Entity(repositoryClass: DestinationsRepository::class)]
class Destination
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'destinations')]
    private ?Book $book;

    #[ORM\Id]
    #[ORM\Column(name: 'destination', length: 100)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?string $destination = null;

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }
}
