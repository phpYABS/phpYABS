<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>phpYabs <? echo $version; ?></title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1 align="center">Elenco Acquisti</h1>
<table width="400" align="center">
<tr>
  <td>Acquisto</td>
  <td>N° Libri</td>
</tr>
<?
  $rset=$conn->Execute("SELECT IdAcquisto, COUNT(IdAcquisto) FROM ".$prefix."_acquisti GROUP BY IdAcquisto");
  
  while(!$rset->EOF) {
    list($IdAcquisto, $nlibri)=$rset->fields;

    echo "<tr>";
    echo "  <td><a href=\"modules.php?Nome=Acquisti&Azione=Acquisto&IdAcquisto=$IdAcquisto\">$IdAcquisto</a></td>";
    echo "  <td>$nlibri</td>";
    echo "</tr>";
	
	$rset->MoveNext();
  }
  $rset->Close();
?>
</table>
</body>
</html>