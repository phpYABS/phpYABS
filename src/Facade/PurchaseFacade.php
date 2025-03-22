<?php

declare(strict_types=1);

namespace PhpYabs\Facade;

use PhpYabs\DB\Acquisto;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PurchaseFacade extends AbstractFacade
{
    public function index(Request $request, Response $response): ResponseInterface
    {
        return $this->buffered($response, function () {
            global $version;

            $dbal = $this->getDoctrineConnection();
            ?>
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
            <html>
            <head>
                <title>phpYabs <?php echo $version; ?></title>
                <link href="css/main.css" rel="stylesheet" type="text/css">
            </head>
            <body>
            <h1 align="center">Elenco Acquisti</h1>
            <table width="400" align="center">
                <tr>
                    <td>Acquisto</td>
                    <td>N° Libri</td>
                </tr>
                <?php
                $rset = $dbal->executeQuery('SELECT purchase_id, COUNT(purchase_id) FROM purchases GROUP BY purchase_id');

            while (false !== ($row = $rset->fetchAssociative())) {
                $purchase_id = $row['purchase_id'] ?? -1;
                $nlibri = $row['nlibri'] ?? 0;

                echo '<tr>';
                echo "  <td><a href=\"modules.php?Nome=Acquisti&Azione=Acquisto&purchase_id=$purchase_id\">$purchase_id</a></td>";
                echo "  <td>$nlibri</td>";
                echo '</tr>';
            }
            ?>
            </table>
            </body>
            </html>
            <?php
        });
    }

    public function current(Request $request, Response $response): ResponseInterface
    {
        return $this->buffered($response, function () {
            $dbal = $this->getDoctrineConnection();

            // se c'è una richiesta di nuovo acquisto, elimino il precedente
            if ('Nuovo' == $_GET['Azione']) {
                unset($_SESSION['purchase_id']);
                unset($_SESSION['totalec']);
                unset($_SESSION['totaleb']);
            }

            $acquisto = new Acquisto($dbal);
            if (isset($_GET['purchase_id'])) {
                if (!$acquisto->setID($_GET['purchase_id'])) {
                    $errmsg = "L'acquisto " . $_GET['purchase_id'] . ' non esiste!';
                }
            } elseif (isset($_SESSION['purchase_id'])) {
                $acquisto->setID($_SESSION['purchase_id']);
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
