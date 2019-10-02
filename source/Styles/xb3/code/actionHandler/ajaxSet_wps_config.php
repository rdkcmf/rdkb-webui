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

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
function validChecksum($WPS_pin){
	if (preg_match("/^\d{4}$|^\d{8}$/", $WPS_pin)!=1) return false;
	if (preg_match("/^\d{4}$/", $WPS_pin)==1) return true;
	$accum = 0;
	$accum += 3 * (intval($WPS_pin / 10000000) % 10);
	$accum += 1 * (intval($WPS_pin / 1000000) % 10);
	$accum += 3 * (intval($WPS_pin / 100000) % 10);
	$accum += 1 * (intval($WPS_pin / 10000) % 10);
	$accum += 3 * (intval($WPS_pin / 1000) % 10);
	$accum += 1 * (intval($WPS_pin / 100) % 10);
	$accum += 3 * (intval($WPS_pin / 10) % 10);
	$accum += 1 * (intval($WPS_pin / 1) % 10);
	return (0 == ($accum % 10));
}
$jsConfig = $_POST['configInfo'];
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
	//$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));
	//only enable wps in private 2.4GHz and private 5GHz ssids
	$ssids = array( 1 , 2 );
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
	//$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));
	//only enable wps in private 2.4GHz and private 5GHz ssids
	$ssids = array( 1 , 2 );
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
		setStr("Device.WiFi.AccessPoint.1.WPS.X_CISCO_COM_ActivatePushButton", "true", true);
		setStr("Device.WiFi.AccessPoint.2.WPS.X_CISCO_COM_ActivatePushButton", "true", true);
	}
	else if(validChecksum($arConfig['pin_number']))
	{
		setStr("Device.WiFi.AccessPoint.1.WPS.X_CISCO_COM_ClientPin", $arConfig['pin_number'], true);
		setStr("Device.WiFi.AccessPoint.2.WPS.X_CISCO_COM_ClientPin", $arConfig['pin_number'], true);
	}
}
else if ("pair_cancel" == $arConfig['target'])
{
	setStr("Device.WiFi.AccessPoint.1.WPS.X_CISCO_COM_CancelSession", "true", true);
	setStr("Device.WiFi.AccessPoint.2.WPS.X_CISCO_COM_CancelSession", "true", true);
}
sleep(1);
echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>