<style>
	body {font-family:"Open Sans",sans-serif;}
	body,td,th {font-size:13px;color:#000;}
	fieldset {border:1px solid silver; border-radius:5px; padding:10px}
	h3 {margin:1px; padding:1px; font-size:14px;}
	a {color:#222}
	a:hover{color:gray}
	input[type=text], input[type=password], select {width:99%; border-radius:3px; height:17px; font-size:12px; border:1px solid silver; padding:2px}
	select {height:22px;padding-left:0; width:100%}
	select:disabled {background:#EBEBE4}
	input.readonly {background-color:#efefef;}
	button.pass-toggle {
		height:23px; width:23px; position:absolute; top:0px; right:0px;
		border:1px solid silver;  border-radius:0 4px 4px 0;
		background:no-repeat center #efefef url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYxIDY0LjE0MDk0OSwgMjAxMC8xMi8wNy0xMDo1NzowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNS4xIFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RDQ3NUZBMkJDQzJEMTFFNUI2QzhFMzYwNEEzQkZFQ0IiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RDQ3NUZBMkNDQzJEMTFFNUI2QzhFMzYwNEEzQkZFQ0IiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpENDc1RkEyOUNDMkQxMUU1QjZDOEUzNjA0QTNCRkVDQiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpENDc1RkEyQUNDMkQxMUU1QjZDOEUzNjA0QTNCRkVDQiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PtZAny0AAAIaSURBVHjajJHPaxNBFMffbJJCiQoBYxNMQCNCaBWklehJBSPYemguGsSrhx7rP1C8tFgopgiKHjwnJr0IuZmLQpCuxW6pWn9uahKRJmk3mmBrZmanM5Nuuo09+ODtzL6Zz/t+ZwYxxgAhBFbEYjHPyMi1mz6/bxBjAqViUcvlXqSy2WwNbCG49mcnJienLuiFwlqxVGZ6YZV9+fqNrXz6zObVhcr4+J0r3TCylOPxuO9+IvGeq3kWF98uLGlLGb4FD5w6HevvH7ho1I36g9nEUDqd1v9RzszNTZTKP1gy9exlNBo9aClEIhFXMpl6/u7DCrs3PT1jV1asn2AweO7P5ia8Ueef5nK5hlVXVRXn8/nHTocDvN4j58PhcOeCFFsnN8YYms1mBbqiWq2sm+J4inLA6XQq+8EgYPsFWoGQAialQAlBHO7U5Wxi5sntKu49gRoKHD8bHbt75vKwUKI8xdjjcvmW1zD86j3qj1y9MaZp2sMO7PEfG63DoQAlDA6fHBwllEHr7xYQYUJxATUZFJq8mTvgdfdtXA+FQo+EWQljvtgiJhDTlBsJz9VXGV6jELh0S6qLetsN7LWNqQktnoTuwljpAepgOw1BgjJNtheWNm3KIn8bNVnvo7tnFxxl3TCHBExt9rxDw1wFpAv75Znd8Pr3j68bRg0xaHcXzyX2cBa2Nspybq21jJ/Luq7LDmi/d/3f2BZgAN8ccHj5vth9AAAAAElFTkSuQmCC')
	}

	/* ============================
	COMMON VIEW ELEMENTS*/
	div#content {border:1px solid #CDCDCD;  width:750px; min-height:550px; margin:auto; margin-top:18px; border-radius:5px;  box-shadow:0 8px 6px -6px #333;}
	div#content-inner {padding:10px 30px; min-height:550px}
	form.content-form {min-height:550px; position:relative; line-height:17px}	
	div.logfile-link {float:right; font-weight:normal; font-size:12px}
	
	/* WIZARD STEPS */
	table.header-wizard {border-top-left-radius:5px; border-top-right-radius:5px; width:100%; box-shadow:0 6px 4px -4px #777;	background-color:#F1F1F1}
	div#wiz {padding:0; margin:7px 0 10px 20px; height:20px }
	div#wiz-steps {margin:0 0 0 10px; padding:0;  clear:both; font-weight:bold;font-size:12px; min-width:250px }
	#wiz span {display:block;float:left; text-align:center; width:15px; margin:3px 4px 0 0; line-height:15px; color:#ccc; border:1px solid #CCCCCC; border-radius:4px;}
	/* WIZ-DEFAULT*/
	#wiz a { position:relative; display:block; width:auto; height:24px; margin-right:18px; padding:0 10px 0 3px; float:left;  line-height:24px; color:#000; background:#E4E4E4; }
	#wiz a:before { width:0; height:0; border-top:12px solid #E4E4E4; border-bottom:12px solid #E4E4E4; border-left:12px solid transparent; position:absolute; content:""; top:0; left:-12px; }
	#wiz a:after { width:0; height:0; border-top:12px solid transparent; border-bottom:12px solid transparent; border-left:12px solid #E4E4E4; position:absolute; content:""; top:0; right:-12px; }
	/* WIZ-COMPLETED */
	#wiz .completed-step a {color:#ccc; background:#999;}
	#wiz .completed-step a:before {border-top:12px solid #999; border-bottom:12px solid #999;}
	#wiz .completed-step a:after {border-left:12px solid #999;}
	#wiz .completed-step span {color:#ccc;}
	/* WIZ-ACTIVE */
	#wiz .active-step a {color:#fff; background:#999;}
	#wiz .active-step a:before {border-top:12px solid #999; border-bottom:12px solid #999;}
	#wiz .active-step a:after {border-left:12px solid #999;}
	#wiz .active-step span {color:#fff;}
	.wiz-dupx-version {white-space:nowrap; color:#999; font-size:11px; font-style:italic; text-align:right;  padding:0 15px 15px 0;}
	.wiz-dupx-version a { color:#999; }
	
	div.circle-pass, div.circle-fail {width:12px;height:12px;border-radius:50px;background:#279723;display:inline-block;}
	div.circle-fail {background:#9A0D1D !important;}
	.dup-pass {display:inline-block; color:green;}
	.dup-fail {display:inline-block; color:#AF0000;}
	.dup-notice {display:inline-block; color:#000;}
	
	div.log-ui-error {padding-top:2px; font-size:14px}
	div#progress-area {padding:5px; margin:150px 0 0 0; text-align:center;}
	div#ajaxerr-data {padding:5px; height:350px; width:99%; border:1px solid silver; border-radius:5px; background-color:#efefef; font-size:12px; overflow-y:scroll}
	
	div.hdr-main {font-size:18px; padding:0 0 5px 0; border-bottom:1px solid #999; font-weight:bold; margin:5px 0 10px 0;}
	div.hdr-sub {font-size:14px; padding:2px 2px 2px 0; border-bottom:1px solid #dfdfdf; font-weight:bold; margin-bottom:5px;}
	div.error-pane {border:1px solid #efefef; border-left:4px solid #D54E21; padding:0 0 0 10px; margin:2px 0 10px 0}
	
	div.footer-buttons {position:absolute; bottom:10px; padding:10px;  right:0}
	div.footer-buttons  input, button {
		color:#000; font-size:12px; border-radius:5px;	padding:6px 8px 4px 8px; border:1px solid #999;
		background-color:#F1F1F1;
		background-image:-ms-linear-gradient(top, #F9F9F9, #ECECEC);
		background-image:-moz-linear-gradient(top, #F9F9F9, #ECECEC);
		background-image:linear-gradient(top, #F9F9F9, #ECECEC);
	}
	div.footer-buttons input[disabled=disabled]{background-color:#F4F4F4; color:silver; border:1px solid silver;}
	div.footer-buttons  input, button {cursor:pointer; border:1px solid #000; }
	form#form-debug {display:block; margin:10px auto; width:750px;}
	form#form-debug a {display:inline-block;}
	form#form-debug pre {margin-top:-2px; display:none}
	
	/* ============================
	INIT 1:SECURE PASSWORD */
	div.i1-pass-area {width:100%; text-align:center}
	div.i1-pass-data {padding:30px; margin:auto; text-align:center; width:300px}
	div.i1-pass-data table {width:100%; border-collapse:collapse; padding:0}
	div.i1-pass-data label {font-weight:bold}
	div.i1-pass-errmsg {color:maroon; font-weight:bold}
	div#i1-pass-input {position:relative; margin:2px 0 15px 0}
	input#secure-pass {border-radius:4px 0 0 4px; width:250px}
	
	/* ============================
	INIT 2:SYSTEM SCAN */
	table.i2-reqs {width:100%; line-height:18px}
	table.i2-reqs td:first-child{width:300px; cursor:pointer}
	table.i2-reqs td.i2-reqs-item:hover{text-decoration:underline}
	table.i2-reqs td.i2-reqs-subitem {padding:2px 0 12px 30px; line-height:18px; display:none; font-size:12px}
	table.i2-reqs td.i2-reqs-subitem b {width:55px; display:inline-block}
	div.i2-err-msg {padding:20px 0 80px 0; line-height:20px}
	div.i2-err-msg i {color:maroon}
	div.i2-detail-info {line-height:22px}
	div.i2-detail-info label {display:inline-block; width: 150px; font-weight: bold}
	
	/* ============================
	STEP 1 VIEW */
	table.s1-opts {width:100%; border:0px;}
	table.s1-opts td{white-space:nowrap; padding:3px;}
	table.s1-opts td:first-child{width:125px;}
	table.s1-advopts td:first-child{width:125px; font-weight:bold}
	
	/*Toggle Buttons */
	div.s1-btngrp {text-align:center; margin:10px 0 0 0}
	div.s1-btngrp input[type=button] {font-size:14px; padding:5px; width:120px; border:1px solid silver;  cursor:pointer}
	div.s1-btngrp input[type=button]:first-child {border-radius:5px 0 0 5px; margin-right:-2px}
	div.s1-btngrp input[type=button]:last-child {border-radius:0 5px 5px 0; margin-left:-2px}
	div.s1-btngrp input[type=button].active {background:#999999; color:#fff; font-weight:bold;  box-shadow:inset 0 0 10px #444;}
	div.s1-btngrp input[type=button].in-active {background:#E4E4E4; }
	div.s1-btngrp input[type=button]:hover {border:1px solid #999}

	/*Basic DB */
	select#dbname-select {width:100%; border-radius:3px; height:20px; font-size:12px; border:1px solid silver;}
	
	/*cPanel DB */
	div#s1-cpnl-area div#cpnl-host-warn {white-space:normal; font-size:11px; display:none; font-style: italic}
	a#s1-cpnl-status-msg {font-size:11px}
	span#s1-cpnl-status-icon {display:none}
	div#s1-cpnl-connect {margin:auto; text-align:center; margin:15px 0 20px 0}
	div#s1-cpnl-status-details {border:1px solid silver; border-radius:3px; background-color:#f9f9f9; padding:10px 10px 2px 10px; margin-top:10px; height:55px; overflow-y:scroll;}
	div#cpnl-dbname-prefix {display:none; float:left; margin-top:3px;}
	span#s1-cpnl-db-opts-lbl {font-size:11px; font-weight:normal; font-style:italic}
	div#s1-cpnl-dbname-area2 table {border-collapse: collapse; width: 100%}
	div#s1-cpnl-dbname-area2 table td {padding:0 !important; margin:0; border:0}
	div#s1-cpnl-dbname-area2 table td:first-child {vertical-align:bottom;}
	div#s1-cpnl-dbname-area2 table td:nth-child(2) {width:100%; padding-right:0 !important}
	div#s1-cpnl-dbuser-area2 table {border-collapse: collapse; width: 100%}
	div#s1-cpnl-dbuser-area2 table td {padding:0 !important; margin:0; border:0}
	div#s1-cpnl-dbuser-area2 table td:first-child {vertical-align:bottom;}
	div#s1-cpnl-dbuser-area2 table td:nth-child(2) {width:100%; padding-right:0 !important}
	
	/*Test DB connection */
	div.s1-dbconn-area {margin:auto; text-align:center; margin:10px 0 15px 0}
	div.s1-dbconn-area input[type=button] {font-size:11px; height:20px; border:1px solid gray; border-radius:3px; cursor:pointer}
	div.s1-dbonn-result-newuser {width:85%; margin:auto; text-align:center; line-height:17px}
	div.s1-dbconn-result  {border:1px solid silver; border-radius:3px; background:#f9f9f9; padding:3px; margin-top:10px; height:200px; overflow-y:scroll; display:none; max-width:680px}
	div.s1-dbconn-result-data small{display:block; font-style:italic; color:#333; padding:3px 2px 5px 2px; border-bottom:1px dashed silver; margin-bottom:10px; text-align: center }
	div.s1-dbconn-result-data table.details {text-align: left; margin: auto}
	div.s1-dbconn-result-data table.details td:first-child {font-weight: bold; width: 65px; vertical-align: top}
	div.s1-dbconn-result-data div.warning {padding:5px 0 2px 0}
	div.s1-dbconn-result-data {white-space: normal}
	div.s1-dbconn-result-faq {font-style: italic; font-size:12px; border-top:1px dashed silver; padding:8px; margin-top:10px}
	div.s1-dbconn-result-data div.warn-msg {text-align: left; padding:5px; margin:10px 0 10px 0}
	div.s1-dbconn-result-data div.warn-msg b{color:maroon}

	/*Warning Area and Message */
	div#s1-warning-area {margin-top:10px;}
	div#s1-warning-msg {padding:5px;font-size:11px; color:#333; line-height:12px;font-style:italic; overflow-y:scroll; height:150px; border:1px solid #dfdfdf; background:#fff; border-radius:3px}
	div#s1-warning-check {padding:3px; font-size:13px; font-weight:normal; }
	div.s1-warning-emptydb {color:#AF2222; margin:2px 0 0 0; font-size:11px; display: none}
	div.s1-warning-manualdb {color:#1B67FF; margin:2px 0 0 0; font-size:11px; display:none}
	div.s1-warning-renamedb {color:#1B67FF; margin:2px 0 0 0; font-size:11px; display:none}
	div#s1-tryagain {padding-top:50px; text-align:center; width:100%; font-size:16px; color:#444; font-weight:bold;}

	/* ============================
	STEP 2 VIEW */
	table.s2-opts{width:100%; border:0;}
	table.s2-opts input {width:95% !important}
	table.s2-opts td{white-space:nowrap; padding:3px;}
	table.s2-opts td:first-child{width:90px;}
	div#s2-adv-opts {margin-top:5px; }
	div.s2-allnonelinks {font-size:11px; float:right;}
	div.s2-manaual-msg {font-style: italic; margin:-2px 0 5px 0}

	/* ============================
	STEP 3 VIEW */
	div.s3-final-msg {height:110px; border:1px solid #CDCDCD; padding:8px;font-size:12px; border-radius:5px;box-shadow:0 4px 2px -2px #777;}
	div.s3-final-title {color:#BE2323; font-size:18px}
	div.s3-connect {font-size:12px; text-align:center; font-style:italic; position:absolute; bottom:10px; padding:10px; width:100%; margin-top:20px}
	table.s3-report-results,
	table.s3-report-errs {border-collapse:collapse; border:1px solid #dfdfdf; }
	table.s3-report-errs  td {text-align:center; width:33%}
	table.s3-report-results th, table.s3-report-errs th {background-color:#efefef; padding:0; font-size:12px; padding:0}
	table.s3-report-results td, table.s3-report-errs td {padding:0; white-space:nowrap; border:1px solid #dfdfdf; text-align:center; font-size:11px}
	table.s3-report-results td:first-child {text-align:left; font-weight:bold; padding-left:3px}

	div.s3-err-msg {padding:8px;  display:none; border:1px dashed #999; margin:10px 0 20px 0; border-radius:5px;}
	div.s3-err-msg div.content{padding:5px; font-size:11px; line-height:17px; max-height:125px; overflow-y:scroll; border:1px solid silver; margin:3px;  }
	div.s3-err-msg div.info-error{padding:7px; background-color:#EAA9AA; border:1px solid silver; border-radius:5px; font-size:12px; line-height:16px }
	div.s3-err-msg div.info-notice{padding:7px; background-color:#FCFEC5; border:1px solid silver; border-radius:5px; font-size:12px; line-height:16px;}
	table.s3-final-step {width:100%;}
	table.s3-final-step td {padding:5px 15px 5px 5px;font-size:14px; }
	table.s3-final-step td:first-child {white-space:nowrap; width:165px}
	div.s3-go-back {border-bottom:1px dotted #dfdfdf; border-top:1px dotted #dfdfdf; margin:auto; text-align:center}
	div.s3-btns-msg {text-align: center; font-size:10px; color:#777; margin:5px 0 15px 0}
	a.s3-final-btns {display: block; width:135; padding:5px; line-height: 1.4; background-color:#F1F1F1; border:1px solid silver;
		color: #000; box-shadow: 5px 5px 5px -5px #949494; text-decoration: none; text-align: center; border-radius: 4px;
	}
	a.s3-final-btns:hover {background-color: #dfdfdf;}
	
	/* ============================
	STEP 4 HELP */
	select#help-lnk {border-radius:3px; font-size:11px; margin:3px 5px 0 0; background-color:#efefef; border:1px solid silver; width:125px}
	div.help {color:#555; font-style:italic; font-size:11px; padding:4px; border-top:1px solid #dfdfdf}
	div.help-page {padding:5px 0 0 5px}
	div.help-page fieldset {margin-bottom:25px}
	div#main-help h3 {background-color:#dfdfdf; border:1px solid silver; border-radius:4px; padding:5px; margin:20px 0 8px 0; font-size:18px}

	/*!
	 * password indicator
	 */
	.top_testresult{font-weight:bold;	font-size:11px; color:#222;	padding:1px 1px 1px 4px; margin:4px 0 0 0; width:495px; dislay:inline-block}
	.top_testresult span{margin:0;}
	.top_shortPass{background:#edabab; border:1px solid #bc0000;display:block;}
	.top_badPass{background:#edabab;border:1px solid #bc0000;display:block;}
	.top_goodPass{background:#ffffe0; border:1px solid #e6db55;	display:block;}
	.top_strongPass{background:#d3edab;	border:1px solid #73bc00; display:block;}

	/*================================================
	PARSLEY:Overrides*/
	input.parsley-error, textarea.parsley-error, select.parsley-error {
	  color:#B94A48 !important;
	  background-color:#F2DEDE !important;
	  border:1px solid #EED3D7 !important;
	}
	ul.parsley-errors-list {margin:1px 0 0 -40px; list-style-type:none; font-size:10px}
</style>