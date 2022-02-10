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
    private ADOConnection $connection;

    public function __construct(ADOConnection $connection)
    {
        $this->connection = $connection;
    }

    protected function getConnection(): ADOConnection
    {
        return $this->connection;
    }
}
