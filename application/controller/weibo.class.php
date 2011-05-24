<?php
class weibo extends app {
	public $authorization = true;

	public function __construct() {
		parent::__construct();
	}

	public function init() {
		require_once config::read("vendor") . "weibo/weibooauth.php";

		define("WB_AKEY", config::read("WEIBO_CONSUMER_KEY"));
		define("WB_SKEY", config::read("WEIBO_CONSUMER_SECRET"));
	}

	public function index() {
		//$this->init_smarty();
		//$this->smarty->display("php:welcome.tpl");
	}
	
	public function user_timeline() {
		$this->init();
    	$this->init_db();
		$relation = new relation();
		$data = $relation->getRelation($this->user["id"], "weibo");
		$o = new WeiboClient(WB_AKEY, WB_SKEY, $data->token, $data->token_secret);
		$user_timeline = $o->user_timeline(1, 10, $data->network_user_id, $data->since_id - 2);
		var_dump($user_timeline);
	}

	public function main_network() {
		$this->init();
		$this->init_db();
		$relation = new relation();
		$data = $relation->getRelation($this->user["id"], "weibo");
		$o = new WeiboClient(WB_AKEY, WB_SKEY, $data->token, $data->token_secret);
		$latest_timeline = $o->user_timeline(1, 1, $data->network_user_id);
		$this->db->BeginTrans();
		$query = "update relations set is_main_network = 1, since_id = ? where user_id = ? and network = 'weibo'";
		$ok = $this->db->execute($query, array($latest_timeline[0]["id"], $this->user["id"]));
		if($ok) {
			$query = "update relations set is_main_network = 0 where user_id = ? and network != 'weibo'";
			$ok = $this->db->execute($query, array($this->user["id"]));
		}
		$this->db->CommitTrans($ok);
		$this->redirect("dashboard");
	}
	
	public function callback() {
		$this->init();
		$token = session::get("weiboRequestToken");
		$o = new WeiboOAuth(WB_AKEY, WB_SKEY, $token['oauth_token'], $token['oauth_token_secret']);
		$accessToken = $o->getAccessToken($this->get('oauth_verifier')) ;

		// save token and user_id
		
		$verify = new WeiboClient(WB_AKEY, WB_SKEY, $accessToken['oauth_token'] , $accessToken['oauth_token_secret']);
		$profile = $verify->verify_credentials();
		
		$this->init_db();
		$relation = new relation();
		$relation->user_id = $this->user["id"];
		$relation->network = "weibo";
		$relation->network_user_id = $accessToken["user_id"];
		$relation->screen_name = $profile["screen_name"];
		$relation->since_id = $profile["status"]["id"];
		$relation->is_main_network = 1;
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
		$o = new WeiboOAuth(WB_AKEY, WB_SKEY);
		$token = $o->getRequestToken();
		session::set("weiboRequestToken", $token);
		$callback = config::read("base.uri") . "weibo/callback";
		$redirect_url = $o->getAuthorizeURL($token['oauth_token'], false, $callback);
		$this->redirect($redirect_url, false);
	}

	public function disconnect() {
		$this->init_db();
		$query = "delete from relations where user_id = ? and network = 'weibo'";
		$this->db->execute($query, array($this->user["id"]));
		$this->redirect("dashboard");
	}
}
