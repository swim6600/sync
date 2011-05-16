<?php
class parseString {
	private $outSting;
	private $inString;
	private $detectOrder;

	public function __construct() {
		$this->detectOrder = "utf-8,gb2312,gbk,EUC-JP,HTML-ENTITIES";
	}

	public function set($inString = "") {
		$this->inString = $inString;
	}

	public function exe($inString = "") {
		if(empty($inString) && empty($this->inString)) {
			return false;
		}

		if($inString) {
			$this->inString = $inString;
		}

		mb_detect_order($this->detectOrder);
		$encoding = mb_detect_encoding($this->inString);

		$outSting = "";
		$mbLength = mb_strlen($this->inString, $encoding);
		for($i = 0; $i < $mbLength; $i ++) {
			$str = mb_substr($this->inString, $i, 1, $encoding);
			$unicode = self::utf8_unicode($str);
			if((hexdec("4e00") <= $unicode && $unicode <= hexdec("9fa5")) or preg_match("/[a-zA-Z0-9]{1}/i", $str)) {
				$outSting .= $str;
			}
		}
		return $this->outSting = $outSting;
	}

	public static function utf8_unicode($c) {
		switch(strlen($c)) {
		case 1:
			return ord($c);
		case 2:
			$n = (ord($c[0]) & 0x3f) << 6;
			$n += ord($c[1]) & 0x3f;
			return $n;
		case 3:
			$n = (ord($c[0]) & 0x1f) << 12;
			$n += (ord($c[1]) & 0x3f) << 6;
			$n += ord($c[2]) & 0x3f;
			return $n;
		case 4:
			$n = (ord($c[0]) & 0x0f) << 18;
			$n += (ord($c[1]) & 0x3f) << 12;
			$n += (ord($c[2]) & 0x3f) << 6;
			$n += ord($c[3]) & 0x3f;
			return $n;
		}
	}
}
