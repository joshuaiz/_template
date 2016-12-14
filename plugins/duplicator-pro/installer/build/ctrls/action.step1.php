<?php
//-- START OF ACTION STEP1
// Exit if accessed directly
if (!defined('DUPLICATOR_PRO_INIT')) {
	$_baseURL = "http://" . strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$_baseURL}");
	exit;
}

//BASIC
if ($_POST['view_mode'] == 'basic') 
{
	$_POST['dbaction']	= isset($_POST['dbaction'])		? $_POST['dbaction']	 : 'create';
	$_POST['dbhost']	= isset($_POST['dbhost'])		? trim($_POST['dbhost']) : null;
	$_POST['dbname']	= isset($_POST['dbname'])		? trim($_POST['dbname']) : null;
	$_POST['dbuser']	= isset($_POST['dbuser'])		? trim($_POST['dbuser']) : null;
	$_POST['dbpass']	= isset($_POST['dbpass'])		? trim($_POST['dbpass']) : null;
	$_POST['dbport']	= isset($_POST['dbhost'])		? parse_url($_POST['dbhost'], PHP_URL_PORT) : 3306;
	$_POST['dbport']    = (! empty($_POST['dbport']))	? $_POST['dbport'] : 3306;
}
//CPANEL
else 
{
	$_POST['dbaction']	= isset($_POST['cpnl-dbaction'])		? $_POST['cpnl-dbaction']	  : 'create';	
	$_POST['dbhost']	= isset($_POST['cpnl-dbhost'])			? trim($_POST['cpnl-dbhost']) : null;
	$_POST['dbname']	= isset($_POST['cpnl-dbname-result'])	? trim($_POST['cpnl-dbname-result']) : null;
	$_POST['dbuser']	= isset($_POST['cpnl-dbuser-result'])	? trim($_POST['cpnl-dbuser-result']) : null;
	$_POST['dbpass']	= isset($_POST['cpnl-dbpass'])			? trim($_POST['cpnl-dbpass']) : null;
	$_POST['dbport']	= isset($_POST['cpnl-dbhost'])			? parse_url($_POST['cpnl-dbhost'], PHP_URL_PORT) : 3306;
	$_POST['dbport']    = (! empty($_POST['cpnl-dbport']))		? $_POST['cpnl-dbport'] : 3306;
	$cpnl_dbcreateuser  = isset($_POST['cpnl-dbuser-chk'])		? true : false;
}

//Advanced Opts
$_POST['package_name']	= isset($_POST['package_name']) ? $_POST['package_name'] : null;
$_POST['dbnbsp']		= (isset($_POST['dbnbsp']) && $_POST['dbnbsp'] == '1') ? true : false;
$_POST['set_file_perms']	= (isset($_POST['set_file_perms']) && $_POST['set_file_perms'] == '1') ? true : false;
$_POST['set_dir_perms']		= (isset($_POST['set_dir_perms']) && $_POST['set_dir_perms'] == '1') ? true : false;
$_POST['file_perms_value']	= isset($_POST['file_perms_value']) ? intval(('0' . $_POST['file_perms_value']), 8) : 0755;
$_POST['dir_perms_value']	= isset($_POST['dir_perms_value']) ? intval(('0' . $_POST['dir_perms_value']), 8) : 0644;
$_POST['ssl_admin']		= (isset($_POST['ssl_admin'])) ? true : false;
$_POST['ssl_login']		= (isset($_POST['ssl_login'])) ? true : false;
$_POST['cache_wp']		= (isset($_POST['cache_wp'])) ? true : false;
$_POST['cache_path']	= (isset($_POST['cache_path'])) ? true : false;
$_POST['zip_manual']	= (isset($_POST['zip_manual']) && $_POST['zip_manual'] == '1') ? true : false;
$_POST['dbcharset']		= isset($_POST['dbcharset']) ? trim($_POST['dbcharset']) : $GLOBALS['DBCHARSET_DEFAULT'];
$_POST['dbcollate']		= isset($_POST['dbcollate']) ? trim($_POST['dbcollate']) : $GLOBALS['DBCOLLATE_DEFAULT'];
$_POST['empty_schedule_storage']	= (isset($_POST['empty_schedule_storage']) && $_POST['empty_schedule_storage'] == '1') ? true : false;
$_POST['retain_config']		= (isset($_POST['retain_config']) && $_POST['retain_config'] == '1') ? true : false;

