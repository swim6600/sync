<?php
class dbManager {
	public $db;

	public function __construct() {
		require config::read('vendor') . 'adodb.inc.php';
	}

	public function getInstance($profile = 'production') {
		$object = config::read("DB.connection." . $profile);
		if($object === false) {
			$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
			$db = &ADONewConnection('mysqli');
			$db->createdatabase = true ;
			$result = $db->PConnect(
			config::read($profile . '.host'),
			config::read($profile . '.user'),
			config::read($profile . '.password'),
			config::read($profile . '.database'));
			if(empty($result)) {
				throw new exception("can not connect to $profile database");
			}
			$db->execute("set names utf8");
			config::write("DB.connection." . $profile, $db);
			return $db;
		}else {
			return $object;
		}
	}
}
