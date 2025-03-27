<?php

declare(strict_types=1);

namespace PhpYabs\DB;

use Doctrine\DBAL\Connection;

abstract class ActiveRecord
{
    public function __construct(protected readonly Connection $dbalConnection)
    {
    }

    protected function getDbalConnection(): Connection
    {
        return $this->dbalConnection;
    }
}
