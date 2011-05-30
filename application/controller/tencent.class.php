<?php
class tencent extends app {
	public $authorization = true;

	public function __construct() {
		parent::__construct();
	}

	public function init() {
		require_once config::read("vendor") . "tencent/opent.php";
		require_once config::read("vendor") . "tencent/api_client.php";

		define("MB_AKEY", config::read("TENCENT_CONSUMER_KEY"));
		define("MB_SKEY", config::read("TENCENT_CONSUMER_SECRET"));
		define("MB_RETURN_FORMAT", 'json');
		define("MB_API_HOST", 'open.t.qq.com');
	}

	public function user_timeline() {
		$this->init();
		$this->init_db();
		$relation = new relation();
		$data = $relation->getRelation($this->user["id"], "tencent");
		$my = new MBApiClient(MB_AKEY, MB_SKEY, $data->token, $data->token_secret);
		$p = array(
		"f" => 0,
		"n" => 10,
		"t" => 0,
		"l" => $data->since_id,
		"type" => 1
		);
		$timeline = $my->getMyTweet($p);
		var_dump($timeline["data"]["info"]);
	}
	
	public function main_network() {
		$this->init();
		$this->init_db();
		$relation = new relation();
		$data = $relation->getRelation($this->user["id"], "tencent");
		$o = new MBApiClient(MB_AKEY, MB_SKEY, $data->token, $data->token_secret);
		$p = array(
		"f" => 0,
		"n" => 1,
		"t" => 0,
		"name" => $data->screen_name
		);
		$last_tweet = $o->getTimeline($p);
		
		$this->db->BeginTrans();
		$query = "update relations set is_main_network = 1, since_id = ? where user_id = ? and network = 'tencent'";
		$ok = $this->db->execute($query, array($last_tweet["data"]["info"][0]["timestamp"], $this->user["id"]));
		if($ok) {
			$query = "update relations set is_main_network = 0 where user_id = ? and network != 'tencent'";
			$ok = $this->db->execute($query, array($this->user["id"]));
		}
		$this->db->CommitTrans($ok);
		$this->redirect("dashboard");
	}

	public function callback() {
		$this->init();
		$token = session::get("tencentRequestToken");
		$o = new MBOpenTOAuth(MB_AKEY, MB_SKEY, $token['oauth_token'], $token['oauth_token_secret']);
		$accessToken = $o->getAccessToken($this->get('oauth_verifier')) ;

		$screen_name = $accessToken["name"];
		// save token and user_id

		$timeline = new MBApiClient(MB_AKEY, MB_SKEY, $accessToken['oauth_token'] , $accessToken['oauth_token_secret']);
		$p = array(
		"f" => 0,
		"n" => 1,
		"t" => 0,
		"name" => $screen_name
		);
		$last_tweet = $timeline->getTimeline($p);

		$this->init_db();
		$relation = new relation();
		$relation->user_id = $this->user["id"];
		$relation->network = "tencent";
		$relation->network_user_id = 0;
		$relation->screen_name = $screen_name;
		$relation->since_id = $last_tweet["data"]["info"][0]["timestamp"];
		$connectedNum = $relation->getConnectedNum();
		if($connectedNum) {
			$relation->is_main_network = 0;
		}else {
			$relation->is_main_network = 1;
		}
		$relation->token = $accessToken["oauth_token"];
		$relation->token_secret = $accessToken["oauth_token_secret"];
		$relation->created = time();
		$relation->updated = time();

		if(!$relation->Save()) {
			throw new Exception(__("exception when saving access token", true));
		}
		$this->redirect("dashboard");
	}

	public function connect() {
		$this->init();
		$o = new MBOpenTOAuth(MB_AKEY, MB_SKEY);
		$callback = config::read("base.uri") . "tencent/callback";
		$token = $o->getRequestToken($callback);
		session::set("tencentRequestToken", $token);
		$redirect_url = $o->getAuthorizeURL($token['oauth_token'], false, '');
		$this->redirect($redirect_url, false);
	}

	public function disconnect() {
		$this->init_db();
		$query = "delete from relations where user_id = ? and network = 'tencent'";
		$this->db->execute($query, array($this->user["id"]));
		$this->redirect("dashboard");
	}
}
