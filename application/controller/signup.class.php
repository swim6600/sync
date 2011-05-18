<?php
class signup extends app {
	public $authorization = false;

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$smartyVars = array("title" => __("Sign up", true));
		$this->smarty->assign($smartyVars);
		$this->smarty->display("php:signup.tpl");
	}

	public function c() {
		if($this->is_post()) {
			$password = $this->get("password");
			$repassword = $this->get("repassword");
			if($password != $repassword) {
				throw new Exception(__("password not input correctly", true));
			}
			$this->init_db();
			$user = new user();
			$user->email = $this->get("email");
			$user->password = md5($password);
			$user->confirmed = "0";
			$user->is_admin = "0";
			if(!$user->Save()) {
				throw new Exception(__("exception when creating user", true));
			}
			$this->login($user->email, $password);
			$this->redirect("/");
		}
	}
}
