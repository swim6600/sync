<?php
function __autoload($name) {
	$root = dirname(__FILE__) . '/../';
	if(file_exists($file = $root . 'lib/'. $name . '.class.php')) {
		require_once $file;
		return true;
	}
	if(file_exists($file = $root . 'implement/'. $name . '.class.php')) {
		require_once $file;
		return true;
	}
	require_once $name . '.php';
}
