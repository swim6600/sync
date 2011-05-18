<?php
class app extends object {
	public $session;
	public $smarty;
	public $db;

	public function __construct() {
		session::start();
		if(session::exists("user")) {
			$this->user = session::get("user");
		}
		$this->init_smarty();
	}

	public function init_db() {
		$databaseManager = new dbManager();
		$this->db = $databaseManager->getInstance();
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

	public function is_post() {
		return $_SERVER["REQUEST_METHOD"] == "POST";
	}

	public function login($email = "", $password = "") {
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
				$currentUser["email"] = $email;
				$currentUser["is_admin"] = $user->is_admin;
				$currentUser["confirmed"] = $user->confirmed;
				
				session::set("user", $currentUser);
				return true;
			}else {
				return false;
			}
		}
	}
	
	public function logout() {
		if(session::get("user")) {
			session::del("user");
		}
	}

	public function redirect($url = "/") {
		header("location: " . $url);
		exit;
	}
}