//LOGGING
$POST_LOG = $_POST;
unset($POST_LOG['dbpass']);
ksort($POST_LOG);

//ACTION VARS
$root_path		= DUPX_Util::set_safe_path($GLOBALS['CURRENT_ROOT_PATH']);
$package_path	= "{$root_path}/{$_POST['package_name']}";
$package_size	= @filesize($package_path);
$ajax1_start	= DUPX_Util::get_microtime();
$zip_support	= class_exists('ZipArchive') ? 'Enabled' : 'Not Enabled';
$on_php_53_plus = version_compare(PHP_VERSION, '5.3.2', '>=');
$JSON = array();
$JSON['pass'] = 0;


/* JSON RESPONSE: Most sites have warnings turned off by default, but if they're turned on the warnings
cause errors in the JSON data Here we hide the status so warning level is reset at it at the end*/
$ajax1_error_level = error_reporting();
error_reporting(E_ERROR);


//====================================================================================================
//DATABASE TEST CONNECTION
//====================================================================================================
if (isset($_GET['dbtest'])) 
{
	$html	  = "<div class='s1-dbconn-result-data'>";
	
	if ($cpnl_dbcreateuser) 	
	{
		$html	 .= "<div class='s1-dbonn-result-newuser'>Unable to test connection when creating a new database user, because the user does not exist. ";
		$html	 .= "Please continue with the setup by clicking the 'Run Deployment' button. ";
		$html	 .= "If there are any issues with creating the new database user a message will be displayed on the next screen.</div>";
	} 
	else 
	{
		$dbConn  = DUPX_Util::mysqli_connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass'], null, $_POST['dbport']);
		$dbConnError = (mysqli_connect_error()) ? 'Error: ' . mysqli_connect_error()  : 'Unable to Connect';

		if ($dbConn) 
		{
			$tstHost = "<div class='dup-pass'>Connected</div>";
			$dbFound = mysqli_select_db($dbConn, $_POST['dbname']);
			switch ($_POST['dbaction']) 
			{ 
				case "create" :
					$tstDB = ($dbFound) 
						? "<div class='dup-fail'>Database already exists.  Use the connect action or change the database name</div>" 
						: "<div class='dup-pass'>Create New Database '{$_POST['dbname']}'</div>";
				break;
				case "manual":
				case "rename":
				case "empty" :
					$tstDB = "<div class='dup-pass'>Database '{$_POST['dbname']}' found</div>" ;
					if (! $dbFound)  {
						$tstDB =  "<div class='dup-fail'>Unable to connect to database '{$_POST['dbname']}' with user '{$_POST['dbuser']}'.</div>";
						$tstDB .= "<small>The user will be assigned to the database on the next screen if not already assigned.</small>";
					}
				break;
			}
		} 
		else 
		{
			$tstHost  = "<div class='dup-fail'>{$dbConnError}</div>";
			$tstDB    = "<div class='dup-fail'>Unable to connect to Host</div>";
		}
		
		$dbvar_version = DUPX_Util::mysql_version($dbConn);
		$dbvar_version = ($dbvar_version == 0) ? 'no connection' : $dbvar_version;
		$dbvar_version_fail = version_compare($dbvar_version, $GLOBALS['FW_VERSION_DB']) < 0;
		$tstCompat = ($dbvar_version_fail) 
						? "<div class='dup-notice'>This Server: [{$dbvar_version}] -- Package Server: [{$GLOBALS['FW_VERSION_DB']}]</div>" 
						: "<div class='dup-pass'>This Server: [{$dbvar_version}] -- Package Server: [{$GLOBALS['FW_VERSION_DB']}]</div>";

		$html	 .= <<<DATA
		<small>
			Using Connection String:<br/>
			Server={$_POST['dbhost']}; Database={$_POST['dbname']}; Uid={$_POST['dbuser']}; Pwd={$_POST['dbpass']}; Port={$_POST['dbport']}
		</small>
		<table class='details'>
			<tr>
				<td>Host:</td>
				<td>{$tstHost}</td>
			</tr>
			<tr>
				<td>Database:</td>
				<td>{$tstDB}</td>
			</tr>	
			<tr>
				<td>Version:</td>
				<td>{$tstCompat}</td>
			</tr>		
		</table>
DATA;
		
		//--------------------------------
		//WARNING: DB has tables with create option
		if ($_POST['dbaction'] == 'create')
		{
			$tblcount = DUPX_Util::dbtable_count($dbConn, $_POST['dbname']);
			$html .= ($tblcount > 0) 
				? "<div class='warn-msg'><b>WARNING:</b> " . sprintf(ERR_DBEMPTY, $_POST['dbname'], $tblcount) . "</div>"
				: '';
		}
		
		//WARNNG: Input has utf8 data
		$dbConnItems = array($_POST['dbhost'], $_POST['dbuser'], $_POST['dbname'],$_POST['dbpass']);
		$dbUTF8_tst  = false;
		foreach ($dbConnItems as $value)
		{
			if (DUPX_Util::is_non_ascii($value)) {
				$dbUTF8_tst = true;
				break;
			}
		}
		$html .=  (! $dbConn && $dbUTF8_tst) 
			? "<div class='warn-msg'><b>WARNING:</b> " . ERR_TESTDB_UTF8 .  "</div>"	
			: '';

		//WARNING Version Incompat
		$html .=  ($dbvar_version_fail)  
			? "<div class='warn-msg'><b>NOTICE:</b> " . ERR_TESTDB_VERSION . "</div>" 
			: '';
	}
	
	$html .= '<div class="s1-dbconn-result-faq"><a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-100-q" target="_blank">Click here for connection issues?</a></div>';
	$html .= '<input type="button" onclick="$(this).parents().eq(1).hide(500)" value="Hide Message"><br/>';
	$html .= "</div>";
	die($html);
}

