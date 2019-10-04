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
include_once __DIR__ .'/../CSRF-Protector-PHP/libs/csrf/csrfprotector_rdkb.php';
//Initialise CSRFGuard library
csrfprotector_rdkb::init();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
        header('X-robots-tag: noindex,nofollow');

	session_start();
	
	/*
	 * Set the Locale for the Web UI based on the LANG setting or current linux locale
	 */
	$locale = getenv("LANG");
	if(isset($locale)) {
	    if(!isset($_SESSION['language']) || setlocale(LC_MESSAGES, 0) != $locale){
    	    //putenv("LANG=" . $locale);
    	    setlocale(LC_MESSAGES, $locale);
    	    setlocale(LC_TIME, $locale);
    	    
    	    $domain = "rdkb";
    	    bindtextdomain($domain, 'locales');
    	    bind_textdomain_codeset($domain, 'UTF-8');
    	    textdomain($domain);
    	    $_SESSION['language'] = $locale; // set the default locale for future pages
	   }
    }
    
	if (!isset($_SESSION["loginuser"])) {
		echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="home_loggedout.php";</script>';
		exit(0);
	}
	$not_admin_pages = array('email_notification.php', 'hs_port_forwarding', 'routing.php', 'dynamic_dns', 'mta', 'voice_quality_metrics' ,'qos');
	$not_mso_pages = array('change_password.php');
	$not_bridge_static_pages = array('local_ip', 'wizard', 'firewall', 'managed', 'parental', 'forwarding', 'triggering', 'dmz', 'routing', 'nat', 'dynamic_dns', 'device_discovery', 'radius_servers', 'dlna', 'digital_media', 'samba', 'local_users', 'wps', 'wifi_spectrum_analyzer');
	if ($_SESSION['loginuser'] == 'admin') {
		foreach ($not_admin_pages as $page) {
			if (strstr($_SERVER['SCRIPT_FILENAME'], $page)) {
				echo '<script type="text/javascript"> alert("'._("Access Denied!").'"); window.history.back(); </script>';
				exit(0);	
			}
		}
	}
	else if ($_SESSION['loginuser'] == 'mso') {
		foreach ($not_mso_pages as $page) {
			if (strstr($_SERVER['SCRIPT_FILENAME'], $page)) {
				echo '<script type="text/javascript"> alert("'._("Access Denied!").'"); window.history.back(); </script>';
				exit(0);	
			}
		}
	}
	if (isset($_SESSION['lanMode']) && $_SESSION["lanMode"] == "bridge-static") {
		foreach ($not_bridge_static_pages as $page) {
			if (strstr($_SERVER['SCRIPT_FILENAME'], $page)) {
				echo '<script type="text/javascript"> alert("'._("Access Denied!").'"); window.history.back(); </script>';
				exit(0);	
			}
		}
	}
	/* demo flag in session */
	/*if (!isset($_SESSION['_DEBUG'])) {
		$_DEBUG = file_exists('/var/ui_dev_debug');
		$_SESSION['_DEBUG'] = $_DEBUG;
	}
	else {
		$_DEBUG = $_SESSION['_DEBUG'];
	}*/
	// disable timeout when debug mode
	//if ($_DEBUG) { $_SESSION["timeout"] = 100000; }
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
	// $lanMode = 'bridge-static';
	if ("bridge-static" != $lanMode && "router" != $lanMode){
		$lanMode = "router";
	}
	// doc lanMode into session, for directly use it in function
	$_SESSION["lanMode"] = $lanMode;
    /*
    ** is GW works in PSM mode or not
    */
	// $psmMode = "Enabled";
	if ("Enabled" != $psmMode && "Disabled" != $psmMode){
		$psmMode = "Disabled";
	}
	// doc psmMode into session, for directly use it in function
	$_SESSION["psmMode"] = $psmMode;
	$title=getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.LocalUI.MSOLogoTitle");
	$msoLogo= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.LocalUI.MSOLogo");
	$logo="cmn/syndication/img/".$msoLogo;
	$partnerId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
?>
<head>
	<title><?php echo $title; ?></title>
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
	<link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/lib/progressBar.css" />
	<!--Character Encoding-->
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta name="robots" content="noindex,nofollow">
    <script type="text/javascript" src="./cmn/js/lib/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery-migrate-1.2.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.validate.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.alerts.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.ciscoExt.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.highContrastDetect.js"></script>
	<script type="text/javascript" src="<?php 
	    if((isset($locale)&&($locale!="")) && !strstr($locale, 'en')) { 
	        echo './locales/'.$locale.'/cmn/js/lib/jquery.radioswitch.js';
	    } else {
	        echo './cmn/js/lib/jquery.radioswitch.js';
	    }?>"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.virtualDialog.js"></script>
	<script type="text/javascript" src="./cmn/js/utilityFunctions.js"></script>
    <script type="text/javascript" src="<?php 
        if((isset($locale)&&($locale!="")) && !strstr($locale, 'en')) {
            echo './locales/'.$locale.'/cmn/js/gateway.js';
        } else {
            echo './cmn/js/gateway.js';
        }?>"></script>
    <script type="text/javascript" src="./cmn/js/lib/bootstrap.min.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/bootstrap-waitingfor.js"></script>
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
<script type="text/javascript">
        var partner_id = '<?php echo $partnerId; ?>';
	$(document).ready(function() {
		$("table.data td").each(function() {
			if($(this).text().split("\n")[0].length > 25)
			{
				if(!partner_id.includes('sky-')|| $(this).attr('headers') == 'server-ipv6')
				   $(this).closest('table').css("table-layout", "fixed");
				$(this).css("word-wrap", "break-word");
			}
		});
	});
</script>
<body>
    <!--Main Container - Centers Everything-->
	<div id="container">
		<!--Header-->
		<div id="header">
			<?php
				if($lanMode != "router")
					echo '<p style="margin: 0">'._("The Device is currently in Bridge Mode.").'</p>';
				else
					echo '<p style="margin: 0">&nbsp;</p>';
			?>
			<h2 id="logo" style="margin-top: 10px"><?php echo "<img src='".$logo."' alt='".$title."'  title='".$title."' />"; ?></h2>
		</div> <!-- end #header -->
		<div id='div-skip-to' style="display: none;">
			<a id="skip-link" name="skip-link" href="#content"><?php echo _("Skip to content")?></a>
		</div>
		<!--Main Content-->
		<div id="main-content">
