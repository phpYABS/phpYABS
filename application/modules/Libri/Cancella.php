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
  <input type="hidden" name="Nome" value="<?=$_GET['Nome']?>">
  <input type="hidden" name="Azione" value="<?=$_GET['Azione']?>">
</form>
  <script language="JavaScript" type="text/javascript">
    document.form1.ISBN.focus();
  </script>
</div>
<?php } else {

    $delbook=new Book();
    $delbook->GetFromDB($_GET['ISBN']);

    if ($_GET['cancella']=="on") {
        $delbook->Delete();
        echo "<p>Libro Cancellato!</p>";
    } elseif (list($ISBN, $Titolo, $Autore, $Editore,$Prezzo)=$delbook->GetFields()) {
      $ISBN=$delbook->GetFullISBN();

      $Valutazione=$delbook->GetValutazione();

      if($Valutazione=="")
        $Valutazione="&nbsp;";

      include PATH_TEMPLATES.'/oldones/libri/tabdel.php';
    } else {
      echo "<p align=\"center\">Libro ".$delbook->GetFullIsbn()." non trovato!</p>";
    }
  }
?>
</body>
</html>
