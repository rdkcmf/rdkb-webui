<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: wireless_network_configuration.usg.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php 
	$ret = init_psmMode("Gateway > Connection > Wi-Fi", "nav-wifi-config");
	if ("" != $ret){echo $ret;	return;}
?>

<?php
/*********************get WiFi parameters***************************/
$wifi_param = array(
	"feq_band" 			=> "Device.WiFi.Radio.1.OperatingFrequencyBand",
	"wireless_mode"			=> "Device.WiFi.Radio.1.OperatingStandards",
	"transmit_power"		=> "Device.WiFi.Radio.1.TransmitPower",
	"channel_automatic"		=> "Device.WiFi.Radio.1.AutoChannelEnable",
	"channel_number"		=> "Device.WiFi.Radio.1.Channel",
	"BG_protect_mode"		=> "Device.WiFi.Radio.1.X_CISCO_COM_CTSProtectionMode",
	"IGMP_Snooping"			=> "Device.WiFi.Radio.1.X_COMCAST_COM_IGMPSnoopingEnable",
	"operation_mode"		=> "Device.WiFi.Radio.1.X_CISCO_COM_11nGreenfieldEnabled",
	"channel_bandwidth"		=> "Device.WiFi.Radio.1.OperatingChannelBandwidth",
	"guard_interval"		=> "Device.WiFi.Radio.1.GuardInterval",
	"reverse_enabled"		=> "Device.WiFi.Radio.1.X_CISCO_COM_ReverseDirectionGrant",
	"ext_channel"			=> "Device.WiFi.Radio.1.ExtensionChannel",
	"MSDU_enabled"			=> "Device.WiFi.Radio.1.X_CISCO_COM_AggregationMSDU",
	"blockACK_enabled"		=> "Device.WiFi.Radio.1.X_CISCO_COM_AutoBlockAck",
	"blockBA_enabled"		=> "Device.WiFi.Radio.1.X_CISCO_COM_DeclineBARequest",
	"DCS_Supported"			=> "Device.WiFi.Radio.1.X_COMCAST-COM_DCSSupported", //1-supported 0-not supported
	"DCS_Enable"			=> "Device.WiFi.Radio.1.X_COMCAST-COM_DCSEnable",
	"HT_TxStream"			=> "Device.WiFi.Radio.1.X_CISCO_COM_HTTxStream",
	"HT_RxStream"			=> "Device.WiFi.Radio.1.X_CISCO_COM_HTRxStream",
	"STBC_enabled" 			=> "Device.WiFi.Radio.1.X_CISCO_COM_STBCEnable",
	"possible_channels"		=> "Device.WiFi.Radio.1.PossibleChannels",
	"feq_band1" 			=> "Device.WiFi.Radio.2.OperatingFrequencyBand",
	"wireless_mode1"		=> "Device.WiFi.Radio.2.OperatingStandards",
	"transmit_power1"		=> "Device.WiFi.Radio.2.TransmitPower",
	"channel_automatic1"		=> "Device.WiFi.Radio.2.AutoChannelEnable",
	"channel_number1"		=> "Device.WiFi.Radio.2.Channel",
	"BG_protect_mode1"		=> "Device.WiFi.Radio.2.X_CISCO_COM_CTSProtectionMode",
	"IGMP_Snooping1"		=> "Device.WiFi.Radio.2.X_COMCAST_COM_IGMPSnoopingEnable",
	"operation_mode1"		=> "Device.WiFi.Radio.2.X_CISCO_COM_11nGreenfieldEnabled",
	"channel_bandwidth1"		=> "Device.WiFi.Radio.2.OperatingChannelBandwidth",
	"guard_interval1"		=> "Device.WiFi.Radio.2.GuardInterval",
	"reverse_enabled1"		=> "Device.WiFi.Radio.2.X_CISCO_COM_ReverseDirectionGrant",
	"ext_channel1"			=> "Device.WiFi.Radio.2.ExtensionChannel",
	"MSDU_enabled1"			=> "Device.WiFi.Radio.2.X_CISCO_COM_AggregationMSDU",
	"blockACK_enabled1"		=> "Device.WiFi.Radio.2.X_CISCO_COM_AutoBlockAck",
	"blockBA_enabled1"		=> "Device.WiFi.Radio.2.X_CISCO_COM_DeclineBARequest",
	"DFS_Support1"			=> "Device.WiFi.Radio.2.X_COMCAST_COM_DFSSupport", //1-supported 0-not supported
	"DFS_Enable1"			=> "Device.WiFi.Radio.2.X_COMCAST_COM_DFSEnable",
	"DCS_Supported1"		=> "Device.WiFi.Radio.2.X_COMCAST-COM_DCSSupported", //1-supported 0-not supported
	"DCS_Enable1"			=> "Device.WiFi.Radio.2.X_COMCAST-COM_DCSEnable",
	"HT_TxStream1"			=> "Device.WiFi.Radio.2.X_CISCO_COM_HTTxStream",
	"HT_RxStream1"			=> "Device.WiFi.Radio.2.X_CISCO_COM_HTRxStream",
	"STBC_enabled1" 		=> "Device.WiFi.Radio.2.X_CISCO_COM_STBCEnable",
	"possible_channels1"		=> "Device.WiFi.Radio.2.PossibleChannels",
	//WiFi radio status
	"Radio_Enable1"			=> "Device.WiFi.Radio.1.Enable",
	"Radio_Enable2"			=> "Device.WiFi.Radio.2.Enable",
	//check if 5g support 802.11ac
	"support_mode_5g"		=> "Device.WiFi.Radio.2.SupportedStandards",
	//AccessPoint
	"AccessPoint_Enable4"		=> "Device.WiFi.AccessPoint.4.Enable",
	//WPS status
	"WPS_Enable1"	=> "Device.WiFi.AccessPoint.1.WPS.Enable",
	"WPS_Pin1"	=> "Device.WiFi.AccessPoint.1.WPS.X_CISCO_COM_Pin",
	"WPS_Config1"	=> "Device.WiFi.AccessPoint.1.WPS.ConfigMethodsEnabled",
	"ModeEnabled1"	=> "Device.WiFi.AccessPoint.1.Security.ModeEnabled",
	"EncrypMethod1"	=> "Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_EncryptionMethod",
	"SSIDAdvert1"	=> "Device.WiFi.AccessPoint.1.SSIDAdvertisementEnabled",
	"WMM_power_save"=> "Device.WiFi.AccessPoint.1.UAPSDEnable",
	"enableWMM"	=> "Device.WiFi.AccessPoint.1.WMMEnable",
	"WPS_Enable2"	=> "Device.WiFi.AccessPoint.2.WPS.Enable",
	"WPS_Pin2"	=> "Device.WiFi.AccessPoint.2.WPS.X_CISCO_COM_Pin",
	"WPS_Config2"	=> "Device.WiFi.AccessPoint.2.WPS.ConfigMethodsEnabled",
	"ModeEnabled2"	=> "Device.WiFi.AccessPoint.2.Security.ModeEnabled",
	"EncrypMethod2"	=> "Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_EncryptionMethod",
	"SSIDAdvert2"	=> "Device.WiFi.AccessPoint.2.SSIDAdvertisementEnabled",
	"WMM_power_save1"=> "Device.WiFi.AccessPoint.2.UAPSDEnable",
	"enableWMM1"	=> "Device.WiFi.AccessPoint.2.WMMEnable",
	"ModeEnabled3"	=> "Device.WiFi.AccessPoint.3.Security.ModeEnabled",
	"EncrypMethod3"	=> "Device.WiFi.AccessPoint.3.Security.X_CISCO_COM_EncryptionMethod",
	"ModeEnabled4"	=> "Device.WiFi.AccessPoint.4.Security.ModeEnabled",
	"EncrypMethod4"	=> "Device.WiFi.AccessPoint.4.Security.X_CISCO_COM_EncryptionMethod",
	"ModeEnabled5"	=> "Device.WiFi.AccessPoint.5.Security.ModeEnabled",
	"EncrypMethod5"	=> "Device.WiFi.AccessPoint.5.Security.X_CISCO_COM_EncryptionMethod",
	"ModeEnabled6"	=> "Device.WiFi.AccessPoint.6.Security.ModeEnabled",
	"EncrypMethod6"	=> "Device.WiFi.AccessPoint.6.Security.X_CISCO_COM_EncryptionMethod",
	"SSID_Enable1"	=> "Device.WiFi.SSID.1.Enable",
	"SSID_Enable2"	=> "Device.WiFi.SSID.2.Enable",
	"network_name1"	=> "Device.WiFi.SSID.1.SSID",
	"mac_address1"	=> "Device.WiFi.SSID.1.BSSID",
	"network_name2" => "Device.WiFi.SSID.2.SSID",
	"mac_address2"	=> "Device.WiFi.SSID.2.BSSID",
	"SSID_SSID3"	=> "Device.WiFi.SSID.3.SSID",
	"SSID_BSSID3"	=> "Device.WiFi.SSID.3.BSSID",
	"SSID_SSID4"	=> "Device.WiFi.SSID.4.SSID",
	"SSID_BSSID4"	=> "Device.WiFi.SSID.4.BSSID",
	"SSID_SSID5"	=> "Device.WiFi.SSID.5.SSID",
	"SSID_BSSID5"	=> "Device.WiFi.SSID.5.BSSID",
	"SSID_SSID6"	=> "Device.WiFi.SSID.6.SSID",
	"SSID_BSSID6"	=> "Device.WiFi.SSID.6.BSSID",
	);
$wifi_value = KeyExtGet("Device.WiFi.", $wifi_param);

