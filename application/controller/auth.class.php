<?php
class auth extends app {
	public $authorized;

    public function __construct() {
    	parent::__construct();
    	if(session::exists("user")) {
    		$this->authorized = true;
    	}else {
    		$this->authorized = false;
    	}
    }
}
