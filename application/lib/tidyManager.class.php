<?php
class tidyManager {
    public $tidy;
    public static $config;
    public static $charset;

    public function __construct() {
        if(!function_exists('tidy_parse_string')) {
            throw new exception('can not use tidy functions');
        }
        $this->tidy = new tidy();
        $this->tidy->config = array(
            'literal-attributes' => true,
            'drop-font-tags' => true,
            'clean' => true,
            'bare' => true,
            'drop-proprietary-attributes' => true
        );
        $this->tidy->charset = 'utf8';
    }
}