$radio_enable		= $wifi_value['Radio_Enable1'];
$network_name		= $wifi_value['network_name1'];
$feq_band			= $wifi_value['feq_band'];
$mac_address		= $wifi_value['mac_address1'];
$encrypt_mode		= $wifi_value['ModeEnabled1'];
$encrypt_method		= $wifi_value['EncrypMethod1'];
$wireless_mode		= $wifi_value['wireless_mode'];
$transmit_power		= $wifi_value['transmit_power'];
$channel_automatic	= $wifi_value['channel_automatic'];
$channel_number		= $wifi_value['channel_number'];
$BG_protect_mode	= $wifi_value['BG_protect_mode'];
$IGMP_Snooping		= $wifi_value['IGMP_Snooping'];
$operation_mode		= $wifi_value['operation_mode'];
$channel_bandwidth	= $wifi_value['channel_bandwidth'];
$guard_interval		= $wifi_value['guard_interval'];
$reverse_enabled	= $wifi_value['reverse_enabled'];
$ext_channel		= $wifi_value['ext_channel'];
$MSDU_enabled		= $wifi_value['MSDU_enabled'];
$blockACK_enabled	= $wifi_value['blockACK_enabled'];
$blockBA_enabled	= $wifi_value['blockBA_enabled'];
$DCS_Supported		= $wifi_value['DCS_Supported'];
$DCS_Enable			= $wifi_value['DCS_Enable'];
$HT_TxStream		= $wifi_value['HT_TxStream'];
$HT_RxStream		= $wifi_value['HT_RxStream'];
$STBC_enabled		= $wifi_value['STBC_enabled'];
$WMM_power_save		= $wifi_value['WMM_power_save'];
$enableWMM			= $wifi_value['enableWMM'];
$possible_channels	= $wifi_value['possible_channels'];
$radio_enable1		= $wifi_value['Radio_Enable2'];
$network_name1		= $wifi_value['network_name2'];
$feq_band1			= $wifi_value['feq_band1'];
$mac_address1		= $wifi_value['mac_address2'];
$encrypt_mode1		= $wifi_value['ModeEnabled2'];
$encrypt_method1	= $wifi_value['EncrypMethod2'];
$wireless_mode1		= $wifi_value['wireless_mode1'];
$transmit_power1	= $wifi_value['transmit_power1'];
$channel_automatic1	= $wifi_value['channel_automatic1'];
$channel_number1	= $wifi_value['channel_number1'];
$BG_protect_mode1	= $wifi_value['BG_protect_mode1'];
$IGMP_Snooping1		= $wifi_value['IGMP_Snooping1'];
$operation_mode1	= $wifi_value['operation_mode1'];
$channel_bandwidth1	= $wifi_value['channel_bandwidth1'];
$guard_interval1	= $wifi_value['guard_interval1'];
$reverse_enabled1	= $wifi_value['reverse_enabled1'];
$ext_channel1		= $wifi_value['ext_channel1'];
$MSDU_enabled1		= $wifi_value['MSDU_enabled1'];
$blockACK_enabled1	= $wifi_value['blockACK_enabled1'];
$blockBA_enabled1	= $wifi_value['blockBA_enabled1'];
$DFS_Support1		= $wifi_value['DFS_Support1'];
$DFS_Enable1		= $wifi_value['DFS_Enable1'];
$DCS_Supported1		= $wifi_value['DCS_Supported1'];
$DCS_Enable1		= $wifi_value['DCS_Enable1'];
$HT_TxStream1		= $wifi_value['HT_TxStream1'];
$HT_RxStream1		= $wifi_value['HT_RxStream1'];
$STBC_enabled1		= $wifi_value['STBC_enabled1'];
$WMM_power_save1	= $wifi_value['WMM_power_save1'];
$enableWMM1			= $wifi_value['enableWMM1'];
$possible_channels1	= $wifi_value['possible_channels1'];

$DFS_Support1 = "false" ; //Remove/disable DFS channels, DFS_Support1 1-supported 0-not supported

$support_mode_5g = $wifi_value['support_mode_5g'];

/**********************get WPS status, manual-disabled or auto-disabled?*******************************/
// $ssidsWPS			= explode(",", getInstanceIds("Device.WiFi.SSID."));
$ssidsWPS			= explode(",", "1,2");	//Currently, only SSID.1(2.4G) and SSID.2(5G) are involved with WPS
$wps_enabled	= "false";
$wps_pin		= "";
$wps_method		= "PushButton";
$f_e_ssid		= "1";
// $wps_enabled	= "true";

//get the first WPS enabled SSID, in principle all WPS should be enabled or disabled simultaneously
foreach ($ssidsWPS as $i){
	if ("true" == $wifi_value['WPS_Enable'.$i]){
		$wps_enabled	= "true";
		$wps_pin	= $wifi_value['WPS_Pin'.$i];
		$wps_method	= $wifi_value['WPS_Config'.$i];
		$f_e_ssid	= $i;
		break;
	}
}

//if wps_config is false, then show WPS disabled, and do not allow to enable it
$wps_config = "false";
foreach ($ssidsWPS as $i){
	if ("true"==$wifi_value['SSID_Enable'.$i] && "true"==$wifi_value['Radio_Enable'.$i]){
		$wps_config	= "true";
		$wps_encrypt_mode	= $wifi_value['ModeEnabled'.$i];
		$wps_encrypt_method	= $wifi_value['EncrypMethod'.$i];
		$wps_broadcastSSID	= $wifi_value['SSIDAdvert'.$i];
		//$wps_filter_enable	= getStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.Enable");
		// do not detect ACL for WPS at this time 0509
		$wps_filter_enable	= "false";
		if (strstr($wps_encrypt_mode, "WEP") || (strstr($wps_encrypt_mode, "WPA") && $wps_encrypt_method=="TKIP") || "false"==$wps_broadcastSSID || "true"==$wps_filter_enable){
			$wps_config	= "false";
			break;
		}
	}
}

if ($_SESSION['_DEBUG']){
	$wps_config = "true";
	$radio_enable = "true";
	$network_name = "111111";
	$feq_band = 	"2.4 GHz";
	$mac_address =	"00:00:00:00:00:00";
	$encrypt_mode = 	"WEP-128";
	$encrypt_method = 	"TKIP";
	$encrypt_mode = 	"WPA-WPA2-Personal";
	$encrypt_method = 	"AES+TKIP";
	$wireless_mode = "g,n";
	$transmit_power = "1";
	$channel_automatic ="true";
	$channel_number =	"7";
	$BG_protect_mode = "Auto";
	$operation_mode =	"b,g,n";
	$channel_bandwidth= "20MHz";
	$guard_interval = "800nsec";
	$reverse_enabled = "true";
	$ext_channel =			"AboveControlChannel";
	$MSDU_enabled =	"true";
	$blockACK_enabled =	"true";
	$blockBA_enabled = "true";
	$HT_TxStream =	"1";	
	$HT_RxStream =	"2";	
	$WMM_power_save =	"false";
	$enableWMM			= "true";
	$STBC_enabled = 	"true";
	$possible_channels = "1-11";
	$radio_enable1 = "false";
	$network_name1 = "222222";
	$feq_band1 = 	"5 GHz";
	$mac_address1 =	"00:66:00:00:00:00";
	$encrypt_mode1 = 	"WPA-WPA2-Personal";
	$encrypt_method1 = 	"AES+TKIP";
	$wireless_mode1 = "ac";
	$transmit_power1 = "19";
	$channel_automatic1 ="true";
	$channel_number1 =	"165";
	$BG_protect_mode1 = "Auto";
	$operation_mode1 =	"n";
	$channel_bandwidth1= "40MHz";
	$guard_interval1 = "Auto";
	$reverse_enabled1 = "true";
	$ext_channel1 =			"BelowControlChannel";
	$MSDU_enabled1 =	"true";
	$blockACK_enabled1 =	"true";
	$blockBA_enabled1 = "true";
	$HT_TxStream1 =	"1";	
	$HT_RxStream1 =	"2";	
	$WMM_power_save1 =	"false";
	$enableWMM1			= "true";
	$STBC_enabled1 = 	"true";
	$possible_channels1="36,40,44,48,149,153,157,161,165";
	$support_mode_5g 	= "a,n,ac";
	// $support_mode_5g 	= "a,n";
}

if ("1-11"==$possible_channels)
$possible_channels = "1,2,3,4,5,6,7,8,9,10,11";

// SSID 1,2 for Private, 3,4 for Home Security, 5,6 for Hot Spot, HotSpot share the same SSID as a service set
//$ssids 		= explode(",", getInstanceIds("Device.WiFi.SSID.")); this will return all 16

// Hardcoded for XB3-1.6
if ("mso" != $_SESSION["loginuser"]) {
	$ssids	= explode(",", "1,2");
}
else {
	$ssids	= explode(",", "1,2,3,4,5,6");
	/*- if xfinitywifi XHS AccessPoint is not up don't show in GUI -*/
	if(strstr(getStr("Device.DeviceInfo.X_COMCAST_COM_xfinitywifiEnable"), "false")){
		unset($ssids[5]);
		unset($ssids[4]);
	}

	if(strstr($wifi_value['AccessPoint_Enable4'], "false")) unset($ssids[3]);
}

/*- In bridge mode don't show 'Mac filter settings ' -*/
	if(strstr($_SESSION["lanMode"], "bridge-static")){
		unset($ssids[1]);
		unset($ssids[0]);
	}

?>


<style>
.forms .readonlyLabel {
	margin: 4px 40px;
}

#content {
	display: none;
}

</style>
 
<script type="text/javascript">

var G_radio_enable	= <?php echo ($radio_enable === "true" ? "true" : "false"); ?>;
var G_radio_enable1	= <?php echo ($radio_enable1 === "true" ? "true" : "false"); ?>;
var G_wps_enabled	= <?php echo ($wps_enabled === "true" ? "true" : "false"); ?>;
var G_wps_method	= "<?php echo $wps_method; ?>";

