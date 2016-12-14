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
class DUPX_ServerConfig
{
    /** 
     *  Clear .htaccess and web.config files and backup
     */
    public static function Reset() 
	{
		if($_POST['retain_config']) 
		{
			return;
		}
		//Apache
		@copy('.htaccess', '.htaccess.orig');
		@unlink('.htaccess');
		
		//IIS
		@copy('web.config', 'web.config.orig');
		@unlink('web.config');

		//.user.ini - For WordFence
		@copy('.user.ini', '.user.ini.orig');
		@unlink('.user.ini');
		
		DUPX_Log::Info("\nWEB SERVER CONFIGURATION FILE RESET:");
		DUPX_Log::Info("- Backup of .htaccess/web.config made to .orig");
		DUPX_Log::Info("- Reset of .htaccess/web.config files");
		file_put_contents('.htaccess', '#Reset by Duplicator Pro. See .orig for the orginal file ');
		@chmod('.htaccess', 0644);
    }
	
	
	/** 
	 * Updates the web server config files
     */
    public static function Setup($mu_mode, $dbh) 
	{
		
		if (! isset($_POST['url_new']) || $_POST['retain_config']) {
			return;
		}
		
		DUPX_Log::Info("\nWEB SERVER CONFIGURATION FILE BASIC SETUP:");
		$newdata  = parse_url($_POST['url_new']);
		$newpath  = DUPX_Util::add_slash(isset($newdata['path'])  ? $newdata['path'] : "");
		
		if($mu_mode == 0)
		{
			// no multisite
			$empty_htaccess = false;
			$query_result = @mysqli_query($dbh, "SELECT option_value FROM `{$GLOBALS['FW_TABLEPREFIX']}options` WHERE option_name = 'permalink_structure' ");
			if ($query_result) 
			{
				$row = @mysqli_fetch_array($query_result);
				if($row != null)
				{
					$permalink_structure = trim($row[0]);
					$empty_htaccess = empty($permalink_structure);
				}
			}

			if($empty_htaccess)
			{
				$tmp_htaccess = '';
			}
			else
			{		
				$tmp_htaccess = <<<HTACCESS
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . {$newpath}index.php [L]
</IfModule>
# END WordPress
HTACCESS;
			}
		}
		
		else if($mu_mode == 1)
		{
			// multisite subdomain
			$tmp_htaccess = <<<HTACCESS
# BEGIN WordPress
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]

# add a trailing slash to /wp-admin
RewriteRule ^wp-admin$ wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^(wp-(content|admin|includes).*) $1 [L]
RewriteRule ^(.*\.php)$ wp/$1 [L]
RewriteRule . index.php [L]
# END WordPress
HTACCESS;
		}
		else
		{
			// multisite subdirectory
			$tmp_htaccess = <<<HTACCESS
# BEGIN WordPress
RewriteEngine On
RewriteBase {$newpath}
RewriteRule ^index\.php$ - [L]

# add a trailing slash to /wp-admin
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]
# END WordPress
HTACCESS;
		}
		
		file_put_contents('.htaccess', $tmp_htaccess);
		@chmod('.htaccess', 0644);
		DUPX_Log::Info("created basic .htaccess file.  If using IIS web.config this process will need to be done manually.");
    }
}
?>
