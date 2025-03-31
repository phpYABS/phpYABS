<?php

declare(strict_types=1);

namespace PhpYabs\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
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
        $sql = <<<SQL
        SELECT purchase_id, COUNT(purchase_id) AS `count`
        FROM purchase_lines
        GROUP BY purchase_id
        SQL;

        return $this->getEntityManager()->getConnection()->fetchAllAssociative($sql);
    }

    public function getLatest(): ?Purchase
    {
        $sql = <<<SQL
        SELECT MAX(p.id)
        FROM purchases p
        SQL;

        $id = $this->getEntityManager()->getConnection()->fetchOne($sql);
        if (!$id) {
            return null;
        }

        return $this->find($id);
    }
}
