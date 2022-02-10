<?php
  //conto gli acquisti
  $rset = $conn->Execute('SELECT COUNT(IdAcquisto) FROM ' . $prefix . '_acquisti');
  [$nacquisti] = $rset->fields;
  $rset->Close();

  //conto i libri acquistati
  $rset = $conn->Execute('SELECT COUNT(*) FROM ' . $prefix . '_acquisti');
  [$libriacq] = $rset->fields;
  $rset->Close();

  //conto i libri non trovati
  $rset = $conn->Execute('SELECT COUNT(ISBN) FROM ' . $prefix . "_hits WHERE trovato='no'");
  [$nerrori] = $rset->fields;
  $rset->Close();

  //conto gli spari totali
  $rset = $conn->Execute('SELECT SUM(hits) FROM ' . $prefix . '_hits');
  [$totspari] = $rset->fields;
  $rset->Close();

  //conto gli spari falliti
  $rset = $conn->Execute('SELECT SUM(hits) FROM ' . $prefix . "_hits WHERE trovato='no'");
  [$errspari] = $rset->fields;
  $rset->Close();

  //calcolo gli spari con successo
  $spariok = $totspari - $errspari;
?>
<html>
<head>
<title>Statistiche</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>

<body>
<p>Libri acquistati: <b><?php echo $libriacq; ?></b></p>
<p>Libri (unici) non trovati: <b><?php echo $nerrori; ?></b></p>
<p>&nbsp;</p>
<p><b>"Spari"</b></p>
<p>Trovati: <b><?php echo $spariok; ?></b></p>
<p>Non trovati: <b><?php echo $errspari; ?></b></p>
<p>Totali: <b><?php echo $totspari; ?></b></p>

</body>
</html>
