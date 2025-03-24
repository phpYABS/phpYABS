<?php

namespace App\Repository;

use PhpYabs\Entity\BuybackRates;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BuybackRates>
 *
 * @method BuybackRates|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuybackRates|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuybackRates[]    findAll()
 * @method BuybackRates[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuybackRatesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuybackRates::class);
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
