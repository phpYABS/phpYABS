<?php

use PhpYabs\Facade\BookFacade;

global $conn;
$facade = new BookFacade($conn);
$facade->elenco();
