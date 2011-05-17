<?php
require '../application/config/config.inc.php';
$auth = new auth;
$controller = $auth->get("controller");
if($controller == false) {
	$controller = config::read('index.controller');
}
if(class_exists($controller)) {
	$dispatch = new $controller;
	if($dispatch->authorization && $auth->authorized == false) {
		// go login page
		$dispatch = new login;
		$redirect = urlencode($_SERVER["REQUEST_URI"]);
	}else {
		$redirect = $auth->get("go");
	}
	$dispatch->index($redirect);
}else {
	throw new Exception(__("oops, what are you looking for, dude?", true));
}
