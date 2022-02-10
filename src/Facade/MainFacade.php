<?php

namespace PhpYabs\Facade;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class MainFacade extends AbstractFacade
{
    public function index(Request $request, Response $response): Response
    {
        ob_start();

        global $ver, $intestazione; ?>
    <html>
    <head>
        <title>phpYabs <?php echo $ver; ?> - <?php echo $intestazione; ?></title>
    </head>
    <frameset cols="160,*">
        <frame src="menu.php" name="menu">
        <frame src="modules.php?Nome=Acquisti&Azione=Nuovo" name="main">
    </frameset><noframes></noframes>
    </html>

<?php

        $response->getBody()->write((string) ob_get_clean());

        return $response;
    }

    public function menu(Request $request, Response $response): Response
    {
        global $edit;
        ob_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META content="text/html; charset=utf-8" http-equiv=Content-Type>
<title>phpYabs - menu</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
<base target="main">
</HEAD>
<BODY>
<h1>Menu</h1>
<p><a href="modules.php?Nome=Acquisti&Azione=Nuovo">Nuovo Acquisto</a></p>
<form action="modules.php" method="get" name="acquisto">
        Recupera acquisto:
  <input name="IdAcquisto" type="text" id="IdAcquisto" maxlength="10" style="width:90px">
  <input name="Nome" value="Acquisti" type="hidden">
  <input name="Azione" value="Acquisto" type="hidden">
  <input type="submit" value="Ok">
</form>
<?php if ($edit) { ?>
    <p><a href="/books/add">Aggiungi Libro</a></p>
    <p><a href="modules.php?Nome=Libri&Azione=Modifica">Modifica Libro</a></p>
    <p><a href="modules.php?Nome=Libri&Azione=Cancella">Cancella Libro</a></p>
    <?php } ?>
<p><a href="modules.php?Nome=Acquisti&Azione=Elenco">Elenco Acquisti</a></p>
<p><a href="modules.php?Nome=Destinazioni">Destinazioni</a></p>
<p><a href="modules.php?Nome=Statistiche">Statistiche</a></p>
<p><a href="/books" target="_blank">Elenco Libri</a></p>
</BODY>
</HTML>
    <?php
        $response->getBody()->write((string) ob_get_clean());

        return $response;
    }

    public function modules(Request $request, Response $response): Response
    {
        ob_start();

        $module = $request->getQueryParams()['Nome'] ?? '';
        if (!preg_match('/^[a-z0-9]+$/i', $module)) {
            $module = '---';
        }

        $file = PATH_APPLICATION . "/modules/$module/index.php";

        if (!file_exists($file)) {
            $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'text/plain')
                ->getBody()->write('Not found');

            return $response;
        }

        include $file;

        $response->getBody()->write((string) ob_get_clean());

        return $response;
    }
}
