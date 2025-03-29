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
            SELECT 'purchases_count' as metric, COUNT(DISTINCT purchase_id) as value FROM purchases
            UNION ALL
            SELECT 'books_purchased', COUNT(*) FROM purchases
            UNION ALL
            SELECT 'not_found_count', COUNT(ISBN) FROM hits WHERE NOT found
            UNION ALL
            SELECT 'total_hits', SUM(hits) FROM hits
            UNION ALL
            SELECT 'error_hits', SUM(hits) FROM hits WHERE NOT found
        SQL;

        $results = $this->connection->fetchAllAssociative($sql);
        
        $stats = array_column($results, 'value', 'metric');
        
        $stats['successful_hits'] = ($stats['total_hits'] ?? 0) - ($stats['error_hits'] ?? 0);

        return [
            'nacquisti' => (int)($stats['purchases_count'] ?? 0),
            'libriacq' => (int)($stats['books_purchased'] ?? 0),
            'nerrori' => (int)($stats['not_found_count'] ?? 0),
            'totspari' => (int)($stats['total_hits'] ?? 0),
            'errspari' => (int)($stats['error_hits'] ?? 0),
            'spariok' => (int)$stats['successful_hits'],
        ];
    }
}
