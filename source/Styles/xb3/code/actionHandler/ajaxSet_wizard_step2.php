<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:
 Copyright 2016 RDK Management
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
<?php include('../includes/actionHandlerUtility.php') ?>
<?php 
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="../index.php";</script>';
	exit(0);
}
$jsConfig = $_POST['configInfo'];
//$jsConfig = '{"network_name":"string", "security":"WPA2-Enterprise", "network_password":"00000000", "network_name1":"string1", "security1":"WPA-WPA2-Enterprise", "network_password1":"11111111"}';
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);
// this method for only restart a certain SSID
function MiniApplySSID($ssid) {
	$apply_id = (1 << intval($ssid)-1);
	$apply_rf = (2  - intval($ssid)%2);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySettingSSID", $apply_id, false);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySetting", "true", true);
}
$validation = true;
if($arConfig['security']!="None"){
	if($validation) $validation = (preg_match("/^[ -~]{8,63}$|^[a-fA-F0-9]{64}$/i", $arConfig['network_password'])==1);
}
if($validation) $validation = valid_ssid_name($arConfig['network_name']);
//Choose a different Network Name (SSID) than the one provided on your gateway
$DefaultSSID = getStr("Device.WiFi.SSID.1.X_COMCAST-COM_DefaultSSID");
if($validation) $validation = ($DefaultSSID != $arConfig['network_name']);
//Choose a different Network Password than the one provided on your gateway
$DefaultKeyPassphrase = getStr("Device.WiFi.AccessPoint.1.Security.X_COMCAST-COM_DefaultKeyPassphrase");
if($validation) $validation = ($DefaultKeyPassphrase != $arConfig['network_password']);
if($arConfig['security1']!="None"){
	if($validation) $validation = (preg_match("/^[ -~]{8,63}$|^[a-fA-F0-9]{64}$/i", $arConfig['network_password1'])==1);
}
if($validation) $validation = valid_ssid_name($arConfig['network_name1']);
//Choose a different Network Name (SSID) than the one provided on your gateway
$DefaultSSID5 = getStr("Device.WiFi.SSID.2.X_COMCAST-COM_DefaultSSID");
if($validation) $validation = ($DefaultSSID5 != $arConfig['network_name1']);
//Choose a different Network Password than the one provided on your gateway
$DefaultKeyPassphrase5 = getStr("Device.WiFi.AccessPoint.2.Security.X_COMCAST-COM_DefaultKeyPassphrase");
if($validation) $validation = ($DefaultKeyPassphrase5 != $arConfig['network_password1']);
if($validation){
	//for WiFi 2.4G
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
		case "None":
		  $encrypt_mode   = "None";
		  $encrypt_method = "None";
		  break;
		default:
		  $encrypt_mode   = "WPA2-Personal";
		  $encrypt_method = "AES";
	}
	setStr("Device.WiFi.SSID.1.SSID", $arConfig['network_name'], true);
	setStr("Device.WiFi.AccessPoint.1.Security.ModeEnabled", $encrypt_mode, true);
	if ("WEP_64" == $arConfig['security']) {
		setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_WEPKey64Bit.1.WEPKey",  $arConfig['network_password'], true);
		setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_WEPKey64Bit.2.WEPKey",  $arConfig['network_password'], true);
		setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_WEPKey64Bit.3.WEPKey",  $arConfig['network_password'], true);
		setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_WEPKey64Bit.4.WEPKey",  $arConfig['network_password'], true);
	}
	else if("WEP_128" == $arConfig['security']) {
		setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_WEPKey128Bit.1.WEPKey", $arConfig['network_password'], true);
		setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_WEPKey128Bit.2.WEPKey", $arConfig['network_password'], true);
		setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_WEPKey128Bit.3.WEPKey", $arConfig['network_password'], true);
		setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_WEPKey128Bit.4.WEPKey", $arConfig['network_password'], true);
	}
	else {	//no open, no wep
			//bCommit false->true still do validation each, have to group set this...
			DmExtSetStrsWithRootObj("Device.WiFi.", true, array(
				array("Device.WiFi.AccessPoint.1.Security.ModeEnabled", "string", $encrypt_mode), 
				array("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_EncryptionMethod", "string", $encrypt_method)));
			setStr("Device.WiFi.AccessPoint.1.Security.X_COMCAST-COM_KeyPassphrase", $arConfig['network_password'], true);
		}
	// setStr("Device.WiFi.Radio.1.X_CISCO_COM_ApplySetting", "true", true);
	MiniApplySSID(1);
	//for WiFi 5G
	switch ($arConfig['security1'])
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
		case "None":
		  $encrypt_mode   = "None";
		  $encrypt_method = "None";
		  break;
		default:
		  $encrypt_mode   = "WPA2-Personal";
		  $encrypt_method = "AES";
	}
	setStr("Device.WiFi.SSID.2.SSID", $arConfig['network_name1'], true);
	setStr("Device.WiFi.AccessPoint.2.Security.ModeEnabled", $encrypt_mode, true);
	if ("WEP_64" == $arConfig['security1']) {
		setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_WEPKey64Bit.1.WEPKey",  $arConfig['network_password1'], true);
		setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_WEPKey64Bit.2.WEPKey",  $arConfig['network_password1'], true);
		setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_WEPKey64Bit.3.WEPKey",  $arConfig['network_password1'], true);
		setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_WEPKey64Bit.4.WEPKey",  $arConfig['network_password1'], true);
	}
	else if("WEP_128" == $arConfig['security1']) {
		setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_WEPKey128Bit.1.WEPKey", $arConfig['network_password1'], true);
		setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_WEPKey128Bit.2.WEPKey", $arConfig['network_password1'], true);
		setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_WEPKey128Bit.3.WEPKey", $arConfig['network_password1'], true);
		setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_WEPKey128Bit.4.WEPKey", $arConfig['network_password1'], true);
	}
	else {	//no open, no wep
			//bCommit false->true still do validation each, have to group set this...
			DmExtSetStrsWithRootObj("Device.WiFi.", true, array(
				array("Device.WiFi.AccessPoint.2.Security.ModeEnabled", "string", $encrypt_mode), 
				array("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_EncryptionMethod", "string", $encrypt_method)));
			setStr("Device.WiFi.AccessPoint.2.Security.X_COMCAST-COM_KeyPassphrase", $arConfig['network_password1'], true);
		}
	// setStr("Device.WiFi.Radio.2.X_CISCO_COM_ApplySetting", "true", true);
	MiniApplySSID(2);
	//changing password for admin case
	if($arConfig['newPassword']) setStr("Device.Users.User.3.X_CISCO_COM_Password", $arConfig['newPassword'], true);
}
echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>