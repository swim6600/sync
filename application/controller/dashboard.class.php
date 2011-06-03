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
		$weibo = $relation->getRelation($this->user["id"], "weibo");
		$tencent = $relation->getRelation($this->user["id"], "tencent");
		$twitter = $relation->getRelation($this->user["id"], "twitter");
		if($this->user['is_admin']) {
			$count = $this->db->GetOne("select count(*) from users where confirmed = 1");
			/**
			 $user = new user;
			 $users = $user->Find("confirmed = 1");
			 **/
			$this->smarty->assign("count", $count);
		}

		$smartyVars = array(
		"user" => $this->user,
		"weibo" => $weibo,
		"tencent" => $tencent,
		"twitter" => $twitter
		);
		$this->smarty->assign($smartyVars);
		$this->smarty->display("php:dashboard.tpl");
	}
}
