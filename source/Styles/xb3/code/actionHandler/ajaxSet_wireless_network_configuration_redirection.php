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
$isCaptiveMode = false;
$CONFIGUREWIFI = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_ConfigureWiFi");
$CaptivePortalEnable = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CaptivePortalEnable");
$personalization_support = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.WiFiPersonalization.Support");
$PersonalizationSMSsupport = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.WiFiPersonalization.SMSsupport");
$DefaultSSID = getStr("Device.WiFi.SSID.1.X_COMCAST-COM_DefaultSSID");
$DefaultSSID5 = getStr("Device.WiFi.SSID.2.X_COMCAST-COM_DefaultSSID");
			
if(!strcmp($personalization_support, "true")) {
  if(!strcmp($CaptivePortalEnable, "true")) {
        if(!strcmp($CONFIGUREWIFI, "true")) {
                $isCaptiveMode = true;
        }
    }
}
if($isCaptiveMode)
{
	if(isset($_POST['rediection_Info'])){
		$jsConfig = $_POST['rediection_Info'];
		$arConfig = json_decode($jsConfig, true);
		$validation = true;

		if($validation) $validation = valid_ssid_name($arConfig['network_name']);
		if($validation) $validation = ($DefaultSSID != $arConfig['network_name']);
		if($validation) $validation = (preg_match("/^[ -~]{8,63}$|^[a-fA-F0-9]{64}$/i", $arConfig['network_password'])==1);
		if($validation) $validation = (preg_match("/^\d{10}$/", $arConfig['phoneNumber'])==1);
	}
	//CloudUIEnable -- to check if "Device.DeviceInfo.X_RDKCENTRAL-COM_CloudPersonalizationURL" is reachable
	/*if(isset($_POST['CloudUIEnable'])){
		$IsCloudReachable = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_IsCloudReachable");
		$response = array();
		array_push($response, $IsCloudReachable);
		echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
	}*/
	if($validation){
		// jsConfig = '{"dualband":"true", "network_name":"'+network_name+'", "network_password":"'+network_password+'", "network5_name":"'+network5_name+'", "network5_password":"'+network5_password+', "phoneNumber":"'+EMS_mobileNumber()+'"}';
		// jsConfig = '{"dualband":"false", "network_name":"'+network_name+'", "network_password":"'+network_password+', "phoneNumber":"'+EMS_mobileNumber()+'"}';
		//print_r($arConfig);
		if($PersonalizationSMSsupport == 'true'){
			//update EMS phoneNumber
			setStr("Device.DeviceInfo.X_COMCAST-COM_EMS_MobileNumber", $arConfig['phoneNumber'], true);
		}
		
		$network_name_arr = array(
			"1" => $arConfig['network_name'],//."-2.4",
			"2" => $arConfig['network_name'],//."-5",
		);
		$network_pass_arr = array(
			"1" => $arConfig['network_password'],//."-2.4",
			"2" => $arConfig['network_password'],//."-5",
		);
		
		// this method for only restart a certain SSID
		function MiniApplySSID($ssid) {
			$apply_id = (1 << intval($ssid)-1);
			$apply_rf = (2  - intval($ssid)%2);
			setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySettingSSID", $apply_id, false);
			setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySetting", "true", true);
		}
		for($i = "1"; $i < 3; $i++){
			$r = (2 - intval($i)%2);	//1,3,5,7 == 1(2.4G); 2,4,6,8 == 2(5G)
			// check if the SSID status is enabled
			if ("false" == getStr("Device.WiFi.SSID.$i.Enable")){
				setStr("Device.WiFi.Radio.$r.Enable", "true", true);
			}
			// check if the LowerLayers radio is enabled
			if ("false" == getStr("Device.WiFi.Radio.$r.Enable")){
				setStr("Device.WiFi.Radio.$r.Enable", "true", true);
			}
			setStr("Device.WiFi.SSID.$i.SSID", $network_name_arr[$i], true);
			setStr("Device.WiFi.AccessPoint.$i.Security.X_COMCAST-COM_KeyPassphrase", $network_pass_arr[$i], true);
			// setStr("Device.WiFi.Radio.$r.X_CISCO_COM_ApplySetting", "true", true);
			MiniApplySSID($i);
		}
		sleep(10);
		$response = array();
		array_push($response, $arConfig['phoneNumber']);
		echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
	}
	else
	{
		setStr("Device.DeviceInfo.X_RDKCENTRAL-COM_UI_ACCESS","captiveportal_invalid_input",true);
		$response = array();
		array_push($response, "outOfCaptivePortal");
		echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
	}
}
else
{
	setStr("Device.DeviceInfo.X_RDKCENTRAL-COM_UI_ACCESS","captiveportal_failure",true);
	$response = array();
	array_push($response, "outOfCaptivePortal");
	echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
}
?>
