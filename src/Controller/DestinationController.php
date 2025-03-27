<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use PhpYabs\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class DestinationController extends AbstractController
{
    public function __construct(
        Connection $doctrineConnection,
        EntityManagerInterface $entityManager,
        private readonly BookRepository $bookRepository,
    ) {
        parent::__construct($doctrineConnection, $entityManager);
    }

    #[Route('/destinations', name: 'destination_list', methods: ['GET'])]
    public function index(Request $request, SessionInterface $session): Response
    {
        $data = ['books' => []];
        $dbal = $this->getDoctrineConnection();

        $totlibri = $this->bookRepository->countAll();
        $data['totLibri'] = $totlibri;

        $get_start = 0;
        $destination = '';

        if ('_NEW' !== $request->query->get('destination', '')) {
            $destination = $request->query->get('destination') ?? $session->get('destination', '');
            $get_start = (int) ($request->query->get('start') ?? $session->get('start', 0));
        }
        $data['destination'] = $destination;

        $session->set('start', $get_start);
        $session->set('destination', $destination);

        switch ($request->query->get('invia', '')) {
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
            if ($request->query->has('destina')) {
                $destina = $request->query->all('destina');
                foreach ($destina as $chiave => $valore) {
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
