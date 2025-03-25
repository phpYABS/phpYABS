<?php

namespace PhpYabs\Repository;

use PhpYabs\Entity\Hit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hit>
 *
 * @method Hit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hit[]    findAll()
 * @method Hit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hit::class);
    }

//    /**
//     * @return Hits[] Returns an array of Hits objects
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

//    public function findOneBySomeField($value): ?Hits
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
