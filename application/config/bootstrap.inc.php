<?php
function exception_handler($e) {
	echo $e->getMessage();
}
set_exception_handler('exception_handler');

function __($var, $return) {
	if($return) {
		return $var;
	}else {
		echo $var;
	}
}

mb_detect_order("utf-8,gb2312,gbk,EUC-JP,HTML-ENTITIES");
ini_set('date.timezone','Asia/Chongqing');
