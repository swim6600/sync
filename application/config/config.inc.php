<?php
require_once 'autoload.php';
config::write('http', '/');
config::write('debug', '1');
config::write('index.controller', 'welcome');

config::write('production.host', 'sync.me');
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
