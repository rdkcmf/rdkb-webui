<?php include('includes/utility.php'); ?>
<?php
session_start();
$DeviceInfo_param = array(
	"ConfigureWiFi"	=> "Device.DeviceInfo.X_RDKCENTRAL-COM_ConfigureWiFi",
	"CloudUIEnable"	=> "Device.DeviceInfo.X_RDKCENTRAL-COM_CloudUIEnable",
	"CloudUIWebURL"	=> "Device.DeviceInfo.X_RDKCENTRAL-COM_CloudUIWebURL",
	);
$DeviceInfo_value = KeyExtGet("Device.DeviceInfo.", $DeviceInfo_param);
$DeviceControl_param = array(
	"LanGwIPv4"	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress",
	"lanMode"	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode",
	"psmMode"	=> "Device.X_CISCO_COM_DeviceControl.PowerSavingModeStatus",
	);
$DeviceControl_value = KeyExtGet("Device.X_CISCO_COM_DeviceControl.", $DeviceControl_param);
$CONFIGUREWIFI	= $DeviceInfo_value["ConfigureWiFi"];
$Cloud_Enabled	= $DeviceInfo_value["CloudUIEnable"];
$Cloud_WebURL	= $DeviceInfo_value["CloudUIWebURL"];
$url = $_SERVER['HTTP_HOST'];
$Wan_IPv4 = getStr("Device.X_CISCO_COM_CableModem.IPAddress");
$Wan_IPv6 = getStr("Device.X_CISCO_COM_CableModem.IPv6Address");
//if user is entering literal IPv6 address then remove "[" and "]"
$url = str_replace("[","",$url);
$url = str_replace("]","",$url);
if(!strcmp($url, $Wan_IPv4) || !strcmp($url, $Wan_IPv6)){
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
	if(strstr($Cloud_Enabled, "true"))	
	{
		header("Location: $Cloud_WebURL");
		exit;
	}
	if(strstr($CONFIGUREWIFI, "true")) {
		$SERVER_ADDR = $_SERVER['SERVER_ADDR'];
		$ip_addr = strpos($SERVER_ADDR, ":") == false ? $LanGwIPv4 : $LanGwIPv6 ;
		header('Location:http://'.$ip_addr.'/captiveportal.php');
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
?>
<head>
	<title>Xfinity</title>
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
			<h2 id="logo"><img src="./cmn/img/logo_xfinity.png" alt="Xfinity" title="Xfinity" /></h2>
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
<?php
//----------Ported from userbar.php for new index page
?>
	<!--dynamic generate user bar icon and tips-->
	<?php
	$a = getStr("Device.X_CISCO_COM_MTA.Battery.RemainingCharge");
	$b = getStr("Device.X_CISCO_COM_MTA.Battery.ActualCapacity");
	$sta_batt = ($a<=$b && $a && $b) ? round(100*$a/$b) : 0;
	//$sta_batt = "61";
	//find battery class manually
	if($sta_batt > 90) { $battery_class = "bat-100"; }
	elseif($sta_batt > 60) { $battery_class = "bat-75"; }
	elseif($sta_batt > 39) { $battery_class = "bat-50"; }
	elseif($sta_batt > 18) { $battery_class = "bat-25"; }
	elseif($sta_batt > 8) { $battery_class = "bat-10"; }
	else { $battery_class = "bat-0"; }
	$fistUSif = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");
	$WANIPv4 = getStr($fistUSif."IPv4Address.1.IPAddress");
	$ids = explode(",", getInstanceIds($fistUSif."IPv6Address."));
	foreach ($ids as $i){
		$val = getStr($fistUSif."IPv6Address.$i.IPAddress");
		if (!strstr($val, "fe80::")){
			$WANIPv6 = $val;
			break;
		}
	}
	$sta_inet = ($WANIPv4 != "0.0.0.0" || strlen($WANIPv6) > 0) ? "true" : "false";
	//in Bridge mode > Internet connectivity status is always active
	$sta_inet = ($_SESSION["lanMode"] == "bridge-static") ? "true" : $sta_inet ;
	$sta_wifi = "false";
	if("Disabled"==$_SESSION["psmMode"]){
		$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));
		foreach ($ssids as $i){
			$r = (2 - intval($i)%2);	//1,3,5,7==1(2.4G); 2,4,6,8==2(5G)
			if ("true" == getStr("Device.WiFi.Radio.$r.Enable") && "true" == getStr("Device.WiFi.SSID.$i.Enable")){	//bwg has radio.enable, active status is “at least one SSID and its Radio is enabled”
				$sta_wifi = "true";
				break;
			}
		}
	}
	if("Disabled"==$_SESSION["psmMode"]) { $sta_moca = getStr("Device.MoCA.Interface.1.Enable"); }
	//$sta_dect = getStr("Device.X_CISCO_COM_MTA.Dect.Enable");
	$sta_fire = getStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevel");
	$_SESSION['sta_inet'] = $sta_inet;
	$_SESSION['sta_wifi'] = $sta_wifi;
	$_SESSION['sta_moca'] = $sta_moca;
	$_SESSION['sta_fire'] = $sta_fire;
	$_SESSION['sta_batt'] = $sta_batt;
	$_SESSION['battery_class'] = $battery_class;
	//$sta_batt = "58";
	//$sta_inet = "true";
	//$sta_wifi = "false";
	//$sta_moca = "true"; //true
	//$sta_dect = "false"; //false
	//$sta_fire = "Low"; //Medium Low High
	?>
	<script type="text/javascript">
	$(document).ready(function() {
		var sta_batt = "<?php echo $sta_batt; ?>";
		var sta_inet = "<?php echo $sta_inet; ?>";
		var sta_wifi = "<?php echo $sta_wifi; ?>";
		var sta_moca = "<?php echo $sta_moca; ?>";
		var sta_fire = "<?php echo $sta_fire; ?>";
		/*
		* get status when hover or tab focused one by one
		* but for screen reader we have to load all status once
		* below code can easily rollback
		*/
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_userbar.php",
			data: { configInfo: "noData" },
			dataType: "json",
			success: function(msg) {
				// theObj.find(".tooltip").html(msg.tips);
				for (var i=0; i<msg.tags.length; i++){
					$("#"+msg.tags[i]).find(".tooltip").html(msg.tips[i]);
				}
			},
			error: function(){
				// does something
			}
		});
		// });
		// show pop-up info when focus
		$("#status a").focus(function() {
			$(this).mouseenter();
		});
		// disappear previous pop-up
		$("#status a").blur(function() {
			$(".tooltip").hide();
		});
	});
