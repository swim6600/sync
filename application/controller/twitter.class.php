<?php
class twitter extends app {
	public $authorization = true;

	public function __construct() {
		parent::__construct();
	}

	private function init() {
		require_once config::read("vendor") . "oauth-php/OAuthStore.php";
		require_once config::read("vendor") . "oauth-php/OAuthRequester.php";

		// register at http://twitter.com/oauth_clients and fill these two
		//define("TWITTER_CONSUMER_KEY", config::read("TWITTER_CONSUMER_KEY"));
		//define("TWITTER_CONSUMER_SECRET", config::read("TWITTER_CONSUMER_SECRET"));
		define("TWITTER_CONSUMER_KEY", "167761523");
		define("TWITTER_CONSUMER_SECRET", "bb36d8eb19a0818a0008702275170c23");

		//http://api.t.sina.com.cn/oauth/request_token
		define("TWITTER_OAUTH_HOST", "http://api.t.sina.com.cn");
		define("TWITTER_REQUEST_TOKEN_URL", TWITTER_OAUTH_HOST . "/oauth/request_token");
		define("TWITTER_AUTHORIZE_URL", TWITTER_OAUTH_HOST . "/oauth/authorize");
		define("TWITTER_ACCESS_TOKEN_URL", TWITTER_OAUTH_HOST . "/oauth/access_token");
		define("TWITTER_PUBLIC_TIMELINE_API", TWITTER_OAUTH_HOST . "/statuses/public_timeline.json");
		define("TWITTER_UPDATE_STATUS_API", TWITTER_OAUTH_HOST . "/statuses/update.json");

		define('OAUTH_TMP_DIR', function_exists('sys_get_temp_dir') ? sys_get_temp_dir() : realpath($_ENV["TMP"]));

		// Twitter test
		$options = array('consumer_key' => TWITTER_CONSUMER_KEY, 'consumer_secret' => TWITTER_CONSUMER_SECRET);
		OAuthStore::instance("2Leg", $options);

		try {
			// Obtain a request object for the request we want to make
			$request = new OAuthRequester(TWITTER_REQUEST_TOKEN_URL, "POST");
			$curl_socksopt = array(
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_PROXY => "localhost:8080",
			CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5
			);
			$result = $request->doRequest(0);
			parse_str($result['body'], $params);

			// now make the request.
			//$request = new OAuthRequester(TWITTER_PUBLIC_TIMELINE_API, 'GET', $params);
			//$result = $request->doRequest(0, $curl_socksopt);
		}
		catch(OAuthException2 $e) {
			echo "Exception" . $e->getMessage();
		}
	}

	public function index() {
		$this->init();
		//$this->smarty->display("php:welcome.tpl");
	}

	public function callback() {
			
	}

	public function connect() {
		;
	}

	public function disconnect() {
			
	}
}
