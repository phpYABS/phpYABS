<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use PhpYabs\Entity\Destination;
use PhpYabs\Repository\BookRepository;
use PhpYabs\Repository\DestinationRepository;
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
        private readonly DestinationRepository $destinationsRepository,
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
                    $book = $this->bookRepository->find($chiave);
                    if (!$book) {
                        continue;
                    }

                    if ('on' == $valore) {
                        $dest = $this->destinationsRepository->findOneBy([
                            'book' => $book,
                            'destination' => $destination,
                        ]);
                        if (!$dest) {
                            $dest = new Destination();
                            $dest->setBook($book);
                            $dest->setDestination($destination);
                            $this->entityManager->persist($dest);
                        }
                    } else {
                        $dest = $this->destinationsRepository->findOneBy([
                            'book' => $book,
                            'destination' => $destination,
                        ]);
                        if ($dest) {
                            $this->entityManager->remove($dest);
                        }
                    }
                }
                $this->entityManager->flush();
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
