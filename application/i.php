<?php
require 'config/config.inc.php';

define("CURRENT_PATH", dirname(__FILE__));

array_shift($argv);
if(isset($argv[0])) {
	$controller = array_shift($argv);
}else {
	throw new Exception(__("param, controller", true));
}
if(isset($argv[0])) {
	$action = array_shift($argv);
}else {
	throw new Exception(__("param, action", true));
}
if(!empty($argv)) {
	$params = $argv;
}else {
	$params = array();
}

if(class_exists($controller)) {
	$dispatch = new $controller;
	if(method_exists($dispatch, $action)) {
		$dispatch->$action($params);
	}else {
		throw new Exception(__("oops, action", true));
	}
}else {
	throw new Exception(__("oops, controller", true));
}
