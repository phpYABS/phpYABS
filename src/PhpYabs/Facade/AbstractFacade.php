<?php

/**
 * @author Davide Bellettini <davide.bellettini@gmail.com>
 * @license GPLv3
 */

namespace PhpYabs\Facade;

/**
 * Base class for Façade pattern
 */
abstract class AbstractFacade
{
    private $connection;

    /**
     * Class constructor, needs a DB connection
     *
     * @param \ADOConnection $connection
     */
    public function __construct(\ADOConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Connection getter
     *
     * @return \ADOConnection
     */
    protected function getConnection()
    {
        return $this->connection;
    }
}
