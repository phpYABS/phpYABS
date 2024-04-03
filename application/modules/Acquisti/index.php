<?php

include match ($_GET['Azione']) {
    'Elenco' => __DIR__ . '/Elenco.php',
    default => __DIR__ . '/Acquisto.php',
};
