<?php
class dashboard extends app {
	public $authorization = true;

    public function __construct() {
    	parent::__construct();
    }
    
    public function index() {
    	$this->smarty->display("php:welcome.tpl");
    }
}
