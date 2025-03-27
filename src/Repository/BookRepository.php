<?php

declare(strict_types=1);

namespace PhpYabs\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpYabs\Entity\Book;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function countAll(): int
    {
        return $this->count([]);
    }

    public function findPaginated(int $offset, int $int): iterable
    {
        return $this->createQueryBuilder('b')
            ->setFirstResult($offset)
            ->setMaxResults($int)
            ->getQuery()
            ->getResult()
        ;
    }
}
