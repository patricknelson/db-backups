<?php
/**
 * Quick and dirty script to perform mysql dumps which are retained temporarily.
 * See: https://github.com/patricknelson/db-backups
 *
 * @author	Patrick Nelson, pat@catchyour.com
 * @since	2015-10-22
 */


###################
## CONFIGURATION ##
###################

// Initialize configuration from db-config.php, starting with some sane defaults.
// Please see db-config.example.php for more information.
$config = [
	"keepDays" => 30,
	"backupDir" => __DIR__ . "/backups",
	"timezone" => "America/New_York",
	"debug" => true,
];
$configPath = __DIR__ . "/db-config.php";
if (file_exists($configPath)) $config = array_merge($config, include($configPath));

// Setup backup directory now if it doesn't already exist.
$backupDir = $config["backupDir"];
if (!is_dir($backupDir)) mkdir($backupDir, 0700, true);

// For easier-to-read string interpolation.
$keepDays = $config["keepDays"];

// Set timezone.
date_default_timezone_set($config["timezone"]);

#######################
## END CONFIGURATION ##
#######################


// Get list of all databases on server.
$result = `mysql -e 'show databases'`;
$result = trim($result);
$databases = explode("\n", $result);
array_shift($databases);

// Go through each database, dump and funnel data through gzip, saving to backup file with dated filename.
$date = date("Y-m-d.ga");
foreach($databases as $dbname) {
	$file = "$backupDir/$dbname.$date.sql.gz";
	$command = "mysqldump $dbname | gzip > $file";
	debug($command);
	$result = `$command`;
}



// Mask used to search for and remove old database files.
$fileMask = "#[-_a-z0-9]+\\.([0-9]{4}-[0-9]{2}-[0-9]{2})\\.([0-9]+[apm]+)\\.sql\\.gz#i";

// Go through list of database back-ups and remove files that are OLDER than (not equal to) the specified amount of days.
// NOTE: This goes by filename only!
$oldStamp = strtotime("-$keepDays days");
$oldStamp = mktime(0, 0, 0, date("n", $oldStamp), date("j", $oldStamp), date("Y", $oldStamp)); // Adjust stamp to normalized time of day (12AM).
$dh = opendir($backupDir);
while (($file = readdir($dh)) !== false) {
	if (!preg_match($fileMask, $file, $matches)) continue; // Skip file -- incorrect filename.
	debug($file);
	$stamp = strtotime($matches[1]);
	if ($stamp < $oldStamp) {
		// Remove old file!
		$filePath = "$backupDir/$file";
		debug("Removed:  $filePath");
		unlink($filePath);
	}
}


function debug($text) {
	global $config;
	if ($config["debug"]) echo "$text\n";
}

