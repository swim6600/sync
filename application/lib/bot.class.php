<?php
class bot extends app{

	public function getNewSinceId() {
	}

	public function getTimeLine() {
	}

	public function updateStatus() {
	}

	public function expand($message) {
		// save message to the datebase if the message is larger than 140 length
		if(mb_strlen($message, "UTF-8") > 140) {
			$this->init_db();
			$model = new expand();
			$model->text = $message;
			$model->created = time();
			if(!$model->Save()) {
				throw new Exception(__("exception when saving message", true));
			}
			
			$key = $this->encode($model->id);
			$url = config::read("short.uri") . $key;
			$message = mb_substr($message, 0, 140 - strlen($url) - 1, "UTF-8") . " " . $url;
		}
		return $message;
	}
}