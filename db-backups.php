<?php

###################
## CONFIGURATION ##
###################

// Total number of days that back-ups are kept.
// NOTE: This goes by filename only! Ex: database.2015-10-21.7am.sql.gz
$keepDays = 30;

// Location to store backed-up database files (in sql.gz format).
// NOTE: Do not include trailing slashes. Must be FULL path.
$backupDir = __DIR__ . "/backups";

// If messages should be output.
define("DEBUG", true);

// Timezone setting.
date_default_timezone_set("America/New_York");

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
foreach($databases as $curdb) {
	$file = "$backupDir/$curdb.$date.sql.gz";
	$command = "mysqldump $curdb | gzip > $file";
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
	$curstamp = strtotime($matches[1]);
	if ($curstamp < $oldStamp) {
		// Remove old file!
		$filePath = "$backupDir/$file";
		debug("Removed:  $filePath");
		unlink($filePath);
	}
}


function debug($text) {
	if (DEBUG) echo "$text\n";
}

