<?php
//-- START OF ACTION STEP2
// Exit if accessed directly
if (! defined('DUPLICATOR_PRO_INIT')) {
	$_baseURL = "http://" . strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$_baseURL}");
	exit; 
}

/* JSON RESPONSE: Most sites have warnings turned off by default, but if they're turned on the warnings
cause errors in the JSON data Here we hide the status so warning level is reset at it at the end*/
$ajax2_error_level = error_reporting();
error_reporting(E_ERROR);

//====================================================================================================
//DATABASE UPDATES
//====================================================================================================

$ajax2_start = DUPX_Util::get_microtime();

//MYSQL CONNECTION
$dbh = DUPX_Util::mysqli_connect($_POST['dbhost'], $_POST['dbuser'], html_entity_decode($_POST['dbpass']), $_POST['dbname'], $_POST['dbport']);
$charset_server = @mysqli_character_set_name($dbh);
@mysqli_query($dbh, "SET wait_timeout = {$GLOBALS['DB_MAX_TIME']}");
DUPX_Util::mysql_set_charset($dbh, $_POST['dbcharset'], $_POST['dbcollate']);

//POST PARAMS
$_POST['blogname'] = mysqli_real_escape_string($dbh, $_POST['blogname']);
$_POST['postguid']		= isset($_POST['postguid']) && $_POST['postguid'] == 1 ? 1 : 0;
$_POST['fullsearch']	= isset($_POST['fullsearch']) && $_POST['fullsearch'] == 1 ? 1 : 0;
$_POST['retain_config']	= isset($_POST['retain_config']) && $_POST['retain_config'] == 1 ? 1 : 0;
$_POST['path_old']		= isset($_POST['path_old']) ? trim($_POST['path_old']) : null;
$_POST['path_new']		= isset($_POST['path_new']) ? trim($_POST['path_new']) : null;
$_POST['siteurl']		= isset($_POST['siteurl']) ? rtrim(trim($_POST['siteurl']), '/') : null;
$_POST['tables']		= isset($_POST['tables']) && is_array($_POST['tables']) ? array_map('stripcslashes', $_POST['tables']) : array();
$_POST['url_old']		= isset($_POST['url_old']) ? trim($_POST['url_old']) : null;
$_POST['url_new']		= isset($_POST['url_new']) ? rtrim(trim($_POST['url_new']), '/') : null;

//LOGGING
$POST_LOG = $_POST;
unset($POST_LOG['tables']);
unset($POST_LOG['plugins']);
unset($POST_LOG['dbpass']);
ksort($POST_LOG);

$date = @date('h:i:s');
$charset_client = @mysqli_character_set_name($dbh);

$log = <<<LOG
\n\n
********************************************************************************
DUPLICATOR INSTALL-LOG
STEP2 START @ {$date}
NOTICE: Do not post to public sites or forums
********************************************************************************
CHARSET SERVER:\t{$charset_server}
CHARSET CLIENT:\t {$charset_client} \n
LOG;
DUPX_Log::Info($log);

//Detailed logging
$log  = "--------------------------------------\n";
$log .= "POST DATA\n";
$log .= "--------------------------------------\n";
$log .= print_r($POST_LOG, true);		
$log .= "--------------------------------------\n";
$log .= "SCANNED TABLES\n";
$log .= "--------------------------------------\n";
$log .= (isset($_POST['tables']) && count($_POST['tables'] > 0)) 
		? print_r($_POST['tables'], true) 
		: 'No tables selected to update';
$log .= "--------------------------------------\n";
$log .= "KEEP PLUGINS ACTIVE\n";
$log .= "--------------------------------------\n";
$log .= (isset($_POST['plugins']) && count($_POST['plugins'] > 0)) 
		? print_r($_POST['plugins'], true) 
		: 'No plugins selected for activation';
DUPX_Log::Info($log, 2);

//UPDATE SETTINGS
$serial_plugin_list = (isset($_POST['plugins']) && count($_POST['plugins'] > 0)) ? @serialize($_POST['plugins']) : '';
mysqli_query($dbh, "UPDATE `{$GLOBALS['FW_TABLEPREFIX']}options` SET option_value = '{$_POST['blogname']}' WHERE option_name = 'blogname' ");
mysqli_query($dbh, "UPDATE `{$GLOBALS['FW_TABLEPREFIX']}options` SET option_value = '{$serial_plugin_list}'  WHERE option_name = 'active_plugins' ");

