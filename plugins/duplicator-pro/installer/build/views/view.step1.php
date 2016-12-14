<?php
//-- START OF VIEW STEP 1
// Exit if accessed directly from wp-admin
if (!defined('DUPLICATOR_PRO_INIT'))
{
	$_baseURL = "http://" . strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$_baseURL}");
	exit;
}

$on_php_53_plus = version_compare(PHP_VERSION, '5.3.2', '>=');
$_POST['dbcharset'] = isset($_POST['dbcharset']) ? trim($_POST['dbcharset']) : $GLOBALS['DBCHARSET_DEFAULT'];
$_POST['dbcollate'] = isset($_POST['dbcollate']) ? trim($_POST['dbcollate']) : $GLOBALS['DBCOLLATE_DEFAULT'];
?>

<style>
	td#cpnl-prefix-dbname {width:10px}
	td#cpnl-prefix-dbuser {width:10px; white-space:normal}
</style>

<form id='s1-input-form' method="post" class="content-form"  data-parsley-validate="true" data-parsley-excluded="input[type=hidden], [disabled], :hidden">
	<input type="hidden" name="view" value="deploy" />
	<input type="hidden" name="view_action" value="deploy" />
	<input type="hidden" name="view_mode" id="s1-input-form-mode" />
	<input type="hidden" name="package_name"  value="<?php echo $GLOBALS['FW_PACKAGE_NAME'] ?>" />

	<div class="hdr-main">
		Step 1: Deploy Files &amp; Database
	</div>

	<div class="s1-btngrp">
		<input id="s1-basic-btn" type="button" value="Basic" class="active" onclick="DUPX.togglePanels('basic')" />
		<input id="s1-cpnl-btn" type="button" value="cPanel" class="in-active" onclick="DUPX.togglePanels('cpanel')" />
	</div>

	<!-- =========================================
	BASIC PANEL -->
	<div id="s1-basic-pane">
		<!-- MYSQL DATABASE -->
		<div class="hdr-sub">Database Setup</div>
		<table class="s1-opts">
			<tr>
				<td>Action</td>
				<td>
					<select name="dbaction" id="dbaction">
						<option value="create">Create New Database</option>
						<option value="empty">Connect and Delete Any Existing Data</option>
						<option value="rename">Connect and Backup Any Existing Data</option>
						<option value="manual">Manual SQL Execution (Advanced)</option>
					</select>
				</td>
			</tr>			
			<tr><td>Host</td><td><input type="text" name="dbhost" id="dbhost" required="true" value="<?php echo htmlspecialchars($GLOBALS['FW_DBHOST']); ?>" placeholder="localhost" /></td></tr>
			<tr>
				<td>Database</td>
				<td>
					<input type="text" name="dbname" id="dbname" required="true" value="<?php echo htmlspecialchars($GLOBALS['FW_DBNAME']); ?>"  placeholder="new or existing database name"  />
					<div class="s1-warning-emptydb">
						Warning: This action will remove <u>any and all</u> data from the database name above!
					</div>
					<div class="s1-warning-renamedb">
						Notice: This action will rename <u>all tables</u> currently in the database name above with the prefix '<?php echo $GLOBALS['DB_RENAME_PREFIX']; ?>'.
					</div>	
					<div class="s1-warning-manualdb">
						Notice: The 'Manual SQL execution' action will prevent the SQL script in the archive from running.  <br/> 
						The database name above should already be pre-populated with data which will be updated in the next step. <br/>
						No data in the database will be modified until after Step 2 runs.
					</div>	
				</td>
			</tr>
			<tr><td>User</td><td><input type="text" name="dbuser" id="dbuser" required="true" value="<?php echo htmlspecialchars($GLOBALS['FW_DBUSER']); ?>" placeholder="valid database username" /></td></tr>
			<tr><td>Password</td><td><input type="text" name="dbpass" id="dbpass" value="<?php echo htmlspecialchars($GLOBALS['FW_DBPASS']); ?>"  placeholder="valid database user password"   /></td></tr>
			<tr>
				<td colspan="2">
					<div class="s1-dbconn-area">
						<input type="button" onclick="DUPX.testDBConnect()" value="Test Connection" />
						<div class="s1-dbconn-result"></div>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<!-- =========================================
	C-PANEL PANEL -->		
	<div id="s1-cpnl-pane">
		<div class="hdr-sub">
			<span id="s1-cpnl-status-icon"></span> cPanel Login: <a id="s1-cpnl-status-msg" href="javascript:void(0)" onclick="$('#s1-cpnl-status-details').toggle()"></a>
		</div>

		<div id="s1-cpnl-area">
			<table class="s1-opts">			
				<tr>
					<td>Host</td>
					<td>
						<input type="text" name="cpnl-host" id="cpnl-host" required="true" value="<?php echo $GLOBALS['FW_CPNL_HOST']; ?>" placeholder="cPanel url" />
						<div id="cpnl-host-warn">
							Caution: The cPanel host name and URL in the browser address bar do not match, in some cases this may be intentional.  
							Please be sure this is the correct server for running the installer before running the deployment.
						</div>
					</td>
				</tr>
				<tr><td>Username</td><td><input type="text" name="cpnl-user" id="cpnl-user" required="true" data-parsley-type="alphanum" value="<?php echo htmlspecialchars($GLOBALS['FW_CPNL_USER']); ?>" placeholder="cPanel username" /></td></tr>
				<tr><td>Password</td><td><input type="text" name="cpnl-pass" id="cpnl-pass" value="<?php echo htmlspecialchars($GLOBALS['FW_CPNL_PASS']); ?>"  placeholder="cPanel password" required="true" /></td></tr>
			</table>

			<div id="s1-cpnl-connect">
				<input type="button" id="s1-cpnl-connect-btn" onclick="DUPX.cpnlConnect()" value="Connect" /> 
				<input type="button" id="s1-cpnl-change-btn" onclick="DUPX.cpnlToggleLogin()" value="Change" style="display:none" /> 
				<div id="s1-cpnl-status-details" style="display:none">	
					<div id="s1-cpnl-status-details-msg">
						Please click the connect button to connect to your cPanel.
					</div>
					<small style="font-style: italic">
						<a href="javascript:void()" onclick="$('#s1-cpnl-status-details').hide()">[Hide Message]</a> &nbsp; 
						<a href='https://snapcreek.com/wordpress-hosting/' target='_blank'>[cPanel Supported Hosts]</a>
					</small>
				</div>
			</div>
		</div>

		<!-- CPNL MYSQL DATABASE -->
		<div class="hdr-sub">Database Setup: <span id="s1-cpnl-db-opts-lbl">cPanel Login Required to enable</span> </div>
		<input type="hidden" name="cpnl-dbname-result" id="cpnl-dbname-result" />
		<input type="hidden" name="cpnl-dbuser-result" id="cpnl-dbuser-result" />
		<table id="s1-cpnl-db-opts" class="s1-opts">
			<tr>
				<td>Action</td>
				<td>
					<select name="cpnl-dbaction" id="cpnl-dbaction">
						<option value="create">Create New Database</option>
						<option value="empty">Connect and Delete Any Existing Data</option>
						<option value="rename">Connect and Backup Any Existing Data</option>
						<option value="manual">Manual SQL Execution (Advanced)</option>
					</select>
				</td>
			</tr>			
			<tr>
				<td>Host</td>
				<td><input type="text" name="cpnl-dbhost" id="cpnl-dbhost" required="true" value="<?php echo htmlspecialchars($GLOBALS['FW_CPNL_DBHOST']); ?>" placeholder="localhost" /></td>
			</tr>
			<tr>
				<td>Database</td>
				<td>
					<!-- EXISTING CPNL DB -->
					<div id="s1-cpnl-dbname-area1">
						<select name="cpnl-dbname-select" id="cpnl-dbname-select" required="true" data-parsley-pattern="^((?!-- Select Database --).)*$"></select>
						<div class="s1-warning-emptydb">
							Warning: This action will remove <u>any and all</u> data from the database selected above!
						</div>
					</div>
					<!-- NEW CPNL DB -->
					<div id="s1-cpnl-dbname-area2">
						<table>
							<tr>
								<td id="cpnl-prefix-dbname"></td>
								<td><input type="text" name="cpnl-dbname-txt" id="cpnl-dbname-txt" required="true" data-parsley-pattern="/^[a-zA-Z0-9-_]+$/" data-parsley-errors-container="#cpnl-dbname-txt-error" value="<?php echo htmlspecialchars($GLOBALS['FW_CPNL_DBNAME']); ?>"  placeholder="new or existing database name"  /></td>
							</tr>
						</table>
						<div id="cpnl-dbname-txt-error"></div>
					</div>
					<div class="s1-warning-renamedb">
						Notice: This action will rename <u>all tables</u> currently in the database selected above with the prefix '<?php echo $GLOBALS['DB_RENAME_PREFIX']; ?>'.
					</div>	
					<div class="s1-warning-manualdb">
						Notice: The 'Manual SQL execution' action will prevent the SQL script in the archive from running.  <br/> 
						The database name above should already be pre-populated with data which will be updated in the next step. <br/>
						No data in the database will be modified until after Step 2 runs.
					</div>							
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="checkbox" name="cpnl-dbuser-chk" id="cpnl-dbuser-chk" style="margin-left:5px" /> <label for="cpnl-dbuser-chk">Create New Database User</label> </td>
			</tr>			
			<tr>
				<td>User</td>
				<td>
					<div id="s1-cpnl-dbuser-area1">
						<select name="cpnl-dbuser-select" id="cpnl-dbuser-select" required="true" data-parsley-pattern="^((?!-- Select User --).)*$"></select>
					</div>
					<div id="s1-cpnl-dbuser-area2">
						<table>
							<tr>
								<td id="cpnl-prefix-dbuser"></td>
								<td><input type="text" name="cpnl-dbuser-txt" id="cpnl-dbuser-txt" required="true" data-parsley-pattern="/^[a-zA-Z0-9-_]+$/" data-parsley-errors-container="#cpnl-dbuser-txt-error" data-parsley-cpnluser="16" value="<?php echo htmlspecialchars($GLOBALS['FW_CPNL_DBUSER']); ?>" placeholder="valid database username" /></td>
							</tr>
						</table>
						<div id="cpnl-dbuser-txt-error"></div>
					</div>					
				</td>
			</tr>
			<tr><td>Password</td><td><input type="text" name="cpnl-dbpass" id="cpnl-dbpass" required="true" placeholder="valid database user password" /></td></tr>
			<tr>
				<td>Prefix</td>
				<td>
					<input type="checkbox" name="cpnl_ignore_prefix"  id="cpnl_ignore_prefix" value="1" onclick="DUPX.cpnlPrefixIgnore()" /> 
					<label for="cpnl_ignore_prefix">Ignore cPanel Prefix</label>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="s1-dbconn-area">
						<input type="button" onclick="DUPX.testDBConnect()" value="Test Connection" />
						<div class="s1-dbconn-result"></div>
					</div>
				</td>
			</tr>
			

		</table>
	</div>


	<!-- ADVANCED OPTS -->
	<?php
	$empty_schedule_display = ($on_php_53_plus) ? 'table-row' : 'none';
	$shell_exec_unzip_path = DUPX_Server::get_unzip_filepath();
	$shell_exec_unzip_available = ($shell_exec_unzip_path != null);
	?>
	<a href="javascript:void(0)" onclick="$('#s1-adv-opts').toggle(250)"><b>Advanced Options...</b></a>
	<div id='s1-adv-opts' style="display:none">
		<table class="s1-opts">
			<tr><td><input type="checkbox" name="zip_manual"  id="zip_manual" value="1" /> <label for="zip_manual">Manual package extraction</label></td></tr>
			<tr style="display: <?php echo $empty_schedule_display; ?>">
				<td>
					<input type="checkbox" name="empty_schedule_storage" id="empty_schedule_storage" value="1" checked /> 
					<label for="empty_schedule_storage">Remove schedules and storage endpoints</label>
				</td>
			</tr>
			<tr><td><input type="checkbox" name="dbnbsp" id="dbnbsp" value="1" /> <label for="dbnbsp">Fix non-breaking space characters</label></td></tr>
			<tr><td><input type="checkbox" name="retain_config" id="retain_config" value="1" /> <label for="retain_config">Retain original .htaccess, .user.ini and web.config</label></td></tr>
		</table>
		
		<table class="s1-opts s1-advopts">
			<tr>
				<td>Logging</td>
				<td colspan="2">
					<input type="radio" name="logging" id="logging-light" value="1" checked="true"> <label for="logging-light">Light</label> &nbsp; 
					<input type="radio" name="logging" id="logging-detailed" value="2"> <label for="logging-detailed">Detailed</label> &nbsp; 
					<input type="radio" name="logging" id="logging-debug" value="3"> <label for="logging-debug">Debug</label>
				</td>
			</tr>
			<tr>
				<td>Archive Engine</td>
				<td style="width:125px">
					<input type="radio" id='archive-engine-zip' name="archive_engine" value="ziparchive" <?php echo (($shell_exec_unzip_available) ? '' : 'checked') ?> />
					<label for='archive-engine-zip'>ZipArchive</label> 
				</td>				
				<td>
					<input type="radio" id='archive-engine-shell' name="archive_engine" value="shellexec_unzip" <?php echo (($shell_exec_unzip_available) ? 'checked' : 'disabled title="Not available"') ?> />
					<label for='archive-engine-shell' <?php echo ($shell_exec_unzip_available) ? '' : 'title="Not available"' ?>>ShellExec Unzip</label> 
				</td>
			</tr>				
			<tr>
				<td>Permissions</td>
				<td style="width:125px">
					<input type="checkbox" name="set_file_perms" id="set_file_perms" value="1" onclick="jQuery('#file_perms_value').prop('disabled', !jQuery(this).is(':checked'));"/> 
					<label for="set_file_perms">All Files</label><input name="file_perms_value" id="file_perms_value" style="width:30px; margin-left:7px;" value="644" disabled>
				</td>				
				<td>
					<input type="checkbox" name="set_dir_perms" id="set_dir_perms" value="1" onclick="jQuery('#dir_perms_value').prop('disabled', !jQuery(this).is(':checked'));"/> 
					<label for="set_dir_perms">All Directories</label><input name="dir_perms_value" id="dir_perms_value" style="width:30px; margin-left:7px;" value="755" disabled>
				</td>
			</tr>
			<tr>
				<td>Config Cache</td>
				<td><input type="checkbox" name="cache_wp" id="cache_wp" <?php echo ($GLOBALS['FW_CACHE_WP']) ? "checked='checked'" : ""; ?> /> <label for="cache_wp">Keep Enabled</label></td>
				<td><input type="checkbox" name="cache_path" id="cache_path" <?php echo ($GLOBALS['FW_CACHE_PATH']) ? "checked='checked'" : ""; ?> /> <label for="cache_path">Keep Home Path</label></td>
			</tr>	
			<tr>
				<td>Config SSL</td>
				<td><input type="checkbox" name="ssl_admin" id="ssl_admin" <?php echo ($GLOBALS['FW_SSL_ADMIN']) ? "checked='checked'" : ""; ?> /> <label for="ssl_admin">Enforce on Admin</label></td>
				<td><input type="checkbox" name="ssl_login" id="ssl_login" <?php echo ($GLOBALS['FW_SSL_LOGIN']) ? "checked='checked'" : ""; ?> /> <label for="ssl_login">Enforce on Login</label></td>
			</tr>
		</table>

		<table class="s1-opts s1-advopts">
			<tr><td>MySQL Charset</td><td><input type="text" name="dbcharset" id="dbcharset" value="<?php echo $_POST['dbcharset'] ?>" /> </td></tr>
			<tr><td>MySQL Collation </td><td><input type="text" name="dbcollate" id="dbcollate" value="<?php echo $_POST['dbcollate'] ?>" /> </tr>
		</table>
		<small><i>For an overview of these settings see the <a href="?view=help&basic" target="_blank">help page</a></i></small>
		<br/>
	</div>

	<!-- NOTICES  -->
	<div id='s1-warning-area'>
		<a href="javascript:void(0)" onclick="$('#s1-warning-msg').toggle(250)"><b>Warnings and Notices...</b></a>
		<div id="s1-warning-msg" style="display:none">
			<b>WARNINGS &amp; NOTICES</b> <br/><br/>

			<b>Disclaimer:</b> 
			This plugin require above average technical knowledge. Please use it at your own risk and always back up your database and files beforehand using another backup
			system besides the Duplicator. If you're not sure about how to use this tool then please enlist the guidance of a technical professional.  <u>Always</u> test 
			this installer in a sandbox environment before trying to deploy into a production setting.
			<br/><br/>

			<b>Database:</b>
			Do not connect to an existing database unless you are 100% sure you want to remove all of it's data. Connecting to a database that already exists will permanently
			DELETE all data in that database. This tool is designed to populate and fill a database with NEW data from a duplicated database using the SQL script in the 
			package name above.
			<br/><br/>

			<b>Setup:</b>
			Only the archive and installer file should be in the install directory, unless you have manually extracted the package and checked the 
			'Manual Package Extraction' checkbox. All other files will be OVERWRITTEN during install.  Make sure you have full backups of all your databases and files 
			before continuing with an installation. Manual extraction requires that all contents in the package are extracted to the same directory as the installer file.  
			Manual extraction is only needed when your server does not support the ZipArchive extension.  Please see the online help for more details.
			<br/><br/>

			<b>After Install:</b> When you are done with the installation you must remove remove the these files:
			<ul>
				<li>installer.php</li>
				<li>installer-data.sql</li>
				<li>installer-backup.php</li>
				<li>installer-log.txt</li>
				<li>database.sql</li>
			</ul>

			These files contain sensitive information and should not remain on a production system for system integrity and security protection.
		</div>
	</div>

	<div id="s1-warning-check">
		<input id="accept-warnings" name="accpet-warnings" type="checkbox" onclick="DUPX.acceptWarning()" /> 
		<label for="accept-warnings">I have read and accept all warnings &amp; notices <small style="font-style:italic">(required to run deployment)</small></label><br/>
	</div><br/><br/><br/>

	<div class="footer-buttons">
		<input id="s1-deploy-btn" type="button" value=" Run Deployment " onclick="DUPX.runDeployment()" />
	</div>				