$(document).ready(function() {
    comcast.page.init("Gateway > Connection > WiFi", "nav-wifi-config");

    var isBridge = "<?php echo $_SESSION["lanMode"]; ?>";


    //Disable all the MAC filter options in user admin mode if bridge mode is enabled
    if (isBridge == 'bridge-static') {       

        $('.div_private_wifi *').addClass('disabled');
        $('.div_private_wifi .btn').click(function(e) {
            e.preventDefault();
        });

	if ("mso" != "<?php echo $_SESSION["loginuser"]; ?>"){
		$("#mac_admin").addClass('disabled');
		$("#mac_ssid").addClass('disabled').prop('disabled',true);
		$("#filtering_mode").addClass('disabled').prop('disabled',true);
		$("#device_name").addClass('disabled').prop("disabled", true);
		$('[id^="mac_address_"]').addClass('disabled').prop("disabled", true);	//1-6
		$("#add_manual").addClass('disabled').prop("disabled", true);
		$("#save_filter").addClass('disabled').prop("disabled", true);
	}

    };

	$("#radio24_switch").radioswitch({
		id: "radio24-switch",
		radio_name: "at_a_glance",
		id_on: "radio_enable",
		id_off: "radio_disable",
		title_on: "Enable radio 2.4G",
		title_off: "Disable radio 2.4G",
		state: G_radio_enable ? "on" : "off"
	}).change(function(){
		save_enable("radio_enable");
	});
	$("#radio5_switch").radioswitch({
		id: "radio5-switch",
		radio_name: "at_a_glance2",
		id_on: "radio_enable1",
		id_off: "radio_disable1",
		title_on: "Enable radio 5G",
		title_off: "Disable radio 5G",
		state: G_radio_enable1 ? "on" : "off"
	}).change(function(){
		save_enable("radio_enable1");
	});
	$("#wps_switch").radioswitch({
		id: "wps-switch",
		radio_name: "wps",
		id_on: "wps_enabled",
		id_off: "wps_disabled",
		title_on: "Enable WPS",
		title_off: "Disable WPS",
		state: G_wps_enabled ? "on" : "off"
	});
	$("#pin_switch").radioswitch({
		id: "pin-switch",
		radio_name: "pin_switch",
		id_on: "pin_enable",
		id_off: "pin_disable",
		title_on: "Enable WPS PIN",
		title_off: "Disable WPS PIN",
		state: G_wps_method !== "PushButton" ? "on" : "off"
	});

	//DFS_Support1 1-supported 0-not supported
	if("<?php echo $DFS_Support1;?>" == "true"){
		if("<?php echo $channel_number1;?>" >= 52 && "<?php echo $channel_number1;?>" <= 140 ) {
			$('[name="DFS_Channel_Selection"]').prop("disabled", false);
		} else {
			$('[name="DFS_Channel_Selection"]').prop("disabled", true);
		}
	} else {
		$('[name="DFS_Channel_Selection"]').prop("disabled", true);
	}
	
	if("<?php echo $DCS_Supported;?>" == "true"){
		$('[name="DCS_Channel_Selection"]').prop("disabled", false);		
	} else {
		$('[name="DCS_Channel_Selection"]').prop("disabled", true);
	}
	
	if("<?php echo $DCS_Supported1;?>" == "true"){
		$('[name="DCS_Channel_Selection1"]').prop("disabled", false);		
	} else {
		$('[name="DCS_Channel_Selection1"]').prop("disabled", true);
	}

    $("[name='channel']").change(function() {
		if($("#channel_automatic").is(":checked")) {
			document.getElementById('channel_number').disabled = true;
			show_extch(0);
			$("#channel_number").hide();
			$("#auto_channel_number").show();
		}
		else {
			document.getElementById('channel_number').disabled = false;
			show_extch(document.getElementById("channel_number").value);
			$("#channel_number").show();
			$("#auto_channel_number").hide();
		}
	}).trigger("change");
	
    $("[name='channel1']").change(function() {
		if($("#channel_automatic1").is(":checked")) {
			document.getElementById('channel_number1').disabled = true;
			$("#channel_number1").hide();
			$("#auto_channel_number1").show();
		}
		else {
			document.getElementById('channel_number1').disabled = false;
			$("#channel_number1").show();
			$("#auto_channel_number1").hide();
		}
	}).trigger("change");

    	$("#wireless_mode").change(function() {
		if($(this).val() == "b,g,n") {
			jConfirm(
				"WARNING:<br/> Changing the Wi-Fi mode to '802.11 b/g/n' will significantly reduce the performance of your Wi-Fi network. This setting is required only if you have older 'b only' Wi-Fi devices in your network. All newer Wi-Fi devices support '802.11 g/n' mode. Are you sure you want to continue with the change?"
				, "Are You Sure?"
				,function(ret) {
					if(!ret) {
						$("#wireless_mode").val('<?php echo $wireless_mode; ?>').attr("selected","selected");
					}
			});
		}
		if("n"==$("#wireless_mode").attr("value")) {
			$("#mixed_mode").prop("checked", true);
			$("#operation_mode").prop("disabled", false);
		}
		else {
			$("#mixed_mode").prop("checked", true);
			$("#operation_mode").prop("disabled", true);
		}
	});
	
	if("n"=='<?php echo $wireless_mode; ?>') {
		$("#mixed_mode").prop("checked", true);
		$("#operation_mode").prop("disabled", false);
	}
	else {
		$("#mixed_mode").prop("checked", true);
		$("#operation_mode").prop("disabled", true);
	}

    $("#wireless_mode1").change(function() {
		// for green field (no mixed radio mode)
		if($("#wireless_mode1").val().indexOf(",") == -1) {
			$("#mixed_mode1").prop("checked", true);
			$("#operation_mode1").prop("disabled", false);
		}
		else {
			$("#mixed_mode1").prop("checked", true);
			$("#operation_mode1").prop("disabled", true);
		}
	}).trigger("change");
	
    $("#channel_number").change(function() {
		show_extch(document.getElementById("channel_number").value);
	}).trigger("change");
	
	$("[name='channel_bandwidth']").change(function() {
		if ($("#channel_bandwidth20").prop("checked")) {
			$('div [id*="Ext"]').prop("disabled", true);
		}
		else {
			$('div [id*="Ext"]').prop("disabled", false);
		}
	}).trigger("change");
	
	$("[name='channel_bandwidth1']").change(function() {
		//enable all channel first
		$("#channel_number1 option").prop("disabled", false);
		
		//disable some channel as per extension channel when NOT 20MHz in 5G (2.4G able to set channel and extension channel together)
		if (!$("#channel_bandwidth201").prop("checked")) {
			//40MHz
			if ($("#channel_bandwidth1").prop("checked")) {
				if ("BelowControlChannel" == "<?php echo $ext_channel1; ?>"){
					$("#channel_number1").find("[value='36'],[value='44'],[value='52'],[value='60'],[value='100'],[value='108'],[value='116'],[value='132'],[value='140'],[value='144'],[value='149'],[value='157'],[value='165']").prop("disabled", true).prop("selected", false);
				}	
				else{	//AboveControlChannel or Auto  //zqiu: exclude 116,140
					$("#channel_number1").find("[value='40'],[value='48'],[value='56'],[value='64'],[value='104'],[value='112'],[value='116'],[value='136'],[value='140'],[value='144'],[value='153'],[value='161'],[value='165']").prop("disabled", true).prop("selected", false);
				}
			} 
			//80MHz
			else if ($("#channel_bandwidth2").prop("checked")) {
				$("#channel_number1").find("[value='116'],[value='120'],[value='124'],[value='128'],[value='132'],[value='136'],[value='140'],[value='144'],[value='165']").prop("disabled", true).prop("selected", false);			
			}
			// NOT 20MHz, disable channel 165
			$("#channel_number1").find("[value='165']").prop("disabled", true).prop("selected", false);
		}
	}).trigger("change");
	
	$("#pair_method_form").validate({
		debug: true,
		rules: {
			pin_number: {
    			required: function() {
    				return ($("#wps_enabled").prop("checked") && $("#pair_method_pin").prop("checked"));
    			},
			maxlength:9
				     }
			},
		submitHandler: function() {
			if($("#wps_disabled").is(":checked")) {
				jAlert("Please enable WPS first!");
			}
			else {
				pair_client();
			}
		}
	});

    $("#pin_switch").change(function(e, skipSave) {
		var wps_method		= $(this).radioswitch("getState").on ? "PushButton,PIN" : "PushButton";

		if ($("#wps_switch").radioswitch("getState").on === false) {
			$(this).radioswitch("doSwitch", G_wps_method == "PushButton,PIN" ? "on" : "off").radioswitch("doEnable", false);
			return;
		}			
		
		if (wps_method != "PushButton") {
			$("#div_method_pin input").prop("disabled", false);
		}
		else {
			$("#div_method_pin input").prop("disabled", true);
		}
		
		document.getElementById('pair_method_push').checked = true;

		if (!skipSave) {
			save_enable("wps_method");
		}
	});	

    $("#wps_switch").change(function(e, skipSave) {
		var wps_enabled		= $(this).radioswitch("getState").on;

		if (wps_enabled) {
			$("#pair_method_form *").removeClass("disabled").prop("disabled", false);
			$("#pin_switch").radioswitch("doEnable", true).trigger("change", [true]);
		}
		else {
			$("#pair_method_form *").addClass("disabled").prop("disabled", true);
			$("#pin_switch").radioswitch("doEnable", false);
		}

		if (!skipSave) {
			save_enable("wps_enabled");
		}
	});	

	$("#wps_switch").trigger("change", [true]);

	// do not detect ACL for WPS at this time 0509
	
/* 	$("#filtering_mode").change(function() {
			if ($("#filtering_mode").val()!="allow_all" && ("1"==$("#mac_ssid").val() || "2"==$("#mac_ssid").val()))
			{
				jConfirm(
					"WARNING:<br/> Changing MAC Filtering Mode to Allow or Deny will disable Wi-Fi Protected Setup (WPS) functionality. Are you sure you want to change?"
					, "Are You Sure?"
					,function(ret) {
					if(!ret) {
						$("#filtering_mode").val("allow_all")
					}
				});
			}	
	}); */

	var doOnce = true;

    $("#mac_ssid").change(function() {
		var ssid_number		= $("#mac_ssid").attr("value");
		
		var mac_ssid_GET	= "<?php echo $_GET['mac_ssid'];?>";
		if(doOnce && mac_ssid_GET) {
			ssid_number	= mac_ssid_GET;
			$("#mac_ssid").val(mac_ssid_GET);
			doOnce = false;
		}

		var jsConfig 	=	'{"ssid_number":"'+ssid_number+'", "target":"'+"mac_ssid"+'"}';	
		
		//jProgress('This may take several seconds...', 60);
		$("#mac_admin_temp, #mac_admin").toggle();

		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_wireless_network_configuration.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				$("#filtering_mode").attr("value", msg.filtering_mode);
				
				//clear the previous filter_table when ssid changed
				$("#filter_table > tbody").empty();
				for (var i=0; i < msg.ft.length; i++)
				{
					add_row("filter_table", -1, msg.ft[i][0], msg.ft[i][1]);
				}
				
				//clear the previous auto_table when ssid changed
				$("#auto_table > tbody").empty();
				for (var i=0; i < msg.at.length; i++)
				{
					add_row("auto_table", -1, msg.at[i][0], msg.at[i][1]);
				}
				
				//jHide();
				$("#mac_admin_temp, #mac_admin").toggle();
			},
			error: function(){            
				//jHide();
				$("#mac_admin_temp, #mac_admin").toggle();
				jAlert("Failure, please try again.");
			}
		});
	}).trigger("change");

	//==disable some radio mode as per security mode (configured on other page)==
	
	var sec_mod = document.getElementById("private_wifi").rows[1].cells[3].innerHTML;
	// if (sec_mod.indexOf("WEP")!=-1 || sec_mod.indexOf("TKIP")!=-1){
		// $("#wireless_mode option[value='n']").prop("disabled", true);
	// }
	// ONLY deal WEP for UI-4.0
	if (sec_mod.indexOf("WEP")!=-1){
		$("#wireless_mode option[value='n']").prop("disabled", true);
	}

	var sec_mod1 = document.getElementById("private_wifi").rows[2].cells[3].innerHTML;
	// if (sec_mod1.indexOf("WEP")!=-1 || sec_mod1.indexOf("TKIP")!=-1){
		// $("#wireless_mode1").find('[value="n"],[value="ac"],[value="n,ac"]').prop("disabled", true);
	// }
	// ONLY deal WEP for UI-4.0
	if (sec_mod1.indexOf("WEP")!=-1){
		$("#wireless_mode1").find('[value="n"],[value="ac"],[value="n,ac"]').prop("disabled", true);
	}
	
	if ($("#public_wifi").find("tr").length <= 1)
	{
		$("#no_public_wifi").show();
	}

	// remove sections as per loginuser, content must be hidden before doc ready
	if ("mso" != "<?php echo $_SESSION["loginuser"]; ?>"){
		$(".div_enable_radio").remove();
		$(".div_public_wifi").remove();
		$(".div_radio_setting").remove();
		$(".div_wps_setting").remove();
		$(".btn-group").show();
	}
	// xb3_R1_4: just for now, remove public WiFi
	// $(".div_public_wifi").remove();

	if ("false"=="<?php echo $wps_config;?>"){
	/*
		$(".wps_config").html('<h2>Wi-Fi Client Setup Configuration(WPS)</h2>\
		<p style="color:red;font-size:130%;font-style:bold;">WPS function is disabled and can not be enabled now!</p>\
		<p>You can take these steps to enable WPS:</p>\
		<p/>(1) enable at least one private Wi-Fi interface</p>\
		<p/>(2) its security mode is not WEP/WPA-TKIP/WPA2-TKIP</p>\
		<p/>(3) its network name is not hidden</p>\
		<p/>(4) its MAC filter function is not enabled</p>\
		<p/>Then please refresh(or back to) this page and try again.</p>');
		$("#pair_method_form").remove();
	*/
		$(".wps_config *").not(".radioswitch_cont, .radioswitch_cont *").unbind("click").prop("disabled", true).addClass("disabled").removeClass("selected");
		$(".wps_config .radioswitch_cont").radioswitch("doEnable", false);
	}
	
	// disable NOT 20MHz channel if 165
	// Warning for DFS channel (52-140)
	$("#channel_number1").change(function(){
		var channel = $("#channel_number1 option:selected").val();

		if(channel >= 52 && channel <= 140 ) {
			jConfirm(
				"WARNING:<br/> You are selecting a Dynamic Frequency Selection (DFS) Channel (52-140). Some Wi-Fi devices do not support DFS channels in the 5 GHz band. For those devices that do not support DFS channels, the 5 GHz Wi-Fi Network Name (SSID) will not be displayed on the list of available networks. Do you wish to continue?"
				, "Are You Sure?"
				,function(ret) {
					if(!ret) {
						$("#channel_number1").val('<?php echo $channel_number1; ?>').attr("selected","selected");
					}
			});
		}

		//DFS_Support1 1-supported 0-not supported
		if("<?php echo $DFS_Support1;?>" == "true"){
			if($("#channel_number1 option:selected").val() >= 52 && $("#channel_number1 option:selected").val() <= 140) {
				$('[name="DFS_Channel_Selection"]').prop("disabled", false);
			} else {
				$('[name="DFS_Channel_Selection"]').prop("disabled", true);
			}
		} else {
			$('[name="DFS_Channel_Selection"]').prop("disabled", true);
		}

		if ("165" == $(this).val()){
			$('[name="channel_bandwidth1"]:not([value="20MHz"])').prop("disabled", true);
		}
		else{
			$('[name="channel_bandwidth1"]').prop("disabled", false);
		}
	});

	// disable NOT 20MHz channel if 165	
	if ( "true" != "<?php echo $channel_automatic1; ?>"  && "165" == "<?php echo $channel_number1; ?>"){
		$('[name="channel_bandwidth1"]:not([value="20MHz"])').prop("disabled", true);
	}
	else{
		$('[name="channel_bandwidth1"]').prop("disabled", false);
	}

	// now we can show target content
	$("#content").show();

});

