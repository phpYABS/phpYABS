<?php

declare(strict_types=1);

namespace PhpYabs\Facade;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class StatisticsFacade extends AbstractFacade
{
    public function index(Request $request, Response $response): ResponseInterface
    {
        $dbal = $this->getDoctrineConnection();

        // conto gli acquisti
        $nacquisti = $dbal->fetchOne('SELECT COUNT(purchase_id) FROM purchases') ?: 0;

        // conto i libri acquistati
        $libriacq = $dbal->fetchOne('SELECT COUNT(*) FROM purchases') ?: 0;

        // conto i libri non trovati
        $nerrori = $dbal->fetchOne("SELECT COUNT(ISBN) FROM hits WHERE found='no'") ?: 0;

        // conto gli spari totali
        $totspari = $dbal->fetchOne('SELECT SUM(hits) FROM hits') ?: 0;

        // conto gli spari falliti
        $errspari = $dbal->fetchOne("SELECT SUM(hits) FROM hits WHERE found='no'") ?: 0;

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

        $view = Twig::fromRequest($request);

        return $view->render($response, 'statistics/index.twig', $data);
    }
}
