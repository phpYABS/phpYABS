<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<META content="text/html; charset=utf-8" http-equiv=Content-Type>
<link href="css/main.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY>
<?php
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

    $book=new PhpYabs_Book();
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
