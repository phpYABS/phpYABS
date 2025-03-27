<?php

declare(strict_types=1);

namespace PhpYabs\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use PhpYabs\Entity\Destination;

/**
 * @extends ServiceEntityRepository<Destination>
 *
 * @method Destination|null find($id, $lockMode = null, $lockVersion = null)
 * @method Destination|null findOneBy(array $criteria, array $orderBy = null)
 * @method Destination[]    findAll()
 * @method Destination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DestinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Destination::class);
    }

    public function findBooksForDestination(string $destination, int $offset): iterable
    {
        $sql = <<<SQL
        SELECT b.id,
               b.ISBN,
               b.title,
               b.author,
               b.publisher,
               b.rate,
               IF(d.book_id IS NOT NULL, 1, 0) AS selected
        FROM books b
                 LEFT JOIN destinations d ON d.book_id = b.id AND d.destination = :destination
        ORDER BY publisher, author, title, ISBN
        LIMIT :offset,50
        SQL;

        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            $sql,
            [
                'destination' => $destination,
                'offset' => $offset,
            ],
            [
                'destination' => Types::STRING,
                'offset' => Types::INTEGER,
            ],
        );
    }
}
