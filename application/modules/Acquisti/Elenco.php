<?php
global $version, $dbal;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>phpYabs <?php echo $version; ?></title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1 align="center">Elenco Acquisti</h1>
<table width="400" align="center">
<tr>
  <td>Acquisto</td>
  <td>N° Libri</td>
</tr>
<?php
$rset = $dbal->executeQuery('SELECT purchase_id, COUNT(purchase_id) FROM purchases GROUP BY purchase_id');

foreach ($rset as $row) {
    $purchase_id = $row['purchase_id'] ?? -1;
    $nlibri = $row['nlibri'] ?? 0;

    echo '<tr>';
    echo "  <td><a href=\"modules.php?Nome=Acquisti&Azione=Acquisto&purchase_id=$purchase_id\">$purchase_id</a></td>";
    echo "  <td>$nlibri</td>";
    echo '</tr>';
}
?>
</table>
</body>
</html>
