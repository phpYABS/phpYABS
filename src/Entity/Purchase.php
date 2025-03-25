<?php

declare(strict_types=1);

namespace PhpYabs\Entity;

use Doctrine\ORM\Mapping as ORM;
use PhpYabs\Repository\PurchaseRepository;

#[ORM\Table(name: 'purchases')]
#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\ManyToOne]
    private ?Book $book;

    #[ORM\Id]
    #[ORM\Column(name: 'purchase_id', options: ['default' => 0])]
    private ?int $purchaseId;
}