//===============================
//ARCHIVE ERROR MESSAGES
//ERR_MAKELOG
($GLOBALS['LOG_FILE_HANDLE'] != false) or DUPX_Log::Error(ERR_MAKELOG);

//ERR_ZIPMANUAL
if ($_POST['zip_manual']) {
	if (!file_exists("wp-config.php") && !file_exists("database.sql")) {
		DUPX_Log::Error(ERR_ZIPMANUAL);
	}
} else {
	//ERR_CONFIG_FOUND
	(!file_exists('wp-config.php')) 
		or DUPX_Log::Error(ERR_CONFIG_FOUND);
	//ERR_ZIPNOTFOUND
	(is_readable("{$package_path}")) 
		or DUPX_Log::Error(ERR_ZIPNOTFOUND);
}


//====================================================================================================
//CPANEL LOGIC: Only run during view 1 input form postback
//====================================================================================================
if ($_POST['view_mode'] == 'cpnl') 
{
	try {
		$json['message'] = '';
		$json['status']  = false;

		$CPNL = new DUPX_cPanel();
		$cpnlToken = $CPNL->create_token($_POST['cpnl-host'], $_POST['cpnl-user'], $_POST['cpnl-pass']);
		$cpnlHost  = $CPNL->connect($cpnlToken);
		$cpnllog = "";

		//CREATE NEW DB
		if ($_POST['dbaction'] == 'create') 
		{
			$result = $CPNL->create_db($cpnlToken, $_POST['dbname']);
			if ($result['status'] !== true) 
			{
				DUPX_Log::Info('#### CPANEL API: create_db ' . print_r($result['cpnl_api'], true));
				DUPX_Log::Error(sprintf(ERR_CPNL_API, $result['status']));
			} else {
				$cpnllog .= "CPANEL API:\tA new database was created\n";
			}
		}

		//CREATE DB USER
		if ($cpnl_dbcreateuser) 
		{
			$result = $CPNL->create_db_user($cpnlToken, $_POST['dbuser'], $_POST['dbpass']);
			if ($result['status'] !== true) 
			{
				DUPX_Log::Info('#### CPANEL API: create_db_user ' . print_r($result['cpnl_api'], true));
				DUPX_Log::Error(sprintf(ERR_CPNL_API, $result['status']));
			} else {
				$cpnllog .= "CPANEL API:\tA new database user was created\n";
			}
		}

		//ASSIGN USER TO DB IF NOT ASSIGNED
		$result = $CPNL->is_user_in_db($cpnlToken, $_POST['dbname'], $_POST['dbuser']);
		if (! $result['status']) 
		{
			$permissions = 'ALL';
			$result = $CPNL->assign_db_user($cpnlToken, $_POST['dbname'], $_POST['dbuser']);
			if ($result['status'] !== true) 
			{
				DUPX_Log::Info('#### CPANEL API: assign_db_user ' . print_r($result['cpnl_api'], true));
				DUPX_Log::Error(sprintf(ERR_CPNL_API, $result['status']));
			}	else {
				$cpnllog .= "CPANEL API:\tDatabase user was assigned to database";
			}
		}
	} 
	catch (Exception $ex) 
	{
		DUPX_Log::Error($ex);
	}
}


