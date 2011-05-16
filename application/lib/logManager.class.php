<?php
class logManager {

	public function __construct() {
		require_once config::read('vendor') . 'Log.php';
	}

	public function logManager() {
		return self;
	}

	public function getInstance($profile = "file") {
		$object = config::read("Log." . $profile);
		if($object === false) {
			$conf = array('mode' => 0600, 'timeFormat' => '%X %x');
			if($profile == "console") {
				$log = &Log::singleton('console', '', '', $conf);
			}else if($profile == "file") {
				$log = &Log::singleton('file', config::read('log') . 'log', '', $conf);
			}else {
				throw new exception('Log type is not specified');
			}
			config::write("Log." . $profile, $log);
			return $log;
		}else {
			return $object;
		}
	}
}
