<?php
class object {
    protected $__i = 0;

    public function __construct() {
    }

    protected function tmp() {
        $args = func_get_args();
        ob_start();
        foreach($args as $arg) {
            var_dump($arg);
            echo "\n---------- spliter [ {$this->__i} ] ----------\n\n";
            $this->__i ++;
        }
        $tmp = ob_get_clean();
        file_put_contents('tmp', $tmp);
    }

	protected function touchFolder($folder) {
		if(!is_dir($folder)) {
			mkdir($folder, 0777, true);
            return true;
        } else {
            return false;
        }
	}

	protected function touchFile($file) {
		if(!is_file($file)) {
			touch($file);
		}
	}

	public function exeTime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
    }

    public function import($name) {
        require config::read('vendor') . $name . '.php';
    }
}
