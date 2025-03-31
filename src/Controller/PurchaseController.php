<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PhpYabs\Repository\BookRepository;
use PhpYabs\Repository\HitRepository;
use PhpYabs\Repository\PurchaseLineRepository;
use PhpYabs\Repository\PurchaseRepository;
use PhpYabs\Service\PurchaseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/purchases')]
class PurchaseController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly PurchaseRepository $purchaseRepository,
        private readonly PurchaseLineRepository $purchaseLineRepository,
        private readonly BookRepository $bookRepository,
        private readonly HitRepository $hitRepository,
    ) {
        parent::__construct($entityManager);
    }

    #[Route('', name: 'purchase_list', methods: ['GET'])]
    public function index(Request $request): Response
    {
        if ($request->query->has('purchase_id')) {
            return $this->redirectToRoute('purchase_current', ['id' => $request->query->get('purchase_id')]);
        }

        $data['purchases'] = $this->purchaseRepository->list();

        return $this->render('purchases/index.html.twig', $data);
    }

    #[Route('/{id}', name: 'purchase_current', methods: ['GET', 'POST'])]
    public function current(Request $request, SessionInterface $session, string $id): Response
    {
        // se c'è una richiesta di nuovo acquisto, elimino il precedente
        if ('new' === $id) {
            $session->remove('purchase_id');
            $session->remove('totalec');
            $session->remove('totaleb');
        }

        $acquisto = new PurchaseService(
            $this->entityManager,
            $this->purchaseRepository,
            $this->purchaseLineRepository,
            $this->bookRepository,
            $this->hitRepository,
        );

        if ('current' === $id && $session->has('purchase_id')) {
            $acquisto->setId($session->get('purchase_id'));
        } elseif (preg_match('/^\\d+$/', $id)) {
            if (!$acquisto->setId((int) $id)) {
                $errmsg = "L'acquisto $id non esiste!";
            }
        }

        $session->set('purchase_id', $acquisto->getId());

        $trovato = true;

        if ($request->request->has('newISBN')) {
            $trovato = $acquisto->addBook($request->request->get('newISBN'));
        } elseif ($request->query->has('delete')) {
            $acquisto->delBook($request->query->get('delete'));
        }

        return $this->render('purchases/current.html.twig', [
            'acquisto' => $acquisto,
            'purchase_id' => $acquisto->getId(),
            'trovato' => $trovato,
            'errmsg' => $errmsg ?? '',
        ]);
    }
}
