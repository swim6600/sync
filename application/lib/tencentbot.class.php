<?php
class tencentbot extends bot{
	public $client;
	public $relation;
	public $newSinceId;
	
	public function __construct($relation) {
		$this->relation = $relation;
		require_once config::read("vendor") . "tencent/opent.php";
		require_once config::read("vendor") . "tencent/api_client.php";

		define("MB_AKEY", config::read("TENCENT_CONSUMER_KEY"));
		define("MB_SKEY", config::read("TENCENT_CONSUMER_SECRET"));
		define("MB_RETURN_FORMAT", 'json');
		define("MB_API_HOST", 'open.t.qq.com');
		$this->client = new MBApiClient(MB_AKEY, MB_SKEY, $relation->token, $relation->token_secret);
	}
	
	public function getNewSinceId() {
		return $this->newSinceId;
	}
	
	public function getTimeLine() {
		$p = array(
		"f" => 0,
		"n" => 10,
		"t" => 0,
		"type" => 1,
		"l" => 0
		);
		$timeline = $this->client->getMyTweet($p);
		
		$i = 0;
		$status = array();
		foreach ($timeline["data"]["info"] as $tweet) {
			$i ++;
			if($tweet["timestamp"] <= $this->relation->since_id) {
				break;
			}
			if($i == 1) {
				$this->newSinceId = $tweet["timestamp"];
			}
			$text = $tweet["origtext"];
			if(isset($tweet["source"])) {
				$text .= " @" . $tweet["source"]["name"] . " " . $tweet["source"]["origtext"];
			}
			$status[] = $this->expand($text);
		}
		
		return array_reverse($status);
	}
	
	public function updateStatus($status) {
		foreach ($status as $message) {
			$p = array(
			"c" => $message,
			"ip" => "",
			"j" => "",
			"w" => "",
			"r" => "",
			"type" => 1
			);
			$this->client->postOne($p);
		}
	}
}