<?php
/*function autoload($class){
	include 'lib/' . $class . '.php';
	}
spl_autoload_register('autoload');
*/
function autoload($class){
	if($class=='View'){
		include 'view/' .$class. '.php';		
	}
	else include 'lib/' . $class . '.php';
}
spl_autoload_register('autoload');