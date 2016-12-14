@@MAIN.DOWNLOAD.PHP@@

<?php
/* ==============================================================================================
ADVANCED FEATURES - Allows admins to perform aditional logic on the import.

$GLOBALS['REPLACE_LIST']
	Add additional search and replace items to step 2 for the serialize engine.  
	Place directly below $GLOBALS['REPLACE_LIST'] variable below your items
	EXAMPLE:
		array_push($GLOBALS['REPLACE_LIST'], array('search' => 'https://oldurl/',  'replace' => 'https://newurl/'));
		array_push($GLOBALS['REPLACE_LIST'], array('search' => 'ftps://oldurl/',   'replace' => 'ftps://newurl/'));
  ================================================================================================= */

//COMPARE VALUES
$GLOBALS['FW_CREATED']		= '%fwrite_created%';
$GLOBALS['FW_VERSION_DUP']	= '%fwrite_version_dup%';
$GLOBALS['FW_VERSION_WP']	= '%fwrite_version_wp%';
$GLOBALS['FW_VERSION_DB']	= '%fwrite_version_db%';
$GLOBALS['FW_VERSION_PHP']	= '%fwrite_version_php%';
$GLOBALS['FW_VERSION_OS']	= '%fwrite_version_os%';
//GENERAL
$GLOBALS['FW_SECUREON']			= '%fwrite_secure_on%';
$GLOBALS['FW_SECUREPASS']		= '%fwrite_secure_pass%';
$GLOBALS['FW_SKIPSCAN']			= '%fwrite_skipscan%'; 
$GLOBALS['FW_PACKAGE_NAME']		= '%fwrite_package_name%';
$GLOBALS['FW_PACKAGE_NOTES']	= '%fwrite_package_notes%';
$GLOBALS['FW_TABLEPREFIX']		= '%fwrite_wp_tableprefix%';
$GLOBALS['FW_BLOGNAME']			= '%fwrite_blogname%';
$GLOBALS['FW_USECDN']			= false;
$GLOBALS['HOST_NAME']			= strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
$GLOBALS['DBSAFE_BLOGNAME']		= preg_replace("/[^A-Za-z0-9?!]/",'',$GLOBALS['FW_BLOGNAME']); 

//STEP1
//BASIC DB
$GLOBALS['FW_DBHOST'] = '%fwrite_dbhost%';
$GLOBALS['FW_DBHOST'] = empty($GLOBALS['FW_DBHOST']) ? 'localhost' : $GLOBALS['FW_DBHOST'];
$GLOBALS['FW_DBNAME'] = '%fwrite_dbname%';
$GLOBALS['FW_DBUSER'] = '%fwrite_dbuser%';
$GLOBALS['FW_DBPASS'] = '%fwrite_dbpass%';
//CPANEL: Login
$GLOBALS['FW_CPNL_HOST']	= '%fwrite_cpnl_host%';
$GLOBALS['FW_CPNL_HOST']	= empty($GLOBALS['FW_CPNL_HOST']) ? "https://{$GLOBALS['HOST_NAME']}:2083" : $GLOBALS['FW_CPNL_HOST'];
$GLOBALS['FW_CPNL_USER']	= '%fwrite_cpnl_user%';
$GLOBALS['FW_CPNL_PASS']	= '%fwrite_cpnl_pass%';
$GLOBALS['FW_CPNL_ENABLE']	= '%fwrite_cpnl_enable%';
$GLOBALS['FW_CPNL_CONNECT'] = '%fwrite_cpnl_connect%';
//CPANEL: DB
$GLOBALS['FW_CPNL_DBACTION']	= '%fwrite_cpnl_dbaction%';
$GLOBALS['FW_CPNL_DBHOST']		= '%fwrite_cpnl_dbhost%';
$GLOBALS['FW_CPNL_DBHOST']		= empty($GLOBALS['FW_CPNL_DBHOST']) ? 'localhost' : $GLOBALS['FW_CPNL_DBHOST'];
$GLOBALS['FW_CPNL_DBNAME']		= strlen('%fwrite_cpnl_dbname%') ?  '%fwrite_cpnl_dbname%' : '';  
$GLOBALS['FW_CPNL_DBUSER']		= '%fwrite_cpnl_dbuser%';
//ADV OPTS
$GLOBALS['FW_SSL_ADMIN']	= '%fwrite_ssl_admin%';
$GLOBALS['FW_SSL_LOGIN']	= '%fwrite_ssl_login%';
$GLOBALS['FW_CACHE_WP']		= '%fwrite_cache_wp%';
$GLOBALS['FW_CACHE_PATH']	= '%fwrite_cache_path%';
$GLOBALS['FW_WPROOT']		= '%fwrite_wproot%';
$GLOBALS['FW_URL_OLD']		= '%fwrite_url_old%';
$GLOBALS['FW_URL_NEW']		= '%fwrite_url_new%';
$GLOBALS['MU_MODE']			= '%mu_mode%';
$GLOBALS['FW_OPTS_DELETE'] = json_decode("%fwrite_opts_delete%", true);

