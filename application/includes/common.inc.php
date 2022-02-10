<?php

// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

/**
 * $Id: file-header.php 299 2009-11-21 17:09:54Z dvbellet $.
 *
 * phpYABS - Web-based book management
 * Copyright (C) 2009 Davide Bellettini
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
 */
require_once __DIR__ . '/config.inc.php';

//starting session
session_start();

$loader = require __DIR__ . '/../../vendor/autoload.php';

global $conn;
$conn = ADONewConnection('pdo');
assert($conn instanceof ADOConnection);

$conn->PConnect(getenv('DB_URL'), getenv('DB_USER'), getenv('DB_PASS'));
