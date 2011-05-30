<?php
class app extends object {
	public $smarty;
	public $db;

	public function __construct() {
		session::start();
		if(session::exists("user")) {
			$this->user = session::get("user");
		}
	}

	protected function init_db($profile = "production") {
		$databaseManager = new dbManager();
		$this->db = $databaseManager->getInstance($profile);
	}

	protected function init_smarty() {
		require_once(config::read("vendor") . 'Smarty.class.php');

		$this->smarty = new Smarty();

		$this->smarty->debugging = false;
		$this->smarty->force_compile = true;

		$this->smarty->template_dir = config::read("root") . 'views/';
		$this->smarty->compile_dir = config::read("root") . 'temp/templates_c/';
		$this->smarty->config_dir = config::read("root") . 'config/';
		$this->smarty->cache_dir = config::read("root") . 'temp/cache/';
		$this->smarty->security = true;
		$this->smarty->allow_php_templates = true;
	}

	public function get($var = "id", $required = false) {
		if(is_array($var)){
			foreach($var as $v){
				if(isset($_GET[$v]) && trim($_GET[$v]) != ''){
					return trim($_GET[$v]);
				}
				if(isset($_POST[$v]) && trim($_POST[$v]) != ''){
					return trim($_POST[$v]);
				}
				if(isset($_REQUEST[$v]) && trim($_REQUEST[$v]) != ''){
					return trim($_REQUEST[$v]);
				}
			}
		}else {
			if(isset($_GET[$var]) && trim($_GET[$var]) != ''){
				return trim($_GET[$var]);
			}
			if(isset($_POST[$var]) && trim($_POST[$var]) != ''){
				return trim($_POST[$var]);
			}
			if(isset($_REQUEST[$var]) && trim($_REQUEST[$var]) != ''){
				return trim($_REQUEST[$var]);
			}
		}
		if($required){
			throw new exception(__('parameter was not passed correctly', true));
		}else {
			return false;
		}
	}

	protected function __getCode($input) {
		$base32 = array (
    		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
    		'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
    		'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
    		'y', 'z', '0', '1', '2', '3', '4', '5');

		$hex = md5($input);
		$hexLen = strlen($hex);
		$subHexLen = $hexLen / 8;
		$output = array();

		for ($i = 0; $i < $subHexLen; $i++) {
			$subHex = substr ($hex, $i * 8, 8);
			$int = 0x3FFFFFFF & (1 * ('0x' . $subHex));
			$out = '';

			for ($j = 0; $j < 6; $j++) {
				$val = 0x0000001F & $int;
				$out .= $base32[$val ^ 7];
				$int = $int >> 5;
			}

			$output[] = $out;
		}

		return $output;
	}
	
	protected function encode($dec) {
		$toRadix = 61;
		$MIN_RADIX = 2;
		$MAX_RADIX = 61;
		// use i as directory, total 61
		$num62 = '0123456789abcdefghjklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if ($toRadix < $MIN_RADIX || $toRadix > $MAX_RADIX) {
			$toRadix = 2;
		}
		if ($toRadix == 10) {
			return $dec;
		}
		$buf = array();
		$charPos = 64;
		$isNegative = $dec < 0;
		if (!$isNegative) {
			$dec = -$dec;
		}

		while (bccomp($dec, -$toRadix) <= 0) {
			$buf[$charPos--] = $num62[-bcmod($dec, $toRadix)];
			$dec = bcdiv($dec, $toRadix);
		}
		$buf[$charPos] = $num62[-$dec];
		if ($isNegative) {
			$buf[--$charPos] = '-';
		}
		$_any = '';
		for ($i = $charPos; $i < 65; $i++) {
			$_any .= $buf[$i];
		}
		return $_any;
	}

	protected function decode($number) {
		$fromRadix = 61;
		// use i as directory, total 61
		$num62 = '0123456789abcdefghjklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$dec = 0;
		$digitValue = 0;
		$len = strlen($number) - 1;
		for ($t = 0; $t <= $len; $t++) {
			$digitValue = strpos($num62, $number[$t]);
			$dec = bcadd(bcmul($dec, $fromRadix), $digitValue);
		}
		return $dec;
	}

	protected function is_post() {
		return $_SERVER["REQUEST_METHOD"] == "POST";
	}

	protected function login($email = "", $password = "") {
		if(session::get("user")) {
			return true;
		}else {
			$this->init_db();
			if(empty($email) || empty($password)) {
				throw new Exception(__("email or password not provided correctly", true));
			}
			$user = new user();
			$user->email = $email;
			$res = $user->Find("email = ?", array($email));
			if($res && $res[0]->password == md5($password)) {
				$currentUser = array();
				$currentUser["id"] = $res[0]->id;
				$currentUser["email"] = $email;
				$currentUser["is_admin"] = $res[0]->is_admin;
				$currentUser["confirmed"] = $res[0]->confirmed;
				
				session::set("user", $currentUser);
				return true;
			}else {
				return false;
			}
		}
	}
	
	protected function logout() {
		if(session::get("user")) {
			session::delcookie("user");
			session::del("user");
		}
	}

	public function redirect($url = "", $baseURI = true) {
		if($baseURI) {
			$url = config::read("base.uri") . $url;
		}
		header("location: " . $url);
		exit;
	}
}