$log  = "--------------------------------------\n";
$log .= "SERIALIZER ENGINE\n";
$log .= "[*] scan every column\n";
$log .= "[~] scan only text columns\n";
$log .= "[^] no searchable columns\n";
$log .= "--------------------------------------";
DUPX_Log::Info($log);

$url_old_json = str_replace('"', "", json_encode($_POST['url_old']));
$url_new_json = str_replace('"', "", json_encode($_POST['url_new']));
$path_old_json = str_replace('"', "", json_encode($_POST['path_old']));
$path_new_json = str_replace('"', "", json_encode($_POST['path_new']));

array_push($GLOBALS['REPLACE_LIST'], 
		array('search' => $_POST['url_old'],  'replace' => $_POST['url_new']), 
		array('search' => $_POST['path_old'], 'replace' => $_POST['path_new']), 
		array('search' => $url_old_json,	  'replace' => $url_new_json), 
		array('search' => $path_old_json,	  'replace' => $path_new_json), 
		array('search' => urlencode($_POST['path_old']), 'replace' => urlencode($_POST['path_new'])), 
		array('search' => urlencode($_POST['url_old']),  'replace' => urlencode($_POST['url_new'])),		
		array('search' => rtrim(DUPX_Util::unset_safe_path($_POST['path_old']), '\\'), 'replace' => rtrim($_POST['path_new'], '/'))
);

/* New search replace logic */
// $mu_mode = 0 (no multisite); 1=(multisite subdomain); 2=(multisite subdirectory)
if($GLOBALS['MU_MODE'] == 1)
{
	$mu_newDomain = parse_url($_POST['url_new']);
	$mu_oldDomain = parse_url($_POST['url_old']);
	$mu_newDomainHost = $mu_newDomain['host'];
	$mu_oldDomainHost = $mu_oldDomain['host'];

	array_push($GLOBALS['REPLACE_LIST'],
			   array('search' => ('.' . $mu_oldDomainHost),  'replace' => ('.' . $mu_newDomainHost)));
}

//DUPX_Log::Info(print_r($_POST, true));
if(isset($_POST['search']))
{
	$search_count = count($_POST['search']);
	if($search_count > 0)
	{
		for($search_index = 0; $search_index < $search_count; $search_index++)
		{
			$search_for = $_POST['search'][$search_index];
			$replace_with = $_POST['replace'][$search_index]; 
			if(trim($search_for) != '')
			{
				array_push($GLOBALS['REPLACE_LIST'], array('search' => $search_for,  'replace' => $replace_with)); 
			}
		}
	}

}

//Remove trailing slashes
function _dupx_array_rtrim(&$value) {
    $value = rtrim($value, '\/');
}
array_walk_recursive($GLOBALS['REPLACE_LIST'], _dupx_array_rtrim);

@mysqli_autocommit($dbh, false);
$report = DUPX_UpdateEngine::load($dbh, $GLOBALS['REPLACE_LIST'], $_POST['tables'], $_POST['fullsearch']);
@mysqli_commit($dbh);
@mysqli_autocommit($dbh, true);


//BUILD JSON RESPONSE
$JSON = array();
$JSON['step1'] = json_decode(urldecode($_POST['json']));
$JSON['step2'] = $report;
$JSON['step2']['warn_all'] = 0;
$JSON['step2']['warnlist'] = array();

DUPX_UpdateEngine::log_stats($report);
DUPX_UpdateEngine::log_errors($report);

//Reset the postguid data
if ($_POST['postguid']) {
	mysqli_query($dbh, "UPDATE `{$GLOBALS['FW_TABLEPREFIX']}posts` SET guid = REPLACE(guid, '{$_POST['url_new']}', '{$_POST['url_old']}')");
	$update_guid = @mysqli_affected_rows($dbh) or 0;
	DUPX_Log::Info("Reverted '{$update_guid}' post guid columns back to '{$_POST['url_old']}'");
}

