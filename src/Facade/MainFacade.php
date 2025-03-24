<?php

declare(strict_types=1);

namespace PhpYabs\Facade;

use PhpYabs\Configuration\Constants;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class MainFacade extends AbstractFacade
{
    public function index(Request $request, Response $response): ResponseInterface
    {
        global $intestazione;
        $view = Twig::fromRequest($request);

        return $view->render($response, 'index.twig', [
            'version' => Constants::VERSION,
            'header' => $intestazione,
        ]);
    }

    public function menu(Request $request, Response $response): ResponseInterface
    {
        global $edit;
        $view = Twig::fromRequest($request);

        return $view->render($response, 'menu.twig', compact('edit'));
    }
}