//===============================
//DB ERROR MESSAGES
//ERR_DBCONNECT
$dbh = DUPX_Util::mysqli_connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass']);
($dbh) or DUPX_Log::Error(ERR_DBCONNECT . mysqli_connect_error());
if ($_POST['dbaction'] == 'empty' || $_POST['dbaction'] == 'rename') {
	mysqli_select_db($dbh, $_POST['dbname']) or DUPX_Log::Error(sprintf(ERR_DBCREATE, $_POST['dbname']));
}
//ERROR: Create new database, existing tables found.
if ($_POST['dbaction'] == 'create') 
{
	$tblcount = DUPX_Util::dbtable_count($dbh, $_POST['dbname']);
	if ($tblcount > 0) {
		DUPX_Log::Error(sprintf(ERR_DBEMPTY, $_POST['dbname'], $tblcount));
	}
}

//ERROR: Manual Mode - Core WP has 12 tables. Check to make
//sure at least 10 are present otherwise present an error message
if ($_POST['dbaction'] == 'manual') 
{
	$tblcount = DUPX_Util::dbtable_count($dbh, $_POST['dbname']);
	if ($tblcount < 10) {
		DUPX_Log::Error(sprintf(ERR_DBMANUAL, $_POST['dbname'], $tblcount));
	}
}

DUPX_Log::Info("********************************************************************************");
DUPX_Log::Info('DUPLICATOR INSTALL-LOG');
DUPX_Log::Info('STEP1 START @ ' . @date('h:i:s'));
DUPX_Log::Info('NOTICE: Do NOT post to public sites or forums!!');
DUPX_Log::Info("********************************************************************************");
DUPX_Log::Info("VERSION:\t{$GLOBALS['FW_VERSION_DUP']}");
DUPX_Log::Info("PHP:\t\t" . phpversion() . ' | SAPI: ' . php_sapi_name());
DUPX_Log::Info("SERVER:\t\t{$_SERVER['SERVER_SOFTWARE']}");
DUPX_Log::Info("DOC ROOT:\t{$root_path}");
DUPX_Log::Info("DOC ROOT 755:\t" . var_export($GLOBALS['CHOWN_ROOT_PATH'], true));
DUPX_Log::Info("LOG FILE 644:\t" . var_export($GLOBALS['CHOWN_LOG_PATH'], true));
DUPX_Log::Info("BUILD NAME: \t{$GLOBALS['FW_PACKAGE_NAME']}");
DUPX_Log::Info("REQUEST URL:\t{$GLOBALS['URL_PATH']}");
DUPX_Log::Info($cpnllog);
$log  = "--------------------------------------\n";
$log .= "POST DATA\n";
$log .= "--------------------------------------\n";
$log .= print_r($POST_LOG, true);
DUPX_Log::Info($log, 2);


//====================================================================================================
//UNZIP & FILE SETUP - Extract the zip file and prep files
//====================================================================================================
$log  = "\n********************************************************************************\n";
$log .= "ARCHIVE SETUP\n";
$log .= "********************************************************************************\n";
$log .= "NAME:\t{$_POST['package_name']}\n";
$log .= "SIZE:\t" . DUPX_Util::readable_bytesize(@filesize($_POST['package_name'])) . "\n";
//$log .= "ZIP:\t{$zip_support} (ZipArchive Support)";
DUPX_Log::Info($log);

$zip_start = DUPX_Util::get_microtime();