</script>
<style>
	#status a:link, #status a:visited {
		text-decoration: none;
		color: #808080;
	}
</style>
<ul id="status">
	<?php
	echo '<li id="sta_batt" class="battery first-child"><div class="sprite_cont"><span class="'.$battery_class.'" ><img src="./cmn/img/icn_battery.png"  alt="Battery icon" title="Battery icon" /></span></div><a role="toolbar" href="javascript: void(0);" tabindex="0">'.$sta_batt.'%</a>
		<!-- NOTE: When this value changes JS will set the battery icon -->
	</li>';
	if ("true"==$sta_inet) {
		echo '<li id="sta_inet" class="internet"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Internet Online" /></span><a href="javascript: void(0);" tabindex="0">Internet<div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_inet" class="internet off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Internet Offline" /></span><a href="javascript: void(0);" tabindex="0">Internet<div class="tooltip">Loading...</div></a></li>';
	}
	if ("true"==$sta_wifi) {
		echo '<li id="sta_wifi" class="wifi"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Wi-Fi Online" /></span><a href="javascript: void(0);" tabindex="0">Wi-Fi<div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_wifi" class="wifi off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Wi-Fi Offline" /></span><a href="javascript: void(0);" tabindex="0">Wi-Fi<div class="tooltip">Loading...</div></a></li>';
	}
	if ("true"==$sta_moca) {
		echo '<li id="sta_moca" class="MoCA"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="MoCA Online" /></span><a href="javascript: void(0);" tabindex="0">MoCA<div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_moca" class="MoCA off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="MoCA Offline" /></span><a href="javascript: void(0);" tabindex="0">MoCA<div class="tooltip">Loading...</div></a></li>';
	}
	/*if ("true"==$sta_dect) {
		echo '<li id="sta_dect" class="DECT"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="DECT Online" /></span><a href="javascript: void(0);" tabindex="0">DECT<div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_dect" class="DECT off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="DECT Offline" /></span><a href="javascript: void(0);" tabindex="0">DECT<div class="tooltip">Loading...</div></a></li>';
	}*/
	if (("High"==$sta_fire) || ("Medium"==$sta_fire)) {
		echo '<li id="sta_fire" class="security last"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Security On" /></span><a href="javascript: void(0);" tabindex="0"><span>'.$sta_fire.' Security</span><div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_fire" class="security last off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Security Off" /></span><a href="javascript: void(0);" tabindex="0"><span>'.$sta_fire.' Security</span><div class="tooltip">Loading...</div></a></li>';
	}
	?>
