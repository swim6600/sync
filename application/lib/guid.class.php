<?php
class guid {
	
	private function getTimeMillis() {
		list($usec, $sec) = explode(' ', microtime());
		return $sec . substr($usec, $sec);
	}

	private function getLocalHost() {
		return strtolower($_ENV['COMPUTERNAME'] . '/' . $_SERVER['SERVER_ADDR']);
	}

	private function getLong() {
		$tmp = rand(0, 1) ? '-' : '';
        return $tmp . rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999) . rand(100, 999) . rand(100, 999);
	}

    public function get() {
        $valueBeforeMD5 = $this->getLocalHost() . ':' . $this->getTimeMillis() . ':' . $this->getLong();
        $valueAfterMD5 = md5($valueBeforeMD5);
		$raw = strtoupper($valueAfterMD5);
		return substr($raw, 0, 8) . '-' . substr($raw, 8, 4) . '-' . substr($raw, 12, 4) . '-' . substr($raw, 16, 4) . '-' . substr($raw, 20);
    }
}
