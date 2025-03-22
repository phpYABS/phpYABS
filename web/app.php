<?php

declare(strict_types=1);

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

use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use PhpYabs\Facade\BookFacade;
use PhpYabs\Facade\MainFacade;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../application/includes/common.inc.php';

$builder = new ContainerBuilder();
$builder->addDefinitions([
    LoggerInterface::class => function () {
        return new Logger('phpyabs', [new SyslogHandler('phpyabs')]);
    },
    Connection::class => function () {
        $parser = new DsnParser();

        return DriverManager::getConnection($parser->parse((string) getenv('DB_URL')));
    },
]);

$builder->useAutowiring(true);
$app = AppFactory::createFromContainer($builder->build());

$twig = Twig::create(__DIR__ . '/../templates');
$app->add(TwigMiddleware::create($app, $twig));

$app->any('/modules.php', [MainFacade::class, 'modules']);
$app->get('/menu.php', [MainFacade::class, 'menu']);
$app->get('/', [MainFacade::class, 'index']);
$app->any('/books', [BookFacade::class, 'elenco']);
$app->any('/books/add', [BookFacade::class, 'aggiungi']);
$app->any('/books/edit', [BookFacade::class, 'modifica']);

$app->run();
