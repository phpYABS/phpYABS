<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use PhpYabs\DB\Acquisto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function current(Request $request, string $id = 'current'): Response
    {
        // se c'è una richiesta di nuovo acquisto, elimino il precedente
        if ('Nuovo' === ($_GET['Azione'] ?? '')) {
            unset($_SESSION['purchase_id']);
            unset($_SESSION['totalec']);
            unset($_SESSION['totaleb']);
        }

        $acquisto = new Acquisto($this->getDoctrineConnection(), $this->entityManager);
        if ('current' === $id && isset($_SESSION['purchase_id'])) {
            $acquisto->setID($_SESSION['purchase_id']);
        } elseif (preg_match('/^\\d+$/', $id)) {
            if (!$acquisto->setID((int) $id)) {
                $errmsg = "L'acquisto $id non esiste!";
            }
        }

        $purchase_id = $_SESSION['purchase_id'] = $acquisto->getID();

        $trovato = true;

        if (isset($_POST['newISBN'])) {
            $trovato = $acquisto->addBook($_POST['newISBN']);
        } elseif (isset($_GET['Cancella'])) {
            $acquisto->delBook($_GET['Cancella']);
        }

        return $this->render('purchases/current.html.twig', [
            'acquisto' => $acquisto,
            'purchase_id' => $purchase_id,
            'trovato' => $trovato,
            'errmsg' => $errmsg ?? '',
        ]);
    }
}
