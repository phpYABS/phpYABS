<?php
global $dbal;

use Doctrine\DBAL\Connection;

assert($dbal instanceof Connection);

$risultato = $dbal->fetchOne('SELECT COUNT(*) FROM buyback_rates');
$totlibri = $risultato ?? 0;

$get_start = 0;
$destinazione = '';

if ('_NEW' !== ($_GET['destinazione'] ?? '')) {
    foreach ([$_GET, $_COOKIE] as $arr) {
        if (isset($arr['destinazione'])) {
            $destinazione = (string) $arr['destinazione'];
            break;
        }
    }

    foreach ([$_GET, $_COOKIE] as $arr) {
        if (isset($arr['start'])) {
            $get_start = $arr['start'];
            break;
        }
    }
}

setcookie('start', (string) $get_start, ['expires' => time() + 604800]);
setcookie('destinazione', $destinazione, ['expires' => time() + 604800]);

switch ($_GET['invia'] ?? '') {
    case 'Avanti':
        $start = $get_start + 50;
        if ($start > $totlibri) {
            $start = $totlibri - ($totlibri % 50);
        }
        break;
    case 'Indietro':
        $start = $get_start - 50;
        if ($start < 0) {
            $start = 0;
        }
        break;
    default:
        if (strlen((string) $get_start) > 0) {
            $start = $get_start;
        } else {
            $start = 0;
        }
        break;
}
if (!strlen($destinazione)) {
    $start = 0;
}
$pag = (int) ($start / 50) + 1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Destinazione Libri</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1 align="center">Aggiunta destinazioni</h1>
<p align="center">Pagina <?php echo $pag; ?></p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
<?php

 if (strlen($destinazione) > 0) {
     $locked = ' disabled';
     echo '<input type="hidden" name="destinazione" value="' . strtoupper($destinazione) . '">';
 } else {
     $locked = '';
 }

?>
<table width="100%" border="1" align="center">
<tr>
<td colspan="7">
<p align="center"><input type="text" name="destinazione" value="<?php echo htmlentities(strtoupper($destinazione)); ?>" style="width: 400px"<?php echo $locked; ?>></p>
</td>
</tr>
<?php
    if (strlen($destinazione) > 0) {
        if (is_array($_GET['destina'])) {
            foreach ($_GET['destina'] as $chiave => $valore) {
                if ('on' == $valore) {
                    $risultato = $conn->Query('SELECT COUNT(*) FROM ' . $prefix . '_destinazioni ' .
          "WHERE ISBN = '$chiave' AND destinazione = '$destinazione'");
                    $esiste = $risultato->fetchField(0);
                    if (!$esiste) {
                        $conn->Execute('INSERT INTO ' . $prefix . '_destinazioni (ISBN, destinazione) ' .
            " VALUES ('$chiave', '$destinazione')");
                    }
                } else {
                    $conn->Execute('DELETE FROM ' . $prefix . "_destinazioni WHERE ISBN='$chiave' " .
          "AND destinazione = '$destinazione'");
                }
            }
        }

        $risultato = $conn->Query('SELECT books.ISBN, Titolo, Autore, Editore FROM '
    . $prefix . '_libri INNER JOIN buyback_rates ON books.ISBN = '
    . $prefix . "_valutazioni.ISBN ORDER BY Editore, Autore, Titolo, ISBN LIMIT $start,50");
        while (false !== ($risultati = $risultato->FetchRow())) {
            $risultato1 = $conn->query('SELECT COUNT(*) FROM ' . $prefix . '_destinazioni ' .
        "WHERE ISBN='{$risultati[0]}' AND destinazione ='$destinazione'");
            $esiste = $risultato1->fetchField(0);
            if ($esiste) {
                $checkedSI = 'checked';
                $checkedNO = '';
            } else {
                $checkedSI = '';
                $checkedNO = 'checked';
            } ?>
      <tr>
        <td>
          S&igrave;
            <input name="destina[<?php echo $risultati[0]; ?>]" type="radio" value="on" <?php echo $checkedSI; ?>>
          No
          <input name="destina[<?php echo $risultati[0]; ?>]" type="radio" value="off" <?php echo $checkedNO; ?>>
        </td>
      <?php
      $risultati[0] = fullisbn($risultati[0]);
            for ($i = 0; $i < (is_countable($risultati) ? count($risultati) : 0); ++$i) {
                if (strlen((string) $risultati[$i]) < 1) {
                    $risultati[$i] = '&nbsp;';
                } else {
                    $risultati[$i] = htmlentities((string) $risultati[$i]);
                }
                echo "<td>$risultati[$i]</td>\n";
            } ?> </tr> <?php
        }
    }
?>
</table>

<input type="hidden" name="start" value="<?php echo $start; ?>">
<table align="center" border="1">
<tr>
  <td><input name="invia" type="submit" value="Avanti"></td>
  <td><input name="invia" type="submit" value="Indietro"></td>
  <td><input name="invia" type="submit" value="Salva"></td>
  <td><input type="reset" value="Azzera"></td>
</tr>
</table>
</form>
<?php
if (strlen($destinazione)) {
    $npag = (int) ($totlibri / 50);
    if ($totlibri % 50) {
        ++$npag;
    }
    for ($i = 1; $i <= $npag; ++$i) {
        echo "<a href=\"{$_SERVER['PHP_SELF']}?destinazione=$destinazione" .
    '&start=' . (($i - 1) * 50) . "\">$i</a>\n";
    }
}
?>
<p align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?destinazione=_NEW">Nuova destinazione</a></p>
</body>
</html>
