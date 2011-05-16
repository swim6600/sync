<?php
class http {
    public $charset;

    public function __construct() {
        if(!function_exists('curl_init')) {
            throw new exception('curl is not enabled');
        }
    }

    public function get($url) {
        $return = $this->fetch($url);
        if(!$return) {
            return false;
        }
        if($this->charset == 'utf-8') {
            return $return;
        }else {
            return mb_convert_encoding($return, 'utf-8', $this->charset);
        }
    }

    private function fetch($url) {
        $url_parse = parse_url($url);
        $scheme = $url_parse['scheme'];
        $host = $url_parse['host'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $scheme . '://' . $host);
        curl_setopt($ch, CURLOPT_TIMEOUT, FILE_TIME_OUT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, FILE_TIME_OUT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $file_contents = curl_exec($ch);
        preg_match('/charset=[a-zA-Z]+-?[0-9]+/i', $file_contents, $match);
        if($match[0]) {
            list($tmp, $charset) = explode('=', trim($match[0]));
            $this->charset = strtolower($charset);
        }
        list($http_head, $http_body) = explode("\r\n\r\n", $file_contents, 2);
        list($http, $state_code, $http_other) = explode(" ", $http_head, 3);
        if($state_code == 301 or $state_code == 302) {
            $http_info = explode("\r\n", $http_head);
            foreach($http_info as $line) {
                list($key, $val) = explode(": ", $line);
                if(strtoupper($key) == "LOCATION") {
                    $new_img_url = $val;
                    break;
                }
            }
            curl_setopt($ch, CURLOPT_URL, $new_img_url);
            $file_contents = curl_exec($ch);
            list($http_head, $http_body) = explode("\r\n\r\n", $file_contents, 2);
            curl_close($ch);
            return $http_body;
        }else if($state_code == 200) {
            curl_close($ch);
            return $http_body;
        }else {
            curl_close($ch);
            return false;
        }
    }
}
