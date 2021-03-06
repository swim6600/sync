<?php
function autoload($name) {
	//$name = strtolower(str_ireplace("-", "_", $name));
	$root = dirname(__FILE__) . '/../';
	if(file_exists($file = $root . 'lib/'. $name . '.class.php')) {
		require_once $file;
		return true;
	}
	
	if(file_exists($file = $root . 'controller/'. $name . '.class.php')) {
		require_once $file;
		return true;
	}

	if(file_exists($file = $root . 'model/'. $name . '.php')) {
		require_once $file;
		return true;
	}
	
	if(file_exists($file = $name . '.class.php')) {
		require_once $file;
		return true;
	}
	
	return false;
}

spl_autoload_register("autoload");