function set_config(jsConfig)
{
	// alert(jsConfig);
	jProgress('This may take several seconds...', 60);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_wireless_network_configuration.php",
		data: { configInfo: jsConfig },
		success: function(msg) {
			jHide();
			window.location.href = "wireless_network_configuration.php";
		},
		error: function(){            
			jHide();
			jAlert("Failure, please try again.");
		}
	});
}

function show_extch(ch)
{
	for (var i=0; i<12; i++)
	{
		if(ch == i)
		{
			document.getElementById("Ext"+i).style.display="";
		}
		else
		{
			document.getElementById("Ext"+i).style.display="none";
		}
	}
}

function check_add()
{
	var name = $("#device_name").val();
	var addr = $("#mac_address_1").attr("value")
		  +":"+$("#mac_address_2").attr("value")
		  +":"+$("#mac_address_3").attr("value")
		  +":"+$("#mac_address_4").attr("value")
		  +":"+$("#mac_address_5").attr("value")
		  +":"+$("#mac_address_6").attr("value");
	var sHex = $("#mac_address_1").attr("value");
		  
	if ("" == name.replace(/\s/g, '')) {
		return ["ERROR", "Please enter device name!"];
	}
	if (!RegExp("^([0-9a-fA-F]{2})(([/\s:-][0-9a-fA-F]{2}){5})$").test(addr) 
		|| ("00:00:00:00:00:00"==addr)
		|| (parseInt(sHex,16)%2 != 0) ) {
		return ["ERROR", "Please enter valid MAC address! \n First byte must be even. \n Each character must be [0-9a-fA-F]."];
	}
	
	$("#device_name").val("");
	$("#mac_address_1").val("");
	$("#mac_address_2").val("");
	$("#mac_address_3").val("");
	$("#mac_address_4").val("");
	$("#mac_address_5").val("");
	$("#mac_address_6").val("");

	return [name, addr];
}

function adjust_row(tid)
{
	// var tb = document.getElementById(tid);
	// for (var i=1, j=1; i < tb.rows.length; i++, j++)
	// {
		// var tr = tb.rows[i];
		// tr.className = j%2 ? "form-row odd" : "form-row";
		// if ("filter_table" == tid)
		// {
			// tr.cells[0].innerHTML = i;
		// }
	// }

	var i=1;
	$("#"+tid+" > tbody > tr").each(function(){
		if ("filter_table" == tid){
			$(this).find("td:eq(0)").text(i);
		}
		
		if (i++ % 2){
			$(this).addClass("odd");
		}
		else{
			$(this).removeClass("odd");
		}	
	});
}

function add_row(tid, idex, name, addr) 
{
	var tb  = document.getElementById(tid);
	var len = tb.rows.length;
	
	if (len == -1) {
		idex = tb.rows.length;
	}
	else if (len > 17) {
		jAlert("No more than 16 devices can be added!");
		return;
	}
	
	if ("filter_table" == tid) {
		for (var i=1; i < tb.rows.length; i++) {
			if (addr == tb.rows[i].cells[2].innerHTML) {
				jAlert("MAC address already exist!");
				return;
			}
		}

		$("#filter_table").append('<tr><td headers="acl-Index">'+idex+'</td><td headers="acl-Name">'+name+'</td><td headers="acl-MAC">'+addr+
		'</td><td  headers="acl-Blank" class="delete"><input class="btn" type="button" value="   X   " onclick="del_row(this)"/></td></tr>');
	}
	else if ("auto_table" == tid) {
		for (var i=1; i < tb.rows.length; i++) {
			if (addr == tb.rows[i].cells[1].innerHTML) {
				jAlert("MAC address already exist!");
				return;
			}
		}

		$("#auto_table").append('<tr><td headers="auto-Name">'+name+'</td><td headers="auto-MAC">'+addr+
		'</td><td headers="auto-Blank" class="edit"><input class="btn" type="button" value="ADD" onclick="add_auto(this)"/></td></tr>');
	}
	
	adjust_row(tid);
}

function del_row(row) 
{
	jConfirm(
		"Are you sure you want to delete this entry from Wi-Fi Control List?"
		, "Are You Sure?"
		,function(ret) {
			if(ret) {
				var idex = row.parentNode.parentNode.rowIndex;
				document.getElementById("filter_table").deleteRow(idex);
				adjust_row("filter_table");
			}
		}
	);

}

function add_auto(row)
{
	var idex = row.parentNode.parentNode.rowIndex;
	var tr = document.getElementById("auto_table").rows[idex];
	add_row("filter_table", -1, tr.cells[0].innerHTML, tr.cells[1].innerHTML);
}

function add_manual()
{
	var ret = check_add();
	if ("ERROR" == ret[0])
	{
		jAlert(ret[1]);
	}
	else
	{
		add_row("filter_table", -1, ret[0], ret[1]);
	}
}

function save_filter()
{
	var ssid_number	=	$("#mac_ssid").attr("value");
	var ft = new Array();
	// var tb = document.getElementById("filter_table");
	var filtering_mode = $("#filtering_mode").attr("value");
	
	// for (var i=1, j=0; i < tb.rows.length; i++, j++)
	// {
		// var tr = tb.rows[i];
		// ft[i-1]=[tr.cells[1].innerHTML, tr.cells[2].innerHTML];
	// }
	
	var i=0;
	$("#filter_table > tbody > tr").each(function(){
		ft[i++]=[$(this).find("td:eq(1)").text(), $(this).find("td:eq(2)").text()];
	});
	
	//notice the "''" with array var	
	var jsConfig 	=	'{"ssid_number":"'+ssid_number+'", "filtering_mode":"'+filtering_mode+'", "ft":'+JSON.stringify(ft)+', "target":"'+"save_filter"+'"}';	
	
	set_config(jsConfig);
}

function save_config(ssid_number, sub_target)
{
	var suf = (("2"==ssid_number) ? "1" : "");
	
	var wireless_mode 	= $("#wireless_mode"+suf).attr("value");
	var transmit_power 	= $("#transmit_power"+suf).attr("value");
	var channel_automatic 	= $("#channel_automatic"+suf).prop("checked");
	var channel_number 	= $("#channel_number"+suf).attr("value");
	var BG_protect_mode 	= $("#BG_protection_mode"+suf).attr("value");
	var IGMP_Snooping	= $("#IGMP_Snooping_enabled"+suf).prop("checked");
	var operation_mode 	= $("#operation_mode"+suf).prop("checked");
	var channel_bandwidth	= $('[name="channel_bandwidth'+suf+'"]:checked').attr("value");
	var guard_interval 	= $('[name="guard_interval'+suf+'"]:checked').attr("value");
	var reverse_enabled 	= $("#reverse_enabled"+suf).prop("checked");
	var ext_channel 	= (("1"==ssid_number && false==channel_automatic) ? ($("#Extension_Channel"+channel_number).val()) : "Auto");
	var MSDU_enabled 	= $("#MSDU_enabled"+suf).prop("checked");
	var blockACK_enabled 	= $("#blockACK_enabled"+suf).prop("checked");
	var blockBA_enabled 	= $("#blockBA_enabled"+suf).prop("checked");
	var DFS_Selection	= $("#DFS_Channel_Selection_enabled").prop("checked");
	var DCS_Selection	= $("#DCS_Channel_Selection_enabled"+suf).prop("checked");
	var HT_TxStream 	= $("#HT_TxStream"+suf).attr("value");
	var HT_RxStream 	= $("#HT_RxStream"+suf).attr("value");
	var WMM_power_save 	= $("#WMM_power_save"+suf).prop("checked");
	var STBC_enabled 	= $("#STBC_enabled"+suf).prop("checked");

	var jsConfig = '{"wireless_mode":"'+wireless_mode+'", "transmit_power":"'+transmit_power+'", "channel_automatic":"'+channel_automatic
					+'", "channel_number":"'+channel_number+'", "BG_protect_mode":"'+BG_protect_mode
					+'", "IGMP_Snooping":"'+IGMP_Snooping+'", "operation_mode":"'+operation_mode
					+'", "channel_bandwidth":"'+channel_bandwidth+'", "guard_interval":"'+guard_interval+'", "reverse_enabled":"'+reverse_enabled
					+'", "ext_channel":"'+ext_channel+'", "MSDU_enabled":"'+MSDU_enabled+'", "blockACK_enabled":"'+blockACK_enabled
					+'", "blockBA_enabled":"'+blockBA_enabled+'", "DFS_Selection":"'+DFS_Selection
					+'", "DCS_Selection":"'+DCS_Selection
					+'", "HT_TxStream":"'+HT_TxStream+'", "HT_RxStream":"'+HT_RxStream
					+'", "WMM_power_save":"'+WMM_power_save+'", "STBC_enabled":"'+STBC_enabled
					+'", "target":"'+"save_config"+'", "ssid_number":"'+ssid_number+'", "sub_target":"'+sub_target+'"}';
		
	set_config(jsConfig);
}