</ul>
<?php
//----------End port of userbar code for new index page
?>
</div><!-- end #sub-header -->
<?php
//Old Nav Bar. Put new login here.
//include('includes/nav.php');
?>
<!--div id="nav"-->
<div style="float: left; margin: 0 20px 20px 0; width: 182px;">
	<form action="check.php" method="post" id="pageForm"  onsubmit="return f();">
	<div>
		<table>
			<tr>
				<td><label for="username"><b>Username:</b></label></td>
				<td><input type="text"     id="username" name="username" style="width: 120px;" class="text" autocomplete="off" /></td>
			</tr>
			<tr>
				<td><label for="password"><b>Password:</b></label></td>
				<td><input type="password" id="password" name="password" style="width: 120px;" class="text" autocomplete="off" /></td>
			</tr>
		</table>
	</div>
	<div class="form-btn">
		<input type="submit" class="btn" value="Login" />
	</div>
</form>
</div>
<script type="text/javascript">
$(document).ready(function() {
	comcast.page.init("Login", "nav-login");
	$("#pageForm").validate({
		errorElement : "p"
		,errorContainer : "#error-msg-box"
		,invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				var message = errors == 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
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
				required: "Username cannot be blank. Please enter a valid username."
			}
			,password: {
				required: "Password cannot be blank. Please enter a valid password."
				,minlength: "Password must be at least 3 characters."
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
<div id="content">
	<h1>Gateway > Login</h1>
	<div id="educational-tip">
		<p class="tip">Please login to view your Wi-Fi passkey or to view and edit detailed network settings.</p>
	</div>
<?php
	//Home Network WiFi Settings
	//Only do WiFi SSID check if we are not in power saving mode
	if ("Disabled"==$_SESSION["psmMode"]) {
		//Assumes that private network is always SSID's 1 and 2
		$wifi_param = array(
			"wifi_24_enabled"	=> "Device.WiFi.SSID.1.Enable",
			"wifi_24_ssid"		=> "Device.WiFi.SSID.1.SSID",
			"wifi_24_passkey"	=> "Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_KeyPassphrase",
			"wifi_50_enabled"	=> "Device.WiFi.SSID.2.Enable",
			"wifi_50_ssid"		=> "Device.WiFi.SSID.2.SSID",
			"wifi_50_passkey"	=> "Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_KeyPassphrase",
		);
		$wifi_value = KeyExtGet("Device.WiFi.", $wifi_param);
		$wifi_24_enabled 	= $wifi_value["wifi_24_enabled"];
		$wifi_24_ssid 		= $wifi_value["wifi_24_ssid"];
		$wifi_24_passkey 	= $wifi_value["wifi_24_passkey"];
		$wifi_50_enabled 	= $wifi_value["wifi_50_enabled"];
		$wifi_50_ssid 		= $wifi_value["wifi_50_ssid"];
		$wifi_50_passkey 	= $wifi_value["wifi_50_passkey"];
		//If at least one private SSID is enabled
		if ( $lanMode == "router" && ("true" == $wifi_24_enabled || "true" == $wifi_50_enabled) ) {
			echo '<div class="module block" id="wifi-config">';
				echo '<div>';
					echo '<h2>Wi-Fi Configuration</h2>';
				echo '</div>';
			//If both 2.4ghz and 5ghz ssid's and passkeys are the same, or only one is active, then just show one row
			if ((($wifi_24_ssid == $wifi_50_ssid) && ($wifi_24_passkey == $wifi_50_passkey)) || !("true" == $wifi_24_enabled && "true" == $wifi_50_enabled)) {
				//Figure out whice one is active
				if ("true" == $wifi_24_enabled) {
					$wifi_ssid = $wifi_24_ssid;
					$wifi_passkey = $wifi_24_passkey;
				} else {
					$wifi_ssid = $wifi_50_ssid;
					$wifi_passkey = $wifi_50_passkey;
				}
				if($isMSO) {
				echo '<div class="form-row even">';
					echo '<div class="form-row even">';
						echo '<span class="readonlyLabel">Wi-Fi SSID:</span>';
						echo '<span style="font-weight: bold;" class="value">'.$wifi_ssid.'</span>';
					echo '</div>';
				echo '</div>';
				}
				else
				{
				echo '<div class="form-row even">';
					echo '<div class="form-row even">';
						echo '<span class="readonlyLabel">Wi-Fi SSID:</span>';
						echo '<span style="font-weight: bold;" class="value">'.$wifi_ssid.'</span>';
					echo '</div>';
					echo '<div class="form-row even">';
						echo '<span class="readonlyLabel">Wi-Fi Passkey:</span>';
						echo '<span class="value">Log in to view passkey</span>';
					echo '</div>';
				echo '</div>';
				}
			//Else if they are both enabled and different SSID's or passkeys, we need 2 rows
			} else {
				if($isMSO) {
				echo '<div class="form-row even">';
					echo '<div class="form-row even">';
						echo '<span class="readonlyLabel">Wi-Fi SSID (2.4Ghz):</span>';
						echo '<span style="font-weight: bold;" class="value">'.$wifi_24_ssid.'</span>';
					echo '</div>';
				echo '</div>';
				echo '<div class="form-row odd">';
					echo '<div class="form-row even">';
						echo '<span class="readonlyLabel">Wi-Fi SSID (5Ghz):</span>';
						echo '<span style="font-weight: bold;" class="value">'.$wifi_50_ssid.'</span>';
					echo '</div>';
				echo '</div>';
				}
				else{
				echo '<div class="form-row even">';
					echo '<div class="form-row even">';
						echo '<span class="readonlyLabel">Wi-Fi SSID (2.4Ghz):</span>';
						echo '<span style="font-weight: bold;" class="value">'.$wifi_24_ssid.'</span>';
					echo '</div>';
					echo '<div class="form-row even">';
						echo '<span class="readonlyLabel">Wi-Fi Passkey (2.4Ghz):</span>';
						echo '<span class="value">Log in to view passkey</span>';
					echo '</div>';
				echo '</div>';
				echo '<div class="form-row odd">';
					echo '<div class="form-row even">';
						echo '<span class="readonlyLabel">Wi-Fi SSID (5Ghz):</span>';
						echo '<span style="font-weight: bold;" class="value">'.$wifi_50_ssid.'</span>';
					echo '</div>';
					echo '<div class="form-row odd">';
						echo '<span class="readonlyLabel">Wi-Fi Passkey (5Ghz):</span>';
						echo '<span class="value">Log in to view passkey</span>';
					echo '</div>';
				echo '</div>';
				}
			}
			echo '</div>';
		}
	} else {
	//Power Saving Mode is Enabled
		echo '<div class="module psm">';
			echo '<div class="select-row">';
				echo '<span class="readonlyLabel label">Power Saving Mode is enabled!</span>';
			echo '</div>';
		echo '</div>';
	}
	echo '<div class="module block" id="home-network">';
		echo '<div>';
			echo '<h2>Home Network</h2>';
			if ("Disabled"==$_SESSION["psmMode"]) {
				/*
				$InterfaceNumber=getStr("Device.Ethernet.InterfaceNumberOfEntries");$InterfaceEnable=0;
				for($i=1;$i<=$InterfaceNumber;$i++){
					$EthernetEnable=getStr("Device.Ethernet.Interface.".$i.".Enable");
					$InterfaceEnable+=($EthernetEnable=="true"?1:0);
				}
				if ($InterfaceEnable==$InterfaceNumber) {
					echo "<div class=\"form-row\"><span class=\"on-off\">On</span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				} else {
					echo "<div class=\"form-row off\"><span class=\"on-off\">Off</span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				}*/
				$ids = explode(",", getInstanceIds("Device.Ethernet.Interface."));
				$ethEnable = false;
				foreach ($ids as $i){
					if ("true" == getStr("Device.Ethernet.Interface.".$i.".Enable")){
						$ethEnable = true;
						break;
					}
				}
				if ($ethEnable) {
					echo "<div class=\"form-row\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Ethernet On' /></span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				} else {
					echo "<div class=\"form-row off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Ethernet Off' /></span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				}
				// if (getStr("Device.WiFi.SSID.1.Enable")=="true" || getStr("Device.WiFi.SSID.2.Enable")=="true") {
				if ("true" == $sta_wifi) {		// define in userhar, should have defined every componet status in userbar
					echo "<div class=\"form-row odd\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Wi-Fi On' /></span> <span class=\"readonlyLabel\">Wi-Fi</span></div>";
				} else {
					echo "<div class=\"form-row odd off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Wi-Fi Off' /></span> <span class=\"readonlyLabel\">Wi-Fi</span></div>";
				}
				if (getStr("Device.MoCA.Interface.1.Enable")=="true") {
					echo "<div class=\"form-row\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='MoCA On' /></span> <span class=\"readonlyLabel\">MoCA</span></div>";
				} else {
					echo "<div class=\"form-row off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='MoCA Off' /></span> <span class=\"readonlyLabel\">MoCA</span></div>";
				}
			}
			else {
				echo "<div class=\"form-row off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Ethernet Off' /></span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				echo "<div class=\"form-row odd off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Wi-Fi Off' /></span> <span class=\"readonlyLabel\">Wi-Fi</span></div>";
				echo "<div class=\"form-row off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='MoCA Off' /></span> <span class=\"readonlyLabel\">MoCA</span></div>";
			}
			?>
			<div class="form-row odd">
				<span class="readonlyLabel">Firewall Security Level:</span> <span class="value"><?php echo getStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevel")?></span>
			</div>
		</div>
	</div> <!-- end .module -->
	<div id="internet-usage" class="module form">
		<h2 style="margin-bottom: -5px;">Connected Devices</h2>
		<table class="data" summary="This table displays Online Devices connected">
		    <tr>
			<th style="background: #f85f01;" id="active-icon" ></th>
			<th style="background: #f85f01;" id="host-name" >Host Name</th>
			<th style="background: #f85f01;" id="mac-address" >MAC Address</th>
			<th style="background: #f85f01;" id="connection-type" >Connection Type</th>
		    </tr>
		<?php
		if ("Disabled"==$_SESSION["psmMode"]) {
			function ProcessLay1Interface($interface){
				if (stristr($interface, "WiFi")){
					if (stristr($interface, "WiFi.SSID.1")) {
						//$host['networkType'] = "Private";
						$host['connectionType'] = "Wi-Fi 2.4G";
					}
					elseif (stristr($interface, "WiFi.SSID.2")) {
						//$host['networkType'] = "Private";
						$host['connectionType'] = "Wi-Fi 5G";
					}
					else {
						//$host['networkType'] = "Public";
						$host['connectionType'] = "Wi-Fi";
					}
				}
				elseif (stristr($interface, "MoCA")) {
					$host['connectionType'] = "MoCA";
					//$host['networkType'] = "Private";
				}
				elseif (stristr($interface, "Ethernet")) {
					$host['connectionType'] = "Ethernet";
					//$host['networkType'] = "Private";
				} 
				else{
					$host['connectionType'] = "Unknown";
					//$host['networkType'] = "Private";
				}
		    	return $host;
			}
			$rootObjName    = "Device.Hosts.Host.";
			$paramNameArray = array("Device.Hosts.Host.");
			$mapping_array  = array("PhysAddress", "HostName", "Active", "Layer1Interface");
			$HostIndexArr = DmExtGetInstanceIds("Device.Hosts.Host.");
			if(0 == $HostIndexArr[0]){
				// status code 0 = success
				$HostNum = count($HostIndexArr) - 1;
			}
			if(!empty($HostNum)){
				$Host = getParaValues($rootObjName, $paramNameArray, $mapping_array);
				//this is to construct host info array
				$j = 1;
				if(!empty($Host)){
					foreach ($Host as $key => $value) {
						if (!strcasecmp("true", $value['Active'])) {
							$HostInfo[$j]['HostName']	= $value['HostName'];
							$HostInfo[$j]['Active']		= $value['Active'];
							$HostInfo[$j]['PhysAddress']	= $value['PhysAddress'];
							$HostInfo[$j]['Layer1Interface']  = $value['Layer1Interface'];
							$j += 1;
						}
					}// end of foreach
					//restrict the listing of connected devices to 10 or 15
					for($i=1; $i<$j && $i<16; $i++) {
						if( $i%2 ) {$divClass="class='form-row '";}
							else {$divClass="class='form-row odd'";}
						$HostName = $HostInfo[$i]['HostName'];
						$ConnectionType = ProcessLay1Interface($HostInfo[$i]['Layer1Interface']);
						$ConnectionType = $ConnectionType['connectionType'];
						if (($HostName == "*") || (strlen($HostName) == 0)) {
							//$HostName = strtoupper($HostInfo[$i]['PhysAddress']);
							$HostName = "";
						}
						echo "<tr $divClass>
							<td width='5%' class='readonlyLabel' headers='active-icon'><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Host On' /></span></td>
							<td width='40%' class='readonlyLabel' headers='host-name'>$HostName</td>
							<td width='' class='readonlyLabel' headers='mac-address'>".strtoupper($HostInfo[$i]['PhysAddress'])."</td>
							<td width='' class='readonlyLabel' headers='connection-type'>$ConnectionType</td>
						      </tr>
						";
					}//end of for
				}//end of empty $host
			}//end of if empty $hostnum
		}//end of psmMode condition
		?>
		</table>
		<?php if((isset($j)) && ($j > 15)) echo "<div>Maximum of 15 connected devices are listed. Please login to view all! </div>" ?>
	</div> <!-- end .module -->
	<!--div class="module">
		<div class="select-row">
			<span class="readonlyLabel label">IGMP Snooping:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
			<?php
			//$IGMP_mode=getStr("Device.X_CISCO_COM_DeviceControl.IGMPSnoopingEnable");
			$IGMP_mode = "false";
			if ($IGMP_mode=="true") { //or Enabled
			?>
			<ul id="IGMP_snooping_switch" class="radio-btns enable">
				<li>
					<input id="IGMP_snooping_enabled" name="IGMP_snooping" type="radio"  value="Enabled" checked="checked" />
					<label for="IGMP_snooping_enabled" >Enable </label>
				</li>
				<li class="radio-off">
					<input id="IGMP_snooping_disabled" name="IGMP_snooping" type="radio"  value="Disabled" />
					<label for="IGMP_snooping_disabled" >Disable </label>
				</li>
			</ul>
			<?php }else{?>
			<ul id="IGMP_snooping_switch" class="radio-btns enable">
				<li>
					<input id="IGMP_snooping_enabled" name="IGMP_snooping" type="radio"  value="Enabled"/>
					<label for="IGMP_snooping_enabled" >Enable </label>
				</li>
				<li class="radio-off">
					<input id="IGMP_snooping_disabled" name="IGMP_snooping" type="radio"  value="Disabled" checked="checked"/>
					<label for="IGMP_snooping_disabled" >Disable </label>
				</li>
			</ul>
			<?php } ?>
		</div>
	</div-->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
