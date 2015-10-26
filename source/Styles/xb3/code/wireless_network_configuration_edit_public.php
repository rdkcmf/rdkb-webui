<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: wireless_network_configuration_edit.php 3160 2010-01-11 23:10:33Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
$id		= isset($_GET['id']) ? $_GET['id'] : "5";
$reset	= isset($_GET['reset']) ? $_GET['reset'] : "n";
$rf		= (2 - intval($id)%2);	//1,3,5,7 == 1(2.4G); 2,4,6,8 == 2(5G)
$hhs_enable 	= getStr("Device.DeviceInfo.X_COMCAST_COM_xfinitywifiEnable");

$radio_freq = ($id%2)?"2.4":"5";

if (("5"!=$id && "6"!=$id) || "true"!=$hhs_enable) {
	if (!$_SESSION['_DEBUG'])
	echo '<script type="text/javascript">alert("HotSpot function is disabled internally, please contact administrator!\n\nYou will be redirected to WiFi status page...");location.href="wireless_network_configuration.php";</script>';
	exit(0);
}

if ("y" == $reset) {
	//!!!!!!this is not DM in box, but the standard TR-181 DM in xml file!!!!!!
	$gre_param = array(
		"max_client"			=> "Device.WiFi.AccessPoint.$id.BssMaxNumSta",
		"DSCPMarkPolicy"		=> "dmsb.hotspot.tunnel.1.DSCPMarkPolicy",
		"PrimaryRemoteEndpoint"	=> "dmsb.hotspot.tunnel.1.PrimaryRemoteEndpoint",
		"SecondaryRemoteEndpoint" => "dmsb.hotspot.tunnel.1.SecondaryRemoteEndpoint",
		"KeepAliveCount"		=> "dmsb.hotspot.tunnel.1.RemoteEndpointHealthCheckPingCount",
		"KeepAliveInterval"		=> "dmsb.hotspot.tunnel.1.RemoteEndpointHealthCheckPingInterval",
		"KeepAliveThreshold"	=> "dmsb.hotspot.tunnel.1.RemoteEndpointHealthCheckPingFailThreshold",
		"KeepAliveFailInterval"	=> "dmsb.hotspot.tunnel.1.RemoteEndpointHealthCheckPingIntervalInFailure",
		"ReconnectPrimary"		=> "dmsb.hotspot.tunnel.1.ReconnectToPrimaryRemoteEndpoint",
		"DHCPCircuitIDSSID"		=> "dmsb.hotspot.tunnel.1.EnableCircuitID",
		"DHCPRemoteID"			=> "dmsb.hotspot.tunnel.1.EnableRemoteID",
	);
	$gre_value = getDefault('/fss/gw/usr/ccsp/config/bbhm_def_cfg.xml', $gre_param);
	
	$max_client			= $gre_value['max_client'];
	$DSCPMarkPolicy 	= $gre_value['DSCPMarkPolicy']=="" ? "0" : $gre_value['DSCPMarkPolicy'];
	$PrimaryRemoteEndpoint 	= $gre_value['PrimaryRemoteEndpoint'];
	$SecondaryRemoteEndpoint = $gre_value['SecondaryRemoteEndpoint'];
	$KeepAliveCount 	= $gre_value['KeepAliveCount'];
	$KeepAliveInterval 	= $gre_value['KeepAliveInterval'];
	$KeepAliveThreshold 	= $gre_value['KeepAliveThreshold'];
	$KeepAliveFailInterval 	= $gre_value['KeepAliveFailInterval'];
	$ReconnectPrimary 	= $gre_value['ReconnectPrimary'];
	$DHCPCircuitIDSSID 	= $gre_value['DHCPCircuitIDSSID']=="1" ? "true" : "false";
	$DHCPRemoteID 		= $gre_value['DHCPRemoteID']=="1" ? "true" : "false";
	
	$wifi_param = array(
		"radio_enable"		=> "eRT.com.cisco.spvtg.ccsp.Device.WiFi.Radio.SSID.$id.WLANEnable",
		"network_name"		=> "eRT.com.cisco.spvtg.ccsp.Device.WiFi.Radio.SSID.$id.SSID",
		"encrypt_mode"		=> "eRT.com.cisco.spvtg.ccsp.Device.WiFi.Radio.SSID.$id.Security",
		"encrypt_method"	=> "eRT.com.cisco.spvtg.ccsp.Device.WiFi.Radio.SSID.$id.Encryption",
		"password_wpa"		=> "eRT.com.cisco.spvtg.ccsp.Device.WiFi.Radio.SSID.$id.Passphrase",
		"password_wep_64"	=> "eRT.com.cisco.spvtg.ccsp.Device.WiFi.Radio.SSID.$id.FIXME",
		"password_wep_128"	=> "eRT.com.cisco.spvtg.ccsp.Device.WiFi.Radio.SSID.$id.FIXME",
		"broadcastSSID"		=> "eRT.com.cisco.spvtg.ccsp.Device.WiFi.Radio.SSID.$id.HideSSID",
		"enableWMM"			=> "eRT.com.cisco.spvtg.ccsp.Device.WiFi.Radio.SSID.$id.WMMEnable",
	);
	$wifi_value = getDefault('/nvram/bbhm_cur_cfg.xml', $wifi_param);

	$radio_enable		= $wifi_value['radio_enable']=="1" ? "true" : "false";
	$network_name		= $wifi_value['network_name'];
	$encrypt_mode		= "None";
	$encrypt_method		= "None";
	$password_wpa		= $wifi_value['password_wpa'];
	$password_wep_64	= $wifi_value['password_wep_64'];
	$password_wep_128	= $wifi_value['password_wep_128'];
	$broadcastSSID 		= $wifi_value['broadcastSSID']=="0" ? "true" : "false";
	$enableWMM			= $wifi_value['enableWMM']=="1" ? "true" : "false";
}
else {
	$wifi_param = array(
		// "wireless_mode"	=> "Device.WiFi.Radio.".((intval($id)+1)%2+1).".OperatingStandards",
		// "lower_layers"	=> "Device.WiFi.SSID.$id.LowerLayers",
		"radio_enable"		=> "Device.WiFi.SSID.$id.Enable",
		"network_name"		=> "Device.WiFi.SSID.$id.SSID",
		"encrypt_mode"		=> "Device.WiFi.AccessPoint.$id.Security.ModeEnabled",
		"encrypt_method"	=> "Device.WiFi.AccessPoint.$id.Security.X_CISCO_COM_EncryptionMethod",
		"password_wpa"		=> "Device.WiFi.AccessPoint.$id.Security.X_CISCO_COM_KeyPassphrase",
		"password_wep_64"	=> "Device.WiFi.AccessPoint.$id.Security.X_CISCO_COM_WEPKey64Bit.1.WEPKey",
		"password_wep_128"	=> "Device.WiFi.AccessPoint.$id.Security.X_CISCO_COM_WEPKey128Bit.1.WEPKey",
		"broadcastSSID"		=> "Device.WiFi.AccessPoint.$id.SSIDAdvertisementEnabled",
		"enableWMM"			=> "Device.WiFi.AccessPoint.$id.WMMEnable",
		"max_client"		=> "Device.WiFi.AccessPoint.$id.X_CISCO_COM_BssMaxNumSta",
		// "max_client"		=> "Device.WiFi.AccessPoint.$id.MaxAssociatedDevices",
		"Radio_".$rf."_Enable"	=> "Device.WiFi.Radio.$rf.Enable",
	);
	$wifi_value = KeyExtGet("Device.WiFi.", $wifi_param);

	$radio_enable		= $wifi_value['radio_enable'];
	$network_name		= $wifi_value['network_name'];
	$encrypt_mode		= $wifi_value['encrypt_mode'];
	$encrypt_method		= $wifi_value['encrypt_method'];
	$password_wpa		= $wifi_value['password_wpa'];
	$password_wep_64	= $wifi_value['password_wep_64'];
	$password_wep_128	= $wifi_value['password_wep_128'];
	$broadcastSSID 		= $wifi_value['broadcastSSID'];
	$enableWMM		= $wifi_value['enableWMM'];
	$max_client		= $wifi_value['max_client'];

	$GRE_Tunnel_param = array(
		"DSCPMarkPolicy" 	=> "Device.X_COMCAST-COM_GRE.Tunnel.1.DSCPMarkPolicy",
		"PrimaryRemoteEndpoint" => "Device.X_COMCAST-COM_GRE.Tunnel.1.PrimaryRemoteEndpoint",
		"SecondaryRemoteEndpoint" => "Device.X_COMCAST-COM_GRE.Tunnel.1.SecondaryRemoteEndpoint",
		"KeepAliveCount" 	=> "Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingCount",
		"KeepAliveInterval" 	=> "Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingInterval",
		"KeepAliveThreshold" 	=> "Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingFailThreshold",
		"KeepAliveFailInterval" => "Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingIntervalInFailure",
		"ReconnectPrimary" 	=> "Device.X_COMCAST-COM_GRE.Tunnel.1.ReconnectToPrimaryRemoteEndpoint",
		"DHCPCircuitIDSSID" 	=> "Device.X_COMCAST-COM_GRE.Tunnel.1.EnableCircuitID",
		"DHCPRemoteID" 		=> "Device.X_COMCAST-COM_GRE.Tunnel.1.EnableRemoteID",
	);
	$GRE_Tunnel_value = KeyExtGet("Device.X_COMCAST-COM_GRE.Tunnel.1.", $GRE_Tunnel_param);

	$DSCPMarkPolicy 	= $GRE_Tunnel_value["DSCPMarkPolicy"];
	$PrimaryRemoteEndpoint 	= $GRE_Tunnel_value["PrimaryRemoteEndpoint"];
	$SecondaryRemoteEndpoint = $GRE_Tunnel_value["SecondaryRemoteEndpoint"];
	$KeepAliveCount 	= $GRE_Tunnel_value["KeepAliveCount"];
	$KeepAliveInterval 	= $GRE_Tunnel_value["KeepAliveInterval"];
	$KeepAliveThreshold 	= $GRE_Tunnel_value["KeepAliveThreshold"];
	$KeepAliveFailInterval 	= $GRE_Tunnel_value["KeepAliveFailInterval"];
	$ReconnectPrimary 	= $GRE_Tunnel_value["ReconnectPrimary"];
	$DHCPCircuitIDSSID 	= $GRE_Tunnel_value["DHCPCircuitIDSSID"];
	$DHCPRemoteID 		= $GRE_Tunnel_value["DHCPRemoteID"];

	//if Radio.{i}.Enable is false, ALL SSIDs belong to that radio shows disabled, else depends on SSID.{i}.Enable
	if ("false" == $wifi_value["Radio_".$rf."_Enable"]){
		$radio_enable = "false";
	}
}

