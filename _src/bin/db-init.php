#!/usr/bin/env php
<?php

ini_set('memory_limit', -1);
chdir(__DIR__.'/../..');

require_once 'config/config.php';
require_once '_src/bin/lib/readpw.php';

$start_t = microtime(true);

$optargs = getopt('p:f:u:e:P');
$rootUser = $optargs['u'] ?? 'root';
$rootPass = $optargs['p'] ?? Input::password("MySQL password for user '$rootUser': ");
$rootAuth = isset($optargs['P']);
$dotenv = $optargs['e'] ?? '.env';
$sourceFile = realpath($optargs['f'] ?? '_src/db-content/latest/dump.sql.gz');
$sourceHead = '_src/db-content/db-head.sql';
$sourceTail = '_src/db-content/db-tail.sql';
$noerrs = '2>/dev/null';
echo "\n";

set_error_handler(function(int $errno, string $errstr) {
    ($errno != 0) || ($errno = -1);
    fprintf(STDERR, "Error: %d -- %s\n", $errno, $errstr);
    exit($errno);
}, E_WARNING|E_USER_ERROR|E_USER_WARNING|E_USER_NOTICE|E_USER_DEPRECATED);

###################################### CREATE DATABASE #################################################
file_exists($sourceFile) || trigger_error("SQL file not found: $sourceFile", E_USER_ERROR);

$tmpdir = sys_get_temp_dir().'/'.'db-init';
file_exists($tmpdir) || mkdir($tmpdir, 0775, true);
$dbfile = "$tmpdir/dump.sql";
!file_exists($dbfile) || unlink($dbfile);
!file_exists($sourceHead) || copy($sourceHead, $dbfile);

$statusCode = -1;
echo "Extracting archive ${dbfile}...";
system("gunzip -c $sourceFile >> $dbfile", $statusCode);
$statusCode == 0 || trigger_error("\nError occurred while decompressing file: $sourceFile");
echo " Done.\n";

!file_exists($sourceTail) || system("cat $sourceTail >> $dbfile", $statusCode);
$statusCode == 0 || trigger_error("Error occurred appending db-tail file: $sourceTail");

$conf = array_combine(
    ['dbhost','dbname','dbpass','dbuser'],
    array_values(array_intersect_key($_ENV, array_flip(['DB_HOST','DB_NAME','DB_PASSWORD','DB_USER'])))
);

$DBs = [$conf, $conf];
$DBs[1]['dbname'] .= '_test';

foreach ($DBs as $conf) {
    extract($conf);

    echo "\n##################### Initializing $dbname #####################\n";
    $rootDb = null;

    try {
        $rootDb = new PDO("mysql:host=$dbhost", $rootUser, $rootPass);
        $rootDb->beginTransaction();
        $rootDb->exec("DROP DATABASE IF EXISTS $dbname");
        $rootDb->exec("CREATE DATABASE $dbname");
        $rootDb->commit();
    }
    catch (PDOException $e) {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }

    $appDb = null;
    $userSpec = "'$dbuser'@'$dbhost'";

    try {
        $appDb = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    }
    catch (PDOException $e) {
        isset($rootDb) || ($rootDb = new PDO("mysql:host=$dbhost", $rootUser, $rootPass));
        $rootDb->beginTransaction();
        $rootDb->exec("CREATE USER IF NOT EXISTS $userSpec");
        $rootDb->exec("ALTER USER $userSpec IDENTIFIED WITH mysql_native_password BY '$dbpass'");

        if ($rootAuth) {
            $rootDb->exec("ALTER USER '$rootUser'@'%' IDENTIFIED WITH mysql_native_password BY '$rootPass';");
        }

        $rootDb->exec("GRANT ALL ON $dbname.* to $userSpec");
        $rootDb->exec("FLUSH PRIVILEGES");
        $rootDb->commit();
    }

    // try to connect as the app one last time before giving up
    $appDb || ($appDb = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass));
    echo "Permissions OK for $userSpec\n";

    echo "\n##################### Writing $dbname #####################\n";

    $fname = basename($dbfile);
    echo "Importing '$fname' to '$dbname'...\n";

    $errCode = -1; $lines = [];
    exec("mysql -v --default-auth=mysql_native_password -h $dbhost -p'$rootPass' -u $rootUser $dbname < '$dbfile'", $lines, $errCode);

    !$errCode || trigger_error("Error occurred while importing '$fname': ".implode("\n", array_slice($lines, 0, 100)), E_USER_ERROR);

    $lines = []; $errCode = 0;
    $q = "ALTER DATABASE $dbname DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci";
    exec("mysql -v --default-auth=mysql_native_password -h $dbhost -u $rootUser -p'$rootPass' --execute='$q' $dbname", $lines, $errCode);
    !$errCode || trigger_error("Error setting default collation '$fname': ".implode("\n", array_slice($lines, 0, 100)), E_USER_ERROR);

    echo "---------------------------------------------------------\n";
}

unlink($dbfile);

echo "\n############################## Done ##############################\n";
printf("Finished in %.3f seconds\n", microtime(true) - $start_t);
