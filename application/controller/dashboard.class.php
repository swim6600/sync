<?php
class dashboard extends app {
	public $authorization = true;

    public function __construct() {
    	parent::__construct();
    }
    
    public function index() {
		$smartyVars = array("user" => $this->user);
		$this->smarty->assign($smartyVars);
    	$this->smarty->display("php:dashboard.tpl");
    }
}
