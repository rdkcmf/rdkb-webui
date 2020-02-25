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
// $jsConfig = '{"radio_enable":"true", "network_name":"ssid1", "wireless_mode":"b,g,n", "security":"WPA2_PSK_AES", "channel_automatic":"false", "channel_number":"6", "network_password":"123456789", "broadcastSSID":"true", "ssid_number":"1"}';
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);
$thisUser = $arConfig['thisUser'];
/*********************************************************************************************/
$i = $arConfig['ssid_number'];
$r = (2 - intval($i)%2);	//1,3,5,7 == 1(2.4G); 2,4,6,8 == 2(5G)
$Mesh_Enable 	= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_xOpsDeviceMgmt.Mesh.Enable");
$Mesh_State 	= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_xOpsDeviceMgmt.Mesh.State");
$Mesh_Mode = ($Mesh_Enable == 'true' && $Mesh_State == 'Full')? true : false;
$network_pass = getStr("Device.WiFi.AccessPoint.$i.Security.X_COMCAST-COM_KeyPassphrase");
$frequency_band = getStr("Device.WiFi.Radio.$r.OperatingFrequencyBand");
$radio_band	= (strstr($frequency_band,"5G")) ? "5" : "2.4";
$Radio_1_Support_Modes = getStr("Device.WiFi.Radio.1.SupportedStandards");
$Radio_2_Support_Modes = getStr("Device.WiFi.Radio.2.SupportedStandards");

if($i != 1 && $i != 2) $Mesh_Mode = false;
// this method for only restart a certain SSID
function MiniApplySSID($ssid) {
	$apply_id = (1 << intval($ssid)-1);
	$apply_rf = (2  - intval($ssid)%2);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySettingSSID", $apply_id, false);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySetting", "true", true);
}
$response_message = '';
//ssid 1,2 for all
//ssid 3,4 for mso only
if ($i == 1 || $i == 2) {
	// check if the LowerLayers radio is enabled. if disable, no need to configure following
	if ("true" == getStr("Device.WiFi.Radio.$r.Enable")) {
		//change SSID status first, if disable, no need to configure following
		setStr("Device.WiFi.SSID.$i.Enable", $arConfig['radio_enable'], true);
		if ("true" == $arConfig['radio_enable'] && (!$Mesh_Mode) )
		{
			$validation = true;
			if(($arConfig['password_update']=="false") && ("mso" == $thisUser)){
				$arConfig['network_password']=$network_pass;
			}
			if ("mso" != $thisUser){
				if($validation) $validation = isValInArray($arConfig['channel_bandwidth'], array('20MHz', '40MHz', '80MHz'));
				if (strstr($Radio_1_Support_Modes, "ax") && strstr($Radio_2_Support_Modes, "ax"))
				{
					if($validation) $validation = (($radio_band=="2.4" && isValInArray($arConfig['wireless_mode'], array("g,n", "g,n,ax"))) || ($radio_band=="5" && isValInArray($arConfig['wireless_mode'], array("a,n,ac", "a,n,ac,ax"))));
				}
				else
				{
					if($validation) $validation = (($radio_band=="2.4" && isValInArray($arConfig['wireless_mode'], array("n", "g,n", "b,g,n"))) || ($radio_band=="5" && isValInArray($arConfig['wireless_mode'], array("n", "a,n", "ac", "n,ac", "a,n,ac"))));
				}

				if ("false"==$arConfig['channel_automatic']){
					$PossibleChannels = getStr("Device.WiFi.Radio.$r.PossibleChannels");
					if(strpos($PossibleChannels, '-') !== false){//1-11
						$PossibleChannelsRange = explode('-', $PossibleChannels);
						$PossibleChannelsArr = range($PossibleChannelsRange[0],$PossibleChannelsRange[1]);
						foreach($PossibleChannelsArr as $key => $val) $PossibleChannelsArr[$key] = (string)$val;
					}
					else {//36,40,44,48,149,153,157,161,165 or 1,2,3,4,5,6,7,8,9,10,11
						$PossibleChannelsArr = explode(',', $PossibleChannels);
					}
					if ($validation && "false"==$arConfig['channel_automatic']) $validation = isValInArray($arConfig['channel_number'], $PossibleChannelsArr);
				}
			}
			if($arConfig['security']!="None"){
					if($validation) $validation = (preg_match("/^[ -~]{8,63}$|^[a-fA-F0-9]{64}$/i", $arConfig['network_password'])==1);
			}
			if($validation && !valid_ssid_name($arConfig['network_name']))
			{
				$validation = false;
				$response_message = 'WiFi name is not valid. Please enter a new name !';
			}
				//Choose a different Network Name (SSID) than the one provided on your gateway
			$DefaultSSID = getStr("Device.WiFi.SSID.$i.X_COMCAST-COM_DefaultSSID");
			if($validation && (strtolower($DefaultSSID) == strtolower($arConfig['network_name']))){
				$validation = false;
				$response_message = 'WiFi name is not valid. Please enter a new name !';
			} 
				//Choose a different Network Password than the one provided on your gateway
			$DefaultKeyPassphrase = getStr("Device.WiFi.AccessPoint.$i.Security.X_COMCAST-COM_DefaultKeyPassphrase");
			if($validation && ($DefaultKeyPassphrase == $arConfig['network_password']) && ($arConfig['security'] != "None")) {
				$validation = false;
				$response_message = 'Please change Network Password !';
			}
			
			if($validation){
				switch ($arConfig['security'])
				{
					case "WPA2_PSK_TKIP":
					  $encrypt_mode   = "WPA2-Personal";
					  $encrypt_method = "TKIP";
					  break;
					case "WPA2_PSK_AES":
					  $encrypt_mode   = "WPA2-Personal";
					  $encrypt_method = "AES";
					  break;
					case "None":
					  $encrypt_mode   = "None";
					  $encrypt_method = "None";
					  break;
					default:
					  $encrypt_mode   = "WPA2-Personal";
					  $encrypt_method = "AES";
				}
				// User "mso" have another page to configure this
                                $channel = getStr("Device.WiFi.Radio.$i.AutoChannelEnable");
				if ("mso" != $thisUser){
					setStr("Device.WiFi.Radio.$i.OperatingChannelBandwidth", $arConfig['channel_bandwidth'], false);
					setStr("Device.WiFi.Radio.$i.OperatingStandards", $arConfig['wireless_mode'], true);
					setStr("Device.WiFi.Radio.$i.AutoChannelEnable", $arConfig['channel_automatic'], true);
						if ("false"==$arConfig['channel_automatic']){
							setStr("Device.WiFi.Radio.$i.Channel", $arConfig['channel_number'], true);
						}
                                                if($arConfig['channel_automatic'] != $channel){
                                                    $fh = fopen("/rdklogs/logs/Consolelog.txt.0","a");
                                                    $data = ($arConfig['channel_automatic'] == 'true') ? "Channel is set to Auto from " .$thisUser. " for radio " .$i. "\n" : "channel is set to Manual from ". $thisUser . " for radio " .$i. " and Channel selected is " .$arConfig['channel_number']. "\n";
                                                    fwrite($fh,$data);
                                                    fclose($fh);
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
					setStr("Device.WiFi.AccessPoint.$i.Security.X_COMCAST-COM_KeyPassphrase", $arConfig['network_password'], true);
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
		}
		// setStr("Device.WiFi.Radio.$r.X_CISCO_COM_ApplySetting", "true", true);
		MiniApplySSID($i);
	}
}
if($response_message!='') {
	$response->error_message = $response_message;
	echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
}
else echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>
