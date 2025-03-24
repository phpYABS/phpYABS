<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use PhpYabs\Configuration\Constants;

date_default_timezone_set(Constants::TIMEZONE);

$DS = DIRECTORY_SEPARATOR;

define('PATH_ROOT', realpath(__DIR__ . '/..'));
define('PATH_APPLICATION', PATH_ROOT . $DS . 'application');
define('PATH_TEMPLATES', PATH_APPLICATION . $DS . 'templates');

$intestazione = 'My Customer';
$edit = true;
$debug = true;
