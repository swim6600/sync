<?php
require_once '__autoload.php';
config::write('http', '/');
config::write('debug', '1');

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

config::write('max_page', 10);
config::write('max_alive_days', 3);

$createTableScript = "
CREATE TABLE `" . config::read('database') ."`.`data_%s` (
    `id` INT( 10 ) NOT NULL auto_increment,
    `bbs_id` INT( 10 ) NOT NULL,
    `subject` VARCHAR( 1024 ) NOT NULL ,
    `author` VARCHAR( 256 ) NOT NULL ,
    `reply` INT( 10 ) NOT NULL ,
    `view` INT( 10 ) NOT NULL ,
    `created` INT( 10 ) NOT NULL ,
    `updated` INT( 10 ) NOT NULL ,
    `category_id` INT( 6 ) NOT NULL ,
    `forum_id` INT( 10 ) NOT NULL ,
    `thread_id` INT( 10 ) NOT NULL ,
    `link` VARCHAR( 256 ) NOT NULL ,
    `author_link` VARCHAR( 256 ) NOT NULL ,
    PRIMARY KEY ( `id` ),
    KEY `bbs_id` (`bbs_id`),
    KEY `thread_id` (`thread_id`)
) ENGINE = MYISAM";
config::write('createTableScript', $createTableScript);

$createCatScript = "CREATE TABLE IF NOT EXISTS `cat` (
    `id` int(10) NOT NULL auto_increment,
    `cid` int(10) NOT NULL,
    `name` varchar(256) NOT NULL,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

$insertDataScript = "INSERT INTO `" . config::read('database') ."`.`data_%s` (
    `id` ,
    `bbs_id`,
    `subject` ,
    `author` ,
    `reply` ,
    `view` ,
    `created` ,
    `updated` ,
    `category_id` ,
    `forum_id` ,
    `thread_id` ,
    `link` ,
    `author_link`
)VALUES (
    NULL , '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
);";
config::write('insertDataScript', $insertDataScript);

$updateDataScript = "UPDATE `" . config::read('database') ."`.`data_%s` SET
    `subject` = '%s',
    `reply` = '%s',
    `view` = '%s',
    `updated` = '%s'
WHERE id = '%s';";
config::write('updateDataScript', $updateDataScript);
require_once 'bootstrap.inc.php';