if ($_POST['zip_manual']) 
{
	DUPX_Log::Info("\n** PACKAGE EXTRACTION IS IN MANUAL MODE ** \n");
} 
else 
{
	if ($GLOBALS['FW_PACKAGE_NAME'] != $_POST['package_name']) 
	{
		$log  = "\n--------------------------------------\n";
		$log .= "WARNING: This package set may be incompatible!  \nBelow is a summary of the package this installer was built with and the package used. \n";
		$log .= "To guarantee accuracy the installer and archive should match. For details see the online FAQs.";
		$log .= "\nCREATED WITH:\t{$GLOBALS['FW_PACKAGE_NAME']} \nPROCESSED WITH:\t{$_POST['package_name']}  \n";
		$log .= "--------------------------------------\n";
		DUPX_Log::Info($log);
	}
	
	if (! class_exists('ZipArchive')) 
	{
		DUPX_Log::Info("ERROR: Stopping install process.  Trying to extract without ZipArchive module installed.  Please use the 'Manual Package extraction' mode to extract zip file.");
		DUPX_Log::Error(ERR_ZIPARCHIVE);
	}
    
	$target = $root_path;
    $shell_exec_unzip_path = DUPX_Server::get_unzip_filepath();
    $archive_name = $_POST['package_name'];

    if($_POST['archive_engine'] == 'shellexec_unzip')
    {
        DUPX_Log::Info('ZIP: Shell Exec Unzip');
        $command .= "$shell_exec_unzip_path -o -qq \"$archive_name\" -d $target 2>&1";
        DUPX_Log::Info("executing $command");
        $stderr = shell_exec($command);
        $log .= "COMPLETE: Shell Exec Unzip Done.";

        if($stderr != '')
        {
			$zip_error_message = ERR_SHELLEXEC_ZIPOPEN . ": $stderr";
			$zip_error_message .= "<br/><br/><strong>To resolve error see <a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-022-q' target='_blank'>https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-022-q</a></strong>";
			DUPX_Log::Error($zip_error_message);
        }
    }
    else
    {
        DUPX_Log::Info('ZIP: Ziparchive Unzip');
        $zip = new ZipArchive();
        if ($zip->open($archive_name) === TRUE) {
            DUPX_Log::Info("EXTRACTING");
            if (! $zip->extractTo($target)) 
			{
				$zip_error_message = ERR_ZIPEXTRACTION;
				$zip_error_message .= "<br/><br/><strong>To resolve error see <a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-022-q' target='_blank'>https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-022-q</a></strong>";
			
                DUPX_Log::Error($zip_error_message);
            }
            $log  = print_r($zip, true);
            $close_response = $zip->close();
            $log .= "COMPLETE: " . var_export($close_response, true);
            DUPX_Log::Info($log);
        } else {
			$zip_error_message = ERR_ZIPOPEN;
			$zip_error_message .= "<br/><br/><strong>To resolve error see <a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-022-q' target='_blank'>https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-022-q</a></strong>";
			
            DUPX_Log::Error($zip_error_message);
        }
        $zip = null;
    }
}


//====================================================================================================
//FILE PERMISSIONS: Recursivly set file/directory permissions if requested
//====================================================================================================
if ($_POST['set_file_perms'] || $_POST['set_dir_perms']) 
{

	if(!class_exists('IgnorantRecursiveDirectoryIterator'))
	{
		// Skips past paths it can't read
		class IgnorantRecursiveDirectoryIterator extends RecursiveDirectoryIterator
		{ 
			function getChildren() 
			{ 
				try
				{ 
					return new IgnorantRecursiveDirectoryIterator($this->getPathname()); 
				} 
				catch(UnexpectedValueException $e) 
				{ 
					return new RecursiveArrayIterator(array()); 
				}
			}
		} 
	} 
	
	DUPX_Log::Info("Resetting permissions");
	$set_file_perms = $_POST['set_file_perms'];
	$set_dir_perms = $_POST['set_dir_perms'];
	$file_perms_value = $_POST['file_perms_value'] ? $_POST['file_perms_value'] : 0755;
	$dir_perms_value = $_POST['dir_perms_value'] ? $_POST['dir_perms_value'] : 0644;

		
	$objects = new RecursiveIteratorIterator(new IgnorantRecursiveDirectoryIterator($root_path), RecursiveIteratorIterator::SELF_FIRST);

	foreach($objects as $name => $object)
	{
   		if($set_file_perms && is_file($name))
		{            
			$retVal = @chmod($name, $file_perms_value);
			if(!$retVal)
			{
            	DUPX_Log::Info("Permissions setting failed");
			}
		}
		else if($set_dir_perms && is_dir($name))
		{            
			$retVal = @chmod($name, $dir_perms_value);
			if(!$retVal)
			{
            	DUPX_Log::Info("Permissions setting failed");
			}
		}
	}
}


//====================================================================================================
//CONFIGS: wp-config, .htaccess, web.confg
//====================================================================================================
DUPX_WPConfig::UpdateStep1();
DUPX_ServerConfig::Reset();