if ($_SESSION['_DEBUG']){
	$radio_enable		= "true";
	$network_name		= "xfinityWiFi";
	$encrypt_mode		= "WPA2-Personal";
	$encrypt_method		= "AES";
	$password_wpa		= "abc123456";
	$password_wep_64	= "wep64";
	$password_wep_128	= "wep128";
	$broadcastSSID		= "true";
	$enableWMM			= "false";
	$max_client		 	= "32";
	$DSCPMarkPolicy 		= "2";
	$PrimaryRemoteEndpoint 		= "www.comcast.com";
	$SecondaryRemoteEndpoint	= "www.google.com";
	$KeepAliveCount 		= "3";
	$KeepAliveInterval 		= "60";
	$KeepAliveThreshold 	= "5";
	$KeepAliveFailInterval 	= "5";
	$ReconnectPrimary 		= "300";
	$DHCPCircuitIDSSID 		= "true";
	$DHCPRemoteID 			= "false";
}

$RemoteEndpointsV4	= array();
$RemoteEndpointsV6	= array();

array_push($RemoteEndpointsV4, $PrimaryRemoteEndpoint);
array_push($RemoteEndpointsV4, $SecondaryRemoteEndpoint);

array_push($RemoteEndpointsV6, $PrimaryRemoteEndpoint);
array_push($RemoteEndpointsV6, $SecondaryRemoteEndpoint);

