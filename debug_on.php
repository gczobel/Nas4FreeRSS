#! /usr/local/bin/php-cgi -f
<?php

require_once('config.inc');

$config['rss']['debug'] = "3";
$config['rss']['debuglog'] = "1";

write_config();


?>