</form>


<!-- =========================================
VIEW: STEP 1 - AJAX RESULT
Auto Posts to view.step2.php  -->
<form id='s1-result-form' method="post" class="content-form" style="display:none">
	<input type="hidden" name="view" value="update" />
	<input type="hidden" name="package_name" value="<?php echo $GLOBALS['FW_PACKAGE_NAME'] ?>" />

	<!-- Set via jQuery -->
	<input type="hidden" name="dbaction" id="ajax-dbaction" />
	<input type="hidden" name="dbhost" id="ajax-dbhost" />
	<input type="hidden" name="dbname" id="ajax-dbname" />
	<input type="hidden" name="dbuser" id="ajax-dbuser" />
	<input type="hidden" name="dbpass" id="ajax-dbpass" />
	<input type="hidden" name="retain_config" id="ajax-retain_config" />

	<input type="hidden" name="logging" id="ajax-logging"  />	
	<input type="hidden" name="dbcharset" id="ajax-dbcharset" />
	<input type="hidden" name="dbcollate" id="ajax-dbcollate" />
	<input type="hidden" name="json"   id="ajax-json" />

    <div class="logfile-link"><a href="installer-log.txt" target="_blank">installer-log.txt</a></div>
	<div class="hdr-main">
		Step 1: Deploy Files &amp; Database
	</div>

	<!--  PROGRESS BAR -->
	<div id="progress-area">
		<div style="width:500px; margin:auto">
			<h3>Processing Files &amp; Database Please Wait...</h3>
			<div id="progress-bar"></div>
			<i>This may take several minutes</i>
		</div>
	</div>

	<!--  AJAX SYSTEM ERROR -->
	<div id="ajaxerr-area" style="display:none">
		<p>Please try again an issue has occurred.</p>
		<div style="padding: 0px 10px 10px 0px;">
			<div id="ajaxerr-data">An unknown issue has occurred with the file and database setup process.  Please see the installer-log.txt file for more details.</div>
			<div style="text-align:center; margin:10px auto 0px auto">
				<input type="button" onclick="$('#s1-result-form').hide();
                        $('#s1-input-form').show(200);" value="&laquo; Try Again" /><br/><br/>
				<i style='font-size:11px'>See online help for more details at <a href='https://snapcreek.com/' target='_blank'>snapcreek.com</a></i>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">
    var CPNL_TOKEN;
    var CPNL_DBINFO			= null;
    var CPNL_DBUSERS		= null;
    var CPNL_CONNECTED		= false;
	var CPNL_PREFIX			= false;

    /** 
     *  Toggles the cpanel Login area  */
    DUPX.togglePanels = function (pane)
    {
        $('#s1-basic-pane, #s1-cpnl-pane').hide();
        $('#s1-basic-btn, #s1-cpnl-btn').removeClass('active in-active');
        if (pane == 'basic') {
            $('#s1-input-form-mode').val('basic');
            $('#s1-basic-pane').show();
            $('#s1-basic-btn').addClass('active');
            $('#s1-cpnl-btn').addClass('in-active');
        } else {
            $('#s1-input-form-mode').val('cpnl');
            $('#s1-cpnl-pane').show();
            $('#s1-cpnl-btn').addClass('active');
            $('#s1-basic-btn').addClass('in-active');
        }
    }	

    /** 
     *  Bacic Action Change  */
    DUPX.basicDBActionChange = function ()
    {
        var action = $('#dbaction').val();
		$('#s1-basic-pane .s1-warning-manualdb').hide();
		$('#s1-basic-pane .s1-warning-emptydb').hide();
		$('#s1-basic-pane .s1-warning-renamedb').hide();
		switch (action) 
		{
			case 'create'  :	break;			
			case 'empty'   : $('#s1-basic-pane .s1-warning-emptydb').show(300);		break;
			case 'rename'  : $('#s1-basic-pane .s1-warning-renamedb').show(300);	break;
			case 'manual'  : $('#s1-basic-pane .s1-warning-manualdb').show(300);	break;
		}
    };

    /** 
     * Accetps Usage Warning */
    DUPX.acceptWarning = function ()
    {
        if ($("#accept-warnings").is(':checked')) {
            $("#s1-deploy-btn").removeAttr("disabled");
        } else {
            $("#s1-deploy-btn").attr("disabled", "true");
        }
    };

    /** 
     * Shows results of database connection 
     * Timeout (45000 = 45 secs) */
    DUPX.testDBConnect = function ()
    {
        var $resource = $('#s1-input-form-mode').val() == 'basic'
                ? $('#s1-basic-pane .s1-dbconn-result')
                : $('#s1-cpnl-pane .s1-dbconn-result');
        $resource.html("Attempting Connection.  Please wait...").show(250);

        $.ajax({
            type: "POST",
            timeout: 45000,
            url: window.location.href + '?' + 'dbtest=1',
            data: $('#s1-input-form').serialize(),
            success: function (data) {
                $resource.html(data);
            },
            error: function () {
                alert('An error occurred while testing the database connection!  Be sure the install file and package are both in the same directory.');
            }
        });
    };

    /** 
     *  Performs cpnl connection and updates UI */
    DUPX.cpnlConnect = function ()
    {
		var $formInput = $('#s1-input-form');
        $formInput.parsley().validate();
        if (!$formInput.parsley().isValid()) {
            return;
        }
		
        $('#s1-cpnl-connect-btn').attr('readonly', 'true').val('Connecting... Please Wait!');
        $('a#s1-cpnl-status-msg, span#s1-cpnl-status-icon').hide();

        var apiAccountActive = function(data)
        {
			var html	= "";
			var error	= "Unknown Error";
			var prefix	= "";
			var validHost  = false;
			var validUser  = false;
			
			if (typeof data == 'undefined')
			{
				error = "Unknown error, unable to retrive data request.";
				CPNL_CONNECTED = false;
			} 
			else if (data.hasOwnProperty('status') && data.status == 0)
			{
				error = data.hasOwnProperty('statusText') ? data.statusText : "Unknown error, unable to retrive status text.";
				CPNL_CONNECTED = false;
			}
			else if (data.hasOwnProperty('result'))
			{
				validHost		= data.result.valid_host;
				validUser		= data.result.valid_user;
				CPNL_DBINFO		= data.result.hasOwnProperty('dbinfo')  ? data.result.dbinfo  : null;
				CPNL_DBUSERS	= data.result.hasOwnProperty('dbusers') ? data.result.dbusers : null;
				CPNL_CONNECTED	= validHost && validUser;
			}

            html += validHost	? "<b>Host:</b>  <div class='dup-pass'>Success</div> &nbsp; "
								: "<b>Host:</b>  <div class='dup-fail'>Unable to Connect</div> &nbsp;";
            html += validUser	? "<b>Account:</b> <div class='dup-pass'>Found</div><br/>"
								: "<b>Account:</b> <div class='dup-fail'>Not Found</div><br/>";

            if (CPNL_CONNECTED)
            {
				var setupDBName = '<?php echo strlen($GLOBALS['FW_CPNL_DBNAME']) > 0 ? $GLOBALS['FW_CPNL_DBNAME'] : 'null'; ?>';
				var setupDBUser = '<?php echo strlen($GLOBALS['FW_CPNL_DBUSER']) > 0 ? $GLOBALS['FW_CPNL_DBUSER'] : 'null'; ?>';
				var $dbNameSelect = $("#cpnl-dbname-select");
				var $dbUserSelect = $("#cpnl-dbuser-select");
				
				//Set Prefix data
				if(data.result.is_prefix_on.status) 
				{
					prefix = $('#cpnl-user').val() + "_";
					var dbnameTxt = $("#cpnl-dbname-txt").val();
					var dbuserTxt = $("#cpnl-dbuser-txt").val();
					
					$("#cpnl-prefix-dbname, #cpnl-prefix-dbuser").show().html(prefix + "&nbsp;");
					if (dbnameTxt.indexOf(prefix) != -1) {
						$("#cpnl-dbname-txt").val(dbnameTxt.replace(prefix, ''));
					}
					if (dbuserTxt.indexOf(prefix) != -1) {
						$("#cpnl-dbuser-txt").val(dbuserTxt.replace(prefix, ''));
					}
					CPNL_PREFIX = true;
				} else {
					$("#cpnl-prefix-dbname, #cpnl-prefix-dbuser").hide().html("");
					$('#cpnl_ignore_prefix').attr('checked', 'true');
					$('#cpnl_ignore_prefix').attr('onclick', 'return false;');
					$('#cpnl_ignore_prefix').attr('onkeydown', 'return false;');
					var $label = $('label[for="cpnl_ignore_prefix"]');
				    $label.css('color', 'gray');
					$label.html($label.text() + ' <i>(this option has been set to readonly by host)</i>');
					CPNL_PREFIX = false;
				}

                //Enable database inputs and show header green go icon
                DUPX.cpnlToggleLogin('on');
                $('span#s1-cpnl-status-icon').html('<div class="circle-pass"></div>');
                $('a#s1-cpnl-status-msg').html('success');
                $('div#s1-cpnl-status-details-msg').html(html);
				
                //Load DB Names
                $dbNameSelect.find('option').remove().end();
				$dbNameSelect.append($("<option selected></option>").val("-- Select Database --").text("-- Select Database --"));
                $.each(CPNL_DBINFO, function (key, value) 
				{
					(setupDBName == value.db)
						? $dbNameSelect.append($("<option selected></option>").val(value.db).text(value.db))
						: $dbNameSelect.append($("<option></option>").val(value.db).text(value.db));
                });
					
                //Load DB Users
                $dbUserSelect.find('option').remove().end();
				$dbUserSelect.append($("<option selected></option>").val("-- Select User --").text("-- Select User --"));
                $.each(CPNL_DBUSERS, function (key, value) 
				{
					(setupDBUser == value.user)
						? $dbUserSelect.append($("<option selected></option>").val(value.user).text(value.user))
						: $dbUserSelect.append($("<option></option>").val(value.user).text(value.user));
                });
				
				 //Warn on host name mismatch
				 var address = window.location.hostname.replace('www.', '');
				 ($('#cpnl-host').val().indexOf(address) == -1)
					? $('#cpnl-host-warn').show()
					: $('#cpnl-host-warn').hide();
            }
            else
            {
                //Auto message display
                html += "<b>Details:</b> Unable to connect. Error status is: '" + error + "'. <br/>";
                $('span#s1-cpnl-status-icon').html('<div class="circle-fail"></div>');
                $('a#s1-cpnl-status-msg').html('failed');
                $('div#s1-cpnl-status-details-msg').html(html);
                $('div#s1-cpnl-status-details').show(500);
                //Inputs
                DUPX.cpnlToggleLogin('off');
            }
            $('a#s1-cpnl-status-msg, span#s1-cpnl-status-icon').show(200);
            $('#s1-cpnl-connect-btn').removeAttr('readonly').val('Connect');
			DUPX.cpnlSetResults();
        }

        DUPX.requestAPI({
            operation: '/cpnl/create_token/',
			timeout: 10000,
            params: {
                host: $('#cpnl-host').val(),
                user: $('#cpnl-user').val(),
                pass: $('#cpnl-pass').val()
            },
            callback: function (data) {
                CPNL_TOKEN = data.result;
                DUPX.requestAPI({
                    operation: '/cpnl/get_setup_data/',
					timeout: 30000,
                    params: {token: data.result},
                    callback: apiAccountActive
                });
            }
        });
    };

    /** 
     *  Enables/Disables database setup and cPanel login inputs  */
    DUPX.cpnlToggleLogin = function (state)
    {
        //Change btn enabled
        if (state == 'on') {
            $('#cpnl-host, #cpnl-user, #cpnl-pass').addClass('readonly').attr('readonly', 'true');
            $('#s1-cpnl-connect-btn').attr('disabled', 'true');
            $('#s1-cpnl-change-btn').removeAttr('disabled').show();
            //Enable cPanel Database
            $('#s1-cpnl-db-opts td').css('color', 'black');
            $('#s1-cpnl-db-opts input, #s1-cpnl-db-opts select').removeAttr('disabled');
        }
        //Change btn disabled
        else
        {
            $('#cpnl-host, #cpnl-user, #cpnl-pass').removeClass('readonly').removeAttr('readonly');
            $('#s1-cpnl-connect-btn').removeAttr('disabled', 'true');
            $('#s1-cpnl-change-btn').attr('disabled', 'true');
            //Disable cPanel Database
            $('#s1-cpnl-db-opts td').css('color', 'silver');
            $('#s1-cpnl-db-opts input, #s1-cpnl-db-opts select').attr('disabled', 'true');
        }
    }

    /** 
     *  Updates action status  */
    DUPX.cpnlDBActionChange = function ()
    {
		var action = $('#cpnl-dbaction').val();
		$('#s1-cpnl-db-opts .s1-warning-manualdb').hide();
		$('#s1-cpnl-db-opts .s1-warning-emptydb').hide();
		$('#s1-cpnl-db-opts .s1-warning-renamedb').hide();
		$('#s1-cpnl-dbname-area1, #s1-cpnl-dbname-area2').hide();
		
		switch (action) 
		{
			case 'create' :	 $('#s1-cpnl-dbname-area2').show(300);	break;			
			case 'empty' :
				$('#s1-cpnl-dbname-area1').show(300);
				$('#s1-cpnl-db-opts .s1-warning-emptydb').show(300);
			break;
			case 'rename' :
				$('#s1-cpnl-dbname-area1').show(300);
				$('#s1-cpnl-db-opts .s1-warning-renamedb').show(300);
			break;
			case 'manual' :
				$('#s1-cpnl-dbname-area1').show(300);
				$('#s1-cpnl-db-opts .s1-warning-manualdb').show(300);
			break;
		}
    };

    /** 
     *  Set the cpnl dbname and dbuser result hidden fields  */
	DUPX.cpnlSetResults = function() 
	{
	   var action = $('#cpnl-dbaction').val();
	   var dbname = $("#cpnl-dbname-txt").val();
	   var dbuser = $("#cpnl-dbuser-txt").val();
	   var prefix = $('#cpnl-user').val() + "_";
	   
	   	if (CPNL_PREFIX) 
		{	
			dbname = prefix + $("#cpnl-dbname-txt").val();
			dbuser = prefix + $("#cpnl-dbuser-txt").val();
		} 
	   
       (action == 'create') 
			? $('#cpnl-dbname-result').val(dbname)
			: $('#cpnl-dbname-result').val($('#cpnl-dbname-select').val());
			
		($('#cpnl-dbuser-chk').is(':checked')) 
			? $('#cpnl-dbuser-result').val(dbuser)
			: $('#cpnl-dbuser-result').val($('#cpnl-dbuser-select').val());
    }
	
	DUPX.cpnlPrefixIgnore = function() 
	{
		if ($('#cpnl_ignore_prefix').prop('checked')) 
		{
			CPNL_PREFIX = false;
			$("#cpnl-prefix-dbname, #cpnl-prefix-dbuser").hide();
		} 
		else 
		{
			CPNL_PREFIX = true;
			$("#cpnl-prefix-dbname, #cpnl-prefix-dbuser").show();
		}
		DUPX.cpnlSetResults();
	}

    /** 
     *  Toggle the DB user name type  */
    DUPX.cpnlDBUserToggle = function ()
    {
		$('#s1-cpnl-dbuser-area1, #s1-cpnl-dbuser-area2').hide();
		 $('#cpnl-dbuser-txt, #cpnl-dbuser-select').removeAttr('disabled');
		 $('#cpnl-dbuser-txt, #cpnl-dbuser-select').removeAttr('required');
		 
        if ($('#cpnl-dbuser-chk').prop('checked')) {
			$('#s1-cpnl-dbuser-area2').show();
            $('#cpnl-dbuser-select').attr('disabled', 'true');
			$('#cpnl-dbuser-txt').attr('required', 'true');

        } else {
			$('#s1-cpnl-dbuser-area1').show();
            $('#cpnl-dbuser-select').attr('required', 'true');
			$('#cpnl-dbuser-txt').attr('disabled', 'true');
        }
		DUPX.cpnlSetResults();
    }

    /** 
     * Performs Ajax post to extract files and create db
     * Timeout (10000000 = 166 minutes) */
    DUPX.runDeployment = function ()
    {
		DUPX.cpnlSetResults();
		var dbhost = $("#dbhost").val();
        var dbname = $("#dbname").val();
		var dbuser = $("#dbuser").val();
		
        if ($('#s1-input-form-mode').val() == 'cpnl')
        {
			dbhost = $("#cpnl-dbhost").val();
			dbname = $("#cpnl-dbname-result").val();
			dbuser = $("#cpnl-dbuser-result").val();	
        }

        var $formInput = $('#s1-input-form');
        var $formResult = $('#s1-result-form');
        $formInput.parsley().validate();
        if (!$formInput.parsley().isValid()) {
            return;
        }

        var msg = "Continue installation with the following settings?\n\n";
        msg += "Server: " + dbhost + "\nDatabase Name: " + dbname + "\nDatabase User: " + dbuser + "\n\n";
        msg += "WARNING: Be sure these database parameters are correct!\n";
        msg += "Entering the wrong information WILL overwrite an existing database.\n";
        msg += "Make sure to have backups of all your data before proceeding.\n\n";

        var answer = confirm(msg);
        if (answer) {
            $.ajax({
                type: "POST",
                timeout: 10000000,
                dataType: "json",
                url: window.location.href,
                data: $formInput.serialize(),
                beforeSend: function () {
                    DUPX.showProgressBar();
                    $formInput.hide();
                    $formResult.show();
                },
                success: function (data) {
                    if (typeof (data) != 'undefined' && data.pass == 1)
                    {
                        if ($('#s1-input-form-mode').val() == 'basic')
                        {
							$("#ajax-dbaction").val($("#dbaction").val());
                            $("#ajax-dbhost").val($("#dbhost").val());
                            $("#ajax-dbname").val($("#dbname").val());
                            $("#ajax-dbuser").val($("#dbuser").val());
                            $("#ajax-dbpass").val($("#dbpass").val());
                        } else {
							$("#ajax-dbaction").val($("#cpnl-dbaction").val());
                            $("#ajax-dbhost").val($("#cpnl-dbhost").val());
                            $("#ajax-dbname").val(dbname);
                            $("#ajax-dbuser").val(dbuser);
                            $("#ajax-dbpass").val($("#cpnl-dbpass").val());
                        }
                        //Advanced Opts
						$("#ajax-retain_config").val($("#retain_config").prop('checked') ? '1' : '0');
                        $("#ajax-dbcharset").val($("#dbcharset").val());
                        $("#ajax-dbcollate").val($("#dbcollate").val());
                        $("#ajax-logging").val($("input:radio[name=logging]:checked").val());
                        $("#ajax-json").val(escape(JSON.stringify(data)));
                        setTimeout(function () {
                            $formResult.submit();
                        }, 1000);
                        $('#progress-area').fadeOut(700);
                    } else {
                        DUPX.hideProgressBar();
                    }
                },
                error: function (xhr) {
                    var status = "<b>server code:</b> " + xhr.status + "<br/><b>status:</b> " + xhr.statusText + "<br/><b>response:</b> " + xhr.responseText + "<br/>";
					status += "<b>Additional Resources:</b><br/> ";
					status += "&raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/'>Help Resources</a><br/>";	
					status += "&raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/'>Technical FAQ</a>";	
                    $('#ajaxerr-data').html(status);
                    DUPX.hideProgressBar();
                }
            });
        }
    };

    //DOCUMENT LOAD
    $(document).ready(function () {
		
		//Custom Validator
		window.Parsley.addValidator('cpnluser', {
			validateString: function(value) {
			  var prefix = CPNL_PREFIX 
					? $('#cpnl-user').val() + "_" + value
					: value;
			  return (prefix.length <= 16);
			},
			messages: {
			  en: 'Database user cannot be more that 16 characters including prefix'
			}
		});
		
        //Attach Events
        $("#dbaction").on("change", DUPX.basicDBActionChange);
        $("#cpnl-dbaction").on("change", DUPX.cpnlDBActionChange);
        $("#cpnl-dbuser-chk").click(DUPX.cpnlDBUserToggle);
		$('#cpnl-dbname-select, #cpnl-dbname-txt').on("change", DUPX.cpnlSetResults);
		$('#cpnl-dbuser-select, #cpnl-dbuser-txt').on("change", DUPX.cpnlSetResults);
		
        //Init
        DUPX.acceptWarning();
		<?php echo ($GLOBALS['FW_CPNL_ENABLE'])  ? 'DUPX.togglePanels("cpanel");' : 'DUPX.togglePanels("basic");'; ?>
		<?php echo ($GLOBALS['FW_CPNL_CONNECT']) ? 'DUPX.cpnlConnect();' : ''; ?>
		$("#cpnl-dbaction").val(<?php echo strlen($GLOBALS['FW_CPNL_DBACTION']) > 0 ? "'{$GLOBALS['FW_CPNL_DBACTION']}'" : 'create'; ?>);
		DUPX.cpnlDBActionChange();
		DUPX.basicDBActionChange();
		DUPX.cpnlDBUserToggle();
        DUPX.cpnlToggleLogin('off');
		DUPX.cpnlSetResults();
    });
</script>
<!-- END OF VIEW STEP 1 -->
