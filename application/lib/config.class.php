<?php
class config {
	private static $config;

	public static function write($var, $val) {
		self::$config->$var = $val;
	}

	public static function read($var) {
		if(isset(self::$config->$var)) {
			return self::$config->$var;
		}else {
			return false;
		}
	}
}
