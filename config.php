#! /usr/local/bin/php-cgi -f
<?php


require_once('config.inc');
require_once('updatenotify.inc');

$config['rss']['rpcpath'] = "/transmission/rpc";
$config['rss']['pause_time'] = 100

$config['rss']['notifications'] = false;
$config['rss']['report_email'] = $config['system']['email']['from'];

$config['rss']['history_pagination'] = true;
$config['rss']['history_pagination_limit'] = 20;

$config['rss']['debug'] = "4";
$config['rss']['debuglog'] = "1";




#install path
#$config['rss']['path'] = "/mnt/Large/embebbed/RSS/";


		

write_config();


?>