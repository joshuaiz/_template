<?php
//-- START OF VIEW INIT 2 
// Exit if accessed directly
if (!defined('DUPLICATOR_PRO_INIT')) {
	$_baseURL = "http://" . strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$_baseURL}");
	exit;
}

//REQUIRMENTS 
$req01a   = DUPX_Server::is_dir_writable($GLOBALS["CURRENT_ROOT_PATH"]) ? 'Pass' : 'Fail';
$req01b   = (file_exists($GLOBALS['FW_PACKAGE_NAME']))	? 'Pass' : 'Fail';
$req01    = ($req01a == 'Pass' && $req01b == 'Pass')	? 'Pass' : 'Fail';
$req02a   = (! DUPX_Server::$php_safe_mode_on)			? 'Pass' : 'Fail';
$req02b   = function_exists('mysqli_connect')			? 'Pass' : 'Fail';
$req02c	  = DUPX_Server::$php_version_safe				? 'Pass' : 'Fail';
$req02	  = ($req02a == 'Pass' && $req02b == 'Pass' && $req02c == 'Pass' )	? 'Pass' : 'Fail';
$all_req  = ($req01 == 'Pass' && $req02 == 'Pass') ? 'Pass' : 'Fail';

//NOTICES

//Directory Setup
$openbase	 = ini_get("open_basedir");
$scanfiles   = @scandir($GLOBALS["CURRENT_ROOT_PATH"]); 
$scancount	 = is_array($scanfiles) ? (count($scanfiles)) : -1;
$notice01a   = ! file_exists('wp-config.php')	? 'Good' : 'Warn';
$notice01b   = $scancount <= 15					? 'Good' : 'Warn';
$notice01    = ($notice01a == 'Good' && $notice01b == 'Good')	? 'Good' : 'Warn';

//Compatibility Checks
$datetime1 = $GLOBALS['FW_CREATED'];
$datetime2 = date("Y-m-d H:i:s");
$fulldays  = round(abs(strtotime($datetime1) - strtotime($datetime2))/86400);

$notice02a	=  $fulldays <= 180 ? 'Good' : 'Warn';
$notice02b	=  version_compare(PHP_VERSION, $GLOBALS['FW_VERSION_PHP']) != -1 ? 'Good' : 'Warn';
$notice02c	=  $GLOBALS['FW_VERSION_OS'] == PHP_OS ? 'Good' : 'Warn';
$notice02	= ($notice02a == 'Good' && $notice02b == 'Good' && $notice02c == 'Good')	? 'Good' : 'Warn';

//Open Base
$notice03	 = empty($openbase)	 ? 'Good' : 'Warn'; 


//SUMMATION
$success   = ($all_req == 'Pass');
$skipscan = ($GLOBALS['FW_SKIPSCAN'] && $success) && (! isset($_GET['view']));

//FORWARD: skip scanner
if ($skipscan && ! $_GET['debug']) {
	DUPX_HTTP::post_with_html(DUPX_HTTP::get_request_uri(), array('view' => 'deploy'));
	exit;
}
?>

