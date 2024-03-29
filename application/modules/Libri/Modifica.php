<?php
use PhpYabs\DB\Book;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Modifica libro</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<script language="JavaScript" type="text/javascript">
document.form1.ISBN.focus()
</script>
<h1 align="center">Modifica Libro</h1>
<?php
  if (!isset($_GET['ISBN'])) {?>
<div align="center">
  <form action="/modules.php" method="get" name="form1">
    ISBN
    <input type="text" name="ISBN">
    <input type="submit" name="Invia" value="Ok">
    <input type="hidden" name="Nome" value="Libri">
    <input type="hidden" name="Azione" value="Modifica">
  </form>
</div>
<?php } else {
      $modbook = new Book();

      $fields = ['ISBN' => $_GET['ISBN'],  'Titolo' => $_GET['Titolo'], 'Autore' => $_GET['Autore'],
          'Editore' => $_GET['Editore'], 'Prezzo' => $_GET['Prezzo'], ];

      $modbook->SetFields($fields);
      $modbook->SetValutazione($_GET['Valutazione']);

      if ($modbook->SaveToDB()) {
          echo '<p align="center">Libro modificato!</p>';
      }

      $modbook->GetFromDB($_GET['ISBN']);

      if ($f = $modbook->GetFields()) {
          [$ISBN, $Titolo, $Autore, $Editore, $Prezzo] = $f;
          $valutazione = $modbook->GetValutazione();
          switch ($valutazione) {
          case 'zero':
            $selzero = 'selected';
            break;
          case 'rotmed':
            $selrotmed = 'selected';
            break;
          case 'rotsup':
            $selrotsup = 'selected';
            break;
          case 'buono':
            $selbuono = 'selected';
            break;
          default:
            $selnull = 'selected';
            break;
        }

          include PATH_TEMPLATES . '/oldones/libri/tabmod.php';
      } else { ?>
<p align="center">Libro non trovato</p>
<?php
    }
  }
?>
</body>
</html>
