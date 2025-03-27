<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stats')]
class StatisticsController extends AbstractController
{
    #[Route('', name: 'statistics', methods: ['GET'])]
    public function index(): Response
    {
        $dbal = $this->getDoctrineConnection();

        // conto gli acquisti
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

        $data = compact(
            'nacquisti',
            'libriacq',
            'nerrori',
            'totspari',
            'errspari',
            'spariok',
        );

        return $this->render('statistics/index.html.twig', $data);
    }
}
