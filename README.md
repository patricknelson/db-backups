# db-backups
Quick and dirty script to perform mysql dumps which are retained temporarily.

### Example Setup

**Note:** When running this script, it assumes that the user is executing this via shell and that they have a `.my.cnf` file setup to allow access to the `mysql` and `mysqldump` clients without a password.

To get started, modify the configuration in the top of the file to suit your needs, e.g.:

```php
// Total number of days that back-ups are kept.
// NOTE: This goes by filename only! Ex: database.2015-10-21.7am.sql.gz
$keepdays = 30;

// Location to store backed-up database files (in sql.gz format).
// NOTE: Do not include trailing slashes. Must be FULL path.
$backupdir = __DIR__ . "/backups";

// If messages should be output.
define("DEBUG", true);

// Timezone setting.
date_default_timezone_set("America/New_York");
```

In the `root` crontab, create the following entry:

```bash
0 12,18 * * * /usr/bin/php /root/db-backups.php &>/dev/null
```
