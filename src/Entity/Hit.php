<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\ORM\Mapping as ORM;
use PhpYabs\Repository\HitRepository;

#[ORM\Table(name: 'hits')]
#[ORM\Entity(repositoryClass: HitRepository::class)]
class Hit
{
    #[ORM\Id]
    #[ORM\Column(name: 'ISBN', length: 9, unique: true, options: ['fixed' => true])]
    private string $isbn;

    #[ORM\Column(name: 'hits', options: ['default' => 0])]
    private int $hits = 0;

    #[ORM\Column]
    private bool $found;

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function matched(): void
    {
        ++$this->hits;
        $this->found = true;
    }

    public function missed(): void
    {
        ++$this->hits;
        $this->found = false;
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

    public function getFound(): bool
    {
        return $this->found;
    }

    public function setFound(?bool $found): static
    {
        $this->found = $found;

        return $this;
    }
}
