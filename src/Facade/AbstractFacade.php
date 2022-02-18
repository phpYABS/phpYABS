<?php

/**
 * @author Davide Bellettini <davide.bellettini@gmail.com>
 * @license GPLv3
 */

namespace PhpYabs\Facade;

use ADOConnection;
use Doctrine\DBAL\Connection;

/**
 * Base class for Façade pattern.
 */
abstract class AbstractFacade
{
    public function __construct(
        private readonly ADOConnection $ADOConnection,
        private readonly Connection $doctrineConnection,
    ) {
    }

    protected function getADOConnection(): ADOConnection
    {
        return $this->ADOConnection;
    }

    protected function getDoctrineConnection(): Connection
    {
        return $this->doctrineConnection;
    }
}
