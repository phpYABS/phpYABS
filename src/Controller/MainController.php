<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class MainController extends AbstractController
{
    #[Route('')]
    public function index(): Response
    {
        return $this->redirectToRoute('purchase_current', ['id' => 'current']);
    }
}
