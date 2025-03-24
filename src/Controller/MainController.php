<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use PhpYabs\Configuration\Constants;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class MainController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        global $intestazione;

        return $this->render('index.twig', [
            'version' => Constants::VERSION,
            'header' => $intestazione,
        ]);
    }

    #[Route('/menu')]
    public function menu(Request $request, Response $response): ResponseInterface
    {
        global $edit;
        $view = Twig::fromRequest($request);

        return $view->render($response, 'menu.twig', compact('edit'));
    }
}
