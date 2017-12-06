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
<?php
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="index.php";</script>';
	exit(0);
}
function randString(){
	$str = "";
	$char = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	for ($i = 0; $i < 6; $i++) {
		$str .= $char[mt_rand(0, 61)];
	}
	return $str;
}
ini_set('upload_tmp_dir','/var/tmp/');
$target = '/var/tmp/';
$target = $target.randString().'.conf';
if($_FILES['file']['error']>0){
	echo "Return Code: ".$_FILES['file']['error'];
	exit;
} else {
	exec('confPhp status',$output,$return_status);
	switch ($return_status)
	{	
	case 1:
		echo "Last save or restore has not finished!";
		break;
	case 2:
		echo "Need to reboot after last restore!";
		break;	
	default:
		if(move_uploaded_file($_FILES['file']['tmp_name'], $target)){
			exec('confPhp restore "'.$target.'"',$output,$return_restore);
			if ($return_restore==-1) echo "Error when to restore configuraion!";
			else {
				sleep(1);
				do {
					sleep(1);
					exec('confPhp status',$output,$return_var);
				} while ($return_var==1);
			}
		}
		else { echo "Error when to restore configuraion!"; }
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- $Id: header.php 3167 2010-03-03 18:11:27Z slemoine $ -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>XFINITY</title>
	<!--CSS-->
	<link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/common-min.css" />
	<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="./cmn/css/ie6-min.css" />
	<![endif]-->
	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="./cmn/css/ie7-min.css" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" media="print" href="./cmn/css/print.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/lib/jquery.radioswitch.css" />
	<!--Character Encoding-->
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<script type="text/javascript" src="./cmn/js/lib/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery-migrate-1.2.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.alerts.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.alerts.progress.js"></script>
</head>
<body>
<script type="text/javascript">
$(window).load(function() {
	if(2 == "<?php echo $return_var; ?>"){	//Need Reboot to restore the saved configuration.
		var info = new Array("btn1", "Device");
		var jsonInfo = '["' + info[0] + '","' + info[1]+ '"]';
		jProgress("Please wait for rebooting ...", 999999);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_Reset_Restore.php",
			data: { resetInfo: jsonInfo }
		});
		setTimeout(checkForRebooting, 4 * 60 * 1000);
	}
});
function checkForRebooting() {
	$.ajax({
		type: "GET",
		url: "index.php",
		timeout: 10000,
		success: function() {
			/* goto login page */
			window.open ("index.php");
			setTimeout(window.close(),1000);
		},
		error: function() {
			/* retry after 2 minutes */
			setTimeout(checkForRebooting, 30 * 1000);
		}
	});
}
</script>
    <!--Main Container - Centers Everything-->
	<div id="container">
		<div id="main-content">
		<?php
		switch ($return_var) {
		case -1:
			echo "<h3>Error, get restore status failure</h3>";
			break;
		case 2:
			echo "<h3>Need Reboot to restore the saved configuration.</h3>";
			break;
		case 3:
			echo "<h3>Error, restore configuration failure!</h3>";
			break;
		default:
			echo "<h3>Restore configuration Failure! Please try later. </h3>";
			break;
		}
		?>
		</div>
	</div>
</body>
</html>