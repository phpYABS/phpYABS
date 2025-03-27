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
        $dbal = $this->connection;

        $nacquisti = $dbal->fetchOne('SELECT COUNT(purchase_id) FROM purchases') ?: 0;

        // conto i libri acquistati
        $libriacq = $dbal->fetchOne('SELECT COUNT(*) FROM purchases') ?: 0;

        // conto i libri non trovati
        $nerrori = $dbal->fetchOne('SELECT COUNT(ISBN) FROM hits WHERE NOT found') ?: 0;

        // conto gli spari totali
        $totspari = $dbal->fetchOne('SELECT SUM(hits) FROM hits') ?: 0;

        // conto gli spari falliti
        $errspari = $dbal->fetchOne('SELECT SUM(hits) FROM hits WHERE NOT found') ?: 0;

        // calcolo gli spari con successo
        $spariok = $totspari - $errspari;

        return compact(
            'nacquisti',
            'libriacq',
            'nerrori',
            'totspari',
            'errspari',
            'spariok',
        );
    }
}