function save_enable(sub_target)
{
	var radio_enable	= $("#radio24_switch").radioswitch("getState").on;
	var ssid_number		= $("#wps_ssid").attr("value");
	var wps_enabled		= $("#wps_switch").radioswitch("getState").on;
	var wps_method		= $("#pin_switch").radioswitch("getState").on ? "PushButton,PIN" : "PushButton";

	if ("radio_enable" == sub_target) {
		ssid_number = "1";
		if (G_radio_enable == radio_enable) return;
		G_radio_enable = radio_enable;
	}
	else if ("radio_enable1" == sub_target) {
		ssid_number = "2";
		radio_enable =	$("#radio5_switch").radioswitch("getState").on;
		sub_target = "radio_enable";
		if (G_radio_enable1 == radio_enable) return;
		G_radio_enable1 = radio_enable;
	}
	else if ("wps_enabled" == sub_target) {
		if (G_wps_enabled == wps_enabled) return;
		G_wps_enabled = wps_enabled;
	}
	else if ("wps_method" == sub_target) {
		if (G_wps_method == wps_method || !wps_enabled) return;
		G_wps_method = wps_method;
	}
	
	var jsConfig = '{"radio_enable":"'+radio_enable+'", "wps_enabled":"'+wps_enabled+'", "wps_method":"'+wps_method
					+'", "target":"'+"save_enable"+'", "sub_target":"'+sub_target+'", "ssid_number":"'+ssid_number+'"}';	
	
	set_config(jsConfig);	
}

function validChecksum(PIN)
{
	if (PIN.search(/^(\d{4}|\d{8}|\d{4}[\-|\s]\d{4})$/) != 0) return false;
	PIN = PIN.replace(" ","");
	PIN = PIN.replace("-","");
	var accum = 0;
	accum += 3 * (parseInt(PIN / 10000000) % 10);
	accum += 1 * (parseInt(PIN / 1000000) % 10);
	accum += 3 * (parseInt(PIN / 100000) % 10);
	accum += 1 * (parseInt(PIN / 10000) % 10);
	accum += 3 * (parseInt(PIN / 1000) % 10);
	accum += 1 * (parseInt(PIN / 100) % 10);
	accum += 3 * (parseInt(PIN / 10) % 10);
	accum += 1 * (parseInt(PIN / 1) % 10);
	return (0 == (accum % 10));
}


function pair_client()
{
	var ssid_number	=	$("#wps_ssid").attr("value");
	var pair_method =	$('[name="pair_method"]:checked').attr("value");
	var pin_number = 	$("#pin_number").attr("value");
	
	var jsConfig = '{"ssid_number":"'+ssid_number+'", "pair_method":"'+pair_method+'", "pin_number":"'+pin_number
					+'", "target":"'+"pair_client"+'"}';	
	
	if ("PushButton"!=pair_method && !validChecksum(pin_number))
	{
		jAlert("Invalid PIN!");
		return;
	}
	
	jProgress('This may take several seconds...', 60);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_wireless_network_configuration.php",
		data: { configInfo: jsConfig },
		success: function(msg) {
			jHide();
			jAlert("WPS in Progress!");
			// if (msg.pair_res == "success")
			// {
				// jAlert("Connection established!");
			// }
			// else
			// {
				// jAlert("Error, please try again!");
			// }
		},
		error: function(){            
			jHide();
			jAlert("Failure, please try again.");
		}
	});	
}

function pair_cancel()
{
	var ssid_number		= $("#wps_ssid").attr("value");
	var wps_enabled		= $("#wps_switch").radioswitch("getState").on;
	
	var jsConfig = '{"ssid_number":"'+ssid_number+'", "target":"'+"pair_cancel"+'"}';	

	if (!wps_enabled) {
		jAlert("Please enable WPS first!");
		return;
	}	
	
	jConfirm(
		"Are you sure you want to cancel WPS progress?"
		,"Confirm:"
		,function(ret) {
			if(ret) {
				jProgress('WPS progress cancelling...', 60);
				$.ajax({
					type: "POST",
					url: "actionHandler/ajaxSet_wireless_network_configuration.php",
					data: { configInfo: jsConfig },
					success: function(msg) {
						jHide();
						jAlert("WPS progress is canceled!");
					},
					error: function(){            
						jHide();
						jAlert("Failure, please try again.");
					}
				});	
			}
		}
	);	
}

</script>

<div id="content">
<h1>Gateway > Connection > Wi-Fi</h1>
<div id="educational-tip">
	<p class="tip">Manage your Wi-Fi connection settings.</p>
	<p class="hidden">Click <strong>EDIT</strong> next to the Network Name you'd like to modify its Wi-Fi network settings: Network Name (SSID), Mode, Security Mode, Channel, Network Password (Key), and Broadcasting feature.</p>
	<p class="hidden" ><strong>MAC Filter Setting</strong> is specific to each Network Name (SSID). Select a MAC Filtering Mode.</p>
	<span class="hidden" style="position:relative; top:-15px ; left: 2px;"><ul style="margin: 1.3em; "><li type="disc" >Allow- All (Default): All wireless client stations can connect to the Gateway; no MAC filtering rules.</li>
	<li type="disc">Allow: Only the devices in the "Wireless Control List" are allowed to connect to the Gateway.</li>
	<li type="disc">Deny: Wireless devices in the "Wireless Control List" are not allowed to connect to the Gateway.</li></ul></span>
	<p class="hidden" style="position:relative; top:-20px ; left: 2px;"><strong>Wireless Control List:</strong> Displays the wireless devices (by Network Name and MAC Address) that were manually added or auto-learned.</p>
	<p class="hidden" style="position:relative; top:-20px ; left: 2px;"><strong>Auto-Learned Wireless Devices</strong> are currently connected to the Gateway. </p>
	<p class="hidden" style="position:relative; top:-20px ; left: 2px;"><strong>Manually-Added Wireless Devices:</strong> Enter a unique name and MAC address for the wireless device you want to manually add, then click <strong>ADD.</strong> </p>
</div>

<div class="module div_enable_radio">
	<div class="select-row">
	<span class="readonlyLabel label">Wi-Fi Radio(2.4 GHz)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
	<span id="radio24_switch"></span>
	</div>
</div>

<div class="module div_enable_radio">
	<div class="select-row">
	<span class="readonlyLabel label">Wi-Fi Radio(5 GHz)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
	<span id="radio5_switch"></span>
	</div>
</div>

<div class="module data data div_private_wifi">
	<h2>Private Wi-Fi Network</h2>
	<table class="data" id="private_wifi" summary="Private Wi-Fi Network">
	<tr>
		<th id="private-Name" class="name" width="20%">Name</th>
		<th id="private-Frequency" class="name" width="20%">Frequency Band:</th>
		<th id="private-MAC" class="protocals" width="20%">MAC Address</th>
		<th id="private-Security" class="security" width="30%">Security Mode</th>
		<th id="private-Blank" class="edit" width="10%">&nbsp;</th>
	</tr>
	<tr class="form-row odd">
		<td headers="private-Name"><b><font color="black"><?php echo $network_name; ?></font></b></td>
		<td headers="private-Frequency"><?php echo $feq_band; ?></td>
		<td headers="private-MAC"><?php echo $mac_address; ?>   </td>
		<td headers="private-Security"><?php echo encrypt_map($encrypt_mode, $encrypt_method);?></td>
		<td headers="private-Blank"><a href="wireless_network_configuration_edit.php?id=1" class="btn">Edit</a></td>
	</tr>
	<tr class="form-row">
		<td headers="private-Name"><b><font color="black"><?php echo $network_name1; ?></font></b></td>
		<td headers="private-Frequency"><?php echo $feq_band1; ?></td>
		<td headers="private-MAC"><?php echo $mac_address1; ?>   </td>
		<td headers="private-Security"><?php echo encrypt_map($encrypt_mode1, $encrypt_method1); ?></td>
		<td headers="private-Blank"><a href="wireless_network_configuration_edit.php?id=2" class="btn">Edit</a></td>
	</tr>
	</table>
	<div class="btn-group" style="display: none;">
		<a href="wireless_network_configuration_wps.php" class="btn">Add Wi-Fi Protected Setup (WPS) Client</a>
	</div>
</div> <!-- end .module -->


<div class="module data data div_public_wifi">
	<h2>Public Wi-Fi Network</h2>
	<table class="data" id="public_wifi" summary="Public Wi-Fi Network">
		<tbody>
		<tr>
			<th id="public-Name" width="20%" class="name">Name</th>
			<th id="public-Frequency" class="name">Frequency Band</th>
			<th id="public-MAC" width="20%" class="protocals">MAC Address</th>
			<th id="public-Security" width="30%" class="security">Security Mode</th>
			<th id="public-Blank" width="10%" class="edit">&nbsp;</th>
		</tr>

		<?php
		//$ssids 		= explode(",", getInstanceIds("Device.WiFi.SSID."));
		$public_v	= array();
		$odd 		= true;

		foreach ($ssids as $i)
		{
			if (intval($i)<3 || intval($i)>6){		//SSID 1,2 for Private, 3,4 for Home Security, 5,6 for Hot Spot
				continue;
			}
			array_push($public_v, array(
				'sufix'	=> (intval($i)==5 || intval($i)==6) ? "_public" : "",
				'id'	=> $i,
				'ssid'	=> $wifi_value['SSID_SSID'.$i],
				'freq'	=> intval($i)%2 ? "2.4 GHz" : "5 GHz",
				'bssid'	=> $wifi_value['SSID_BSSID'.$i],
				'secur'	=> encrypt_map($wifi_value['ModeEnabled'.$i], $wifi_value['EncrypMethod'.$i])
				));
		}

		for ($j=0; $j<count($public_v); $j++)
		{
			echo '<tr class="'.(($odd=!$odd)?"odd":"even").'">';
			echo 	'<td headers="public-Name"><b><font color="black">'.$public_v[$j]['ssid'].'</font></b> </td>';
			echo 	'<td headers="public-Frequency">'.$public_v[$j]['freq'].'</td>';
			echo 	'<td headers="public-MAC">'.$public_v[$j]['bssid'].'</td>';
			echo 	'<td headers="public-Security">'.$public_v[$j]['secur'].'</td>';
			echo 	'<td headers="public-Blank"><a class="btn '.$public_v[$j]['sufix'].'" href="wireless_network_configuration_edit'.$public_v[$j]['sufix'].'.php?id='.$public_v[$j]['id'].'">Edit</a></td>';
			echo '</tr>';
		}
		?>
		</tbody>
	</table>
	<div id="no_public_wifi" style="display: none;">
		<p>There are no valid public Wi-Fi found!</p>
	</div>
</div>



