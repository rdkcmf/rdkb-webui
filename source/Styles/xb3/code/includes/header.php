<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
	session_start();
	if (!isset($_SESSION["loginuser"])) {
		echo '<script type="text/javascript">alert("Please Login First!"); location.href="home_loggedout.php";</script>';
		exit(0);
	}
	$not_cusadmin_pages = array('email_notification.php', 'hs_port_forwarding', 'routing.php');
	$not_admin_pages = array('email_notification.php', 'hs_port_forwarding', 'routing.php', 'dynamic_dns', 'mta');
	if ($_SESSION['loginuser'] == 'cusadmin') {
		foreach ($not_cusadmin_pages as $page) {
			if (strstr($_SERVER['SCRIPT_FILENAME'], $page)) {
				echo '<script type="text/javascript"> alert("Access Denied!"); window.history.back(); </script>';
				exit(0);	
			}
		}
	}
	if ($_SESSION['loginuser'] == 'admin') {
		foreach ($not_admin_pages as $page) {
			if (strstr($_SERVER['SCRIPT_FILENAME'], $page)) {
				echo '<script type="text/javascript"> alert("Access Denied!"); window.history.back(); </script>';
				exit(0);	
			}
		}
	}
	/* demo flag in session */
	if (!isset($_SESSION['_DEBUG'])) {
		$_DEBUG = file_exists('/var/ui_dev_debug');
		$_SESSION['_DEBUG'] = $_DEBUG;
	}
	else {
		$_DEBUG = $_SESSION['_DEBUG'];
	}
	// disable timeout when debug mode
	if ($_DEBUG) { $_SESSION["timeout"] = 100000; }
	$header_param = array(
		"Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode",
		"Device.X_CISCO_COM_DeviceControl.PowerSavingModeStatus"
	);
	$header_value = DmExtGetStrsWithRootObj("Device.X_CISCO_COM_DeviceControl.", $header_param);
	$lanMode 	= $header_value[1][1];
	$psmMode 	= $header_value[2][1];
    /*
    ** is GW works in Bridge mode or not
    */
	$lanMode = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode");
	// $lanMode = 'bridge-static';
	if ("bridge-static" != $lanMode && "router" != $lanMode){
		$lanMode = "router";
	}
	// doc lanMode into session, for directly use it in function
	$_SESSION["lanMode"] = $lanMode;
    /*
    ** is GW works in PSM mode or not
    */
	$psmMode = getStr("Device.X_CISCO_COM_DeviceControl.PowerSavingModeStatus");
	// $psmMode = "Enabled";
	if ("Enabled" != $psmMode && "Disabled" != $psmMode){
		$psmMode = "Disabled";
	}
	// doc psmMode into session, for directly use it in function
	$_SESSION["psmMode"] = $psmMode;
?>
<head>
	<title>Xfinity</title>
	<!--CSS-->
	<link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/common-min.css?sid=<?php echo $_SESSION["sid"]; ?>" />
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
    <script type="text/javascript" src="./cmn/js/lib/jquery.validate.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.alerts.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.alerts.progress.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.ciscoExt.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.highContrastDetect.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.radioswitch.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.virtualDialog.js"></script>
	<script type="text/javascript" src="./cmn/js/utilityFunctions.js"></script>
    <script type="text/javascript" src="./cmn/js/comcast.js"></script>
	<style>
	#div-skip-to {
		position:relative; 
		left: 150px;
		top: -300px;
	}
	#div-skip-to a {
		position: absolute;
		top: 0;
	}
	#div-skip-to a:active, #div-skip-to a:focus {
		top: 300px;
		color: #0000FF;		
		/*background-color: #b3d4fc;*/
	}
	</style>	
</head>
<body>
    <!--Main Container - Centers Everything-->
	<div id="container">
		<!--Header-->
		<div id="header">
			<?php
				if($lanMode != "router")
					echo '<p style="margin: 0">The Device is currently in Bridge Mode.</p>';
				else
					echo '<p style="margin: 0">&nbsp;</p>';
			?>
			<h2 id="logo" style="margin-top: 10px"><img src="./cmn/img/logo_xfinity.png" alt="Xfinity" title="Xfinity" /></h2>
		</div> <!-- end #header -->
		<div id='div-skip-to' style="display: none;">
			<a id="skip-link" name="skip-link" href="#content">Skip to content</a>
		</div>
		<!--Main Content-->
		<div id="main-content">
