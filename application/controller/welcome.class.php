<?php
class welcome extends app {
	public $authorization = false;

    public function __construct() {
    	parent::__construct();
    }
    
    public function index() {
		$this->init_smarty();
    	$this->smarty->display("php:welcome.tpl");
    }
}
