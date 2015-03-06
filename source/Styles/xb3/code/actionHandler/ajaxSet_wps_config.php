<?php

$jsConfig = $_REQUEST['configInfo'];
//$jsConfig = '{"ssid_number":"1", "target":"pair_client", "wps_enabled":"true", "wps_method":"PushButton,PIN", "pair_method":"PIN", "pin_number":"12345678"}';

$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);

$i = $arConfig['ssid_number'];	//when pair, select the first WPS enabled SSID

// this method for only restart a certain SSID
function MiniApplySSID($ssid) {
	$apply_id = (1 << intval($ssid)-1);
	$apply_rf = (2  - intval($ssid)%2);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySettingSSID", $apply_id, false);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySetting", "true", true);
}

if ("wps_enabled" == $arConfig['target'])
{
	//enable or disable WPS in all SSID, GUI ensure that only change will be commit to backend
	$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));
	foreach ($ssids as $i){
		setStr("Device.WiFi.AccessPoint.$i.WPS.Enable", $arConfig['wps_enabled'], true);
		// setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);
	}
	// setStr("Device.WiFi.Radio.1.X_CISCO_COM_ApplySetting", "true", true);
	// setStr("Device.WiFi.Radio.2.X_CISCO_COM_ApplySetting", "true", true);
	MiniApplySSID(1);
	MiniApplySSID(2);	
}
else if("wps_method" == $arConfig['target'])
{
	$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));
	foreach ($ssids as $i){
		setStr("Device.WiFi.AccessPoint.$i.WPS.ConfigMethodsEnabled", $arConfig['wps_method'], true);
		// setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);
	}
	// setStr("Device.WiFi.Radio.1.X_CISCO_COM_ApplySetting", "true", true);
	// setStr("Device.WiFi.Radio.2.X_CISCO_COM_ApplySetting", "true", true);
	MiniApplySSID(1);
	MiniApplySSID(2);
}
else if ("pair_client" == $arConfig['target'])
{
	if ("PushButton" == $arConfig['pair_method']) 
	{
		setStr("Device.WiFi.AccessPoint.$i.WPS.X_CISCO_COM_ActivatePushButton", "true", true);
	}
	else 
	{
		setStr("Device.WiFi.AccessPoint.$i.WPS.X_CISCO_COM_ClientPin", $arConfig['pin_number'], true);
	}
}
else if ("pair_cancel" == $arConfig['target'])
{
	setStr("Device.WiFi.AccessPoint.$i.WPS.X_CISCO_COM_CancelSession", "true", true);
}
sleep(1);
echo $jsConfig;	
?>
