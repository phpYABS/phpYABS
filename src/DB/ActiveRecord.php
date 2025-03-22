<?php

// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

namespace PhpYabs\DB;

use Doctrine\DBAL\Connection;

/**
 * phpYABS - Web-based book management
 * Copyright (C) 2003-2012 Davide Bellettini.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Davide Bellettini <davide.bellettini@gmail.com>
 */

/**
 * Base DB class.
 */
abstract class ActiveRecord
{
    /**
     * Table prefix.
     */
    private string $prefix = 'phpyabs';
    private readonly Connection $dbalConnection;

    public function __construct(?Connection $dbalConnection = null)
    {
        if (is_null($dbalConnection)) {
            global $dbal;
            $dbalConnection = $dbal;
        }

        $this->dbalConnection = $dbalConnection;
    }

    /**
     * Table prefix getter.
     *
     * @return string (e.g. phpyabs)
     */
    protected function getPrefix(): string
    {
        return $this->prefix;
    }

    protected function getDbalConnection(): Connection
    {
        return $this->dbalConnection;
    }
}
