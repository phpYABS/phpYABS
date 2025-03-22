<?php

namespace PhpYabs\Facade;

use PhpYabs\Configuration\Configuration;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class MainFacade extends AbstractFacade
{
    public function index(Request $request, Response $response): Response
    {
        global $intestazione;
        $view = Twig::fromRequest($request);

        return $view->render($response, 'index.twig', [
            'version' => Configuration::VERSION,
            'header' => $intestazione,
        ]);
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
<link href="/css/main.css" rel="stylesheet" type="text/css">
<base target="main">
</HEAD>
<BODY>
<h1>Menu</h1>
<p><a href="/purchases/current?Azione=Nuovo">Nuovo Acquisto</a></p>
<form action="/purchases" method="get" name="acquisto">
        Recupera acquisto:
  <input name="purchase_id" type="text" id="purchase_id" maxlength="10" style="width:90px">
  <input type="submit" value="Ok">
</form>
<?php if ($edit) { ?>
    <p><a href="/books/add">Aggiungi Libro</a></p>
    <p><a href="/books/edit">Modifica Libro</a></p>
    <p><a href="/books/delete">Cancella Libro</a></p>
    <?php } ?>
<p><a href="/purchases">Elenco Acquisti</a></p>
<p><a href="/destinations">Destinazioni</a></p>
<p><a href="/stats">Statistiche</a></p>
<p><a href="/books" target="_blank">Elenco Libri</a></p>
</BODY>
</HTML>
    <?php
        $response->getBody()->write((string) ob_get_clean());

        return $response;
    }
}