?>

<style>
.error{
	display: inline;
}
.forms .readonlyLabel {
	margin: 6px 40px 0 0;
}
</style>

<script type="text/javascript">
//global ssid number value
var ssid_number = "<?php echo $id; ?>";
var radio_freq	= (parseInt(ssid_number)%2)?"2.4":"5";

$(document).ready(function() {
    comcast.page.init("Gateway > Connection > Wireless > Edit "+radio_freq+" GHz", "nav-wifi-config");
	$("#wireless_network_switch").radioswitch({
		id: "wireless-network-switch",
		radio_name: "wireless_network",
		id_on: "radio_enable",
		id_off: "radio_disabled",
		title_on: "Enable radio",
		title_off: "Disable radio",
		state: <?php echo ($radio_enable === "true" ? "true" : "false");?> ? "on" : "off"
	});
	$("#circuit_switch").radioswitch({
		id: "circuit-switch",
		radio_name: "DHCPCircuitIDSSID",
		id_on: "circuit_enabled",
		id_off: "circuit_disabled",
		title_on: "Enable Circuit-ID SSID",
		title_off: "Disable Circuit-ID SSID",
		state: <?php echo ($DHCPCircuitIDSSID === "true" ? "true" : "false");?> ? "on" : "off"
	});
	$("#remote_switch").radioswitch({
		id: "remote-switch",
		radio_name: "DHCPRemoteID",
		id_on: "remote_enabled",
		id_off: "remote_disabled",
		title_on: "Enable Remote-ID",
		title_off: "Disable Remote-ID",
		state: <?php echo ($DHCPRemoteID === "true" ? "true" : "false");?> ? "on" : "off"
	});

	init_form();

    $("#security").change(function() {
		$("#add_with").prop("disabled", ($("#security").val().indexOf("WPA")==-1));
		$("#netPassword-footnote").text($("option:selected", $(this)).attr("title"));
		$("#network_password").prop("disabled", ($("#add_with").val()=="None" && $("#security").val().indexOf("WEP")==-1) || ($("#add_with").val()=="None"));
    });
		
    $("#add_with").change(function() {
		$("#network_password").prop("disabled", ($("#add_with").val()=="None" && $("#security").val().indexOf("WEP")==-1) || ($("#add_with").val()=="None"));
    });
	
	/*$("#password_show").change(function() {
		if ($("#password_show").is(":checked")) {
			document.getElementById("password_field").innerHTML = 
			'<input type="text"     size="23" id="network_password" name="network_password" class="text" value="' + $("#network_password").val() + '" />'
		}
		else {
			document.getElementById("password_field").innerHTML = 
			'<input type="password" size="23" id="network_password" name="network_password" class="text" value="' + $("#network_password").val() + '" />'
		}
		if ("None" == $("#security").val()) {
			$("#network_password").prop("disabled", true);
		}
		else {
			$("#network_password").prop("disabled", false);
		}
	});*/
	
    $("#wireless_network_switch").change(function() {
		if ($(this).radioswitch("getState").on === false) {
			$(":input:not('.btn')").not(".radioswitch_cont input").prop("disabled", true);
			$("#circuit_switch, #remote_switch").radioswitch("doEnable", false);
		}
		else {
			$(":input").not(".radioswitch_cont input").prop("disabled", false);
			$("#circuit_switch, #remote_switch").radioswitch("doEnable", true);
			//$("#password_show").change();
			$("#security").change();
		}
	}).trigger("change");

/*
 *  Manage password field: open wep networks don't use passwords
 */
    $.validator.addMethod("wep_64", function(value, element, param) {
    	//console.log("wep64" + param);
		return !param || /^[a-fA-F0-9]{10}$|^[\S]{5}$/i.test(value);
	}, "5 Ascii characters or 10 Hex digits.");
    $.validator.addMethod("wep_128", function(value, element, param) {
    	//console.log("wep128");
		return !param || /^[a-fA-F0-9]{26}$|^[\S]{13}$/i.test(value);
	}, "13 Ascii characters or 26 Hex digits.");
    $.validator.addMethod("wpa", function(value, element, param) {
    	//console.log("wpa");
		return !param || /^[a-fA-F0-9]{64}$|^[\S]{8,63}$/i.test(value);
	}, "8 to 63 Ascii characters or 64 Hex digits.");
    $.validator.addMethod("wpa2", function(value, element, param) {
		return !param || /^[\S]{8,63}$/i.test(value);
	}, "8 to 63 Ascii characters.");
    $.validator.addMethod("ssid_name", function(value, element, param) {
		return !param || /^[a-zA-Z0-9\-_.]{3,31}$/i.test(value);
	}, "3 to 31 characters combined with alphabet, digit, underscore, hyphen and dot");

    // XFSETUP HOME xfinitywifi cablewifi
    // a term starting with the following combination of text in uppercase or lowercase should not be allowed

    $.validator.addMethod("not_XFSETUP", function(value, element, param) {
		return value.toLowerCase().indexOf("xfsetup") != 0;
	}, 'SSID starting with "XFSETUP" is reserved !');

    $.validator.addMethod("not_HOME", function(value, element, param) {
		return value.toLowerCase().indexOf("home") != 0;
	}, 'SSID starting with "HOME" is reserved !');

/*
wep 64 ==> 5 Ascii characters or 10 Hex digits
wep 128 ==> 13 Ascii characters or 26 Hex digits
wpapsk ==> 8 to 63 Ascii characters or 64 Hex digits
wpa2psk ==> 8 to 63 Ascii characters
*/
    $("#pageForm").validate({
		errorElement: "div",
		rules: {
			network_name: {
				ssid_name: true,
				not_XFSETUP: true,
				not_HOME: true
			},
			network_password: {
				required: function() {
					return ($("#security option:selected").val() != "None");
				}
				,wep_64: function() {
					return ($("#security option:selected").val() == "WEP_64");
				}
				,wep_128: function() {
					return ($("#security option:selected").val() == "WEP_128");
				}
				// ,wpa: function() {
					// return ($("#security option:selected").val() == "WPA_PSK_TKIP" || $("#security option:selected").val() == "WPA_PSK_AES");
				// }
				// ,wpa2: function() {
					// return ($("#security option:selected").val() == "WPA2_PSK_TKIP" || $("#security option:selected").val() == "WPA2_PSK_AES" || $("#security option:selected").val() == "WPA2_PSK_TKIPAES" || $("#security option:selected").val() == "WPAWPA2_PSK_TKIPAES");
				// }
				,wpa: function() {
					return ($("#security option:selected").val() != "None" && $("#security option:selected").val() != "WEP_64" && $("#security option:selected").val() != "WEP_128");
				}
			},
			max_client:{
				required: true,
				digits: true,
				max: 127,
 				min: 1
			},
			DSCPMarkPolicy:{
				required: true,
				digits: true
			},
			KeepAliveCount:{
				required: true,
				digits: true
			},
			KeepAliveInterval:{
				required: true,
				digits: true
			},
			KeepAliveThreshold:{
				required: true,
				digits: true
			},
			KeepAliveFailInterval:{
				required: true,
				digits: true
			},
			ReconnectPrimary:{
				required: true,
				number: true
			},
			RemoteEndpointsV4_1:{
				required: function(){return ($("#RemoteEndpointsV6_1").val().replace(/\s/g, '') == "")},
			},
			RemoteEndpointsV6_1:{
				required: function(){return ($("#RemoteEndpointsV4_1").val().replace(/\s/g, '') == "") && $(this).is(":visible")},
			},
			RemoteEndpointsV4_2:{
				required: function(){return ($("#RemoteEndpointsV6_2").val().replace(/\s/g, '') == "")},
			},
			RemoteEndpointsV6_2:{
				required: function(){return ($("#RemoteEndpointsV4_2").val().replace(/\s/g, '') == "") && $(this).is(":visible")},
			}		
		}
    });	

	$("#restore_settings").click(function(){
		jConfirm(
			"This will change your settings in this page to default values. Are you sure you want to change the settings to the default values? (take effect immediately)"
			, "Reset Default Settings"
			,function(ret) {
				if(ret) {
					if (-1 == location.href.indexOf("reset=y")){
						location.href = location.href+'&reset=y';
					}
					else{
						location.reload();
					}
				}
			}
		);
	});
	
	$("#save_settings").click(function(){	
		var security 		= $("#security").val().split(".");
		var encrypt_mode 	= security[0];
		var encrypt_method 	= "None";
		if (security.length > 1){
			encrypt_mode 	= security[0]+"-"+$("#add_with").val();
			encrypt_method 	= security[1];		
		}
		var PrimaryRemoteEndpoint = $("#RemoteEndpointsV4_1").val();
		var SecondaryRemoteEndpoint = $("#RemoteEndpointsV4_2").val();
		
		if($("#pageForm").valid()) {
			jProgress('This may take several seconds...', 60);
			$.ajax({
				type:"POST",
				url:"actionHandler/ajaxSet_wireless_network_configuration_edit_public.php",
				data:{
					ssid_number:		ssid_number, 
					radio_freq:			radio_freq, 
					radio_enable:		$("#wireless_network_switch").radioswitch("getState").on,
					network_name:		$("#network_name").val(), 
					encrypt_mode:		encrypt_mode, 
					encrypt_method:		encrypt_method,
					network_password:	$("#network_password").val(),
					broadcastSSID:		$("#broadcastSSID").prop("checked"),
					enableWMM:			$("#enableWMM").prop("checked"),
					max_client:			$("#max_client").val(), 
					DSCPMarkPolicy:		$("#DSCPMarkPolicy").val(), 
					PrimaryRemoteEndpoint: $("#RemoteEndpointsV4_1").val(),
					SecondaryRemoteEndpoint: $("#RemoteEndpointsV4_2").val(),
					KeepAliveCount:		$("#KeepAliveCount").val(), 
					KeepAliveInterval:	$("#KeepAliveInterval").val(), 
					KeepAliveThreshold:	$("#KeepAliveThreshold").val(), 
					KeepAliveFailInterval:	60*$("#KeepAliveFailInterval").val(),
					ReconnectPrimary:	3600*$("#ReconnectPrimary").val(),
					DHCPCircuitIDSSID:	$("#circuit_switch").radioswitch("getState").on,
					DHCPRemoteID:		$("#remote_switch").radioswitch("getState").on
					<?php if ("y" == $reset) {
						echo ', radio_reset:	"true"';
					}?>				
				},
				success:function(){
					jHide();
					//location.href = location.href.replace(/&reset=y/g, "");
					location.href = 'wireless_network_configuration.php';
				},
				error:function(){
					jHide();
					jAlert("Something wrong, please try later!");
				}
			});
		}
	});
	
	//do apply right after press "restore default" button
	if ("y" == "<?php echo $reset; ?>"){
		$("#save_settings").click();
	}
});

