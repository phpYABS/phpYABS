<?php

switch ($_GET['Azione']) {
    default:
    case 'Acquisto':
    case 'Nuovo':
        include __DIR__ . '/Acquisto.php';
        break;
    case 'Elenco':
        include __DIR__ . '/Elenco.php';
        break;
}
