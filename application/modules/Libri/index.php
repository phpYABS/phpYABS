<?php

declare(strict_types=1);

$action = $_REQUEST['Azione'] ?? null;

if (is_string($action) && preg_match('/^[a-z0-9]+$/i', $action)) {
    include $action . '.php';
}
