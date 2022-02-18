<?php

switch ($_GET['Azione']) {
    case 'Elenco':
        include __DIR__ . '/Elenco.php';
        break;
    case 'Acquisto':
    case 'Nuovo':
    default:
        include __DIR__ . '/Acquisto.php';
        break;
}
