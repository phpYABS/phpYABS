<?php

declare(strict_types=1);

namespace PhpYabs\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpYabs\Entity\PurchaseLine;

/**
 * @extends ServiceEntityRepository<PurchaseLine>
 *
 * @method PurchaseLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseLine[]    findAll()
 * @method PurchaseLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseLine::class);
    }

    public function getCurrentId(): int
    {
        $sql = <<<SQL
        SELECT MAX(purchase_id)
        FROM purchases
        SQL;

        return (int) $this->getEntityManager()->getConnection()->fetchOne($sql);
    }
}
