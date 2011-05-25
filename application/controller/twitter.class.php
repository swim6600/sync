<?php
class twitter extends app {
	public $authorization = true;

	public function __construct() {
		parent::__construct();
	}

	private function init() {
		define('CONSUMER_KEY', config::read("TWITTER_CONSUMER_KEY"));
		define('CONSUMER_SECRET', config::read("TWITTER_CONSUMER_SECRET"));
		require_once config::read("vendor") . "twitter/twitteroauth.php";
	}

	public function main_network() {
		$this->init();
		$this->init_db();
		$relation = new relation();
		$data = $relation->getRelation($this->user["id"], "twitter");
		$o = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $data->token, $data->token_secret);
		$profile = $o->get('account/verify_credentials');

		$this->db->BeginTrans();
		$query = "update relations set is_main_network = 1, since_id = ? where user_id = ? and network = 'twitter'";
		$ok = $this->db->execute($query, array($profile->status->id_str, $this->user["id"]));
		if($ok) {
			$query = "update relations set is_main_network = 0 where user_id = ? and network != 'twitter'";
			$ok = $this->db->execute($query, array($this->user["id"]));
		}
		$this->db->CommitTrans($ok);
		$this->redirect("dashboard");
	}
	
	public function callback() {
		$this->init();
		$token = session::get("twitterRequestToken");
		$o = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $token['oauth_token'], $token['oauth_token_secret']);
		$accessToken = $o->getAccessToken($this->get('oauth_verifier'));
		
		$verify = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $accessToken['oauth_token'], $accessToken['oauth_token_secret']);
		$profile = $verify->get('account/verify_credentials');
		
		$this->init_db();
		$relation = new relation();
		$relation->user_id = $this->user["id"];
		$relation->network = "twitter";
		$relation->network_user_id = $accessToken["user_id"];
		$relation->screen_name = $profile->screen_name;
		$relation->since_id = $profile->status->id_str;
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
		$o = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
		$callback = config::read("base.uri") . "twitter/callback";
		$token = $o->getRequestToken($callback);
		session::set("twitterRequestToken", $token);
		$redirect_url = $o->getAuthorizeURL($token['oauth_token']);
		$this->redirect($redirect_url, false);
	}

	public function disconnect() {
		$this->init_db();
		$query = "delete from relations where user_id = ? and network = 'twitter'";
		$this->db->execute($query, array($this->user["id"]));
		$this->redirect("dashboard");
	}
}
