<?php

namespace PhpYabs\Repository;

use PhpYabs\Entity\BuybackRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BuybackRate>
 *
 * @method BuybackRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuybackRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuybackRate[]    findAll()
 * @method BuybackRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuybackRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuybackRate::class);
    }

//    /**
//     * @return BuybackRates[] Returns an array of BuybackRates objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BuybackRates
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
