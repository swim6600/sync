<?php
class auth extends app {
	public $user;
	public $authorized;

    public function __construct() {
    	parent::__construct();
    	if(session::exists("user")) {
    		$this->user = session::get("user");
    		$this->authorized = true;
    	}else {
    		$this->authorized = false;
    	}
    }
}
