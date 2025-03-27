<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use PhpYabs\Repository\StatisticsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/statistics')]
class StatisticsController extends AbstractController
{
    public function __construct(private StatisticsRepository $repository)
    {
    }

    #[Route('', name: 'statistics', methods: ['GET'])]
    public function index(): Response
    {
        $data = $this->repository->getStatistics();

        return $this->render('statistics/index.html.twig', $data);
    }
}