<div class="module data data div_radio_setting">
	<h2>2.4GHz Wireless Basic Setting</h2>
	<div class="form-row">
		<label for="wireless_mode">Mode:</label>
		<select name="wireless_mode" id="wireless_mode">
		<!--option value="n"  		<?php //if ("n" == $wireless_mode) echo 'selected="selected"';?> >802.11 n</option-->
		<option value="g,n" 	<?php if ("g,n" == $wireless_mode) echo 'selected="selected"';?> >802.11 g/n</option>
		<option value="b,g,n" 	<?php if ("b,g,n" == $wireless_mode) echo 'selected="selected"';?> >802.11 b/g/n</option>
		</select>
	</div>
	<div class="form-row odd">
		<label for="transmit_power">Transmit Power:</label>
		<select name="transmit_power" id="transmit_power">
			<?php $int_power = (int)$transmit_power; ?>
			<option value="100" <?php if ($int_power>75) echo 'selected="selected"';?> >100%</option>
			<option value="75"  <?php if ($int_power>50 && $int_power<=75) echo 'selected="selected"';?> >75%</option>
			<option value="50"  <?php if ($int_power>25 && $int_power<=50) echo 'selected="selected"';?> >50%</option>
			<option value="25"  <?php if ($int_power>12 && $int_power<=25) echo 'selected="selected"';?> >25%</option>
			<option value="12"  <?php if ($int_power <=12) echo 'selected="selected"';?> >12.5%</option>
		</select>
	</div>
	<div class="form-row " id="channel_switch">
		<label for="channel_manual">Channel Selection:</label>
		<input type="radio"  name="channel" value="manual"  id="channel_manual" checked="checked" /><b>Manual</b>
		<label for="channel_automatic" class="acs-hide"></label>
		<input type="radio"  name="channel" value="auto" 	id="channel_automatic" <?php if ("true" == $channel_automatic) echo 'checked="checked"';?> /><b>Automatic</b>
	</div>
	<div id="old_channel_number" class="form-row odd manual-only">
		<label for="channel_number">Channel:</label>
		<select name="channel_number" id="channel_number">
			<?php
				//dynamic generate possible channels
				$channels = explode(",", $possible_channels);
				foreach ($channels as $val){
					echo '<option value="'.$val.($val==$channel_number ? '" selected="selected">' : '" >').$val.'</option>';
				}
			?>
		</select>
		<label for="auto_channel_number" class="acs-hide"></label>
		<select id="auto_channel_number" disabled="disabled"><option selected="selected" ><?php echo $channel_number; ?></option></select>
	</div>
	<div class="form-row ">
		<div class="form-btn">
		<label for="save_basic" class="acs-hide"></label>
			<input type="submit" id="save_basic" value="Save Basic Setting" class="btn right" onclick="save_config('1', 'save_basic');"/>
		</div>
	</div>
</div>



<div class="module data data div_radio_setting">
	<h2>2.4GHz Wireless Advanced Setting</h2><br/>
	<div class="form-row">
		<label for="BG_protection_mode">BG Protection Mode:</label>
		<select name="BG_protection_mode" id="BG_protection_mode">
		<option value="Auto" selected="selected">Auto</option>
		<option value="Disabled" <?php if ("Disabled"==$BG_protect_mode) echo 'selected="selected"';?> >Manual</option>
		</select>
	</div>
	<div class="form-row odd">
		<label for="IGMP_Snooping_disabled">IGMP Snooping</label>
		<input type="radio"  name="IGMP_Snooping" value="false" id="IGMP_Snooping_disabled" checked="checked" /><b>Disable</b>
		<label for="IGMP_Snooping_enabled" class="acs-hide"></label>
		<input type="radio"  name="IGMP_Snooping" value="true"  id="IGMP_Snooping_enabled" <?php if ("true"==$IGMP_Snooping) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row">
		<label for="mixed_mode">Operation Mode:</label>
		<input type="radio"  name="operation_mode" value="false" id="mixed_mode" checked="checked" /><b>Mixed Mode</b>
		<label for="operation_mode" class="acs-hide"></label>
		<input type="radio"  name="operation_mode" value="true"  id="operation_mode" <?php if ("true"==$operation_mode) echo 'checked="checked"';?> /><b>Green Field</b>
	</div>
	<div class="form-row odd" id="bandwidth_switch">
		<label for="channel_bandwidth20">Channel Bandwidth:</label>
		<input type="radio"  name="channel_bandwidth" value="20MHz" id="channel_bandwidth20" checked="checked" /><b>20</b>
		<label for="channel_bandwidth" class="acs-hide"></label>
		<input type="radio"  name="channel_bandwidth" value="40MHz" id="channel_bandwidth" <?php if ("40MHz"==$channel_bandwidth) echo 'checked="checked"';?> /><b>20/40</b>
	</div>
	<div class="form-row">
		<label for="guard_interval800ns">Guard Interval:</label>
		<input type="radio"  name="guard_interval" value="400nsec"  id="guard_interval800ns" checked="checked" /><b>400ns</b>
		<label for="guard_interval400ns" class="acs-hide"></label>
		<input type="radio"  name="guard_interval" value="800nsec"  id="guard_interval400ns" <?php if ("800nsec"==$guard_interval) echo 'checked="checked"';?> /><b>800ns</b>
		<label for="guard_intervalauto" class="acs-hide"></label>
		<input type="radio"  name="guard_interval" value="Auto"     id="guard_intervalauto"  <?php if ("Auto"==$guard_interval) echo 'checked="checked"';?> /><b>Auto</b>
	</div>
	<div class="form-row odd">
		<label for="Reverse_Direction_Grant_disabled">Reverse Direction Grant</label>
		<input type="radio"  name="Reverse_Direction_Grant" value="disabled" id="Reverse_Direction_Grant_disabled" checked="checked" /><b>Disable</b>
		<label for="reverse_enabled" class="acs-hide"></label>
		<input type="radio"  name="Reverse_Direction_Grant" value="enabled"  id="reverse_enabled" <?php if ("true"==$reverse_enabled) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row" id="Ext1" style="display:none;">
		<label for="Extension_Channel1">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel1">
		<option value="AboveControlChannel" selected="selected">2442MHz(Channel5)</option>
		</select>
	</div>
	<div class="form-row" id="Ext2" style="display:none;">
		<label for="Extension_Channel2">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel2">
		<option value="AboveControlChannel" selected="selected">2447MHz(Channel6)</option>
		</select>
	</div>
	<div class="form-row" id="Ext3" style="display:none;">
		<label for="Extension_Channel3">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel3">
		<option value="AboveControlChannel" selected="selected">2452MHz(Channel7)</option>
		</select>
	</div>
	<div class="form-row" id="Ext4" style="display:none;">
		<label for="Extension_Channel4">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel4">
		<option value="AboveControlChannel" selected="selected">2457MHz(Channel8)</option>
		</select>
	</div>
	<div class="form-row" id="Ext5" style="display:none;">
		<label for="Extension_Channel5">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel5">
		<option value="AboveControlChannel" <?php if ("AboveControlChannel"==$ext_channel) echo 'selected="selected"';?> >2462MHz(Channel9)</option>
		<option value="BelowControlChannel" <?php if ("BelowControlChannel"==$ext_channel) echo 'selected="selected"';?> >2437MHz(Channel1)</option>
		</select>
	</div>
	<div class="form-row" id="Ext6" style="display:none;">
		<label for="Extension_Channel6">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel6">
		<option value="AboveControlChannel" <?php if ("AboveControlChannel"==$ext_channel) echo 'selected="selected"';?> >2467MHz(Channel10)</option>
		<option value="BelowControlChannel" <?php if ("BelowControlChannel"==$ext_channel) echo 'selected="selected"';?> >2427MHz(Channel2)</option>
		</select>
	</div>
	<div class="form-row" id="Ext7" style="display:none;">
		<label for="Extension_Channel7">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel7">
		<option value="AboveControlChannel" <?php if ("AboveControlChannel"==$ext_channel) echo 'selected="selected"';?> >2472MHz(Channel11)</option>
		<option value="BelowControlChannel" <?php if ("BelowControlChannel"==$ext_channel) echo 'selected="selected"';?> >2432MHz(Channel3)</option>
		</select>
	</div>
	<div class="form-row" id="Ext8" style="display:none;">
		<label for="Extension_Channel8">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel8">
		<option value="BelowControlChannel" selected="selected">2437MHz(Channel4)</option>
		</select>
	</div>
	<div class="form-row" id="Ext9" style="display:none;">
		<label for="Extension_Channel9">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel9">
		<option value="BelowControlChannel" selected="selected">2442MHz(Channel5)</option>
		</select>
	</div>
	<div class="form-row" id="Ext10" style="display:none;">
		<label for="Extension_Channel10">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel10">
		<option value="BelowControlChannel" selected="selected">2447MHz(Channel6)</option>
		</select>
	</div>
	<div class="form-row" id="Ext11" style="display:none;">
		<label for="Extension_Channel11">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel11">
		<option value="BelowControlChannel" selected="selected">2452MHz(Channel7)</option>
		</select>
	</div>
	<div class="form-row" id="Ext0" style="">
		<label for="Extension_Channel0">Extension Channel:</label>
		<select name="Extension_Channel" id="Extension_Channel0">
		<option value="Auto" selected="selected">Auto</option>
		</select>
	</div>

	<div class="form-row odd">
		<label for="Aggregation_MSDU(A-MSDU)_disabled">Aggregation MSDU(A-MSDU)</label>
		<input type="radio"  name="Aggregation_MSDU(A-MSDU)" value="disabled" id="Aggregation_MSDU(A-MSDU)_disabled" checked="checked" /><b>Disable</b>
		<label for="MSDU_enabled" class="acs-hide"></label>
		<input type="radio"  name="Aggregation_MSDU(A-MSDU)" value="enabled"  id="MSDU_enabled" <?php if ("true"==$MSDU_enabled) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row">
		<label for="Auto_Block_Ack_disabled">Auto Block Ack:</label>
		<input type="radio"  name="Auto_Block_Ack" value="disabled"  id="Auto_Block_Ack_disabled" checked="checked" /><b>Disable</b>
		<label for="blockACK_enabled" class="acs-hide"></label>
		<input type="radio"  name="Auto_Block_Ack" value="enabled"   id="blockACK_enabled" <?php if ("true"==$blockACK_enabled) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row odd">
		<label for="Decline_BA_Request_disabled">Decline BA Request:</label>
		<input type="radio"  name="Decline_BA_Request" value="disabled" id="Decline_BA_Request_disabled" checked="checked" /><b>Disable</b>
		<label for="blockBA_enabled" class="acs-hide"></label>
		<input type="radio"  name="Decline_BA_Request" value="enabled"  id="blockBA_enabled" <?php if ("true"==$blockBA_enabled) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row">
		<label for="HT_TxStream">HT TxStream:</label>
		<select name="HT_TxStream" id="HT_TxStream">
		<option value="3" selected="selected" >3</option>
		<option value="2" <?php if ("2"==$HT_TxStream) echo 'selected="selected"';?> >2</option>
		<option value="1" <?php if ("1"==$HT_TxStream) echo 'selected="selected"';?> >1</option>
		</select>
	</div>
	<div class="form-row odd">
		<label for="HT_RxStream">HT RxStream:</label>
		<select name="HT_RxStream" id="HT_RxStream">
		<option value="3" selected="selected" >3</option>
		<option value="2" <?php if ("2"==$HT_RxStream) echo 'selected="selected"';?> >2</option>
		<option value="1" <?php if ("1"==$HT_RxStream) echo 'selected="selected"';?> >1</option>
		</select>
	</div>

	<div class="form-row">
		<label for="WMM_power_save">WMM Power Save:</label>
		<input type="checkbox" id="WMM_power_save" name="WMM_power_save" <?php if ("true"==$WMM_power_save) echo 'checked="checked"';?> <?php if ("false"==$enableWMM) echo 'disabled="disabled"';?> /> 
		<span class="footnote" >This item depends on WMM. Enable WMM in atleast one SSID to make this work.</span>
	</div>
	<div class="form-row odd">
		<label for="STBC_disabled">STBC:</label>
		<input type="radio"  name="STBC" value="disabled" id="STBC_disabled" checked="checked" /><b>Disable</b>
		<label for="STBC_enabled" class="acs-hide"></label>
		<input type="radio"  name="STBC" value="enabled"  id="STBC_enabled" <?php if ("true"==$STBC_enabled) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row">	
		<label for="DCS_Channel_Selection_disabled">Dynamic Channel Selection:</label>
		<input type="radio"  name="DCS_Channel_Selection" value="disabled" id="DCS_Channel_Selection_disabled" checked="checked" /><b>Disable</b>
		<label for="DCS_Channel_Selection_enabled" class="acs-hide"></label>
		<input type="radio"  name="DCS_Channel_Selection" value="enabled"  id="DCS_Channel_Selection_enabled" <?php if ("true"==$DCS_Enable) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row odd">
		<div class="form-btn">
		<label for="save_advance" class="acs-hide"></label>
			<input type="submit" id="save_advance" value="Save Advanced Settings" class="btn" onclick="save_config('1', 'save_advance');"/>
		</div>
	</div>