//====================================================================================================
//DATABASE ROUTINES
//====================================================================================================
if ($_POST['dbaction'] != 'manual')
{
	//===============================
	//CREATE DATABASE SCRIPT COPY
	if (filesize("{$root_path}/database.sql") > 100000000) {
		DUPX_Log::Info("\nWARNING: Database Script is larger than 100MB this may lead to PHP memory allocation issues on some budget hosts.");
	}
	
	@chmod("{$root_path}/database.sql", 0777);
	$sql_file = file_get_contents('database.sql', true);
	if ($sql_file == false || strlen($sql_file) < 10) {
		$sql_file = file_get_contents('installer-data.sql', true);
		if ($sql_file == false || strlen($sql_file) < 10) {
			DUPX_Log::Info("ERROR: Unable to read from the extracted database.sql file .\nValidate the permissions and/or group-owner rights on directory '{$root_path}'\n");
		}
	}

	//Complex Subject See: http://webcollab.sourceforge.net/unicode.html
	//Removes invalid space characters
	if ($_POST['dbnbsp']) {
		DUPX_Log::Info("ran fix non-breaking space characters\n");
		$sql_file = preg_replace('/\xC2\xA0/', ' ', $sql_file);
	}

	//Write new contents to install-data.sql
	@chmod($sql_result_file_path, 0777);
	file_put_contents($GLOBALS['SQL_FILE_NAME'], $sql_file);

	$sql_result_file_data = explode(";\n", $sql_file);
	$sql_result_file_length = count($sql_result_file_data);
	$sql_result_file_path = "{$root_path}/{$GLOBALS['SQL_FILE_NAME']}";
	$sql_file = null;

	if (!is_readable($sql_result_file_path) || filesize($sql_result_file_path) == 0) {
		DUPX_Log::Info("ERROR: Unable to create new sql file {$GLOBALS['SQL_FILE_NAME']}.\nValidate the permissions and/or group-owner rights on directory '{$root_path}' and file '{$GLOBALS['SQL_FILE_NAME']}'\n");
	}

	DUPX_Log::Info("\nUPDATED FILES:");
	DUPX_Log::Info("- SQL FILE:  '{$sql_result_file_path}'");
	DUPX_Log::Info("- WP-CONFIG: '{$root_path}/wp-config.php'");
	$zip_end = DUPX_Util::get_microtime();
	DUPX_Log::Info("\nARCHIVE RUNTIME: " . DUPX_Util::elapsed_time($zip_end, $zip_start));
	DUPX_Log::Info("\n");

	
	//===============================
	//RUN DATABASE SCRIPT
	@mysqli_query($dbh, "SET wait_timeout = {$GLOBALS['DB_MAX_TIME']}");
	@mysqli_query($dbh, "SET max_allowed_packet = {$GLOBALS['DB_MAX_PACKETS']}");
	DUPX_Util::mysql_set_charset($dbh, $_POST['dbcharset'], $_POST['dbcollate']);

	//Set defaults incase the variable could not be read
	$dbvar_maxtime = DUPX_Util::mysql_variable_value($dbh, 'wait_timeout');
	$dbvar_maxpacks = DUPX_Util::mysql_variable_value($dbh, 'max_allowed_packet');
	$dbvar_version = DUPX_Util::mysql_version($dbh);
	$dbvar_maxtime = is_null($dbvar_maxtime) ? 300 : $dbvar_maxtime;
	$dbvar_maxpacks = is_null($dbvar_maxpacks) ? 1048576 : $dbvar_maxpacks;
	$drop_tbl_log   = 0;
	$rename_tbl_log = 0;
	
	DUPX_Log::Info("{$GLOBALS['SEPERATOR1']}");
	DUPX_Log::Info('DATABASE-ROUTINES');
	DUPX_Log::Info("{$GLOBALS['SEPERATOR1']}");
	DUPX_Log::Info("--------------------------------------");
	DUPX_Log::Info("SERVER ENVIROMENT");
	DUPX_Log::Info("--------------------------------------");
	DUPX_Log::Info("MYSQL VERSION:\tThis Server: {$dbvar_version} -- Build Server: {$GLOBALS['FW_VERSION_DB']}");
	DUPX_Log::Info("TIMEOUT:\t{$dbvar_maxtime}");
	DUPX_Log::Info("MAXPACK:\t{$dbvar_maxpacks}");
	
	if (version_compare($dbvar_version, $GLOBALS['FW_VERSION_DB']) < 0) {
		DUPX_Log::Info("\nWARNING: This servers version [{$dbvar_version}] is less than the build version [{$GLOBALS['FW_VERSION_DB']}].  This may cause issues with the import process.\n");
	}

	//CREATE DB
	switch ($_POST['dbaction']) {
		case "create":	
			if ($_POST['view_mode'] == 'basic') 
			{
				mysqli_query($dbh, "CREATE DATABASE IF NOT EXISTS `{$_POST['dbname']}`");
			}
			mysqli_select_db($dbh, $_POST['dbname'])
				or DUPX_Log::Error(sprintf(ERR_DBCONNECT_CREATE, $_POST['dbname']));
			break;
		case "empty":	
			//DROP DB TABLES
			$sql = "SHOW TABLES FROM `{$_POST['dbname']}`";
			$found_tables = null;
			if ($result = mysqli_query($dbh, $sql)) {
				while ($row = mysqli_fetch_row($result)) {
					$found_tables[] = $row[0];
				}
				if (count($found_tables) > 0) {
					foreach ($found_tables as $table_name) {
						$sql = "DROP TABLE `{$_POST['dbname']}`.`{$table_name}`";
						if (!$result = mysqli_query($dbh, $sql)) {
							DUPX_Log::Error(sprintf(ERR_DBTRYCLEAN, "{$_POST['dbname']}.{$table_name}"));
						}
					}
					$drop_tbl_log = count($found_tables);
				}
			}
			break;
		case "rename" :
			//RENAME DB TABLES
			$sql = "SHOW TABLES FROM `{$_POST['dbname']}` WHERE  `Tables_in_{$_POST['dbname']}` NOT LIKE '{$GLOBALS['DB_RENAME_PREFIX']}%'";
			$found_tables = null;
			if ($result = mysqli_query($dbh, $sql)) {
				while ($row = mysqli_fetch_row($result)) {
					$found_tables[] = $row[0];
				}
				if (count($found_tables) > 0) {
					foreach ($found_tables as $table_name) {
						$sql = "RENAME TABLE `{$_POST['dbname']}`.`{$table_name}` TO  `{$_POST['dbname']}`.`{$GLOBALS['DB_RENAME_PREFIX']}{$table_name}`";
						if (!$result = mysqli_query($dbh, $sql)) {
							DUPX_Log::Error(sprintf(ERR_DBTRYRENAME, "{$_POST['dbname']}.{$table_name}"));
						}
					}
					$rename_tbl_log = count($found_tables);
				}
			}
			break;
	}
}


