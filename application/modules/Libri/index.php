<?php

if (preg_match('/^[a-z0-9]+$/i', (string) ($action = $_REQUEST['Azione']))) {
    include $action . '.php';
}