function init_form() {
	//re-style each div
	$('#pageForm > div').removeClass("odd");
	$('#pageForm > div:visible:odd').addClass("odd");

	var network_name 		= "<?php echo $network_name; ?>";
	var encrypt_mode 		= "<?php echo $encrypt_mode; ?>";
	var encrypt_method 		= "<?php echo $encrypt_method; ?>";
	var password_wpa 		= "<?php echo $password_wpa; ?>";
	var password_wep_64 	= "<?php echo $password_wep_64; ?>";
	var password_wep_128 	= "<?php echo $password_wep_128; ?>";
	var broadcastSSID 		= "<?php echo $broadcastSSID; ?>";
	var enableWMM 			= "<?php echo $enableWMM; ?>";	
	var max_client 			= "<?php echo $max_client; ?>";
	var DSCPMarkPolicy 			= "<?php echo $DSCPMarkPolicy; ?>";
	var RemoteEndpointsV4_1		= "<?php if (isset($RemoteEndpointsV4[0])) echo $RemoteEndpointsV4[0]; ?>";
	var RemoteEndpointsV6_1		= "<?php if (isset($RemoteEndpointsV6[0])) echo $RemoteEndpointsV6[0]; ?>";
	var RemoteEndpointsV4_2		= "<?php if (isset($RemoteEndpointsV4[1])) echo $RemoteEndpointsV4[1]; ?>";
	var RemoteEndpointsV6_2		= "<?php if (isset($RemoteEndpointsV6[1])) echo $RemoteEndpointsV6[1]; ?>";
	var KeepAliveCount 			= "<?php echo $KeepAliveCount; ?>";
	var KeepAliveInterval 		= "<?php echo $KeepAliveInterval; ?>";
	var KeepAliveThreshold 		= "<?php echo $KeepAliveThreshold; ?>";	
	var KeepAliveFailInterval 	= "<?php echo $KeepAliveFailInterval; ?>";
	KeepAliveFailInterval	= KeepAliveFailInterval/60;
	var ReconnectPrimary 		= "<?php echo $ReconnectPrimary; ?>";
	ReconnectPrimary = ReconnectPrimary/3600; //zqiu: translate it to hrs
	
	var sec_list = new Array(
		["Open (risky)",	"None",				"Open networks do not have a password."],
		["WEP 64 (risky)",	"WEP-64",			"WEP 64 requires a 5 ASCII character or 10 hex character password. Hex means only the following characters can be used: ABCDEF0123456789."],
		["WEP 128 (risky)",	"WEP-128",			"WEP 128 requires a 13 ASCII character or 16 hex character password. Hex means only the following characters can be used: ABCDEF0123456789."],
		["WPA (TKIP)",		"WPA.TKIP",			"WPA requires an 8-63 ASCII character or a 64 hex character password. Hex means only the following characters can be used: ABCDEF0123456789."],
		["WPA (AES)",		"WPA.AES",			"WPA requires an 8-63 ASCII character or a 64 hex character password. Hex means only the following characters can be used: ABCDEF0123456789."],
		["WPA2 (TKIP)",		"WPA2.TKIP",		"WPA2 requires a 8-63 ASCII character password."],
		["WPA2 (AES)",		"WPA2.AES",			"WPA2 requires a 8-63 ASCII character password."],
		["WPA2 (TKIP/AES)",	"WPA2.AES+TKIP",	"WPA2 requires a 8-63 ASCII character password."],
		["WPAWPA2(TKIP/AES) (recommended)", 	"WPA-WPA2.AES+TKIP", "WPA2 requires a 8-63 ASCII character password."]);

	var add_list = new Array(
		["NONE",		"None", 		""],
		["PSK",			"Personal", 	""],
		["EAP/DOT1x",	"Enterprise", 	""],
		["DOT1x-OPEN",	"None", 		""]);
		
	//soft-gre
	$("#max_client").val(max_client);
	$("#DSCPMarkPolicy").val(DSCPMarkPolicy);
	$("#RemoteEndpointsV4_1").val(RemoteEndpointsV4_1);
	$("#RemoteEndpointsV6_1").val(RemoteEndpointsV6_1);
	$("#RemoteEndpointsV4_2").val(RemoteEndpointsV4_2);
	$("#RemoteEndpointsV6_2").val(RemoteEndpointsV6_2);
	$("#KeepAliveCount").val(KeepAliveCount);
	$("#KeepAliveInterval").val(KeepAliveInterval);
	$("#KeepAliveThreshold").val(KeepAliveThreshold);
	$("#KeepAliveFailInterval").val(KeepAliveFailInterval);
	$("#ReconnectPrimary").val(ReconnectPrimary);

	//HomeSecurity and HotSpot Common Part
	$("#network_name").val(network_name);
	$("#broadcastSSID").prop("checked", ("true"==broadcastSSID));
	$("#enableWMM").prop("checked", ("true"==enableWMM));
	
	//security mode
	var sec_val = encrypt_mode;
	if (encrypt_mode.indexOf("WPA-WPA2")!=-1){
		sec_val = "WPA-WPA2."+encrypt_method;
	}
	else if (encrypt_mode.indexOf("WPA2")!=-1){
		sec_val = "WPA2."+encrypt_method;
	}
	else if (encrypt_mode.indexOf("WPA")!=-1){
		sec_val = "WPA."+encrypt_method;
	}
	for (var i=0; i<sec_list.length; i++){
		$("#security").append('<option value="'+sec_list[i][1]+'" title="'+sec_list[i][2]+'">'+sec_list[i][0]+'</option>');
	}
	if (($('#security [value="'+sec_val+'"]')).length < 1){
		$("#security").val("WPA-WPA2.AES+TKIP")
	}
	else{
		$("#security").val(sec_val);
	}
	
	//addtional mode
	var add_val = "None";
	if (encrypt_mode.indexOf("Personal")!=-1){
		add_val = "Personal";
	}
	else if (encrypt_mode.indexOf("Enterprise")!=-1){
		add_val = "Enterprise";
	}
	for (var i=0; i<add_list.length; i++){
		$("#add_with").append('<option value="'+add_list[i][1]+'" title="'+add_list[i][2]+'">'+add_list[i][0]+'</option>');
	}
	$("#add_with").val(add_val);
	// disable radius server at this point
	$("#add_with").find("[value='Enterprise']").prop("disabled", true);
	
	$("#security").change();

	//wifi password
	var network_password = password_wpa;
	if ("WEP-64"==encrypt_mode){
		network_password = password_wep_64;
	}
	else if (("WEP-128"==encrypt_mode)){
		network_password = password_wep_128;
	}		
	$("#network_password").val(network_password);
	
	//for UI-4.0, remove some security options
    if ("2.4"==radio_freq){
        $("#security").find("[value='WPA.TKIP'],[value='WPA.AES'],[value='WPA2.TKIP'],[value='WPA2.AES+TKIP']").remove();
    }
    else{
        $("#security").find("[value='WPA.TKIP'],[value='WPA.AES'],[value='WPA2.TKIP'],[value='WPA2.AES+TKIP'],[value='WEP-64'],[value='WEP-128']").remove();
    }   
}

