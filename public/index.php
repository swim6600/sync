<?php
require '../application/config/config.inc.php';
$auth = new auth;
$url = $auth->get("url");
if($url !== false) {
	$path = explode("/", $url);
	$controller = array_shift($path);
	if(isset($path[0])) {
		$action = array_shift($path);
	}else {
		$action = false;
	}
	foreach ($path as $value) {
		if(strpos($value, ":")) {
			list($var, $val) = explode(":", $value);
			$_REQUEST[$var] = $val;
		}
	}
}else {
	$controller = $auth->get("controller");
	if($controller === false) {
		$controller = config::read('index.controller');
	}
	$action = $auth->get("action");
}
if(class_exists($controller)) {
	$dispatch = new $controller;
	if($dispatch->authorization && $auth->authorized == false) {
		// go login page
		$dispatch = new signin;
		$redirect = urlencode($_SERVER["REQUEST_URI"]);
	}else {
		$redirect = $auth->get("go");
	}
	if($action === false) {
		$dispatch->index($redirect);
	}else {
		$dispatch->$action();
	}
}else {
	throw new Exception(__("oops, what are you looking for, dude?", true));
}