DUPX_Log::Info("--------------------------------------");
DUPX_Log::Info("DATABASE RESULTS");
DUPX_Log::Info("--------------------------------------");
	
if ($_POST['dbaction'] == 'manual')
{
	DUPX_Log::Info("\n** SQL EXECUTION IS IN MANUAL MODE **");
	DUPX_Log::Info("- No SQL script has been ran -");
	
	$JSON['table_count'] = 0;
	$JSON['table_rows']  = 0;
	$JSON['query_errs']  = 0;
} 
else
{
	//WRITE DATA
	$profile_start = DUPX_Util::get_microtime();
	$fcgi_buffer_pool = 5000;
	$fcgi_buffer_count = 0;
	$dbquery_rows = 0;
	$dbtable_rows = 1;
	$dbquery_errs = 0;
	$counter = 0;
	@mysqli_autocommit($dbh, false);
	while ($counter < $sql_result_file_length) 
	{

		$query_strlen = strlen(trim($sql_result_file_data[$counter]));
		if ($dbvar_maxpacks < $query_strlen) 
		{
			DUPX_Log::Info("**ERROR** Query size limit [length={$query_strlen}] [sql=" . substr($sql_result_file_data[$counter], 75) . "...]");
			$dbquery_errs++;
		} 
		elseif ($query_strlen > 0) 
		{
			@mysqli_free_result(@mysqli_query($dbh, ($sql_result_file_data[$counter])));
			$err = mysqli_error($dbh);
			//Check to make sure the connection is alive
			if (!empty($err)) 
			{
				if (!mysqli_ping($dbh)) 
				{
					mysqli_close($dbh);
					$dbh = DUPX_Util::mysqli_connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbname']);
					// Reset session setup
					@mysqli_query($dbh, "SET wait_timeout = {$GLOBALS['DB_MAX_TIME']}");
					DUPX_Util::mysql_set_charset($dbh, $_POST['dbcharset'], $_POST['dbcollate']);
				}
				DUPX_Log::Info("**ERROR** database error write '{$err}' - [sql=" . substr($sql_result_file_data[$counter], 0, 75) . "...]");
				$dbquery_errs++;

			//Buffer data to browser to keep connection open				
			} 
			else 
			{
				if ($fcgi_buffer_count++ > $fcgi_buffer_pool) {
					$fcgi_buffer_count = 0;
				}
				$dbquery_rows++;
			}
		}
		$counter++;
	}
	@mysqli_commit($dbh);
	@mysqli_autocommit($dbh, true);

	DUPX_Log::Info("ERRORS FOUND:\t{$dbquery_errs}");
	DUPX_Log::Info("DROPPED TABLES:\t{$drop_tbl_log}");
	DUPX_Log::Info("RENAMED TABLES:\t{$rename_tbl_log}");
	DUPX_Log::Info("QUERIES RAN:\t{$dbquery_rows}\n");

	$dbtable_count = 0;
	if ($result = mysqli_query($dbh, "SHOW TABLES")) {
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			$table_rows = DUPX_Util::table_row_count($dbh, $row[0]);
			$dbtable_rows += $table_rows;
			DUPX_Log::Info("{$row[0]}: ({$table_rows})");
			$dbtable_count++;
		}
		@mysqli_free_result($result);
	}

	if ($dbtable_count == 0) {
		DUPX_Log::Info("NOTICE: You may have to manually run the installer-data.sql to validate data input. Also check to make sure your installer file is correct and the
			table prefix '{$GLOBALS['FW_TABLEPREFIX']}' is correct for this particular version of WordPress. \n");
	}


	//DATA CLEANUP: Perform Transient Cache Cleanup
	//Remove all duplicator entries and record this one since this is a new install.
	$dbdelete_count = 0;
	$dbdelete_count1 = 0;
	$dbdelete_count2 = 0;
	$dbdelete_count3 = 0;
	$dbdelete_count4 = 0;

	@mysqli_query($dbh, "DELETE FROM `{$GLOBALS['FW_TABLEPREFIX']}duplicator_pro_packages`");
	$dbdelete_count1 = @mysqli_affected_rows($dbh);

	@mysqli_query($dbh, "DELETE FROM `{$GLOBALS['FW_TABLEPREFIX']}options` WHERE `option_name` LIKE ('_transient%') OR `option_name` LIKE ('_site_transient%')");
	$dbdelete_count2 = @mysqli_affected_rows($dbh);

	if (($_POST['empty_schedule_storage']) == true || ($on_php_53_plus == false)) 
	{    
		@mysqli_query($dbh, "DELETE FROM `{$GLOBALS['FW_TABLEPREFIX']}duplicator_pro_entities` WHERE `type` = 'DUP_PRO_Storage_Entity'");
		$dbdelete_count3 = @mysqli_affected_rows($dbh);

		@mysqli_query($dbh, "DELETE FROM `{$GLOBALS['FW_TABLEPREFIX']}duplicator_pro_entities` WHERE `type` = 'DUP_PRO_Schedule_Entity'");
		$dbdelete_count4 = @mysqli_affected_rows($dbh);
	}

	$dbdelete_count = (abs($dbdelete_count1) + abs($dbdelete_count2) + abs($dbdelete_count3) + abs($dbdelete_count4));

	DUPX_Log::Info("Removed '{$dbdelete_count}' cache/transient rows");
	//Reset Duplicator Options
	foreach ($GLOBALS['FW_OPTS_DELETE'] as $value) {
		mysqli_query($dbh, "DELETE FROM `{$GLOBALS['FW_TABLEPREFIX']}options` WHERE `option_name` = '{$value}'");	
	}

	@mysqli_close($dbh);

	$JSON['table_count'] = $dbtable_count;
	$JSON['table_rows']  = $dbtable_rows;
	$JSON['query_errs']  = $dbquery_errs;			
	$profile_end = DUPX_Util::get_microtime();
	DUPX_Log::Info("\nSECTION RUNTIME: " . DUPX_Util::elapsed_time($profile_end, $profile_start));
}


//FINAL RESULTS
$ajax1_end = DUPX_Util::get_microtime();
$ajax1_sum = DUPX_Util::elapsed_time($ajax1_end, $ajax1_start);
DUPX_Log::Info("\n{$GLOBALS['SEPERATOR1']}");
DUPX_Log::Info('STEP1 COMPLETE @ ' . @date('h:i:s') . " - TOTAL RUNTIME: {$ajax1_sum}");
DUPX_Log::Info("{$GLOBALS['SEPERATOR1']}");

$JSON['pass'] = 1;
echo json_encode($JSON);
error_reporting($ajax1_error_level);

die('');
//-- END OF ACTION STEP1
?>