</script>

<style type="text/css">
label{
	margin-right: 10px !important;
}
.forms .checkbox {
	margin: 0 auto;
}
.radiolist {
	margin-left: -30px !important;
}
</style>

<div id="content">
	<h1>Gateway > Connection >  Wi-Fi > Edit <?php echo $radio_freq;?> GHz</h1>
	<div id="educational-tip">
		<p class="tip">Manage your Public Wi-Fi network settings.</p>
		<p class="hidden"><strong>Network Name (SSID):</strong> Identifies your home Wi-Fi network from other nearby Wi-Fi networks. Your default name can be found on the bottom label of the Gateway, but can be changed for easier identification.</p>
		<p class="hidden"><strong>Mode:</strong> <?php echo $radio_freq;?> GHz operates in b/g/n modes. Unless you have older Wi-Fi devices that use only 'b' mode, use the default 802.11 g/n for faster performance.</p>
		<p class="hidden"><strong>Security Mode:</strong> Secures data between your Wi-Fi devices and the Gateway. The default WPAWPA2-PSK (TKIP/AES) setting is compatible with most computers and provides the best security and performance.</p>
		<p class="hidden"><strong>Network Password (Key):</strong> Required by Wi-Fi devices to connect to your secure network. The default setting can be found on the bottom label of the Gateway. </p>
		<p class="hidden"><strong>Broadcast Network Name (SSID):</strong>  If enabled, the Network Name (SSID) will be shown in the list of available networks. (If unchecked, you'll need to enter the exact Network Name (SSID) to connect.)</p>
	</div>
<!--HotSpot WiFi-->
	<div class="module forms" id="div_hot_spot">	
		<form  method="post" id="pageForm">
			<h2>Public Wi-Fi Network Configuration (<?php echo $radio_freq;?> GHz)</h2>
			<div class="form-row odd">
				<span class="readonlyLabel label">Wireless Network:</span>
				<span id="wireless_network_switch"></span>
			</div>
			<div class="form-row">
				<label for="network_name">Network Name (SSID):</label>
				<input type="text" size="23" id="network_name" name="network_name" class="text" />
			</div>
			<div class="form-row">
				<label for="encryption_method">Security Mode:</label>
				<select name="encryption_method" id="security">
					<!--dynamic genarate in javascript-->
				</select>
				<span for="add_with">with:</span>
				<select id="add_with" name="add_with">
					<!--dynamic genarate in javascript-->
				</select>
			</div>
			<div class="form-row odd" id="div_network_password">
				<label for="network_password">Network Password:</label>
				<span id="password_field"><input type="password" size="23" id="network_password" name="network_password" class="text" value="" /></span>
				<p id="netPassword-footnote" class="footnote">8-16 characters. Letter and numbers only. No spaces. Case sensitive.</p><br/>
			</div>
			<!--div class="form-row" id="div_password_show">
				<label for="password_show">Show Network Password:</label>
				<span class="checkbox"><input type="checkbox" id="password_show" name="password_show" /></span>
			</div-->
			<div id="div_broadcastSSID" class="form-row odd">
				<label for="broadcastSSID">Broadcast Network Name (SSID):</label>
				<span class="checkbox"><input type="checkbox" id="broadcastSSID" name="broadcastSSID" /><b>Enabled</b></span>
			</div>
			<div id="div_enableWMM" class="form-row">
				<label for="enableWMM">Enable WMM:</label>
				<span class="checkbox"><input type="checkbox" id="enableWMM" name="enableWMM"  /><b>Enabled</b></span>
			</div>
			<!--newly added for hotspot later than 3.0-->
			<div class="form-row odd"><label for="max_client">Maximum Number Of Concurrent Clients:</label>
				<input type="text" name="max_client" id="max_client">
			</div>
			<div class="form-row"><label for="DSCPMarkPolicy">DSCP Value For Tunneled Packets:</label>
				<input type="text" name="DSCPMarkPolicy" id="DSCPMarkPolicy">
			</div>
			<div class="form-row odd"><label for="RemoteEndpointsV4_1">WLAN GW Primary IP Address (IPv4/IPV6):</label>
				<input type="text" name="RemoteEndpointsV4_1" id="RemoteEndpointsV4_1">
				/<input type="text" name="RemoteEndpointsV6_1" id="RemoteEndpointsV6_1" />
			</div>
			<div class="form-row"><label for="RemoteEndpointsV4_2">WLAN GW Secondary IP Address (IPv4/IPV6):</label>
				<input type="text" name="RemoteEndpointsV4_2" id="RemoteEndpointsV4_2">
				/<input type="text" name="RemoteEndpointsV6_2" id="RemoteEndpointsV6_2" />
			</div>
			<div class="form-row odd"><label for="KeepAliveCount">WLAN GW Ping Count:</label>
				<input type="text" name="KeepAliveCount" id="KeepAliveCount">
			</div>
			<div class="form-row"><label for="KeepAliveInterval">WLAN GW Health Check Ping Interval:</label>
				<input type="text" name="KeepAliveInterval" id="KeepAliveInterval"><span>sec</span>
			</div>
			<div class="form-row odd"><label for="KeepAliveThreshold">WLAN GW Failover Threshold:</label>
				<input type="text" name="KeepAliveThreshold" id="KeepAliveThreshold">
			</div>
			<div class="form-row"><label for="KeepAliveFailInterval">WLAN GW Failure Ping Interval:</label>
				<input type="text" name="KeepAliveFailInterval" id="KeepAliveFailInterval"><span>min</span>
			</div>
			<div class="form-row odd"><label for="ReconnectPrimary">Number Of Hours To Reattempt Connection To Primary WLAN GW:</label>
				<input type="text" name="ReconnectPrimary" id="ReconnectPrimary"><span>hrs</span>
			</div>
			<div class="form-row ctl-switch">
				<span class="readonlyLabel label"> Circuit-ID SSID:</span>
				<span id="circuit_switch"></span>
			</div>
			<div class="form-row odd ctl-switch">
				<span class="readonlyLabel label"> Remote-ID:</span>
				<span id="remote_switch"></span>
			</div>
			<div class="form-row odd">
				<input type="button" id="save_settings"    name="save_settings"    class="btn" value="Save Settings" />
				<input type="reset"  id="cancel_settings"  name="cancel_settings"  class="btn" value="Cancel" onclick="location.reload();" />
				<input type="button" id="restore_settings" name="restore_settings" class="btn alt" value="Restore Default Settings" />
			</div>
		</form>
	</div> <!-- end .module -->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
