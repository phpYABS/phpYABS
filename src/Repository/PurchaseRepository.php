<?php

declare(strict_types=1);

namespace PhpYabs\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpYabs\DTO\PurchaseListDTO;
use PhpYabs\Entity\Purchase;

/**
 * @extends ServiceEntityRepository<Purchase>
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function list(): array
    {
        return $this
            ->createQueryBuilder('p')
            ->select(sprintf(
                'NEW %s(p.id, COALESCE(SUM(pl.quantity), 0))',
                PurchaseListDTO::class,
            ))
            ->leftJoin('p.lines', 'pl')
            ->groupBy('p.id')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getLatest(): ?Purchase
    {
        return $this
            ->createQueryBuilder('pl')
            ->orderBy('pl.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }
}