//DATABASE SETUP: all time in seconds	
@ini_set('mysql.connect_timeout', '5000');
$GLOBALS['DB_MAX_TIME'] = 5000;
$GLOBALS['DB_MAX_PACKETS'] = 268435456;
$GLOBALS['DBCHARSET_DEFAULT'] = 'utf8';
$GLOBALS['DBCOLLATE_DEFAULT'] = 'utf8_general_ci';
$GLOBALS['DB_RENAME_PREFIX'] = 'x-bak__';

//UPDATE TABLE SETTINGS
$GLOBALS['REPLACE_LIST'] = array();
$GLOBALS['DEBUG_JS'] = false;

//PHP SETUP: all time in seconds
@ini_set('memory_limit', '5000M');
@ini_set("max_execution_time", '5000');
@ini_set("max_input_time", '5000');
@ini_set('default_socket_timeout', '5000');
@set_time_limit(0);
/* ================================================================================================
  END ADVANCED FEATURES: Do not edit below here.
  =================================================================================================== */

//CONSTANTS
define("DUPLICATOR_PRO_INIT", 1); 
define("DUPLICATOR_PRO_SSDIR_NAME", 'wp-snapshots-dup-pro');  //This should match DUPLICATOR_PRO_SSDIR_NAME in duplicator.php

//SHARED POST PARMS
$_GET['debug']		= isset($_GET['debug']) ? true : false ;
$_GET['basic']		= isset($_GET['basic']) ? true : false ;
$_POST['view']		= isset($_POST['view']) ? $_POST['view'] : "secure";

//GLOBALS
$GLOBALS["VIEW"] = isset($_GET["view"]) ? $_GET["view"] : $_POST["view"];
$GLOBALS["SQL_FILE_NAME"] = "installer-data.sql";
$GLOBALS["LOG_FILE_NAME"] = "installer-log.txt";
$GLOBALS['SEPERATOR1'] = str_repeat("********", 10);
$GLOBALS['LOGGING'] = isset($_POST['logging']) ? $_POST['logging'] : 1;
$GLOBALS['CURRENT_ROOT_PATH'] = dirname(__FILE__);
$GLOBALS['CHOWN_ROOT_PATH'] = @chmod("{$GLOBALS['CURRENT_ROOT_PATH']}", 0755);
$GLOBALS['CHOWN_LOG_PATH'] = @chmod("{$GLOBALS['CURRENT_ROOT_PATH']}/{$GLOBALS['LOG_FILE_NAME']}", 0644);
$GLOBALS['URL_SSL'] = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? true : false;
$GLOBALS['URL_PATH'] = ($GLOBALS['URL_SSL']) ? "https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}" : "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";

//Restart log if user starts from step 1
if ($GLOBALS["VIEW"] == "deploy") 
{
    $GLOBALS['LOG_FILE_HANDLE'] = @fopen($GLOBALS['LOG_FILE_NAME'], "w+");
} else {
    $GLOBALS['LOG_FILE_HANDLE'] = @fopen($GLOBALS['LOG_FILE_NAME'], "a+");
}
?>
	
@@CLASS.LOGGING.PHP@@

@@CLASS.UTILS.PHP@@

@@CLASS.HTTP.PHP@@

@@CLASS.SERVER.PHP@@

@@CLASS.CONF.SRV.PHP@@

@@CLASS.CONF.WP.PHP@@

@@CLASS.ENGINE.PHP@@

@@CLASS.CPANEL.PHP@@

@@CLASS.DB.BASE.PHP@@

@@CLASS.DB.MYSQLI.PHP@@

@@CLASS.DB.PDO.PHP@@

@@INC.LIBS.PHP@@

@@ACTION.API.PHP@@

<?php

//Register API Engine
$API_SERVER = new DUPX_API_SERVER();
$API_SERVER->add_controller(new DUPX_cPanel());
$API_SERVER->listen(false);

