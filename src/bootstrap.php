<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpYabs\Configuration\Configuration;

date_default_timezone_set(Configuration::TIMEZONE);

$DS = DIRECTORY_SEPARATOR;

define('PATH_ROOT', realpath(__DIR__ . '/..'));
define('PATH_APPLICATION', PATH_ROOT . $DS . 'application');
define('PATH_TEMPLATES', PATH_APPLICATION . $DS . 'templates');

$intestazione = 'My Customer';
$edit = true;
$debug = true;