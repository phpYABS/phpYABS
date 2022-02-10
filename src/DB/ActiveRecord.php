<?php

// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

namespace PhpYabs\DB;

use ADOConnection;

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
     * Database connection.
     */
    protected ADOConnection $_db;

    /**
     * Table prefix.
     */
    private string $prefix = 'phpyabs';

    public function __construct(ADOConnection $connection = null)
    {
        if (is_null($connection)) {
            global $conn;
            $connection = $conn;
        }

        $this->_db = $connection;
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
}