if (isset($_POST['view_action'])) 
{
    switch ($_POST['view_action']) {
		case "deploy" : ?> 	@@ACTION.STEP1.PHP@@ 	<?php break;
        case "update" : ?>  @@ACTION.STEP2.PHP@@ 	<?php break;
    }
    @fclose($GLOBALS["LOG_FILE_HANDLE"]);
    die("");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow">
	<title>WordPress Duplicator</title>
	@@INC.LIBS.CSS.PHP@@	
	@@INC.CSS.PHP@@	
	@@INC.LIBS.JS.PHP@@
	@@INC.JS.PHP@@
</head>
<body>

<div id="content">
<!-- HEADER TEMPLATE: Common header on all steps -->
<table cellspacing="0" class="header-wizard">
	<tr>
		<td style="width:100%;">
			<div style="font-size:22px; padding:5px 0px 0px 0px">
				<!-- !!DO NOT CHANGE/EDIT OR REMOVE PRODUCT NAME!!
				If your interested in Private Label Rights please contact us at the URL below to discuss
				customizations to product labeling: https://snapcreek.com	-->
				&nbsp; Duplicator Pro - Installer
			</div>
		</td>
		<td style="white-space:nowrap; text-align:right">
			<?php if (! $_GET['basic']) :?>
				<select id="help-lnk">
				<option value="null"> - Resources -</option>
					<option value="?view=scan&basic">&raquo; System Scan</option>
					<option value="?view=help&basic">&raquo; Installer Help</option>
					<option value="https://snapcreek.com/support/docs/">&raquo; Online Help</option>
				</select>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php if (in_array($GLOBALS["VIEW"], array('help', 'secure', 'scan', 'api'))) :?>
				<div style="margin:4px 0px 10px 15px;">	</div>
			<?php else : ?>
				<?php
					$step1CSS = 'active-step';
					$step2CSS = '';
					$step3CSS = '';
					if ($GLOBALS["VIEW"] == 'update') {
						$step1CSS =  "complete-step";							
						$step2CSS =  "active-step";							
					}
					if ($GLOBALS["VIEW"] == "test") {
						$step1CSS =  "complete-step";							
						$step2CSS =  "complete-step";							
						$step3CSS =  "active-step";
					}
				?>
				<div id="wiz">
					<div id="wiz-steps">
						<div class="<?php echo $step1CSS; ?>"><a><span>1</span> Deploy</a></div>
						<div class="<?php echo $step2CSS; ?>"><a><span>2</span> Update </a></div>
						<div class="<?php echo $step3CSS; ?>"><a><span>3</span> Test </a></div>
					</div>
				</div>
			<?php endif; ?>
		</td>
		<td class="wiz-dupx-version"> 
			version:	<?php echo $GLOBALS['FW_VERSION_DUP'] ?> &raquo; 
			<a href="?view=help&basic" target="_blank">help</a>
		</td>
	</tr>
</table>	

<!-- =========================================
FORM DATA: User-Interface views -->
<div id="content-inner">
	<?php
		switch ($GLOBALS["VIEW"]) {
			case "api"    : ?>	@@VIEW.API.PHP@@	<?php break;
			case "secure" : ?>	@@VIEW.INIT1.PHP@@ 	<?php break;
			case "scan"   : ?>	@@VIEW.INIT2.PHP@@ 	<?php break;
			case "deploy" : ?>	@@VIEW.STEP1.PHP@@	<?php break;
			case "update" : ?>	@@VIEW.STEP2.PHP@@	<?php break;
			case "test"   : ?>	@@VIEW.STEP3.PHP@@	<?php break;
			case "help"   : ?>	@@VIEW.HELP.PHP@@	<?php break;
			default : 
				echo "Invalid View Requested";
		}
	?>
</div>
</div>
	
<?php if ($_GET['debug']) :?>
	<form id="form-debug" method="post" action="?debug=1">
		<input id="debug-view" type="hidden" name="view" />
		DEBUG MODE ON:	<hr size="1" />
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('secure')">[Password]</a> &nbsp; 
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('scan')">[Scanner]</a> &nbsp; 
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('deploy')">[Deploy - 1]</a> &nbsp; 
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('update')">[Update - 2]</a> &nbsp; 
		<a href="javascript:void(0)" onclick="DUPX.debugNavigate('test')">[Test - 3]</a> &nbsp; 
		<br/><br/>
		<a href="javascript:void(0)"  onclick="$('#debug-vars').toggle()"><b>PAGE VARIABLES</b></a>
		<pre id="debug-vars"><?php print_r($GLOBALS); ?></pre>
	</form>
	
	<script>
		DUPX.debugNavigate = function(view) 
		{
			$('#debug-view').val(view);
			$('#form-debug').submit();
		}
	</script>
<?php endif; ?>
	
	
<!-- Used for integrity check do not remove:
DUPLICATOR_PRO_INSTALLER_EOF -->
</body>
</html>