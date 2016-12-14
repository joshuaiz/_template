<?php
// Exit if accessed directly
if (!defined('DUPLICATOR_PRO_INIT')) {
	$_baseURL = "http://" . strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$_baseURL}");
	exit;
}

/** 
 * Class used to update and edit web server configuration files
 * for both Apache and IIS files .htaccess and web.config  */
class DUPX_WPConfig
{
	/** 
	 * Updates the web server config files in Step 1  */
    public static function UpdateStep1() 
	{
		$root_path	= DUPX_Util::set_safe_path($GLOBALS['CURRENT_ROOT_PATH']);
		$wpconfig   = @file_get_contents('wp-config.php', true);

		$patterns = array(
			"/'DB_NAME',\s*'.*?'/",
			"/'DB_USER',\s*'.*?'/",
			"/'DB_PASSWORD',\s*'.*?'/",
			"/'DB_HOST',\s*'.*?'/");

		$replace = array(
			"'DB_NAME', "	  . '\'' . $_POST['dbname']				. '\'',
			"'DB_USER', "	  . '\'' . $_POST['dbuser']				. '\'',
			"'DB_PASSWORD', " . '\'' . DUPX_Util::preg_replacement_quote($_POST['dbpass']) . '\'',
			"'DB_HOST', "	  . '\'' . $_POST['dbhost']				. '\'');

		//SSL CHECKS
		if ($_POST['ssl_admin']) {
			if (! strstr($wpconfig, 'FORCE_SSL_ADMIN')) {
				$wpconfig = $wpconfig . PHP_EOL . "define('FORCE_SSL_ADMIN', true);";
			}
		} else {
			array_push($patterns, "/'FORCE_SSL_ADMIN',\s*true/");
			array_push($replace,  "'FORCE_SSL_ADMIN', false");
		}

		if ($_POST['ssl_login']) {
			if (! strstr($wpconfig, 'FORCE_SSL_LOGIN')) {
				$wpconfig = $wpconfig . PHP_EOL . "define('FORCE_SSL_LOGIN', true);";
			}
		} else {
			array_push($patterns, "/'FORCE_SSL_LOGIN',\s*true/");
			array_push($replace, "'FORCE_SSL_LOGIN', false");
		}

		//CACHE CHECKS
		if ($_POST['cache_wp']) {
			if (! strstr($wpconfig, 'WP_CACHE')) {
				$wpconfig = $wpconfig . PHP_EOL . "define('WP_CACHE', true);";
			}
		} else {
			array_push($patterns, "/'WP_CACHE',\s*true/");
			array_push($replace,  "'WP_CACHE', false");
		}
		if (! $_POST['cache_path']) {
			array_push($patterns, "/'WPCACHEHOME',\s*'.*?'/");
			array_push($replace,  "'WPCACHEHOME', ''");
		}

		$wpconfig = preg_replace($patterns, $replace, $wpconfig);
		$wpconfig_path = "{$root_path}/wp-config.php";
		if (! is_writable($wpconfig_path) ) 
		{
			chmod($wpconfig_path, 0644)
				? DUPX_Log::Info("File Permission Update: {$wpconfig_path} set to 0644")
				: DUPX_Log::Info("\nWARNING: Unable to update file permissions and write to {$wpconfig_path}.  Check that the wp-config.php is in the archive.zip and check with your host or administrator to enable PHP to write to the wp-config.php file.");
		}
		$wpconfig_updated = file_put_contents('wp-config.php', $wpconfig);
		if ($wpconfig_updated === false) {
			DUPX_Log::Info("\nWARNING: Unable to udpate {$wpconfig_path} file.  Be sure the file is present in your archive and PHP has permissions to update the file.");
		}
		$wpconfig = null;
	
	}
	
	/** 
	 * Updates the web server config files in Step 2 */
    public static function UpdateStep2() 
	{
		//Placeholder step 2 logic
	}
	
}
?>