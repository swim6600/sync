<?php
class sync extends app {

	public function __construct() {
	}
	
	public function loader() {
		$this->init_db();
		$model = new user();
		$users = $model->Find("confirmed = 1");
		foreach ($users as $user) {
			exec("php " . config::read("root") . "i.php sync run " . $user->id);
		}
	}

	public function run($params) {
		$user_id = $params[0];
		$this->init_db();
		$model = new relation();
		$relations = $model->getRelations($user_id);
		
		// networks contain other network without mainnetwork
		$networks = array();
		$messageCount = 0;
		if(!empty($relations) && count($relations) > 1) {
			foreach ($relations as $relation) {
				if($relation->is_main_network) {
					$mainNetwork = $relation;
				}else {
					$networks[] = $relation;
				}
			}

			$timeline_bot = $this->getProcesser($mainNetwork);
			$status = $timeline_bot->getTimeLine($mainNetwork);
			$messageCount = count($status);
			foreach ($networks as $network) {
				$update_bot = $this->getProcesser($network);
				$update_bot->updateStatus($status);
			}
			if($messageCount > 0) {
				$update = array(
					"since_id" => $timeline_bot->getNewSinceId(),
					"modified" => time()
				);
				$mainNetwork->update($update);
			}
		}
		echo sprintf("update ok: %s tweets.\n", $messageCount);
	}

	private function getProcesser($relation) {
		if ($relation->network === "twitter") {
			$processer = new twitterbot($relation);
		}
		if ($relation->network === "weibo") {
			$processer = new weibobot($relation);
		}
		if ($relation->network === "tencent") {
			$processer = new tencentbot($relation);
		}

		return $processer;
	}
}
