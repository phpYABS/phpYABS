<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DestinationController extends AbstractController
{
    #[Route('/destinations', methods: ['GET'])]
    public function index(): Response
    {
        $data = ['books' => []];
        $dbal = $this->getDoctrineConnection();

        $risultato = $dbal->fetchOne('SELECT COUNT(*) FROM books');
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
                        $dbal->executeStatement(<<<SQL
                            INSERT INTO destinations (book_id, destination)
                            VALUES (:book_id, :destination)
                            ON DUPLICATE KEY UPDATE destination = :destination
                            SQL,
                            [
                                'book_id' => $chiave,
                                'destination' => $destination,
                            ],
                            [
                                'book_id' => Types::INTEGER,
                                'destination' => Types::STRING,
                            ],
                        );
                    } else {
                        $dbal->executeStatement(<<<SQL
                            DELETE FROM destinations
                            WHERE book_id = :book_id
                            AND destination = :destination
                            SQL,
                            [
                                'book_id' => $chiave,
                                'destination' => $destination,
                            ],
                            [
                                'book_id' => Types::INTEGER,
                                'destination' => Types::STRING,
                            ],
                        );
                    }
                }
            }

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
            $data['pages'] = [];

            $npag = (int) ceil($totlibri / 50);
            for ($i = 1; $i <= $npag; ++$i) {
                $data['pages'][] = [
                    'page' => $i,
                    'start' => (($i - 1) * 50),
                ];
            }
        }

        return $this->render('destinations/index.html.twig', $data);
    }
}
