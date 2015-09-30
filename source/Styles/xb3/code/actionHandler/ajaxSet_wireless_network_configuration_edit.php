<!--
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2015 RDK Management

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
-->
<?php 

$jsConfig = $_REQUEST['configInfo'];
// $jsConfig = '{"radio_enable":"true", "network_name":"ssid1", "wireless_mode":"b,g,n", "security":"WPA2_PSK_AES", "channel_automatic":"false", "channel_number":"6", "network_password":"123456789", "broadcastSSID":"true", "ssid_number":"1"}';

$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);

$thisUser = $arConfig['thisUser'];

/*********************************************************************************************/
$i = $arConfig['ssid_number'];
$r = (2 - intval($i)%2);	//1,3,5,7 == 1(2.4G); 2,4,6,8 == 2(5G)

// this method for only restart a certain SSID
function MiniApplySSID($ssid) {
	$apply_id = (1 << intval($ssid)-1);
	$apply_rf = (2  - intval($ssid)%2);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySettingSSID", $apply_id, false);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySetting", "true", true);
}

//change SSID status first, if disable, no need to configure following
setStr("Device.WiFi.SSID.$i.Enable", $arConfig['radio_enable'], true);

if ("true" == $arConfig['radio_enable']) 
{
	// check if the LowerLayers radio is enabled
	if ("false" == getStr("Device.WiFi.Radio.$r.Enable")){
		setStr("Device.WiFi.Radio.$r.Enable", "true", true);
	}
	
	switch ($arConfig['security'])
	{
		case "WEP_64":
		  $encrypt_mode   = "WEP-64";
		  $encrypt_method = "None";
		  break;
		case "WEP_128":
		  $encrypt_mode   = "WEP-128";
		  $encrypt_method = "None";
		  break;
		case "WPA_PSK_TKIP":
		  $encrypt_mode   = "WPA-Personal";
		  $encrypt_method = "TKIP";
		  break;
		case "WPA_PSK_AES":
		  $encrypt_mode   = "WPA-Personal";
		  $encrypt_method = "AES";
		  break;
		case "WPA2_PSK_TKIP":
		  $encrypt_mode   = "WPA2-Personal";
		  $encrypt_method = "TKIP";
		  break;
		case "WPA2_PSK_AES":
		  $encrypt_mode   = "WPA2-Personal";
		  $encrypt_method = "AES";
		  break;
		case "WPA2_PSK_TKIPAES":
		  $encrypt_mode   = "WPA2-Personal";
		  $encrypt_method = "AES+TKIP";
		  break;
		case "WPAWPA2_PSK_TKIPAES":
		  $encrypt_mode   = "WPA-WPA2-Personal";
		  $encrypt_method = "AES+TKIP";
		  break;
		default:
		  $encrypt_mode   = "None";
		  $encrypt_method = "None";
	}

	// User "mso" have another page to configure this
	if ("mso" != $thisUser){
		setStr("Device.WiFi.Radio.$i.OperatingStandards", $arConfig['wireless_mode'], true);
		setStr("Device.WiFi.Radio.$i.AutoChannelEnable", $arConfig['channel_automatic'], true);
		if ("false"==$arConfig['channel_automatic']){
			setStr("Device.WiFi.Radio.$i.Channel", $arConfig['channel_number'], true);
		}
	}
	
	if ("None" == $arConfig['security']) {
		setStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", $encrypt_mode, true);
	}
	else if ("WEP_64" == $arConfig['security']) {
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.1.WEPKey",  $arConfig['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.2.WEPKey",  $arConfig['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.3.WEPKey",  $arConfig['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.4.WEPKey",  $arConfig['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", $encrypt_mode, true);
	}
	else if("WEP_128" == $arConfig['security']) {
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.1.WEPKey", $arConfig['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.2.WEPKey", $arConfig['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.3.WEPKey", $arConfig['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.4.WEPKey", $arConfig['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", $encrypt_mode, true);
	}
	else {	//no open, no wep
		//bCommit false->true still do validation each, have to group set this...
		DmExtSetStrsWithRootObj("Device.WiFi.", true, array(
			array("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", "string", $encrypt_mode), 
			array("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_EncryptionMethod", "string", $encrypt_method)));
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_KeyPassphrase", $arConfig['network_password'], true);
	}

	setStr("Device.WiFi.SSID.$i.SSID", $arConfig['network_name'], true);
	setStr("Device.WiFi.AccessPoint.$i.SSIDAdvertisementEnabled", $arConfig['broadcastSSID'], true);

	if ("mso" == $thisUser){
		// if ("false" == $arConfig['enableWMM']){
			// setStr("Device.WiFi.AccessPoint.$i.UAPSDEnable", "false", true);
		// }
		// setStr("Device.WiFi.AccessPoint.$i.WMMEnable", $arConfig['enableWMM'], true);

		//when disable WMM, make sure UAPSD is disabled as well, have to use group set		
		if (getStr("Device.WiFi.AccessPoint.$i.WMMEnable") != $arConfig['enableWMM']) {
			DmExtSetStrsWithRootObj("Device.WiFi.", true, array(
				array("Device.WiFi.AccessPoint.$i.UAPSDEnable", "bool", "false"),
				array("Device.WiFi.AccessPoint.$i.WMMEnable",   "bool", $arConfig['enableWMM'])));			
		}
	}
}


// setStr("Device.WiFi.Radio.$r.X_CISCO_COM_ApplySetting", "true", true);
MiniApplySSID($i);

echo $jsConfig;

?>
