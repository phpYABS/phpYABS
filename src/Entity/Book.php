<?php

namespace PhpYabs\Entity;

use PhpYabs\Repository\BooksRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'books')]
#[ORM\Index(name: 'title', columns: ['title'])]
#[ORM\Index(name: 'author', columns: ['author'])]
#[ORM\Index(name: 'publisher', columns: ['publisher'])]
#[ORM\Entity(repositoryClass: BooksRepository::class)]
class Book
{
    #[ORM\Column(name: "ISBN", length: 9, options: ["fixed" => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private ?string $isbn = null;

    #[ORM\Column(name: "title", length: 40, options: ["default" => '\'\''])]
    private ?string $title = '\'\'';

    #[ORM\Column(name: "author", length: 20, nullable: true, options: ["default" => 'NULL'])]
    private ?string $author = 'NULL';

    #[ORM\Column(name: "publisher", length: 25, nullable: true, options: ["default" => 'NULL'])]
    private ?string $publisher = 'NULL';

    #[ORM\Column(name: "price", type: Types::DECIMAL, precision: 5, scale: 2, options: ["default" => 0.00])]
    private ?string $price = '0.00';

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }
}
