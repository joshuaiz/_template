<?php
// Exit if accessed directly
if (!defined('DUPLICATOR_PRO_INIT')) {
	$_baseURL = "http://" . strlen($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: {$_baseURL}");
	exit;
}
?>

<script type="text/javascript">
	//Unique namespace
	DUPX = new Object();
	DUPX.Util = new Object();
	DUPX.GLB_DEBUG =  <?php echo ($_GET['debug'] || $GLOBALS['DEBUG_JS']) ? 'true' : 'false'; ?>;
	
	DUPX.showProgressBar = function () 
	{
		DUPX.animateProgressBar('progress-bar');
		$('#ajaxerr-area').hide();
		$('#progress-area').show();
	}
	
	DUPX.hideProgressBar = function () 
	{
		$('#progress-area').hide(100);
		$('#ajaxerr-area').fadeIn(400);
	}
	
	DUPX.animateProgressBar = function(id) {
		//Create Progress Bar
		var $mainbar   = $("#" + id);
		$mainbar.progressbar({ value: 100 });
		$mainbar.height(25);
		runAnimation($mainbar);

		function runAnimation($pb) {
			$pb.css({ "padding-left": "0%", "padding-right": "90%" });
			$pb.progressbar("option", "value", 100);
			$pb.animate({ paddingLeft: "90%", paddingRight: "0%" }, 3500, "linear", function () { runAnimation($pb); });
		}
	}
	
	/*
	 * DUPX.requestAPI({
	 *			operation : '/cpnl/create_token/', 
	 *			data : params,
	 *			callback :	function(){});
	 */
	DUPX.requestAPI = function(obj)
	{
		var timeout   = obj.timeout || 120000;  //default to 120 seconds
		var apiPath	  = ( obj.operation.substr(-1) !== '/') ? apiPath += '/' :  obj.operation;
		var requestURI = window.location.origin + window.location.pathname + '/api' + apiPath + window.location.search
		
		for (var key in obj.params) 
		{
			if (obj.params.hasOwnProperty(key) && typeof(obj.params[key]) != 'undefined') 
			{
				obj.params[key] = encodeURIComponent(obj.params[key].replace(/&amp;/g, "&")); 
  			}
		}

		if (DUPX.GLB_DEBUG) {
			console.log('==============================================================');
			console.log('API REQUEST: ' + obj.operation);
			console.log(obj.params);
		}
		
		//Requets to API are capped at 2 mins
		$.ajax({
			type: "POST",
			timeout: timeout,
			dataType: "json",
			url: requestURI,
			data:  obj.params,		
			success: function(data) { if (DUPX.GLB_DEBUG) console.log(data); obj.callback(data); },
			error:   function(data) { if (DUPX.GLB_DEBUG) console.log(data); obj.callback(data); }
		});
	}
	
	DUPX.Util.formatBytes = function (bytes,decimals) 
	{
		if(bytes == 0) return '0 Byte';
		var k = 1000;
		var dm = decimals + 1 || 3;
		var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
		var i = Math.floor(Math.log(bytes) / Math.log(k));
		return (bytes / Math.pow(k, i)).toPrecision(dm) + ' ' + sizes[i];
	}

	
	$(document).ready(function() {
		//ATTACHED EVENTS
		$('#help-lnk').change(function() {
			if ($(this).val() != "null") 
				window.open($(this).val())
		});		
	});
</script>
