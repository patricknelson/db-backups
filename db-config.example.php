<?php
/**
 * For a custom configuration, save this file to "db-config.php" and modify it to suit your needs.
 */

return [
	// Total number of days that back-ups are kept.
	// NOTE: This goes by filename only! Ex: database.2015-10-21.7am.sql.gz
	"keepDays" => 30,
	
	// Location to store backed-up database files (in sql.gz format).
	// NOTE: Do not include trailing slashes. Must be FULL path.
	"backupDir" => __DIR__ . "/backups",
	
	// Timezone setting.
	"timezone" => "America/New_York",
	
	// If messages should be output.
	"debug" => true,
];
