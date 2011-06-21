<?php
class twitterbot extends bot{
	public $client;
	public $relation;
	public $newSinceId;

	public function __construct($relation) {
		$this->relation = $relation;
		define('CONSUMER_KEY', config::read("TWITTER_CONSUMER_KEY"));
		define('CONSUMER_SECRET', config::read("TWITTER_CONSUMER_SECRET"));
		require_once config::read("vendor") . "twitter/twitteroauth.php";
		$this->client = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $relation->token, $relation->token_secret);
	}

	public function getNewSinceId() {
		return $this->newSinceId;
	}

	public function getTimeLine() {
		$params = array(
			"user_id" => $this->relation->network_user_id,
			"count" => 10,
			"include_rts" => "1",
			"since_id" => $this->relation->since_id
		);
		$user_timeline = $this->client->get("statuses/user_timeline", $params);
		if (empty($user_timeline)) {
			throw new Exception("nothing need to be sync");
		}
		$i = 0;
		$status = array();
		foreach ($user_timeline as $timeline) {
			if(!empty($timeline->in_reply_to_status_id_str)) {
				continue;
			}
			$i ++;
			if($i == 1) {
				$this->newSinceId = $timeline->id_str;
			}
			$text = $timeline->text;
			preg_match(config::read("ignored"), $text, $match);
			if(empty($match[0])) {
				$status[] = $this->expand($text);
			}
		}

		return array_reverse($status);
	}

	public function updateStatus($status) {
		foreach ($status as $message) {
			$params = array(
				"status" => $message
			);
			$this->client->post("statuses/update", $params);
		}
	}
}