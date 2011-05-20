<?php
class dashboard extends app {
	public $authorization = true;

    public function __construct() {
    	parent::__construct();
    }
    
    public function index() {
    	$this->init_db();
		$this->init_smarty();
		$relation = new relation();
		$weibo = array();
		$weibo["is_connected"] = $relation->is_connected($this->user["id"], "weibo");
		$smartyVars = array("user" => $this->user, "weibo" => $weibo);
		$this->smarty->assign($smartyVars);
    	$this->smarty->display("php:dashboard.tpl");
    }
}
