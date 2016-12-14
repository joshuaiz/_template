<?php
//-- START OF VIEW INIT 1

	$_POST['secure-pass'] = isset($_POST['secure-pass']) ? $_POST['secure-pass'] : '' ;
	$_POST['secure-try']  = isset($_POST['secure-try'])  ? 1 : 0 ;
	$page_url = DUPX_HTTP::get_request_uri();
	$page_err = 0;
	
	//FORWARD: password not enabled
	if (! $GLOBALS['FW_SECUREON'] && ! $_GET['debug']) {
		DUPX_HTTP::post_with_html($page_url, array('view' => 'scan'));
		exit;
	}
	
	//POSTBACK: valid password
	if ($_POST['secure-pass'] == base64_decode($GLOBALS['FW_SECUREPASS'])) {
		DUPX_HTTP::post_with_html($page_url, array('view' => 'scan'));
		exit;
	} 
	
	//ERROR: invalid password
	if ($_POST['secure-try'] && $_POST['secure-pass'] != base64_decode($GLOBALS['FW_SECUREPASS'])) {
		$page_err = 1;
	}
?>


<!-- =========================================
VIEW: STEP 0 - PASSWORD -->
<form method="post" id="i1-pass-form" class="content-form"  data-parsley-validate="">
	<input type="hidden" name="view" value="secure" />
	<input type="hidden" name="secure-try" value="1" />
	
	<div class="hdr-main">
		Installer Password
	</div>
	
	<?php if ($page_err) : ?>
		<div class="error-pane">
			<p>Invalid Password! Please try again...</p>
		</div>
	<?php endif; ?>
	
	<div style="text-align: center">
		This file has been protected by a password.  Please provide the password that was used when the package was created.  If you do not remember the password
		check the details of the package on the site where it was created.
	</div>
	
	<div class="i1-pass-area">
		<div class="i1-pass-data">
			<label for="secure-pass">&nbsp; Enter Password</label> 
			<div id="i1-pass-input">
				<input type="password" name="secure-pass" id="secure-pass" required="required" />
				<button type="button" class="pass-toggle" onclick="DUPX.togglePassword()" title="Show/Hide the password"></button>
			</div>
			<input type="button" name="secure-btn" id="secure-btn" value="Submit" onclick="DUPX.checkPassword()" />
		</div>
	</div>
	
</form>

<script>
	/** 
	 * Submits the password for validation
	 */
	DUPX.checkPassword = function() 
	{
		var $form = $('#i1-pass-form');
		$form.parsley().validate();
		if (! $form.parsley().isValid()) {
			return;
		}
		$form.submit();
	}
	
	/** 
	 * Submits the password for validation
	 */
	DUPX.togglePassword = function() 
	{
		var $input = $('#secure-pass');
		if (($input).attr('type') == 'text') {
			$input.attr('type', 'password');
		} else {
			$input.attr('type', 'text');
		}
	}
</script>
<!-- END OF VIEW INIT 1 -->