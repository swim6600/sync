<?php
class show extends app {
	public $authorization = false;

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$key = $this->get();
		$id = $this->decode($key);
		$this->init_db();
		$this->init_smarty();
		$model = new expand();
		$r = $model->Find("id = ?", array("id" => $id));
		if(empty($r)) {
			throw new Exception(__("no specified message foud", true));
		}
		$smartyVars = array(
			"title" => __("Expanded message", true),
			"message" => $r[0]->text,
			"created" => $r[0]->created
		);
		$this->smarty->assign($smartyVars);
		$this->smarty->display("php:show.tpl");
	}
}
