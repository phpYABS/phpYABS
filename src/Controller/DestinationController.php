<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Doctrine\DBAL\Types\Types;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class DestinationController extends AbstractController
{
    public function index(Request $request, Response $response): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $data = [];
        $dbal = $this->getDoctrineConnection();

        $risultato = $dbal->fetchOne('SELECT COUNT(*) FROM buyback_rates');
        $totlibri = $risultato ?? 0;
        $data['totLibri'] = $totlibri;

        $get_start = 0;
        $destination = '';

        if ('_NEW' !== ($_GET['destination'] ?? '')) {
            foreach ([$_GET, $_COOKIE] as $arr) {
                if (isset($arr['destination'])) {
                    $destination = (string) $arr['destination'];
                    break;
                }
            }

            foreach ([$_GET, $_COOKIE] as $arr) {
                if (isset($arr['start'])) {
                    $get_start = (int) $arr['start'];
                    break;
                }
            }
        }
        $data['destination'] = $destination;

        setcookie('start', (string) $get_start, ['expires' => time() + 604800]);
        setcookie('destination', $destination, ['expires' => time() + 604800]);

        switch ($_GET['invia'] ?? '') {
            case 'Avanti':
                $start = $get_start + 50;
                if ($start > $totlibri) {
                    $start = $totlibri - ($totlibri % 50);
                }
                break;
            case 'Indietro':
                $start = $get_start - 50;
                if ($start < 0) {
                    $start = 0;
                }
                break;
            default:
                if (strlen((string) $get_start) > 0) {
                    $start = $get_start;
                } else {
                    $start = 0;
                }
                break;
        }
        if (!strlen($destination)) {
            $start = 0;
        }
        $pag = (int) ($start / 50) + 1;
        $data['pag'] = $pag;
        if (strlen($destination) > 0) {
            if (isset($_GET['destina']) && is_array($_GET['destina'])) {
                foreach ($_GET['destina'] as $chiave => $valore) {
                    if ('on' == $valore) {
                        $esiste = $dbal->fetchOne('SELECT COUNT(*) FROM destinations ' .
                            "WHERE ISBN = '$chiave' AND destination = '$destination'");
                        if (!$esiste) {
                            $dbal->executeStatement('INSERT INTO destinations (ISBN, destination) ' .
                                " VALUES ('$chiave', '$destination')");
                        }
                    } else {
                        $dbal->executeStatement("DELETE FROM destinations WHERE ISBN='$chiave' " .
                            "AND destination = '$destination'");
                    }
                }
            }

            $sql = <<<SQL
            SELECT b.ISBN,
                   b.title,
                   b.author,
                   b.publisher,
                   IF(d.ISBN IS NOT NULL, 1, 0) AS selected
            FROM books b
                     INNER JOIN buyback_rates br ON b.ISBN = br.ISBN
                     LEFT JOIN destinations d ON d.ISBN = b.ISBN AND d.destination = :destination
            ORDER BY publisher, author, title, ISBN
            LIMIT :offset,50
            SQL;

            $books = $dbal->fetchAllAssociative(
                $sql,
                [
                    'destination' => $destination,
                    'offset' => $start,
                ],
                [
                    'destination' => Types::STRING,
                    'offset' => Types::INTEGER,
                ],
            );

            $data['books'] = $books;

            $npag = (int) ceil($totlibri / 50);
            for ($i = 1; $i <= $npag; ++$i) {
                $data['pages'][] = [
                    'page' => $i,
                    'start' => (($i - 1) * 50),
                ];
            }
        }

        return $view->render($response, 'destinations/index.twig', $data);
    }
}
