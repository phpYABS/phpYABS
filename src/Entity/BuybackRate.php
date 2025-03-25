<?php

namespace PhpYabs\Entity;

use PhpYabs\Repository\BuybackRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'buyback_rates')]
#[ORM\Entity(repositoryClass: BuybackRateRepository::class)]
class BuybackRate
{
    #[ORM\Column(name: "ISBN", length: 9, options: ["fixed" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private ?string $isbn = null;

    #[ORM\Column(name: "rate", type: Types::STRING)]
    private ?string $rate = '\'zero\'';

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
}
