<?php
// vim: set shiftwidth=4 tabstop=4 expandtab cindent :

/*
 * phpYABS - Web-based book management
 * Copyright (C) 2003-2012 Davide Bellettini
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
 
/**
 * @author Davide Bellettini <davide.bellettini@gmail.com>
 * @license GNU GPL 3 or later
 */

require_once __DIR__ .'/../application/includes/common.inc.php';
$app = new Silex\Application();

$mainFacade = new \PhpYabs\Facade\MainFacade($conn);

$app->get('/menu.php', array($mainFacade, 'menu'));
$app->get('/', array($mainFacade, 'index'));

$app->run();
