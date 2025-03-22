<?php

declare(strict_types=1);

namespace PhpYabs\Facade;

use PhpYabs\DB\Acquisto;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class PurchaseFacade extends AbstractFacade
{
    public function index(Request $request, Response $response): ResponseInterface
    {
        $sql =<<<SQL
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

        return $this->buffered($response, function () use ($id) {
            $dbal = $this->getDoctrineConnection();

            // se c'è una richiesta di nuovo acquisto, elimino il precedente
            if ('Nuovo' === ($_GET['Azione'] ?? '')) {
                unset($_SESSION['purchase_id']);
                unset($_SESSION['totalec']);
                unset($_SESSION['totaleb']);
            }

            $acquisto = new Acquisto($dbal);
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
            ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML PUBLIC 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Valutazione dei libri in acquisto</title>
<link href="/css/main.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="document.libro.newISBN.focus()">
<font face="Arial, Helvetica, sans-serif">
<h1 align="center">Valutazione dei libri in acquisto</h1>
<h2 align="center">Acquisto N° <?php echo $purchase_id; ?></h2>
<?php if (isset($errmsg)) {
    echo "<p align=\"center\"><font color=\"RED\">$errmsg</font></p>";
} ?>
<?php
            $acquisto->printAcquisto();
            if (!$trovato) {
                echo "<script language=\"Javascript\">alert('Libro non trovato!');</script>";
            }
            $bill = $acquisto->getBill();
            ?>
<p align="center"><?php echo $acquisto->numBook(); ?> Libri acquistati<br>Totale contanti: <?php echo $bill['totalec']; ?> &euro;
&nbsp;&nbsp;&nbsp;&nbsp;Totale buono: <?php echo $bill['totaleb']; ?> &euro;
&nbsp;&nbsp;&nbsp;&nbsp;Totale rottamazione: <?php echo $bill['totaler']; ?> &euro;</p>
<div align="center">
    <form action="/purchases/current" method="post" name="libro">
    ISBN o EAN
    <input name="newISBN" type="text" maxlength="13">
  <input type="submit" value="Ok">
</form>

</div>
</font>
</body>
</html>
<?php
        });
    }
}