</div>

<div class="module data data div_radio_setting">
	<h2>5GHz Wireless Basic Setting</h2>
	<div class="form-row">
		<label for="wireless_mode1">Mode:</label>
		<select name="wireless_mode1" id="wireless_mode1">
		<?php if (strstr($support_mode_5g, "ac")){ ?>
            <option value="n"       <?php if ("n"      == $wireless_mode1) echo 'selected="selected"';?> >802.11 n</option>
			<option value="ac" 		<?php if ("ac"     == $wireless_mode1) echo 'selected="selected"';?> >802.11 ac</option>
			<option value="n,ac"	<?php if ("n,ac"   == $wireless_mode1) echo 'selected="selected"';?> >802.11 n/ac</option>
			<option value="a,n,ac"	<?php if ("a,n,ac" == $wireless_mode1) echo 'selected="selected"';?> >802.11 a/n/ac</option>	
		<?php } else{ ?>
			<option value="n"   	<?php if ("n"      == $wireless_mode1) echo 'selected="selected"';?> >802.11 n</option>
			<option value="a,n" 	<?php if ("a,n"    == $wireless_mode1) echo 'selected="selected"';?> >802.11 a/n</option>	
		<?php }	?>
		</select>
	</div>
	<div class="form-row odd">
		<label for="transmit_power1">Transmit Power:</label>
		<select name="transmit_power1" id="transmit_power1">
			<?php $int_power = (int)$transmit_power1; ?>
			<option value="100" <?php if ($int_power>75) echo 'selected="selected"';?> >100%</option>
			<option value="75"  <?php if ($int_power>50 && $int_power<=75) echo 'selected="selected"';?> >75%</option>
			<option value="50"  <?php if ($int_power>25 && $int_power<=50) echo 'selected="selected"';?> >50%</option>
			<option value="25"  <?php if ($int_power>12 && $int_power<=25) echo 'selected="selected"';?> >25%</option>
			<option value="12"  <?php if ($int_power <=12) echo 'selected="selected"';?> >12.5%</option>
		</select>
	</div>
	<div class="form-row " id="channel_switch1">
		<label for="channel_manual1">Channel Selection:</label>
		<input type="radio"  name="channel1" value="manual"  id="channel_manual1" <?php if ("true" != $channel_automatic1) echo 'checked="checked"';?> /><b>Manual</b>
		<label for="channel_automatic1" class="acs-hide"></label>
		<input type="radio"  name="channel1" value="auto" 	id="channel_automatic1" <?php if ("true" == $channel_automatic1) echo 'checked="checked"';?> /><b>Automatic</b>
	</div>
	<div id="new" class="form-row odd manual-only">
		<label for="channel_number1">Channel:</label>
		<select name="channel_number1" id="channel_number1">
			<?php
				//dynamic generate possible channels
				$channels = explode(",", $possible_channels1);
				foreach ($channels as $val){
					echo '<option value="'.$val.($val==$channel_number1 ? '" selected="selected">' : '" >').$val.'</option>';
				}
			?>
		</select>
		<label for="auto_channel_number1" class="acs-hide"></label>
		<select id="auto_channel_number1" disabled="disabled"><option selected="selected" ><?php echo $channel_number1; ?></option></select>
	</div>
	<div class="form-row ">
		<div class="form-btn">
		<label for="save_basic1" class="acs-hide"></label>
			<input type="submit" id="save_basic1" value="Save Basic Setting" class="btn right" onclick="save_config('2', 'save_basic');"/>
		</div>
	</div>
</div>



<div class="module data data div_radio_setting">
	<h2>5GHz Wireless Advanced Setting</h2><br/>
	<div class="form-row odd" style="display:none;">
		<label for="BG_protection_mode1">BG Protection Mode:</label>
		<select name="BG_protection_mode1" id="BG_protection_mode1">
			<option value="auto" selected="selected">Auto</option>
			<option value="Disabled" <?php //if ("Disabled"==$BG_protect_mode1) echo 'selected="selected"';?> >Manual</option>
		</select>
	</div>
	<div class="form-row">
		<label for="IGMP_Snooping_disabled1">IGMP Snooping</label>
		<input type="radio"  name="IGMP_Snooping1" value="false" id="IGMP_Snooping_disabled1" checked="checked" /><b>Disable</b>
		<label for="IGMP_Snooping_enabled1" class="acs-hide"></label>
		<input type="radio"  name="IGMP_Snooping1" value="true"  id="IGMP_Snooping_enabled1" <?php if ("true"==$IGMP_Snooping1) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row odd">
		<label for="mixed_mode1">Operation Mode:</label>
		<input type="radio"  name="operation_mode1" value="false" id="mixed_mode1" checked="checked" /><b>Mixed Mode</b>
		<label for="operation_mode1" class="acs-hide"></label>
		<input type="radio"  name="operation_mode1" value="true"  id="operation_mode1" <?php if ("true"==$operation_mode1) echo 'checked="checked"';?> /><b>Green Field</b>
	</div>
	<div class="form-row" id="bandwidth_switch1">
		<label for="channel_bandwidth201">Channel Bandwidth:</label>
		<input type="radio"  name="channel_bandwidth1" value="20MHz" id="channel_bandwidth201" checked="checked" /><b>20</b>
		<?php if (strstr($support_mode_5g, "ac")){ ?>
			<label for="channel_bandwidth1" class="acs-hide"></label>
			<input type="radio"  name="channel_bandwidth1" value="40MHz"  id="channel_bandwidth1" <?php if ("40MHz"==$channel_bandwidth1) echo 'checked="checked"';?> /><b>20/40</b>
			<label for="channel_bandwidth2" class="acs-hide"></label>
			<input type="radio"  name="channel_bandwidth1" value="80MHz"  id="channel_bandwidth2" <?php if ("80MHz"==$channel_bandwidth1) echo 'checked="checked"';?> /><b>20/40/80</b>
		<?php } else{ ?>
			<label for="channel_bandwidth1" class="acs-hide"></label>
			<input type="radio"  name="channel_bandwidth1" value="40MHz"  id="channel_bandwidth1" <?php if ("40MHz"==$channel_bandwidth1) echo 'checked="checked"';?> /><b>20/40</b>
		<?php }	?>
	</div>
	<div class="form-row odd">
		<label for="guard_interval800ns1">Guard Interval:</label>
		<input type="radio"  name="guard_interval1" value="400nsec"  id="guard_interval800ns1" checked="checked" /><b>400ns</b>
		<label for="guard_interval400ns1" class="acs-hide"></label>
		<input type="radio"  name="guard_interval1" value="800nsec"  id="guard_interval400ns1" <?php if ("800nsec"==$guard_interval1) echo 'checked="checked"';?> /><b>800ns</b>
		<label for="guard_intervalauto1" class="acs-hide"></label>
		<input type="radio"  name="guard_interval1" value="Auto"     id="guard_intervalauto1"  <?php if ("Auto"==$guard_interval1) echo 'checked="checked"';?> /><b>Auto</b>
	</div>
	<div class="form-row">
		<label for="Reverse_Direction_Grant_disabled1">Reverse Direction Grant</label>
		<input type="radio"  name="Reverse_Direction_Grant1" value="disabled" id="Reverse_Direction_Grant_disabled1" checked="checked" /><b>Disable</b>
		<label for="reverse_enabled1" class="acs-hide"></label>
		<input type="radio"  name="Reverse_Direction_Grant1" value="enabled"  id="reverse_enabled1" <?php if ("true"==$reverse_enabled1) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row odd">
		<label for="Aggregation_MSDU(A-MSDU)_disabled1">Aggregation MSDU(A-MSDU)</label>
		<input type="radio"  name="Aggregation_MSDU(A-MSDU)1" value="disabled" id="Aggregation_MSDU(A-MSDU)_disabled1" checked="checked" /><b>Disable</b>
		<label for="MSDU_enabled1" class="acs-hide"></label>
		<input type="radio"  name="Aggregation_MSDU(A-MSDU)1" value="enabled"  id="MSDU_enabled1" <?php if ("true"==$MSDU_enabled1) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row">
		<label for="Auto_Block_Ack_disabled1">Auto Block Ack:</label>
		<input type="radio"  name="Auto_Block_Ack1" value="disabled"  id="Auto_Block_Ack_disabled1" checked="checked" /><b>Disable</b>
		<label for="blockACK_enabled1" class="acs-hide"></label>
		<input type="radio"  name="Auto_Block_Ack1" value="enabled"   id="blockACK_enabled1" <?php if ("true"==$blockACK_enabled1) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row odd">
		<label for="Decline_BA_Request_disabled1">Decline BA Request:</label>
		<input type="radio"  name="Decline_BA_Request1" value="disabled" id="Decline_BA_Request_disabled1" checked="checked" /><b>Disable</b>
		<label for="blockBA_enabled1" class="acs-hide"></label>
		<input type="radio"  name="Decline_BA_Request1" value="enabled"  id="blockBA_enabled1" <?php if ("true"==$blockBA_enabled1) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row">
		<label for="DFS_Channel_Selection_disabled">DFS Channel Selection In Auto Mode:</label>
		<input type="radio"  name="DFS_Channel_Selection" value="disabled" id="DFS_Channel_Selection_disabled" checked="checked" /><b>Disable</b>
		<label for="DFS_Channel_Selection_enabled" class="acs-hide"></label>
		<input type="radio"  name="DFS_Channel_Selection" value="enabled"  id="DFS_Channel_Selection_enabled" <?php if ("true"==$DFS_Enable1) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row odd">
	<label for="HT_TxStream1">HT TxStream:</label>
		<select name="HT_TxStream1" id="HT_TxStream1">
		<option value="3" selected="selected" >3</option>
		<option value="2" <?php if ("2"==$HT_TxStream1) echo 'selected="selected"';?> >2</option>
		<option value="1" <?php if ("1"==$HT_TxStream1) echo 'selected="selected"';?> >1</option>
		</select>
	</div>
	<div class="form-row ">
		<label for="HT_RxStream1">HT RxStream:</label>
		<select name="HT_RxStream1" id="HT_RxStream1">
		<option value="3" selected="selected" >3</option>
		<option value="2" <?php if ("2"==$HT_RxStream1) echo 'selected="selected"';?> >2</option>
		<option value="1" <?php if ("1"==$HT_RxStream1) echo 'selected="selected"';?> >1</option>
		</select>
	</div>
	<div class="form-row odd">
		<label for="WMM_power_save1">WMM Power Save:</label>
		<input type="checkbox" id="WMM_power_save1" name="WMM_power_save1" <?php if ("true"==$WMM_power_save1) echo 'checked="checked"';?> <?php if ("false"==$enableWMM1) echo 'disabled="disabled"';?> />
		<span class="footnote" >This item depends on WMM. Enable WMM in atleast one SSID to make this work.</span>
	</div>
	<div class="form-row ">
		<label for="STBC_disabled1">STBC:</label>
		<input type="radio"  name="STBC1" value="disabled" id="STBC_disabled1" checked="checked" /><b>Disable</b>
		<label for="STBC_enabled1" class="acs-hide"></label>
		<input type="radio"  name="STBC1" value="enabled"  id="STBC_enabled1" <?php if ("true"==$STBC_enabled1) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row odd">
		<label for="DCS_Channel_Selection_disabled1">Dynamic Channel Selection:</label>
		<input type="radio"  name="DCS_Channel_Selection1" value="disabled" id="DCS_Channel_Selection_disabled1" checked="checked" /><b>Disable</b>
		<label for="DCS_Channel_Selection_enabled1" class="acs-hide"></label>
		<input type="radio"  name="DCS_Channel_Selection1" value="enabled"  id="DCS_Channel_Selection_enabled1" <?php if ("true"==$DCS_Enable1) echo 'checked="checked"';?> /><b>Enable</b>
	</div>
	<div class="form-row"> 
		<div class="form-btn">
		<label for="save_advance1" class="acs-hide"></label>
			<input type="submit" id="save_advance1" value="Save Advanced Settings" class="btn" onclick="save_config('2', 'save_advance');"/>
		</div>
	</div>