<!-- =========================================
VIEW: STEP 0 - SYSTEM SCAN -->
<form method="post" class="content-form">
	<input type="hidden" name="view" value="deploy" />
	
	<div class="hdr-main">
		<?php echo ( $success) ? '<div class="circle-pass"></div> ' : '<div class="circle-fail"></div>'; ?>	System Scan
	</div>
	<small style="display:block; margin:-10px 0 5px 0">Requirements must pass in order to run the installer.  Notices are general recommendations about the current environment.</small>
	<br/>

	<!-- ====================================
	REQUIREMENTS -->
	<div class="hdr-sub">Requirements</div>
	<table class="i2-reqs">
		<tr>
			<td class="i2-reqs-item">+ Directory Setup</td>
			<td><span class="<?php echo ($req01 == 'Pass') ? 'dup-pass' : 'dup-fail' ?>"><?php echo $req01; ?></span></td>
		</tr>
		<tr>
			<td colspan="2" class="i2-reqs-subitem">
				<?php
					echo "<span style='font-weight:bold'>Root Path:</span> {$GLOBALS['CURRENT_ROOT_PATH']} <br/>";
					printf("<b>[%s]</b> %s <br/>", $req01a, "Is Writable by PHP");
					printf("<b>[%s]</b> %s <br/>", $req01b, "Contains Archive: <i>{$GLOBALS['FW_PACKAGE_NAME']}</i>");
				?>
				<small>
					- The Root Path above must be writable by PHP in order to extract the archive.zip properly<br/>
					- The archive file name above must be the <u>exact</u> name of the archive file placed in the root path (character for character).
					When downloading the package files make sure both files are from the same package line in the packages view.
				</small>
			</td>
		</tr>
		<tr>
			<td class="i2-reqs-item">+ PHP Setup</td>
			<td class="<?php echo ($req02 == 'Pass') ? 'dup-pass' : 'dup-fail' ?>"><?php echo $req02; ?></td>
		</tr>
		<tr>
			<td colspan="2" class="i2-reqs-subitem">
				<?php
					printf("<b>[%s]</b> %s <br/>", $req02a, "Safe Mode Off");
					printf("<b>[%s]</b> %s <br/>", $req02b, "mysqli support" . ' <a href="http://us2.php.net/manual/en/mysqli.installation.php" target="_blank">[details]</a>' );
					printf("<b>[%s]</b> %s <br/>", $req02c, "version: " . DUPX_Server::$php_version . " (PHP 5.2.17+ is required)");
				?>
				<small>
					If the items above do not pass contact your hosting provider or server administrator.<br/>
					<div style="padding:0 0 0 15px">
						Safe Mode: This value should be disabled via the php.ini by your host.<br/>
						MySQLi: This is a very common module.  The <a href="http://us2.php.net/manual/en/mysqli.installation.php" target="_blank">install</a> will need to be performed by your host. <br/>
						PHP: Upgrading your PHP version will need to be performed your host.
					</div>
				</small>
			</td>
		</tr>
	</table>
	<br/>
	
	<!-- ====================================
	NOTICES -->
	<div class="hdr-sub">Notices</div>
	<table class="i2-reqs">
		<tr>
			<td class="i2-reqs-item">+ Directory Setup</td>
			<td class="<?php echo ($notice01 == 'Good') ? 'dup-pass' : 'dup-fail' ?>"><?php echo $notice01; ?></td>
		</tr>	
		<tr>
			<td colspan="2" class="i2-reqs-subitem">
			<?php
				echo "Root Path: {$GLOBALS['CURRENT_ROOT_PATH']} <br/>";
				printf("<b>[%s]</b> %s <br/>", $notice01a, "WordPress configuration file wp-config.php check.");
				printf("<b>[%s]</b> %s <br/>", $notice01b, "Directory/file count check found [{$scancount}] files/directories.");
				echo "<small>Any file/directory currently in the root path will be overwritten if it also exists inside the archive file.  This check shows as a warning "
				. "if it detects more than 10 files/directories.  The notice is to prevent users from accidentally overwriting a large amount of files.</small>";
			?>
			</td>
		</tr>	
		<tr>
			<td class="i2-reqs-item">+ Compatibility</td>
			<td class="<?php echo ($notice02 == 'Good') ? 'dup-pass' : 'dup-fail' ?>"><?php echo $notice02; ?></td>
		</tr>	
		<tr>
			<td colspan="2" class="i2-reqs-subitem">
			<?php
				$phpversion = PHP_VERSION;
				$currentOS = PHP_OS;
				printf("<b>[%s]</b> %s <br/>", $notice02a, "<b>AGE:</b> The package is {$fulldays} day(s) old. Packages older than 180 days might be considered stale");
				printf("<b>[%s]</b> %s <br/>", $notice02b, "<b>PHP:</b> This server is using PHP '{$phpversion}'. The package was built with '{$GLOBALS['FW_VERSION_PHP']}'");
				printf("<b>[%s]</b> %s <br/>", $notice02c, "<b>OS:</b> The current OS is '{$currentOS}'.  The package was built on '{$GLOBALS['FW_VERSION_OS']}'");
				echo "<b></b> <b>DB:</b> The package was built with a database engine version of '{$GLOBALS['FW_VERSION_DB']}' <br/>";
				echo "<small>These values are just notices and may not have an impact on the deployment of this package.  The 'Test Connection' button on step 1"
					. " will show any possible database incompatibility issues.</small>";
			?>
			</td>
		</tr>			
		<tr>
			<td class="i2-reqs-item">+ Open Base Dir</td>
			<td class="<?php echo ($notice03 == 'Good') ? 'dup-pass' : 'dup-fail' ?>"><?php echo $notice03; ?></td>
		</tr>	
		<tr>
			<td colspan="2" class="i2-reqs-subitem">
				The setting <i><a href="http://www.php.net/manual/en/ini.core.php#ini.open-basedir" target="_blank">open_basedir</a></i> is enabled. 
				Please work with your host and follow these steps to prevent issues: 
				<ol style="margin:7px; line-height:19px">
					<li>Disable the open_basedir setting in the php.ini file</li>
					<li>
						If the host will not disable, then add the path below to open_basedir<br/>
						<i style="color:maroon">"<?php echo str_replace('\\', '/', dirname( __FILE__ )); ?>"</i>
					</li>
					<li>Restart the web server</li>
				</ol>
				<small>
					Note: This warning will still show if you choose option #2 and open_basedir is enabled 
					<i><a href="http://www.php.net/manual/en/ini.core.php#ini.open-basedir" target="_blank">[more details]</a></i>
				</small>
			</td>
		</tr>			
	</table>
	<br/><br/>

	
	<!-- DETAILS -->
	<div class="hdr-sub">Details</div>
	<div class="i2-detail-info">
		<label>Plugin Version:</label>  <?php echo $GLOBALS['FW_VERSION_DUP'] ?><br/>
		<label>Archive Name:</label> <?php echo $GLOBALS['FW_PACKAGE_NAME'] ?> <br/>
		<label>Package Notes:</label> <?php echo empty($GLOBALS['FW_PACKAGE_NOTES']) ? 'No notes provided for this package.' : $GLOBALS['FW_PACKAGE_NOTES']; ?><br/>
		<label>Web Server:</label>  <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br/>
		<label>PHP SAPI:</label>  <?php echo php_sapi_name(); ?><br/>
		<label>PHP ZIP Archive:</label> <?php echo class_exists('ZipArchive') ? 'Is Installed' : 'Not Installed'; ?> <br/>
		<label>Try CDN Request:</label> <?php echo ( DUPX_Util::try_CDN("ajax.aspnetcdn.com", 443) && DUPX_Util::try_CDN("ajax.googleapis.com", 443)) ? 'Yes' : 'No'; ?> <br/>
		<label>Skip Scanner:</label>  <?php echo ($GLOBALS['FW_SKIPSCAN']) ? 'Yes' : 'No' ?><br/>
	</div><br/><br/>

	<?php if (! $success) :?>
		<div class="i2-err-msg">
			<i>
				This installation will not be able to proceed until the system requirements pass. Please adjust your servers settings or contact your server administrator, 
				hosting provider or visit the resources below for additional help.
			</i>
			<div style="padding:10px">
				&raquo; <a href="https://snapcreek.com/duplicator/docs/faqs-tech/" target="_blank">Technical FAQs</a> <br/>
				&raquo; <a href="https://snapcreek.com/support/docs/" target="_blank">Online Documentation</a> <br/>
			</div>
		</div>
	<?php endif; ?>	
	
	<?php if (! $_GET['basic']) :?>
		<div class="footer-buttons">
			<input id="step0-deploy-btn" type="submit" value=" Start Deployment " <?php echo ($success) ? '' : 'disabled="disabled"'; ?>  />
		</div>
	<?php endif; ?>
</form>

<script>
	//DOCUMENT LOAD
	$(document).ready(function() {
		$("td.i2-reqs-item").click(function() {
			var text = $(this).text().replace(/\+|\-/, "");
			var info = $(this).parent().closest('tr').next().find('.i2-reqs-subitem');
			if (info.is(':hidden')) {
				$(this).html("- " + text );
				info.show();
			} else {
				$(this).html("+ " + text );
				info.hide();
			}
		});
		
	});
</script>
<!-- END OF VIEW INIT 2 -->