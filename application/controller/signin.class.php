<?php
class signin extends app {
	public $authorization = false;

	public function __construct() {
		parent::__construct();
	}

	public function index($redirect) {
		$this->init_smarty();
		$smartyVars = array("title" => __("Sign in", true), "redirect" => $redirect);
		$this->smarty->assign($smartyVars);
		$this->smarty->display("php:signin.tpl");
	}

	public function auth() {
		if($this->is_post()) {
			if($this->login($this->get("email"), $this->get("password"))) {
				if($redirect = $this->get("redirect")) {
					$this->redirect(urldecode($redirect));
				}else {
					$this->redirect("dashboard");
				}
			}else {
				throw new Exception(__("signin failed", true));
			}
		}
	}

	public function logout() {
		parent::logout();
		$this->redirect();
	}
}
