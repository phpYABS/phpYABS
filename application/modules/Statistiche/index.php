<?php
global $dbal;

// conto gli acquisti
$nacquisti = $dbal->fetchOne('SELECT COUNT(purchase_id) FROM purchases');

// conto i libri acquistati
$libriacq = $dbal->fetchOne('SELECT COUNT(*) FROM purchases');

// conto i libri non trovati
$nerrori = $dbal->fetchOne("SELECT COUNT(ISBN) FROM hits WHERE found='no'");

// conto gli spari totali
$totspari = $dbal->fetchOne('SELECT SUM(hits) FROM hits');

// conto gli spari falliti
$errspari = $dbal->fetchOne("SELECT SUM(hits) FROM hits WHERE found='no'");

// calcolo gli spari con successo
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
