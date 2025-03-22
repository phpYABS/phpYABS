<?php

declare(strict_types=1);

namespace PhpYabs\Facade;

use PhpYabs\Configuration\Configuration;
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
            'version' => Configuration::VERSION,
            'header' => $intestazione,
        ]);
    }

    public function menu(Request $request, Response $response): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'menu.twig');
    }
}
