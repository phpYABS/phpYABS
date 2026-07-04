<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: 'isbn')]
class Book
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id;

    #[Assert\Isbn]
    #[ORM\Column(name: 'ISBN', length: 13, unique: true)]
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

    #[ORM\Column(type: 'money', length: 255)]
    private ?Money $price = null;

    #[ORM\Column(enumType: Rate::class)]
    private ?Rate $rate = Rate::ZERO;

    /**
     * @var Collection<int, Destination>
     */
    #[ORM\OneToMany(targetEntity: Destination::class, mappedBy: 'book', cascade: ['persist', 'remove'])]
    private Collection $destinations;

    public function __construct()
    {
        $this->destinations = new ArrayCollection();
    }

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

    public function getPrice(): ?Money
    {
        return $this->price;
    }

    public function setPrice(string|int|Money $price): Book
    {
        if ($price instanceof Money) {
            $this->price = $price;
        } else {
            $this->price = Money::EUR($price);
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
        $isbn13 = ISBN::fromString((string) $this->isbn)->version13;

        // 979-prefixed ISBNs have no ISBN-10 equivalent
        return str_starts_with((string) $isbn13, '978')
            ? (string) $isbn13->version10
            : (string) $isbn13;
    }

    public function getIsbnWithoutChecksum(): string
    {
        return substr((string) ISBN::fromString((string) $this->isbn)->version13, 3, 9);
    }

    public function getStoreCredit(): Money
    {
        return match ($this->getRate()) {
            Rate::ROTMED => Money::EUR(50),
            Rate::ROTSUP => Money::EUR(100),
            Rate::BUONO => $this->getPrice()->divide(3),
            default => Money::EUR(0),
        };
    }

    public function getCashValue(): Money
    {
        return match ($this->getRate()) {
            Rate::ROTMED => Money::EUR(50),
            Rate::ROTSUP => Money::EUR(100),
            Rate::BUONO => $this->getPrice()->divide(4),
            default => Money::EUR(0),
        };
    }

    public function getDestinations(): string
    {
        return implode(', ', $this->destinations->map(
            fn (Destination $d) => $d->getDestination(),
        )->toArray());
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function convertISBNto13(): void
    {
        $this->isbn = (string) ISBN::fromString($this->isbn)->version13;
    }
}
