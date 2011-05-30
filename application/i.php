<?php
require 'config/config.inc.php';
if(isset($argv[1])) {
	$controller = $argv[1];
}
if(isset($argv[2])) {
	$user_id = $argv[2];
}else {
	throw new Exception(__("oops, user id", true));
}

$action = "index";

if(class_exists($controller)) {
	$dispatch = new $controller;
	if(method_exists($dispatch, $action)) {
		$dispatch->$action($user_id);
	}else {
		throw new Exception(__("oops, action", true));
	}
}else {
	throw new Exception(__("oops, controller", true));
}
