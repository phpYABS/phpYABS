<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\ORM\Mapping as ORM;
use PhpYabs\Repository\DestinationRepository;

#[ORM\Table(name: 'destinations')]
#[ORM\Entity(repositoryClass: DestinationRepository::class)]
class Destination
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'destinations')]
    private Book $book;

    #[ORM\Id]
    #[ORM\Column(name: 'destination', length: 100)]
    private string $destination;

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
