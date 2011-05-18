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
    
    public function execute() {
    	var_dump($_REQUEST);
    	var_dump($this->get("something"));
    }
}
