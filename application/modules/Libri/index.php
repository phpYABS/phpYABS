<?php

if (preg_match('/^[a-z0-9]+$/i', $action = $_REQUEST['Azione'])) {
    include $action . '.php';
}
