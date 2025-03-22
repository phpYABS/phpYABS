<?php

declare(strict_types=1);

namespace PhpYabs\Facade;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class DestinationFacade extends AbstractFacade
{
    public function index(Request $request, Response $response): ResponseInterface
    {
        return $this->buffered($response, function () {
            $dbal = $this->getDoctrineConnection();

            $risultato = $dbal->fetchOne('SELECT COUNT(*) FROM buyback_rates');
            $totlibri = $risultato ?? 0;

            $get_start = 0;
            $destination = '';

            if ('_NEW' !== ($_GET['destination'] ?? '')) {
                foreach ([$_GET, $_COOKIE] as $arr) {
                    if (isset($arr['destination'])) {
                        $destination = (string) $arr['destination'];
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
            setcookie('destination', $destination, ['expires' => time() + 604800]);

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
            if (!strlen($destination)) {
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
            <form action="/destinations" method="GET">
                <?php

                if (strlen($destination) > 0) {
                    $locked = ' disabled';
                    echo '<input type="hidden" name="destination" value="' . strtoupper($destination) . '">';
                } else {
                    $locked = '';
                }

            ?>
                <table width="100%" border="1" align="center">
                    <tr>
                        <td colspan="7">
                            <p align="center"><input type="text" name="destination"
                                                     value="<?php echo htmlentities(strtoupper($destination)); ?>"
                                                     style="width: 400px"<?php echo $locked; ?>></p>
                        </td>
                    </tr>
                    <?php
                if (strlen($destination) > 0) {
                    if (is_array($_GET['destina'])) {
                        foreach ($_GET['destina'] as $chiave => $valore) {
                            if ('on' == $valore) {
                                $risultato = $dbal->fetchOne('SELECT COUNT(*) FROM destinations ' .
                                    "WHERE ISBN = '$chiave' AND destination = '$destination'");
                                $esiste = $risultato->fetchField(0);
                                if (!$esiste) {
                                    $dbal->executeStatement('INSERT INTO destinations (ISBN, destination) ' .
                                        " VALUES ('$chiave', '$destination')");
                                }
                            } else {
                                $dbal->executeStatement("DELETE FROM destinations WHERE ISBN='$chiave' " .
                                    "AND destination = '$destination'");
                            }
                        }
                    }

                    $risultato = $dbal->executeQuery(<<<SQL
        SELECT books.ISBN, title, author, publisher
        FROM books
        INNER JOIN buyback_rates
        ON books.ISBN = buyback_rates.ISBN ORDER BY publisher, author, title, ISBN LIMIT $start,50
        SQL
                    );

                    while (false !== ($risultati = $risultato->fetchNumeric())) {
                        $esiste = $dbal->fetchOne('SELECT COUNT(*) FROM destinations ' .
                            "WHERE ISBN='{$risultati[0]}' AND destination ='$destination'");
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
                                    <input name="destina[<?php echo $risultati[0]; ?>]" type="radio"
                                           value="on" <?php echo $checkedSI; ?>>
                                    No
                                    <input name="destina[<?php echo $risultati[0]; ?>]" type="radio"
                                           value="off" <?php echo $checkedNO; ?>>
                                </td>
                                <?php
                            $risultati[0] = fullisbn($risultati[0]);
                        foreach ($risultati as $i => $risultato) {
                            if (strlen((string) $risultato) < 1) {
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
            if (strlen($destination)) {
                $npag = (int) ceil($totlibri / 50);
                for ($i = 1; $i <= $npag; ++$i) {
                    echo "<a href=\"{$_SERVER['PHP_SELF']}?destination=$destination" .
                        '&start=' . (($i - 1) * 50) . "\">$i</a>\n";
                }
            }
            ?>
            <p align="center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?destination=_NEW">Nuova destination</a></p>
            </body>
            </html>
            <?php
        });
    }
}
