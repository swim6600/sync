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

	/**
	 * session get function
	 * $_COOKIE will also be found if session var not exists
	 * @param mixed $var
	 * @return mixed|boolean
	 */
	public static function get($var) {
		if(self::exists($var)) {
			return $_SESSION[$var];
		}else {
			return false;
		}
	}

	public static function del($var) {
		unset($_SESSION[$var]);
	}
	
	public static function delcookie($var) {
		$value = session::get($var);
		self::setcookie($var, $value, time() - 3600);
	}

	public static function set($var, $value) {
		$_SESSION[$var] = $value;
		self::setcookie($var, $value, time() + config::read("cookie_expire"));
	}
	
	public static function setcookie($var, $value, $expire) {
		if(is_array($value)) {
			foreach ($value as $k => $v) {
				self::setcookie($var . "[$k]", $v, $expire);
			}
		}else {
			if($expire == 0) {
				$value == null;
			}
			setcookie($var, $value, $expire, config::read("cookie_path"), config::read("cookie_domain"));
		}
	}

	public static function exists($var) {
		if(array_key_exists($var, $_SESSION)) {
			return true;
		}else {
			if(array_key_exists($var, $_COOKIE)) {
				self::set($var, $_COOKIE[$var]);
				return true;
			}else {
				return false;
			}
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