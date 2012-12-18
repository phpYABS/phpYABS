<?php


require_once __DIR__ .'/../application/includes/common.inc.php';
$app = new Silex\Application();

$mainFacade = new \PhpYabs\Facade\MainFacade($conn);

$app->match('/modules.php', array($mainFacade, 'modules'));
$app->get('/menu.php', array($mainFacade, 'menu'));
$app->get('/', array($mainFacade, 'index'));

$app->run();