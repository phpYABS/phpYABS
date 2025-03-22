<?php
use PhpYabs\DB\Book;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Cancella Libro</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1 align="center">ATTENZIONE!!</h1>
<h1 align="center">Il seguente libro sta per essere cancellato</h1>
<h2 align="center">L'operazione &egrave; IRREVERSIBILE</h2>
<?php if (!isset($_GET['ISBN'])) {?>
  <div align="center">
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form1">
  ISBN
  <input type="text" name="ISBN">
  <input type="submit" name="Invia" value="Ok">
  <input type="hidden" name="Nome" value="<?php echo $_GET['Nome']; ?>">
  <input type="hidden" name="Azione" value="<?php echo $_GET['Azione']; ?>">
</form>
  <script language="JavaScript" type="text/javascript">
    document.form1.ISBN.focus();
  </script>
</div>
<?php } else {
    $delbook = new Book();
    $delbook->getFromDB($_GET['ISBN']);

    if ('on' == $_GET['cancella']) {
        $delbook->delete();
        echo '<p>Libro Cancellato!</p>';
    } elseif ($f = $delbook->getFields()) {
        [$ISBN, $Titolo, $Autore, $Editore, $Prezzo] = $f;
        $ISBN = $delbook->getFullISBN();

        $Valutazione = $delbook->getValutazione();

        if ('' == $Valutazione) {
            $Valutazione = '&nbsp;';
        }

        include PATH_TEMPLATES . '/oldones/libri/tabdel.php';
    } else {
        echo '<p align="center">Libro ' . $delbook->getFullIsbn() . ' non trovato!</p>';
    }
}
?>
</body>
</html>
