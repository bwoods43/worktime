<?php
require_once './app/config.php';
require_once UB_PATH . '/utility-belt.php';

try {
	UB_Controller_Front::run($routes);
}
catch ( Exception $e ) {
	$c = new Errors_Controller();
	$c->index($e->getCode(), $e);
}




