<?php die(); ?>
<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2018 RDK Management

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/
?>
<?php
include_once __DIR__ .'/CSRF-Protector-PHP/libs/csrf/csrfprotector_rdkb.php';
//Initialise CSRFGuard library
csrfprotector_rdkb::init();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
	session_start();
	if (!isset($_SESSION["loginuser"])) {
		echo '<script type="text/javascript">alert("Please Login First!"); location.href="index.php";</script>';
		exit(0);
	}
?>
<head>
    <title>XFINITY</title>
	<!--CSS-->
	<link rel="stylesheet" type="text/css" media="screen" href="cmn/css/common-min.css" />
	<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="cmn/css/ie6-min.css" />
	<![endif]-->
	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="cmn/css/ie7-min.css" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" media="print" href="cmn/css/print.css" />
	<!--Character Encoding-->
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <script type="text/javascript" src="./cmn/js/lib/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery-migrate-1.2.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.validate.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.alerts.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.alerts.progress.js"></script>
	<script type="text/javascript" src="./cmn/js/utilityFunctions.js"></script>
    <script type="text/javascript" src="./cmn/js/comcast.js"></script>
	<script type="text/javascript">
     $(document).ready(function() { 
	 	$('#restoreBtn').click(function(e){
		e.preventDefault();
		jConfirm(
		"Alert: Click 'OK' would lost your current configuration ! \nAre you sure you want to restore saved configuration?"
		,"Restore Saved Configuration"
		,function(ret) {
		if(ret) { 
			var path=document.getElementById('id1').value;
			if((path==null || path=="")){
				alert("Please Select a file to Restore the Configuration!");
			}
			else{
				$('form').submit();
			}
		} } );
	 });
	 $("#id1").focus();
	 });
	</script>
</head>
<body style="background-color: #ffffff;">
	<form enctype="multipart/form-data" action="at_mycomputer_upload.php" method="post">
		<input id="id1" name="file" type="file" style="border: solid 1px;">   </input>
		</br>
		</br>
		<input id="restoreBtn" type="button" value="Restore"> </input>
	</form>
</body>
</html>
