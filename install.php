#! /usr/local/bin/php-cgi -f
<?php
ini_set('html_errors', false);
ini_set('max_execution_time', 0);

require_once('config.inc');
require_once('updatenotify.inc');

$WWWPATH = '/usr/local/www/';
$INSTALLPATH = getcwd() . '/';

#echo 'Extracting RSS.tar.gz' . PHP_EOL;
#exec('tar -xzf RSS.tar.gz');

if (!is_dir($WWWPATH.'ext')) mkdir($WWWPATH.'ext');

# Clean up old files
exec("rm {$WWWPATH}extension_rss_*");
exec("rm -rf {$WWWPATH}ext/RSS");

foreach (glob('www/*.{php,inc}', GLOB_BRACE) as $filename) {
    echo "Linking {$filename} as {$WWWPATH}extension_" . basename($filename) . PHP_EOL;
    symlink($INSTALLPATH . $filename, $WWWPATH . 'extension_' . basename($filename));
}

echo 'Linking ext directory' . PHP_EOL;
symlink($INSTALLPATH . 'www/ext/RSS', $WWWPATH.'ext/RSS');

if (!is_array($config['rss'])) {
    echo 'Initializing configuration' . PHP_EOL;
    $config['rss'] = array(
        'feeds' => array('rule' => array()),
        'filters' => array('rule' => array())
    );
}

# Clearing old cookies
foreach ($config['rss']['feeds']['rule'] as &$feed)
	if (!isset($feed['cookie'])) $feed['cookie'] = '';

# Set install path
$config['rss']['path'] = $INSTALLPATH;

# Creating rc script to copy files on NAS boot.
$config['rc']['postinit']['cmd'] = '/usr/local/bin/php-cgi ' . $INSTALLPATH . 'sys/rss_start.php';

#Put default values
$config['rss']['rpcpath'] = "/transmission/rpc";
$config['rss']['pause_time'] = 100;

$config['rss']['notifications'] = false;
$config['rss']['report_email'] = $config['system']['email']['from'];

$config['rss']['history_pagination'] = true;
$config['rss']['history_pagination_limit'] = 20;

$config['rss']['debug'] = "4";

# Save configuration files.
echo 'Saving configuration changes' . PHP_EOL;
write_config();

echo "

Finished installing the RSS extension for transmission.  Please bear
in mind that this extension pulls most of transmission's settings
from Nas4Free' XML configuration file.  If you have modified the
transmission installation, you may need to modify
{$INSTALLPATH}www/ext/rss_functions.inc
to reflect your changes.  Look at the \$TRANSMISSION variable at the
top of the file.

";