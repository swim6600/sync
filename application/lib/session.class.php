<?php
class session {
	public static $started;

	public function __construct() {
		if (!isset($_SESSION)) {
			throw new Exception(__("session could not be started"));
		}
	}
	
	public static function start() {
		if(!self::$started) {
			session_start();
			self::$started = true;
		}
	}

	public static function get($var) {
		if($this->exists($var)) {
			return $_SESSION[$var];
		}else {
			return false;
		}
	}

	public static function del($var) {
		unset($_SESSION[$var]);
	}

	public static function set($var, $value) {
		$_SESSION[$var] = $value;
	}

	public static function exists($var) {
		if(array_key_exists($var, $_SESSION)) {
			return true;
		}else {
			return false;
		}
	}

	public static function id($id = null) {
		if ($id) {
			session_id($id);
			return true;
		}
		if (isset($_SESSION)) {
			return session_id();
		} else {
			return false;
		}
	}
}