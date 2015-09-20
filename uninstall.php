#!/usr/local/bin/php-cgi -f
<?php
require_once('config.inc');

#http://stackoverflow.com/questions/7497733/how-can-use-php-to-check-if-a-directory-is-empty
function is_dir_empty($dir) {
  if (!is_readable($dir)) return NULL;
  $handle = opendir($dir);
  while (false !== ($entry = readdir($handle))) {
    if ($entry != "." && $entry != "..") {
      return FALSE;
    }
  }
  return TRUE;
}

#Remove the RSS settings.
$INSTALLPATH = $config['rss']['path'];
$WWWPATH = '/usr/local/www/';

if (isset($config['rss']) && isset($config['rss']['feeds'])) {
	#Get cron uuid from feeds
	foreach	($config['rss']['feeds']['rule'] as $feed) {
		if (isset($feed['schedule_uuid'])) {
			#

			echo 'Looking for job...', PHP_EOL;
			foreach ( $config['cron']['job'] as $key=> $job) {
				if ($job['uuid'] === $feed['schedule_uuid']) {
					echo "Removing cron...'{$job['desc']}' uuid=" . $feed['schedule_uuid'] . PHP_EOL;
					unset($config['cron']['job'][$key]);
				}
			}
		}
		
	}	
}

#Remove config
echo 'Removing RSS settings' . PHP_EOL;
unset($config['rss']);
write_config();

#Remove RC script
echo 'Looking for rc command...' . PHP_EOL;
if (is_array($config['rc'])) {
   if (!is_array($config['rc']['postinit'])) {
      echo 'No rc command to remove.' . PHP_EOL;
   } else {
      foreach ( $config['rc']['postinit'] as $key=> $cmd) {
         if (strpos($cmd, 'sys/rss_start.php') !== FALSE) {
            echo "Removing command..." . PHP_EOL;
            unset($config['rc']['postinit'][$key]);
            write_config();
         }
      }
   }
}

echo "Removing symbolic links...", PHP_EOL;
#Unlink RSS folder.
$linkfile = $WWWPATH.'ext/RSS';
if (file_exists($linkfile) and is_dir($linkfile)) {
   if(is_link($linkfile)) {
      echo "Removing symbolic link $linkfile", PHP_EOL;
      unlink($linkfile);
   } else {
      echo "$linkfile exists but is not symbolic link. Not removed.", PHP_EOL;
   }
}

#Unlink php files on www folder.
foreach (glob($WWWPATH . 'extension_rss_*{php,inc}', GLOB_BRACE) as $linkfile) {
   if (file_exists($linkfile)) {
      if(is_link($linkfile)) {
         echo "Removing symbolic link $linkfile", PHP_EOL;
         unlink($linkfile);
      } else {
         echo "$linkfile exists but is not symbolic link. Not removed.", PHP_EOL;
      }
   }
}

if (is_dir_empty($WWWPATH.'ext')) {
   echo "Removing 'ext' folder", PHP_EOL;
   exec('rm -rf ' . $WWWPATH .'ext');
}
echo PHP_EOL, "Done.", PHP_EOL, "For a complete uninstall, remove the files on the directory '$INSTALLPATH' running the command 'rm -rf $INSTALLPATH'" , PHP_EOL

?>