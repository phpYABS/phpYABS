<?php
if(eregi('^[a-z0-9]+$', $action = $_GET['Azione']))
    include($action . '.php');
