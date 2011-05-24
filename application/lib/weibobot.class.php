<?php
class weibobot extends bot{
	public $client;
	public $relation;
	public $newSinceId;

	public function __construct($relation) {
		$this->relation = $relation;
		require_once config::read("vendor") . "weibo/weibooauth.php";
		define("WB_AKEY", config::read("WEIBO_CONSUMER_KEY"));
		define("WB_SKEY", config::read("WEIBO_CONSUMER_SECRET"));
		$this->client = new WeiboClient(WB_AKEY, WB_SKEY, $relation->token, $relation->token_secret);
	}

	public function getNewSinceId() {
		return $this->newSinceId;
	}
	
	public function getTimeLine() {
		$user_timeline = $this->client->user_timeline(1, 10, $this->relation->network_user_id, $this->relation->since_id);
		if (empty($user_timeline)) {
			throw new Exception("nothing need to be sync");
		}
		$i = 0;
		$status = array();
		foreach ($user_timeline as $timeline) {
			$i ++;
			if($i == 1) {
				$this->newSinceId = $timeline["id"];
			}
			$text = $timeline["text"];
			if(isset($timeline["retweeted_status"])) {
				$text .= " @" . $timeline["retweeted_status"]["user"]["screen_name"] . " " . $timeline["retweeted_status"]["text"];
			}
			$status[] = $this->expand($text);
		}
		
		return array_reverse($status);
	}

	public function updateStatus($status) {
		foreach ($status as $message) {
			$this->client->update($message);
		}
	}
}