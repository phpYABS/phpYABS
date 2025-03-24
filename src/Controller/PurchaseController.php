<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use PhpYabs\DB\Acquisto;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class PurchaseController extends AbstractController
{
    public function index(Request $request, Response $response): ResponseInterface
    {
        $sql = <<<SQL
        SELECT purchase_id, COUNT(purchase_id) AS `count`
        FROM purchases
        GROUP BY purchase_id
        SQL;

        $data['purchases'] = $this->getDoctrineConnection()->fetchAllAssociative($sql);

        $view = Twig::fromRequest($request);

        return $view->render($response, 'purchases/index.twig', $data);
    }

    public function current(Request $request, Response $response, array $params): ResponseInterface
    {
        $id = $params['id'] ?? 'current';

        // se c'è una richiesta di nuovo acquisto, elimino il precedente
        if ('Nuovo' === ($_GET['Azione'] ?? '')) {
            unset($_SESSION['purchase_id']);
            unset($_SESSION['totalec']);
            unset($_SESSION['totaleb']);
        }

        $acquisto = new Acquisto($this->getDoctrineConnection());
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

        $view = Twig::fromRequest($request);

        return $view->render($response, 'purchases/current.twig', [
            'acquisto' => $acquisto,
            'purchase_id' => $purchase_id,
            'trovato' => $trovato,
            'errmsg' => $errmsg ?? '',
        ]);
    }
}
