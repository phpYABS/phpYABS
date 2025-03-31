<?php

declare(strict_types=1);

namespace PhpYabs\Repository;

use Doctrine\DBAL\Connection;

class StatisticsRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function getStatistics(): array
    {
        $sql = <<<SQL
            SELECT 'purchases_count' as metric, COALESCE(COUNT(DISTINCT purchase_id), 0) as value FROM purchases
            UNION ALL
            SELECT 'books_purchased', COALESCE(COUNT(*), 0) FROM purchases
            UNION ALL
            SELECT 'not_found_count', COALESCE(COUNT(ISBN), 0) FROM hits WHERE NOT found
            UNION ALL
            SELECT 'total_hits', COALESCE(SUM(hits), 0) FROM hits
            UNION ALL
            SELECT 'error_hits', COALESCE(SUM(hits), 0) FROM hits WHERE NOT found
            UNION ALL
            SELECT 'successful_hits', COALESCE(SUM(hits), 0) FROM hits WHERE found
        SQL;

        $results = $this->connection->fetchAllAssociative($sql);

        return array_column($results, 'value', 'metric');
    }
}
