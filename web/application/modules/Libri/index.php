<?php
if(eregi('^[a-z0-9]+$', $action = $_REQUEST['Azione']))
    include($action . '.php');
