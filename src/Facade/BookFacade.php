<?php
namespace PhpYabs\Facade;

use PhpYabs\Book;

class BookFacade extends AbstractFacade
{

    public function aggiungi()
    {
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Aggiungi libro</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$addbook=new Book($this->getConnection());
  if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$addbook->IsValidISBN($_POST['ISBN'])) {
      include PATH_TEMPLATES.'/oldones/libri/tabadd.php';
  } else {
      $fields=Array("ISBN" => $_POST['ISBN'],"Titolo" => $_POST['Titolo'], "Autore" => $_POST['Autore'],
          "Editore" => $_POST['Editore'], "Prezzo" => $_POST['Prezzo']);

      $addbook->SetFields($fields);
      $addbook->SetValutazione($_POST['Valutazione']);

      if($addbook->SaveToDB())
          echo "<p>Libro inserito</p>";
      else
          echo "<p>Errore nell'inserimento del libro</p>";
  }
?>
</body>
</html>
<?php
    }

    public function elenco()
    {
?>

    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <HTML>
    <HEAD>
        <META content="text/html; charset=utf-8" http-equiv=Content-Type>
        <link href="css/main.css" rel="stylesheet" type="text/css">
    </HEAD>
    <BODY>
        <?php
        global $conn, $prefix;

        $rset = $conn->Execute('SELECT COUNT(*) FROM '.$prefix.'_valutazioni');
        $count = $rset->fields[0];

        $limit = isset($_GET['limit']) && preg_match('/\d+/', $_GET['limit']) ? $_GET['limit'] : 0;

        $rset=$conn->Execute("SELECT ISBN FROM ".$prefix."_valutazioni LIMIT $limit, 50");
        echo "<table border=\"1\" align=\"center\" width=\"755\">\n";
        echo "<tr>\n";
        echo "<td>ISBN</td>\n";
        echo "<td>Titolo</td>\n";
        echo "<td>Autore</td>\n";
        echo "<td>Editore</td>\n";
        echo "<td>Prezzo</td>\n";
        echo '</tr>', PHP_EOL;

        while (!$rset->EOF) {
            echo "<tr>\n";

            $book=new Book();
            $book->GetFromDB($rset->fields['ISBN']);

            foreach ($book->fields as $chiave => $valore) {
                if(!is_numeric($chiave))
                    echo "<td>$valore</td>";
            }

            echo "</tr>";
            $rset->MoveNext();

            unset($book);
        }
        echo "</table>";

        echo "<a href=\"modules.php?Nome=Libri&Azione=Elenco&Limit=".($limit+50)."\">Pagina ".($limit/50+2)."</a>";
        ?>
    <p><?=$count?> libri presenti.</p>
    </BODY>
    </HTML>
<?php

    }
}
