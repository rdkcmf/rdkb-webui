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
/*
 * Set the Locale for the Web UI based on the LANG setting or current linux locale
 */
$locale = getenv("LANG");
if(isset($locale)) {
    if(!isset($_SESSION['language'])){
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
?>
<?php include('includes/utility.php'); ?>
<?php
    $apipath = "fwd=api/v1/configurator-initiate-dpp";
    if( ($_SERVER["QUERY_STRING"] == $apipath) || ($_SERVER["QUERY_STRING"] == $apipath.'/') ) {
        header("Location: http://".$_SERVER['HTTP_HOST']."/api.php?REQUEST_METHOD=".$_SERVER["REQUEST_METHOD"]."&REQUEST_BODY=".urlencode(file_get_contents('php://input')));
    }
?>
<?php
        header('X-robots-tag: noindex,nofollow');
	$CONFIGUREWIFI			= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_ConfigureWiFi");
	//$CloudUIEnable		= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CloudUIEnable");
	//$CloudUIWebURL		= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CloudUIWebURL");
	$CaptivePortalEnable	= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CaptivePortalEnable");
$DeviceControl_param = array(
	"LanGwIPv4"	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress",
	"lanMode"	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode",
	"psmMode"	=> "Device.X_CISCO_COM_DeviceControl.PowerSavingModeStatus",
	);
$enableRFCpativePortal = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_RFC.Feature.CaptivePortalForNoCableRF.Enable");
$cableRFSignalStatus = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CableRfSignalStatus");
$modelName= getStr("Device.DeviceInfo.ModelName");
$wan_enabled=getStr("Device.Ethernet.X_RDKCENTRAL-COM_WAN.Enabled");
$allowEthWanMode= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.AllowEthernetWAN");
$DeviceControl_value = KeyExtGet("Device.X_CISCO_COM_DeviceControl.", $DeviceControl_param);
$url = $_SERVER['HTTP_HOST'];
$Wan_IPv4 = getStr("Device.X_CISCO_COM_CableModem.IPAddress");
$Wan_IPv6 = getStr("Device.X_CISCO_COM_CableModem.IPv6Address");
//if user is entering literal IPv6 address then remove "[" and "]"
$url = str_replace("[","",$url);
$url = str_replace("]","",$url);
if((!strcmp($url, $Wan_IPv4) || ((inet_pton($url)!="") || (inet_pton($Wan_IPv6!==""))) &&(($Wan_IPv6!="") &&(inet_pton($url) == inet_pton($Wan_IPv6) )))) {
	$isMSO  = true;
}
else {
	$isMSO  = false;
}
$lanMode = $DeviceControl_value['lanMode'];
$psmMode = $DeviceControl_value['psmMode'];
/*-------- redirection logic - uncomment the code below while checking in --------*/
	//$LanGwIPv4
	$LanGwIPv4 = $DeviceControl_value['LanGwIPv4'];
	//$LanGwIPv6
	$interface = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstDownstreamIpInterface");
	$idArr = explode(",", getInstanceIds($interface."IPv6Address."));
	foreach ($idArr as $key => $value) {
		$ipv6addr = getStr($interface."IPv6Address.$value.IPAddress");
		if (stripos($ipv6addr, "fe80::") !== false) {
			$LanGwIPv6 = $ipv6addr;
		}
		else{
			$LanGwIPv6 = $ipv6addr;
		}
	}
if(!$isMSO) {
        setStr("Device.DeviceInfo.X_RDKCENTRAL-COM_UI_ACCESS","ui_access",true);
	//If Cloud redirection is set, then everything through local GW should be redirected
	/*--if(!strcmp($Cloud_Enabled, "true"))	
	{
		header("Location: $Cloud_WebURL");
		exit;
	}*/
	if(!strcmp($CaptivePortalEnable, "true") && (PREPAID == false) ) {
		
		$SERVER_ADDR = $_SERVER['SERVER_ADDR'];
		$ip_addr = strpos($SERVER_ADDR, ":") == false ? $LanGwIPv4 : $LanGwIPv6 ;
		$SecWebUI = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_RFC.Feature.SecureWebUI.Enable");
		$LocFqdn = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_RFC.Feature.SecureWebUI.LocalFqdn");
		if(strcmp($url,$ip_addr)){
			if(($enableRFCpativePortal=="true") && ($cableRFSignalStatus=="false") && !($wan_enabled=="true") &&($modelName!="X5001")){
				if (!strcmp($SecWebUI, "true")) {
                                        header('Location:https://'.$LocFqdn.'/no_rf_signal.php');
                                }
                                else {
                                        header('Location:http://'.$ip_addr.'/no_rf_signal.php');
                                }
				exit;
			}
		}
		if(!strcmp($CONFIGUREWIFI, "true")) {
			if (!strcmp($SecWebUI, "true")) {
                                header('Location:https://'.$LocFqdn.'/captiveportal.php');
                        }
                        else {
                               header('Location:http://'.$ip_addr.'/captiveportal.php');
                        }
			exit;
		}
	}
}
?>
<?php
//----------Ported from includes/header.php for new login page
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
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
$title = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.LocalUI.MSOLogoTitle");
$msoLogo = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.LocalUI.MSOLogo");
$logo = "cmn/syndication/img/".$msoLogo;
$partnersId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
if (strpos($partnersId, "sky-") !== false) {
    $battery = FALSE;
    $MoCA = FALSE;
    $voice_Dig = TRUE;
} else {
    $battery = TRUE;
    $MoCA = TRUE;
    $voice_Dig = FALSE;
}
?>
<head>
	<title><?php echo $title; ?></title>
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
	<link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/lib/progressBar.css" />
	<!--Character Encoding-->
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta name="robots" content="noindex,nofollow">
	<script type="text/javascript" src="./cmn/js/lib/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery-migrate-1.2.1.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.validate.js"></script>
	<script type="text/javascript" src="<?php 
	    if((isset($locale)&&($locale!="")) && !strstr($locale, 'en')) { 
	        echo './locales/'.$locale.'/cmn/js/lib/jquery.alerts.js';
	    } else {
	        echo './cmn/js/lib/jquery.alerts.js';
	    }?>"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.ciscoExt.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.highContrastDetect.js"></script>
	<script type="text/javascript" src="<?php 
	    if((isset($locale)&&($locale!="")) && !strstr($locale, 'en')) { 
	        echo './locales/'.$locale.'/cmn/js/lib/jquery.radioswitch.js';
	    } else {
	        echo './cmn/js/lib/jquery.radioswitch.js';
	    }?>"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.virtualDialog.js"></script>
	<script type="text/javascript" src="<?php 
	    if((isset($locale)&&($locale!="")) && !strstr($locale, 'en')) { 
	        echo './locales/'.$locale.'/cmn/js/utilityFunctions.js';
	    } else {
	        echo './cmn/js/utilityFunctions.js';
	    }?>"></script>
    <script type="text/javascript" src="<?php 
        if((isset($locale)&&($locale!="")) && !strstr($locale, 'en')) {
            echo './locales/'.$locale.'/cmn/js/gateway.js';
        } else {
            echo './cmn/js/gateway.js';
        }?>"></script>	
	<script type="text/javascript" src="./cmn/js/lib/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php 
    if((isset($locale)&&($locale!="")) && !strstr($locale, 'en')) { 
        echo './locales/'.$locale.'/cmn/js/lib/bootstrap-waitingfor.js';
    } else {
        echo './cmn/js/lib/bootstrap-waitingfor.js';
    }?>"></script>

	<style>

		.helix{
			margin-top: 0px !important;
		}
	
	</style>
</head>
<body>
	<!--Main Container - Centers Everything-->
	<div id="container">
		<!--Header-->
		<div id="header">
			<h2 id="logo"><?php echo "<img src='".$logo."' alt='".$title."'  title='".$title."' />"; ?></h2>
		</div> <!-- end #header -->
		<div id='div-skip-to' style="display: none;">
			<a id="skip-link" name="skip-link" href="#content">Skip to content</a>
		</div>
		<!--Main Content-->
		<div id="main-content">
<?php
//----------End Header code
?>
<!-- $Id: at_a_glance.dory.php 2943 2009-08-25 20:58:43Z slemoine $ -->
<div id="sub-header">
</div><!-- end #sub-header -->
<?php
//Old Nav Bar. Put new login here.
//include('includes/nav.php');
?>



<h1><?php echo _('Gateway > Login')?></h1>
<div style="float: left; margin: 0 20px 20px 0; width: 60%; height:290px;background:white;">

	<form action="check.php" method="post" id="pageForm"  onsubmit="return f();">
	<div class="form-row">
		<p><?php echo _('Please login to view and manage your Gateway settings.')?></p>
	</div>
	<div>
		<table style="background:white; text-align:center;">
			<tr>
				<td><label for="username"><b><?php echo _('Username:')?></b></label></td>
				<td><input type="text"     id="username" name="username" style="width: 250px;" class="text" autocomplete="off" /></td>
			</tr>
			<tr>
				<td><label for="password"><b><?php echo _('Password:')?></b></label></td>
				<td><input type="password" id="password" name="password" style="width: 250px;" class="text" autocomplete="off" /></td>
			</tr>
		</table>
	</div>
	<div class="form-btn" style="margin-top: 25px;text-align:center;">
		<input type="submit" class="btn" value="<?php echo _('Login')?>" />
	</div>
</form>
</div>
<script type="text/javascript">
$(document).ready(function() {

	var logo= "<?php echo $msoLogo;  ?>";
	if(logo.indexOf('videotron')!==-1){
	
		$('#logo').addClass("helix");
	
	}

	var user_type = "<?php echo ($isMSO)?'mso':'admin'; ?>";
        var partner_id = '<?php echo getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId"); ?>';
	gateway.page.init("Login", "nav-login");
	$("#pageForm").validate({
		errorElement : "p"
		,errorContainer : "#error-msg-box"
		,invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				var message = errors == 1 ? '<?php echo _("You missed 1 field. It has been highlighted")?>' : '<?php echo _("You missed") ?> ' + errors + ' <?php echo _("fields. They have been highlighted")?>';
				$("div.error").html(message);
				$("div.error").show();
			} else {
				$("div.error").hide();
			}
		}
		,rules : {
			username: {
				required: true
				,minlength: 3
			}
			,password: {
				required: true
				,minlength: 3
			}
		}
		,messages: {
			username: {
				required: "<?php echo _('Username cannot be blank. Please enter a valid username.')?>"
			}
			,password: {
				required: "<?php echo _('Password cannot be blank. Please enter a valid password.')?>"
				,minlength: "<?php echo _('Password must be at least 3 characters.')?>"
			}
		}
	});
	$("#username").focus();
	$("#username").val("");
	$("#password").val("");
});
function f()
{
	var username;
	username = document.getElementById("username");
	username.value = (username.value.toLowerCase());
	//get the form id and submit it
	var form = document.getElementById("pageForm");
	form.submit();
	return true;
}
</script>
<?php include('includes/footer.php'); ?>
