<?php
// se c'è una richiesta di nuovo acquisto, elimino il precedente
use Doctrine\DBAL\Connection;
use PhpYabs\DB\Acquisto;

if ('Nuovo' == $_GET['Azione']) {
    unset($_SESSION['purchase_id']);
    unset($_SESSION['totalec']);
    unset($_SESSION['totaleb']);
}

global $dbal;
assert($dbal instanceof Connection);
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
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="document.libro.newISBN.focus()">
<font face="Arial, Helvetica, sans-serif">
<h1 align="center">Valutazione dei libri in acquisto</h1>
<h2 align="center">Acquisto N° <?php echo $purchase_id; ?></h2>
<?php if (isset($errmsg) && $errmsg) {
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
    <input type="hidden" name="Nome" value="<?php echo $_GET['Nome']; ?>">
    <input type="hidden" name="Azione" value="<?php echo $_GET['Azione']; ?>">
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?Nome=' . $_GET['Nome'] .
    '&Azione=Acquisto'; ?>" method="post" name="libro">
    ISBN o EAN
    <input name="newISBN" type="text" maxlength="13">
  <input type="submit" value="Ok">
</form>

</div>
</font>
</body>
</html>
