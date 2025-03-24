<?php

namespace PhpYabs\Entity;

use App\Repository\DestinationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'destinations')]
#[ORM\Entity(repositoryClass: DestinationsRepository::class)]
class Destinations
{
    #[ORM\Column(name: "ISBN", length: 9, options: ["fixed" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private ?string $isbn = null;

    #[ORM\Column(name: "destination", length: 100)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private ?string $destination = null;

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

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