</div>

<div class="module data" id="mac_admin_temp" style="display: none;">
	<h2>MAC Filter Setting</h2>
	<div>
		<p>This may take several seconds...</p>
	</div>
</div>

<div class="module data" id="mac_admin">
	<h2>MAC Filter Setting</h2>
	<div>
		<p>You can control the Wi-Fi access to the USG using the below Mac-Filter settings.</p>
	</div>
	<div class="form-row">
		<label for="mac_ssid">SSID:</label>
		<select name="SSID" id="mac_ssid">
			<!--option value="1" selected="selected"><?php //echo $network_name;?></option>
			<option value="2" ><?php //echo $network_name1;?></option-->
			<?php
				foreach ($ssids as $i) {
					echo '<option value="'.$i.'" '.(("1"==$i)?'selected="selected"':"").'>'.getStr("Device.WiFi.SSID.$i.SSID").'</option>';
				}
			?>		
		</select>
	</div>
	<div class="form-row">
		<label for="filtering_mode">MAC Filtering Mode:</label>
		<select name="filtering_mode" id="filtering_mode">
			<option value="allow_all" id="allow_all">Allow-All</option>
			<option value="allow"     id="allow">Allow</option>
			<option value="deny"      id="deny">Deny</option>
		</select>
	</div>	
	
	<div class="form-row">
		<p><strong>Wi-Fi Control List(up to 16 items)</strong></p>
		<table class="data" id="filter_table" summary="Wi-Fi Control List">
			<thead>
		    <tr>
		        <th id="acl-Index">#</th>
		        <th id="acl-Name">Device Name</th>
				<th id="acl-MAC">MAC Address</th>
				<th id="acl-Blank" colspan="2">&nbsp;</th>
		    </tr>
			</thead>
			<tbody>
		    <!--tr class="form-row odd">
		        <td></td>
		        <td></td>
				<td></td>
		        <td class="delete"></td>
		    </tr-->
			</tbody>
			<tfoot>
				<tr class="acs-hide">
					<td headers="acl-Index">null</td>
					<td headers="acl-Name">null</td>
					<td headers="acl-MAC">null</td>
					<td headers="acl-Blank">null</td>
				</tr>
			</tfoot>
		</table><br>

		<p><strong>Auto-Learned Wi-Fi Devices</strong></p>
		<table class="data" id="auto_table" summary="Auto-Learned Wi-Fi Devices">
			<thead>
		    <tr>
		        <th id="auto-Name">Device Name</th>
				<th id="auto-MAC">MAC Address</th>
				<th id="auto-Blank" colspan="2">&nbsp;</th>
		    </tr>
			</thead>
			<tbody>
		    <!--tr class="form-row odd acs-hide" >
		        <td>name</td>
				<td>00:00:00:00:00:00</td>
		        <td class="edit">add</td>
		    </tr-->
			</tbody>
			<tfoot>
				<tr class="acs-hide">
					<td headers="auto-Name">null</td>
					<td headers="auto-MAC">null</td>
					<td headers="auto-Blank">null</td>
				</tr>
			</tfoot>
		</table>
		<br>

		<p><strong>Manually-Added Wi-Fi Devices</strong></p>
		<table class="wireless data" id="manual_table" summary="Manually-Added Wi-Fi Devices">
			<thead>
			<tr>
				<th id="manual-Name">Device Name</th>
				<th id="manual-MAC">MAC Address</th>
				<th id="manual-Blank" colspan="2">&nbsp;</th>
			</tr>
			</thead>
			<tbody>
			<tr class="form-row odd">
				<td headers="manual-Name">
				<label for="device_name" class="acs-hide"></label>
					<input type="text"  id="device_name" name="device_name" size="10">
				</td>
				<td headers="manual-MAC"> 
					 <label for="mac_address_1" class="acs-hide"></label>
					 <input type="text" size="2" maxlength="2" id="mac_address_1" name="mac_address_1"/><label for="mac_address_2" class="acs-hide"></label>
					:<input type="text" size="2" maxlength="2" id="mac_address_2" name="mac_address_2"/><label for="mac_address_3" class="acs-hide"></label>
					:<input type="text" size="2" maxlength="2" id="mac_address_3" name="mac_address_3"/><label for="mac_address_4" class="acs-hide"></label>
					:<input type="text" size="2" maxlength="2" id="mac_address_4" name="mac_address_4"/><label for="mac_address_5" class="acs-hide"></label>
					:<input type="text" size="2" maxlength="2" id="mac_address_5" name="mac_address_5"/><label for="mac_address_6" class="acs-hide"></label>
					:<input type="text" size="2" maxlength="2" id="mac_address_6" name="mac_address_6"/>
				</td>
				<td headers="manual-Blank" class="edit">
				<label for="add_manual" class="acs-hide"></label>
				<input type="button" id="add_manual" value="ADD" class="btn" onclick="add_manual()"/></td>
			</tr>
			</tbody>
			<tfoot>
				<tr class="acs-hide">
					<td headers="manual-Name">null</td>
					<td headers="manual-MAC">null</td>
					<td headers="manual-Blank">null</td>
				</tr>
			</tfoot>
		</table>
		<br>

		<div class="form-row odd">
			<p class="form-btn"><input type="submit" id="save_filter" value="SAVE FILTER SETTING" class="btn right" size="1" onclick="save_filter()"/></p>
		</div>
	</div>
</div>			

<div class="module forms enable div_wps_setting wps_config">
	<h2>Wi-Fi Client Setup Configuration(WPS)</h2>
	<div class="form-row"><p>You must enable WPS to connect your device to this device</p></div>
	<!--div class="form-row">
		<label for="ssid">SSID:</label>
		<select name="ssid" id="wps_ssid">
			<option value="1" selected="selected"><?php //echo $network_name;?></option>
			<option value="2" ><?php //echo $network_name1;?></option>
		</select>
	</div-->
	<div class="form-row" style="display: none;">
		<label for="wps_ssid">SSID:</label>
		<select name="ssid" id="wps_ssid">
			<option value="<?php echo $f_e_ssid; ?>" selected="selected"><?php echo $f_e_ssid; ?></option>
		</select>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel label">WPS:</span>
		<span id="wps_switch"></span>
	</div>
	<!--div class="form-row ">
		<span class="readonlyLabel">Security:</span> 
		<span class="value" id="wps_security"></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Encryption:</span>
		<span class="value" id="wps_encryption"></span>
	</div-->
	<div class="form-row ">
		<span class="readonlyLabel">AP PIN:</span> 
		<span class="value" id="wps_pin">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $wps_pin; ?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel label">WPS Pin Method:</span>
		<span id="pin_switch"></span>
	</div>
</div>

<form id="pair_method_form">
<div class="module data div_wps_setting wps_config" id="jjj">
	<h2>Connect to your WPS-supported device</h2><br/>
	<!--div class="form-row" style="position:relative; top:0px; right:-13px">
		<label for="ssid"><b>SSID:</b></label>
		<select name="ssid" id="wps_ssid">
			<option value="1" selected="selected"><?php //echo $network_name;?></option>
			<option value="2" ><?php //echo $network_name1;?></option>
		</select>
	</div-->
	<div class="form-row odd">
		<h3>
		<label for="pair_method_push" class="acs-hide"></label>
			<input type="radio"  name="pair_method" value="PushButton" id="pair_method_push" checked="checked"><b>Push Button(recommended)</b>
		</h3>&nbsp; &nbsp; Push the WPS button on the Gateway or click "PAIR WITH MY Wi-Fi CLIENT" below to connect your
		<br/>  &nbsp; &nbsp; Wireless client to your network.<br/><br/>
	</div>
	<div class="form-row" id="div_method_pin">
		<h3>
		<label for="pair_method_pin" class="acs-hide"></label>
			<input type="radio"  name="pair_method" value="PIN" id="pair_method_pin" /><b>PIN Number</b>
		</h3>&nbsp; &nbsp; If your Wireless client supports WPS(PIN Type), enter the PIN Number here.
		<br/><br/> &nbsp; &nbsp; Enter Wireless Client's PIN
		<label for="pin_number" class="acs-hide"></label>
		<input type="text" size="15" maxlength="15" id="pin_number" name="pin_number" />
	</div>
	<div class="form-btn">
	<label for="wps_pair" class="acs-hide"></label>
		<input id="wps_pair"   name="wps_pair"   type="submit" style="text-transform : none;" value="PAIR WITH MY Wi-Fi CLIENT" class="btn" size="3" />
		<label for="wps_cancel" class="acs-hide"></label>
		<input id="wps_cancel" name="wps_cancel" type="button" value="CANCEL " class="btn" onclick="pair_cancel()"/>
	</div>
</div>
</form>
</div><!-- end #content -->

<?php include('includes/footer.php'); //sleep(3);?>
