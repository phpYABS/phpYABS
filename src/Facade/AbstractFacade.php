<?php

/**
 * @author Davide Bellettini <davide.bellettini@gmail.com>
 * @license GPLv3
 */

namespace PhpYabs\Facade;

use ADOConnection;

/**
 * Base class for Façade pattern.
 */
abstract class AbstractFacade
{
    public function __construct(private readonly ADOConnection $connection)
    {
    }

    protected function getConnection(): ADOConnection
    {
        return $this->connection;
    }
}
