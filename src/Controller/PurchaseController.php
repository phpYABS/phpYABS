<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use PhpYabs\DB\Acquisto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/purchases')]
class PurchaseController extends AbstractController
{
    #[Route('', name: 'purchase_list', methods: ['GET'])]
    public function index(): Response
    {
        $sql = <<<SQL
        SELECT purchase_id, COUNT(purchase_id) AS `count`
        FROM purchases
        GROUP BY purchase_id
        SQL;

        $data['purchases'] = $this->getDoctrineConnection()->fetchAllAssociative($sql);

        return $this->render('purchases/index.html.twig', $data);
    }

    #[Route('/{id}', name: 'purchase_current', methods: ['GET', 'POST'])]
    public function current(Request $request, SessionInterface $session, string $id = 'current'): Response
    {
        // se c'è una richiesta di nuovo acquisto, elimino il precedente
        if ('Nuovo' === $request->query->get('Azione', '')) {
            $session->remove('purchase_id');
            $session->remove('totalec');
            $session->remove('totaleb');
        }

        $acquisto = new Acquisto($this->getDoctrineConnection(), $this->entityManager);
        if ('current' === $id && $session->has('purchase_id')) {
            $acquisto->setID($session->get('purchase_id'));
        } elseif (preg_match('/^\\d+$/', $id)) {
            if (!$acquisto->setID((int) $id)) {
                $errmsg = "L'acquisto $id non esiste!";
            }
        }

        $session->set('purchase_id', $acquisto->getID());

        $trovato = true;

        if ($request->request->has('newISBN')) {
            $trovato = $acquisto->addBook($request->request->get('newISBN'));
        } elseif ($request->query->has('Cancella')) {
            $acquisto->delBook($request->query->get('Cancella'));
        }

        return $this->render('purchases/current.html.twig', [
            'acquisto' => $acquisto,
            'purchase_id' => $acquisto->getID(),
            'trovato' => $trovato,
            'errmsg' => $errmsg ?? '',
        ]);
    }
}
