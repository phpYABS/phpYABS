<?php

switch($_GET['Azione']) 
{
	default:
	case 'Acquisto':
	case 'Nuovo':
		include 'Acquisto.php';
		break;
	case 'Elenco':
		include 'Elenco.php';
		break;
}