/* FINAL UPDATES: Must happen after the global replace to prevent double pathing
  http://xyz.com/abc01 will become http://xyz.com/abc0101  with trailing data */
mysqli_query($dbh, "UPDATE `{$GLOBALS['FW_TABLEPREFIX']}options` SET option_value = '{$_POST['url_new']}'  WHERE option_name = 'home' ");
mysqli_query($dbh, "UPDATE `{$GLOBALS['FW_TABLEPREFIX']}options` SET option_value = '{$_POST['siteurl']}'  WHERE option_name = 'siteurl' ");


//====================================================================================================
//FINAL CLEANUP
//====================================================================================================
DUPX_Log::Info("\n********************************************************************************");
DUPX_Log::Info('START FINAL CLEANUP: ' . @date('h:i:s'));
DUPX_Log::Info("********************************************************************************");

/*CREATE NEW USER LOGIC */
if (strlen($_POST['wp_username']) >= 4 && strlen($_POST['wp_password']) >= 6) {
	
	$newuser_check = mysqli_query($dbh, "SELECT COUNT(*) AS count FROM `{$GLOBALS['FW_TABLEPREFIX']}users` WHERE user_login = '{$_POST['wp_username']}' ");
	$newuser_row   = mysqli_fetch_row($newuser_check);
    $newuser_count = is_null($newuser_row) ? 0 : $newuser_row[0];
	
	if ($newuser_count == 0) {
	
		$newuser_datetime =	@date("Y-m-d H:i:s");
		$newuser_security = mysqli_real_escape_string($dbh, 'a:1:{s:13:"administrator";s:1:"1";}');

		$newuser_test1 = @mysqli_query($dbh, "INSERT INTO `{$GLOBALS['FW_TABLEPREFIX']}users` 
			(`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) 
			VALUES ('{$_POST['wp_username']}', MD5('{$_POST['wp_password']}'), '{$_POST['wp_username']}', '', '{$newuser_datetime}', '', '0', '{$_POST['wp_username']}')");

		$newuser_insert_id = mysqli_insert_id($dbh);

		$newuser_test2 = @mysqli_query($dbh, "INSERT INTO `{$GLOBALS['FW_TABLEPREFIX']}usermeta` 
				(`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', '{$GLOBALS['FW_TABLEPREFIX']}capabilities', '{$newuser_security}')");

		$newuser_test3 = @mysqli_query($dbh, "INSERT INTO `{$GLOBALS['FW_TABLEPREFIX']}usermeta` 
				(`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', '{$GLOBALS['FW_TABLEPREFIX']}user_level', '10')");
				
		//Misc Meta-Data Settings:
		@mysqli_query($dbh, "INSERT INTO `{$GLOBALS['FW_TABLEPREFIX']}usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', 'rich_editing', 'true')");
		@mysqli_query($dbh, "INSERT INTO `{$GLOBALS['FW_TABLEPREFIX']}usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', 'admin_color',  'fresh')");
		@mysqli_query($dbh, "INSERT INTO `{$GLOBALS['FW_TABLEPREFIX']}usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', 'nickname', '{$_POST['wp_username']}')");

		if ($newuser_test1 && $newuser_test2 && $newuser_test3) {
			DUPX_Log::Info("NEW WP-ADMIN USER: New username '{$_POST['wp_username']}' was created successfully \n ");
		} else {
			$newuser_warnmsg = "NEW WP-ADMIN USER: Failed to create the user '{$_POST['wp_username']}' \n ";
			$JSON['step2']['warnlist'][] = $newuser_warnmsg;
			DUPX_Log::Info($newuser_warnmsg);
		}			
	} 
	else {
		$newuser_warnmsg = "NEW WP-ADMIN USER: Username '{$_POST['wp_username']}' already exists in the database.  Unable to create new account \n";
		$JSON['step2']['warnlist'][] = $newuser_warnmsg;
		DUPX_Log::Info($newuser_warnmsg);
	}
}

/*MU Updates*/
$mu_newDomain = parse_url($_POST['url_new']);
$mu_oldDomain = parse_url($_POST['url_old']);
$mu_newDomainHost = $mu_newDomain['host'];
$mu_oldDomainHost = $mu_oldDomain['host'];
$mu_newUrlPath = parse_url($_POST['url_new'], PHP_URL_PATH);
$mu_oldUrlPath = parse_url($_POST['url_old'], PHP_URL_PATH);

if(empty($mu_newUrlPath) || ($mu_newUrlPath == '/'))
{
	$mu_newUrlPath = '/';
}
else
{
	$mu_newUrlPath = rtrim($mu_newUrlPath, '/') . '/';
}

if(empty($mu_oldUrlPath) || ($mu_oldUrlPath == '/'))
{
	$mu_oldUrlPath = '/';
}
else
{
	$mu_oldUrlPath = rtrim($mu_oldUrlPath, '/') . '/';
}


$mu_updates = @mysqli_query($dbh, "UPDATE `{$GLOBALS['FW_TABLEPREFIX']}blogs` SET domain = '{$mu_newDomainHost}' WHERE domain = '{$mu_oldDomainHost}'");
if ($mu_updates) {
	DUPX_Log::Info("Update MU table blogs: domain {$mu_newDomainHost} ");
} else {
	DUPX_Log::Info("UPDATE `{$GLOBALS['FW_TABLEPREFIX']}blogs` SET domain = '{$mu_newDomainHost}' WHERE domain = '{$mu_oldDomainHost}'");
}

/*UPDATE WP-CONFIG FILE */
$patterns = array("/('|\")WP_HOME.*?\)\s*;/", 
				  "/('|\")WP_SITEURL.*?\)\s*;/",
				  "/('|\")DOMAIN_CURRENT_SITE.*?\)\s*;/",
				  "/('|\")PATH_CURRENT_SITE.*?\)\s*;/");						
$replace  = array("'WP_HOME', '{$_POST['url_new']}');",
				  "'WP_SITEURL', '{$_POST['url_new']}');",
				  "'DOMAIN_CURRENT_SITE', '{$mu_newDomainHost}');",
				  "'PATH_CURRENT_SITE', '{$mu_newUrlPath}');");
$config_file = @file_get_contents('wp-config.php', true);

function is_constant($token) {
    return $token == T_CONSTANT_ENCAPSED_STRING || $token == T_STRING ||
        $token == T_LNUMBER || $token == T_DNUMBER;
}

function strip($value) {
    return preg_replace('!^([\'"])(.*)\1$!', '$2', $value);
}

// If token_get_all doesn't exist just skip the WP_CONTENT_DIR and WP_CONTENT_URL steps
if(function_exists('token_get_all'))
{
	$defines = array();
	$tokens = token_get_all($config_file);
	$token = reset($tokens);
	while ($token) {
		if (is_array($token)) {
			if ($token[0] == T_WHITESPACE || $token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) {
				// do nothing
			} else if ($token[0] == T_STRING && strtolower($token[1]) == 'define') {
				$state = 1;
			} else if ($state == 2 && is_constant($token[0])) {
				$key = $token[1];
				$state = 3;
			} else if ($state == 4 && is_constant($token[0])) {
				$value = $token[1];
				$state = 5;
			}
		} else {
			$symbol = trim($token);
			if ($symbol == '(' && $state == 1) {
				$state = 2;
			} else if ($symbol == ',' && $state == 3) {
				$state = 4;
			} else if ($symbol == ')' && $state == 5) {
				$defines[strip($key)] = strip($value);
				$state = 0;
			}
		}
		$token = next($tokens);
	}


	// Tweak WP_CONTENT_DIR and WP_CONTENT_URL
	if(array_key_exists('WP_CONTENT_DIR', $defines))
	{
		$new_content_dir =  str_replace($_POST['path_old'], $_POST['path_new'], DUPX_Util::set_safe_path($defines['WP_CONTENT_DIR']));

		array_push($patterns, "/('|\")WP_CONTENT_DIR.*?\)\s*;/");
		array_push($replace, "'WP_CONTENT_DIR', '{$new_content_dir}');");
	}

	if(array_key_exists('WP_CONTENT_URL', $defines))
	{
		$new_content_url =  str_replace($_POST['url_old'], $_POST['url_new'], $defines['WP_CONTENT_URL']);

		array_push($patterns, "/('|\")WP_CONTENT_URL.*?\)\s*;/");
		array_push($replace, "'WP_CONTENT_URL', '{$new_content_url}');");
	}
}
else
{
	DUPX_Log::Info("\ntoken_get_all doesn't exist so skipping WP_CONTENT_DIR and WP_CONTENT_URL processing.");
}

$config_file = preg_replace($patterns, $replace, $config_file);
file_put_contents('wp-config.php', $config_file);

//===============================
//NOTICES TESTS
//===============================
DUPX_Log::Info("\n--------------------------------------");
DUPX_Log::Info("NOTICES");
DUPX_Log::Info("--------------------------------------");
$config_vars = array('WPCACHEHOME', 'COOKIE_DOMAIN', 'WP_SITEURL', 'WP_HOME', 'WP_TEMP_DIR');
$config_found = DUPX_Util::string_has_value($config_vars, $config_file);

//Files
if ($config_found) {
	$msg = 'WP-CONFIG NOTICE: The wp-config.php has one or more of the following values set [' . implode(", ", $config_vars) . '].  Please validate these values are correct by opening the file and checking the values.';
	$JSON['step2']['warnlist'][] = $msg;
	DUPX_Log::Info($msg);
}

//Database
$result = @mysqli_query($dbh, "SELECT option_value FROM `{$GLOBALS['FW_TABLEPREFIX']}options` WHERE option_name IN ('upload_url_path','upload_path')");
if ($result) {
	while ($row = mysqli_fetch_row($result)) {
		if (strlen($row[0])) {
			$msg = "MEDIA SETTINGS NOTICE: The table '{$GLOBALS['FW_TABLEPREFIX']}options' has at least one the following values ['upload_url_path','upload_path'] set please validate settings. These settings can be changed in the wp-admin by going to /wp-admin/options.php'";
			$JSON['step2']['warnlist'][] = $msg;
			DUPX_Log::Info($msg);
			break;
		}
	}
}

if($GLOBALS['MU_MODE'] == 2)
{
	// _blogs update path column to replace /oldpath/ with /newpath/ */
	$result = @mysqli_query($dbh, "UPDATE `{$GLOBALS['FW_TABLEPREFIX']}blogs` SET path = CONCAT('$mu_newUrlPath', SUBSTRING(path, LENGTH('$mu_oldUrlPath') + 1))");

	if($result === false)
		DUPX_Log::Error("Update to blogs table failed\n" .  mysqli_error($dbh));
	
	if (empty($JSON['step2']['warnlist'])) 
		DUPX_Log::Info("No Warnings Found\n");
}

if(($GLOBALS['MU_MODE'] == 1) || ($GLOBALS['MU_MODE'] == 2))
{
	// _site update path column to replace /oldpath/ with /newpath/ */
	$result = @mysqli_query($dbh, "UPDATE `{$GLOBALS['FW_TABLEPREFIX']}site` SET path = CONCAT('$mu_newUrlPath', SUBSTRING(path, LENGTH('$mu_oldUrlPath') + 1)), domain = '$mu_newDomainHost'");

	if($result === false)
		DUPX_Log::Error("Update to site table failed\n".  mysqli_error($dbh));
	
	if (empty($JSON['step2']['warnlist'])) 
		DUPX_Log::Info("No Warnings Found\n");
}



if (empty($JSON['step2']['warnlist'])) {
	DUPX_Log::Info("No Warnings Found\n");
}

$JSON['step2']['warn_all'] = empty($JSON['step2']['warnlist']) ? 0 : count($JSON['step2']['warnlist']);


//CONFIG Setup
DUPX_ServerConfig::Setup($GLOBALS['MU_MODE'], $dbh);

mysqli_close($dbh);
@unlink('database.sql');

$ajax2_end = DUPX_Util::get_microtime();
$ajax2_sum = DUPX_Util::elapsed_time($ajax2_end, $ajax2_start);
DUPX_Log::Info("********************************************************************************");
DUPX_Log::Info('STEP 2 COMPLETE @ ' . @date('h:i:s') . " - TOTAL RUNTIME: {$ajax2_sum}");
DUPX_Log::Info("********************************************************************************");

$JSON['step2']['pass'] = 1;
error_reporting($ajax2_error_level);
die(json_encode($JSON));
//-- END OF ACTION STEP2
?>
