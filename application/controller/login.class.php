<?php
class login extends app {
	public $authorization = false;

    public function __construct() {
    	parent::__construct();
    }
    
    public function index($redirect = "") {
    	$this->smarty->assign(array('redirect' => $redirect));
    	$this->smarty->display("php:login.tpl");
    }
}
