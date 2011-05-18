<?php
class app extends object {
	public $session;
	public $smarty;

	public function __construct() {
		session::start();
		$this->init_smarty();
	}

	protected function init_smarty() {
		require_once(config::read("vendor") . 'Smarty.class.php');

		$this->smarty = new Smarty();
		
		$this->smarty->debugging = false;
		$this->smarty->force_compile = true;
		
		$this->smarty->template_dir = config::read("root") . 'views/';
		$this->smarty->compile_dir = config::read("root") . 'temp/templates_c/';
		$this->smarty->config_dir = config::read("root") . 'config/';
		$this->smarty->cache_dir = config::read("root") . 'temp/cache/';
		$this->smarty->security = true;
		$this->smarty->allow_php_templates = true;
	}

	public function get($var = "id", $required = false) {
		if(is_array($var)){
			foreach($var as $v){
				if(isset($_GET[$v]) && $_GET[$v]!=''){
					return $_GET[$v];
				}
				if(isset($_POST[$v]) && $_POST[$v]!=''){
					return $_POST[$v];
				}
				if(isset($_REQUEST[$v]) && $_REQUEST[$v] !=''){
					return $_REQUEST[$v];
				}
			}
		}else {
			if(isset($_GET[$var]) && $_GET[$var]!=''){
				return $_GET[$var];
			}
			if(isset($_POST[$var]) && $_POST[$var]!=''){
				return $_POST[$var];
			}
			if(isset($_REQUEST[$var]) && $_REQUEST[$var] !=''){
				return $_REQUEST[$var];
			}
		}
		if($required){
			throw new exception(__('bad request, parameter was not passed correctly', true));
		}else {
			return false;
		}
	}
}
