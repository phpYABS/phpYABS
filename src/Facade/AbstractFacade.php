<?php

declare(strict_types=1);

/**
 * @author Davide Bellettini <davide.bellettini@gmail.com>
 * @license GPLv3
 */

namespace PhpYabs\Facade;

use Doctrine\DBAL\Connection;
use Slim\Psr7\Response;

/**
 * Base class for Façade pattern.
 */
abstract class AbstractFacade
{
    public function __construct(
        private readonly Connection $doctrineConnection,
    ) {
    }

    protected function getDoctrineConnection(): Connection
    {
        return $this->doctrineConnection;
    }

    protected function buffered(Response $response, callable $callable): Response
    {
        ob_start();

        $callable();

        $response->getBody()->write((string) ob_get_clean());

        return $response;
    }
}
