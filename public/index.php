<?php
error_reporting(-1);
// use autoloader (SPL)
require_once('../app/core/Autoloader.php');
spl_autoload_register('Autoloader::loadlibs');

$app = new App;


?>
