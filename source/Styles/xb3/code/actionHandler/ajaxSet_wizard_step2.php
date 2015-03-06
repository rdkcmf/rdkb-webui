<?php 

$jsConfig = $_REQUEST['configInfo'];
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
	default:
	  $encrypt_mode   = "None";
	  $encrypt_method = "None";
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
else {
	setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_KeyPassphrase", $arConfig['network_password'], true); 
	setStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_EncryptionMethod", $encrypt_method, true);
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
	default:
	  $encrypt_mode   = "None";
	  $encrypt_method = "None";
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
else {
	setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_KeyPassphrase", $arConfig['network_password1'], true); 
	setStr("Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_EncryptionMethod", $encrypt_method, true);
}

// setStr("Device.WiFi.Radio.2.X_CISCO_COM_ApplySetting", "true", true);
MiniApplySSID(2);

echo $jsConfig;

?>
