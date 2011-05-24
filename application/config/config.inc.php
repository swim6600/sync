<?php
require_once 'autoload.php';
config::write('base.uri', 'http://192.168.100.51/');
config::write('debug', '1');
config::write('index.controller', 'welcome');
config::write('auth.controller', 'dashboard');


config::write('TWITTER_CONSUMER_KEY', 'i68binlpzc6qEM7RjKGZQA');
config::write('TWITTER_CONSUMER_SECRET', 'Em7UsufAA5YFs7I55t3hNTfTlWVfRoJtrv4KmaeVGhc');
config::write('WEIBO_CONSUMER_KEY', '167761523');
config::write('WEIBO_CONSUMER_SECRET', 'bb36d8eb19a0818a0008702275170c23');
config::write('TENCENT_CONSUMER_KEY', '099be17c4b53404bbb55ee97500707d8');
config::write('TENCENT_CONSUMER_SECRET', '868db36d580d3ec4f82cde3d1eb5751c');

//config::write("cookie_expire", 3600 * 24 * 7);
config::write("cookie_expire", 3000);
config::write("cookie_path", "/");
config::write("cookie_domain", "sync.me");

config::write('production.host', 'localhost');
config::write('production.user', 'root');
config::write('production.password', 'root');
config::write('production.database', 'sync');

/**
config::write('test.host', 'sync.me');
config::write('test.user', 'root');
config::write('test.password', 'root');
config::write('test.database', 'sync');
**/

config::write('root', str_replace("\\", "/", dirname(__FILE__)) . '/../');
config::write('vendor', config::read('root') . 'vendor/');
config::write('log', config::read('root') . 'log/');

require_once 'bootstrap.inc.php';
