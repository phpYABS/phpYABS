<?php

declare(strict_types=1);

/**
 * @author Davide Bellettini <davide.bellettini@gmail.com>
 * @license GPLv3
 */

namespace PhpYabs\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;

/**
 * Base class for Façade pattern.
 */
abstract class AbstractController extends BaseController
{
    public function __construct(
        private readonly Connection $doctrineConnection,
        protected readonly EntityManagerInterface $entityManager,
    ) {
    }

    protected function getDoctrineConnection(): Connection
    {
        return $this->doctrineConnection;
    }
}
