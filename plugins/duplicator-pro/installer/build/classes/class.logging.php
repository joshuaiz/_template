<?php
// Exit if accessed directly
if (!defined('DUPLICATOR_PRO_INIT')) {
	$_baseURL = "http://" . strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$_baseURL}");
	exit;
}

define('ERR_CONFIG_FOUND',		'A wp-config.php already exists in this location.  This error prevents users from accidentally overwriting the wrong directories contents.  You have two options: <ul><li>Empty this root directory except for the package and installer and try again.</li><li>Delete just the wp-config.php file and try again.  This will over-write all other files in the directory.</li></ul>');
define('ERR_ZIPNOTFOUND',		'The packaged zip file was not found. Be sure the zip package is in the same directory as the installer file.  If you are trying to reinstall a package you can copy the package from the "' . DUPLICATOR_PRO_SSDIR_NAME . '" directory back up to your root which is the same location as your installer file.');
define('ERR_SHELLEXEC_ZIPOPEN', 'Failed to extract archive using shell_exec unzip');
define('ERR_ZIPOPEN',			'Failed to open zip archive file. Please be sure the archive is completely downloaded before running the installer. Try to extract the archive manually to make sure the file is not corrupted.');
define('ERR_ZIPEXTRACTION',		'Errors extracting zip file.  Portions or part of the zip archive did not extract correctly.    Try to extract the archive manually with a client side program like unzip/win-zip/winrar to make sure the file is not corrupted.  If the file extracts correctly then there is an invalid file or directory that PHP is unable to extract.  This can happen if your moving from one operating system to another where certain naming conventions work on one environment and not another. <br/><br/> Workarounds: <br/> 1. Create a new package and be sure to exclude any directories that have name checks or files in them.   This warning will be displayed on the scan results under "Name Checks". <br/> 2. Manually extract the zip file with a client side program.  Then under advanced options in step 1 of the installer check the "Manual package extraction" option and perform the install.');
define('ERR_ZIPMANUAL',			'When choosing manual package extraction, the contents of the package must already be extracted and the wp-config.php and database.sql files must be present in the same directory as the installer file for the process to continue.  Please manually extract the package into the current directory before continuing in manual extraction mode.  Also validate that the wp-config.php and database.sql files are present.');
define('ERR_MAKELOG',			'PHP is having issues writing to the log file <b>' . DUPX_Util::set_safe_path($GLOBALS['CURRENT_ROOT_PATH']) . '\installer-log.txt .</b> In order for the Duplicator to proceed validate your owner/group and permission settings for PHP on this path. Try temporarily setting you permissions to 777 to see if the issue gets resolved.  If you are on a shared hosting environment please contact your hosting company and tell them you are getting errors writing files to the path above when using PHP.');
define('ERR_ZIPARCHIVE',		'In order to extract the archive.zip file the PHP ZipArchive module must be installed.  Please read the FAQ for more details.  You can still install this package but you will need to check the Manual package extraction checkbox found in the Advanced Options.  Please read the online user guide for details in performing a manual package extraction.');
define('ERR_MYSQLI_SUPPORT',	'In order to complete an install the mysqli extension for PHP is required. If you are on a hosted server please contact your host and request that mysqli be enabled.  For more information visit: http://php.net/manual/en/mysqli.installation.php');
define('ERR_DBCONNECT',			'DATABASE CONNECTION FAILED!<br/>');
define('ERR_DBCONNECT_CREATE',  'DATABASE CREATION FAILURE!<br/> Unable to create database "%s". Check to make sure the user has "Create" privileges.  Some hosts will restrict creation of a database only through the cpanel.  Try creating the database manually to proceed with installation.  If the database already exists select the action "Connect to Existing Database and Remove All Data" which will remove all existing tables.');
define('ERR_DBTRYCLEAN',		'DATABASE CREATION FAILURE!<br/> Unable to remove table from database "%s".<br/>  Please remove all tables from this database and try the installation again.  If no tables show in database, then Drop the database and re-create it.');
define('ERR_DBTRYRENAME',		'DATABASE CREATION FAILURE!<br/> Unable to rename a table from database "%s".<br/> Be sure the database user has RENAME privelages for this specific database on all tables.');
define('ERR_DBCREATE',			'The database "%s" does not exist.<br/>  Change the action to create in order to "Create New Database" to create the database.  Some hosting provider do not allow database creation except through their control panels. In this case you will need to login to your hosting providers control panel and create the database manually.  Please contact your hosting provider for further details on how to create the database.');
define('ERR_DBEMPTY',			'The database "%s" already exists and has "%s" tables.  When using the "Create New Database" action the database should not exist.  Select the action "Connect and Delete Any Existing Data" or "Connect and Backup Any Existing Data" to remove or backup the existing tables or choose a database name that does not already exist. Some hosting providers do not allow table removal or renaming from scripts.  In this case you will need to login to your hosting providers control panel and remove or rename the tables manually.  Please contact your hosting provider for further details.  Always backup all your data before proceeding!');
define('ERR_DBMANUAL',			'The database "%s" has "%s" tables. This does not look to be a valid WordPress database.  The base WordPress install has 12 tables.  Please validate that this database is indeed pre-populated with a valid WordPress database.  The "Manual SQL execution" mode requires that you have a valid WordPress database already installed.');
define('ERR_CPNL_API',			'The cPanel API had the following issues when trying to communicate on this host: <br/> %s');
define('ERR_TESTDB_UTF8',		'UTF8 Characters were detected as part of the database connection string. If your connection fails be sure  to update the MySQL my.ini configuration file setting to support UTF8 characters by enabling this option [character_set_server=utf8] and restarting the database server.');
define('ERR_TESTDB_VERSION',	'In order to avoid database incompatibility issues make sure the database versions between the build and installer servers are as close as possible. If the package was created on a newer database version than where it is being installed then you might run into issues.<br/><br/> It is best to make sure the server where the installer is running has the same or higher version number than where it was built.  If the major and minor version are the same or close for example [5.7 to 5.6], then the migration should work without issues.  A version pair of [5.7 to 5.1] is more likely to cause issues unless you have a very simple setup.  If the versions are too far apart work with your hosting provider to upgrade the MySQL engine on this server.');

/** * *****************************************************
 * DUPX_Log  
 * Class used to log information  */


class DUPX_Log {

    /** METHOD: LOG
     *  Used to write debug info to the text log file
     *  @param string $msg		Any text data
     *  @param int $loglevel	Log level
     */
    static public function Info($msg, $logging = 1) {
        if ($logging <= $GLOBALS["LOGGING"]) {
            @fwrite($GLOBALS["LOG_FILE_HANDLE"], "{$msg}\n");
        }
    }
	
	static public function Error($errorMessage) {
		if ($logging <= $GLOBALS["LOGGING"]) {
			$breaks = array("<br />","<br>","<br/>");  
			$msg = str_ireplace($breaks, "\r\n", $errorMessage);  
            @fwrite($GLOBALS["LOG_FILE_HANDLE"], "\nINSTALLER ERROR:\n{$msg}\n");
			@fclose($GLOBALS["LOG_FILE_HANDLE"]);
        }
        die("<div class='log-ui-error'><b style='color:#B80000;'>INSTALL ERROR!</b><br/>{$errorMessage}</div><br/>");
    }
}
?>
