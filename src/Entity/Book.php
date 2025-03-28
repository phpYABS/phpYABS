<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use PhpYabs\Repository\BookRepository;
use PhpYabs\ValueObject\ISBN;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'books')]
#[ORM\Index(name: 'title', columns: ['title'])]
#[ORM\Index(name: 'author', columns: ['author'])]
#[ORM\Index(name: 'publisher', columns: ['publisher'])]
#[ORM\Entity(repositoryClass: BookRepository::class)]
#[UniqueEntity(fields: 'isbn')]
class Book
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id;

    #[Assert\Length(min: 9, max: 9)]
    #[ORM\Column(name: 'ISBN', length: 9, unique: true, options: ['fixed' => true])]
    private ?string $isbn = null;

    #[Assert\NotBlank]
    #[ORM\Column(name: 'title', length: 40)]
    private ?string $title;

    #[Assert\NotBlank]
    #[ORM\Column(name: 'author', length: 20)]
    private ?string $author = 'NULL';

    #[Assert\NotBlank]
    #[ORM\Column(name: 'publisher', length: 25)]
    private ?string $publisher = 'NULL';

    #[ORM\Column(name: 'price', type: Types::DECIMAL, precision: 5, scale: 2, options: ['default' => 0.00])]
    private ?string $price = '0.00';

    #[ORM\Column(enumType: Rate::class)]
    private ?Rate $rate = Rate::ZERO;

    /**
     * @var Collection<int, Destination>
     */
    #[ORM\OneToMany(targetEntity: Destination::class, mappedBy: 'book', cascade: ['persist', 'remove'])]
    private Collection $destinations;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Book
    {
        $this->id = $id;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): Book
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): Book
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): Book
    {
        $this->author = $author;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): Book
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string|int|Money $price): Book
    {
        if ($price instanceof Money) {
            $this->price = bcdiv($price->getAmount(), '100', 2);
        } elseif (is_int($price)) {
            $this->price = bcdiv((string) $price, '100', 2);
        } else {
            $this->price = $price;
        }

        return $this;
    }

    public function getRate(): ?Rate
    {
        return $this->rate;
    }

    public function setRate(?Rate $rate): Book
    {
        $this->rate = $rate;

        return $this;
    }

    public function getFullIsbn(): string
    {
        return (string) ISBN::fromString($this->isbn)->version10;
    }

    public function getPriceObject(): Money
    {
        return Money::EUR((int) ((float) $this->price * 100));
    }

    public function getStoreCredit(): Money
    {
        return match ($this->getRate()) {
            Rate::ROTMED => Money::EUR(50),
            Rate::ROTSUP => Money::EUR(100),
            Rate::BUONO => $this->getPriceObject()->divide(3),
            default => Money::EUR(0),
        };
    }

    public function getCashValue(): Money
    {
        return match ($this->getRate()) {
            Rate::ROTMED => Money::EUR(50),
            Rate::ROTSUP => Money::EUR(100),
            Rate::BUONO => $this->getPriceObject()->divide(4),
            default => Money::EUR(0),
        };
    }

    public function getDestinations(): Collection
    {
        return $this->destinations;
    }

    public function setDestinations(Collection $destinations): Book
    {
        $this->destinations = $destinations;

        return $this;
    }
}
