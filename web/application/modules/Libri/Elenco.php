<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<META content="text/html; charset=utf-8" http-equiv=Content-Type>
<link href="css/main.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY>
<?php
  $limit=$_GET['Limit'];
  if(!isset($limit) || !ereg('^[0-9]+$', $limit))
    $limit=0;
  
  $rset=$conn->Execute("SELECT ISBN FROM ".$prefix."_valutazioni LIMIT $limit, 50");
  echo "<table border=\"1\" align=\"center\" width=\"755\">\n";
  echo "<tr>\n";
  echo "<td>ISBN</td>\n";
  echo "<td>Titolo</td>\n";
  echo "<td>Autore</td>\n";
  echo "<td>Editore</td>\n";
  echo "<td>Prezzo</td>\n";        
  while(!$rset->EOF) {
    echo "<tr>\n";
	
    loadClass('PhpYabs_Book');
    $book=new PhpYabs_Book();
	$book->GetFromDB($rset->fields['ISBN']);
		
    foreach($book->fields as $chiave => $valore) {
	  if(!is_numeric($chiave))
	    echo "<td>$valore</td>";
	}

    echo "</tr>";
	$rset->MoveNext();
	
	$book=NULL;
  }
  echo "</table>";  
  
  echo "<a href=\"modules.php?Nome=Libri&Azione=Elenco&Limit=".($limit+50)."\">Pagina ".($limit/50+2)."</a>";
?>
</BODY>
</HTML>