<?php

namespace PhpYabs\Entity;

use PhpYabs\Repository\HitsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hits')]
#[ORM\Entity(repositoryClass: HitsRepository::class)]
class Hit
{
    #[ORM\Column(name: "ISBN", length: 9, options: ["fixed" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private ?string $isbn = null;

    #[ORM\Column(name: "hits", options: ["default" => 0])]
    private ?int $hits = 0;

    #[ORM\Column(name: "found", type: Types::STRING)]
    private ?string $found = '\'yes\'';

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getHits(): ?int
    {
        return $this->hits;
    }

    public function setHits(int $hits): static
    {
        $this->hits = $hits;

        return $this;
    }

    public function getFound(): ?string
    {
        return $this->found;
    }

    public function setFound(string $found): static
    {
        $this->found = $found;

        return $this;
    }
}
