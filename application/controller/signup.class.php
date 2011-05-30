<?php
class signup extends app {
	public $authorization = false;

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->init_smarty();
		$smartyVars = array("title" => __("Sign up", true));
		$this->smarty->assign($smartyVars);
		$this->smarty->display("php:signup.tpl");
	}

	public function c() {
		if($this->is_post()) {
			$email = $this->get("email");
			if(!$this->is_email($email)) {
				throw new Exception(__("email not correct", true));
			}
			$password = $this->get("password");
			$repassword = $this->get("repassword");
			if($password != $repassword) {
				throw new Exception(__("password not correct", true));
			}
			$this->init_db();
			$user = new user();
			$user->email = $email;
			$user->password = md5($password);
			$user->confirmed = "0";
			$user->is_admin = "0";
			$user->created = time();
			if(!$user->Save()) {
				throw new Exception(__("exception when creating user", true));
			}
			$this->login($user->email, $password);
			$this->redirect();
		}
	}
}
