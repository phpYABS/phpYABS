<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

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
        EntityManagerInterface $entityManager,
        private readonly BookRepository $bookRepository,
        private readonly DestinationRepository $destinationsRepository,
    ) {
        parent::__construct($entityManager);
    }

    #[Route('/destinations', name: 'destination_list', methods: ['GET', 'POST'])]
    public function index(Request $request, SessionInterface $session): Response
    {
        $data = ['books' => []];
        $totlibri = $this->bookRepository->countAll();
        $data['totLibri'] = $totlibri;

        // navigation arrives via GET links, saves and paging buttons via POST
        $input = $request->isMethod('POST') ? $request->request : $request->query;

        $get_start = 0;
        $destination = '';

        if ('_NEW' !== $input->get('destination', '')) {
            $destination = $input->get('destination') ?? $session->get('destination', '');
            $get_start = (int) ($input->get('start') ?? $session->get('start', 0));
        }
        $data['destination'] = $destination;

        $session->set('start', $get_start);
        $session->set('destination', $destination);

        switch ($request->isMethod('POST') ? $input->get('invia', '') : '') {
            case 'forward':
                $start = $get_start + 50;
                if ($start > $totlibri) {
                    $start = $totlibri - ($totlibri % 50);
                }
                break;
            case 'back':
                $start = max($get_start - 50, 0);
                break;
            default:
                $start = $get_start;
                break;
        }
        if (!strlen($destination)) {
            $start = 0;
        }
        $data['start'] = $start;
        $session->set('start', $start);
        $pag = (int) ($start / 50) + 1;
        $data['pag'] = $pag;
        if (strlen($destination) > 0) {
            if ($request->isMethod('POST')
                && $request->request->has('destina')
                && $this->isCsrfTokenValid('submit', $request->request->getString('_token'))
            ) {
                $destina = $request->request->all('destina');
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

            $data['books'] = $this->destinationsRepository->findBooksForDestination($destination, $start);
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
