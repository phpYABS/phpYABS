<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use PhpYabs\Configuration\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        global $intestazione;

        return $this->render('index.html.twig', [
            'version' => Constants::VERSION,
            'header' => $intestazione,
        ]);
    }
}
