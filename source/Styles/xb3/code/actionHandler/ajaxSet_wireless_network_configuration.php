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
<?php include('../includes/utility.php'); ?>
<?php include('../includes/actionHandlerUtility.php') ?>
<?php

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
$Mesh_Mode = ($Mesh_Enable == 'true' && $Mesh_State == 'Full')? true : false;
// if both the LowerLayers radios are down ignore WPS options
$Radio_1_Enable = getStr("Device.WiFi.Radio.1.Enable");
$Radio_2_Enable = getStr("Device.WiFi.Radio.2.Enable");
$Radio_Enable = ($Radio_1_Enable == 'true' || $Radio_2_Enable == 'true') ? true : false ;
$partnerId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
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
function validFilterParam($ft){
	foreach($ft as $val){
		if(!(printableCharacters($val[0]) && validMAC($val[1]))) return false;
	}
	return true;
}
$jsConfig = $_POST['configInfo'];
//$jsConfig = '{"ssid_number":"1", "ft":[["1","2"],["c","d"]], "target":"save_filter"}';
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);
if (array_key_exists('band_steering', $arConfig))
{
	//band_steering is only for mso
	if ($_SESSION["loginuser"] == "mso") {
		if($arConfig['band_steering_history'] == "true")
		{
			$BandSteeringHistory = getStr("Device.WiFi.X_RDKCENTRAL-COM_BandSteering.History");
			echo htmlspecialchars($BandSteeringHistory, ENT_NOQUOTES, 'UTF-8');
		}
		if($arConfig['save_steering_settings'] == "true")
		{
			$validation = true;
			if($validation) $validation = isValInArray($arConfig['bs_enable'], array('true', 'false'));
			if($validation) $validation = (preg_match('/^\d\d*$/', $arConfig['UtilzThreshold1']) == 1);
			if($validation) $validation = (preg_match('/^-?\d\d*$/', $arConfig['RSSIThreshold1']) == 1);
			if($validation) $validation = (preg_match('/^\d\d*$/', $arConfig['PhyRateThreshold1']) == 1);
			if($validation) $validation = (preg_match('/^\d\d*$/', $arConfig['UtilzThreshold2']) == 1);
			if($validation) $validation = (preg_match('/^-?\d\d*$/', $arConfig['RSSIThreshold2']) == 1);
			if($validation) $validation = (preg_match('/^\d\d*$/', $arConfig['PhyRateThreshold2']) == 1);
			if($validation){
				setStr("Device.WiFi.X_RDKCENTRAL-COM_BandSteering.Enable", $arConfig['bs_enable'], true);
				setStr("Device.WiFi.X_RDKCENTRAL-COM_BandSteering.BandSetting.1.UtilizationThreshold", $arConfig['UtilzThreshold1'], true);
				setStr("Device.WiFi.X_RDKCENTRAL-COM_BandSteering.BandSetting.1.RSSIThreshold", $arConfig['RSSIThreshold1'], true);
				setStr("Device.WiFi.X_RDKCENTRAL-COM_BandSteering.BandSetting.1.PhyRateThreshold", $arConfig['PhyRateThreshold1'], true);
				setStr("Device.WiFi.X_RDKCENTRAL-COM_BandSteering.BandSetting.2.UtilizationThreshold", $arConfig['UtilzThreshold2'], true);
				setStr("Device.WiFi.X_RDKCENTRAL-COM_BandSteering.BandSetting.2.RSSIThreshold", $arConfig['RSSIThreshold2'], true);
				setStr("Device.WiFi.X_RDKCENTRAL-COM_BandSteering.BandSetting.2.PhyRateThreshold", $arConfig['PhyRateThreshold2'], true);
			}
			echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
		}
	}
}
else if (array_key_exists('prefer_private', $arConfig)) {
	setStr("Device.WiFi.X_RDKCENTRAL-COM_PreferPrivate", $arConfig['isChecked'], true);
	echo htmlspecialchars($arConfig['isChecked'], ENT_NOQUOTES, 'UTF-8');
}
else
{
	$i = $arConfig['ssid_number'];
	// this method for only restart a certain SSID
	function MiniApplySSID($ssid) {
		$apply_id = (1 << intval($ssid)-1);
		$apply_rf = (2  - intval($ssid)%2);
		setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySettingSSID", $apply_id, false);
		setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySetting", "true", true);
	}
       $channel = getStr("Device.WiFi.Radio.$i.AutoChannelEnable");
	if ("save_config" == $arConfig['target'])
	{
		//save_basic & save_advance is only for mso
		if ($_SESSION["loginuser"] == "mso"){
			if ("save_basic" == $arConfig['sub_target'])
			{
				if(preg_match('/^100$|^75$|^50$|^25$|^12$/', $arConfig['transmit_power']) == 1){
					setStr("Device.WiFi.Radio.$i.TransmitPower", $arConfig['transmit_power'], false);
					if(!$Mesh_Mode) setStr("Device.WiFi.Radio.$i.AutoChannelEnable", $arConfig['channel_automatic'], false);
				}
			}
			else if ("save_advance" == $arConfig['sub_target'])
			{
				$validation = true;
				if($validation) $validation = isValInArray($arConfig['BG_protect_mode'], array('Auto', 'Disabled'));
				if($validation) $validation = isValInArray($arConfig['channel_bandwidth'], array('20MHz', '40MHz', '80MHz'));
				if($validation) $validation = isValInArray($arConfig['guard_interval'], array('400nsec', '800nsec', 'Auto'));
				if($validation){
					setStr("Device.WiFi.Radio.$i.X_CISCO_COM_CTSProtectionMode", $arConfig['BG_protect_mode'], false);
					setStr("Device.WiFi.Radio.$i.X_COMCAST_COM_IGMPSnoopingEnable", $arConfig['IGMP_Snooping'], false);
					if(!$Mesh_Mode) setStr("Device.WiFi.Radio.$i.OperatingChannelBandwidth", $arConfig['channel_bandwidth'], false);
					setStr("Device.WiFi.Radio.$i.GuardInterval", $arConfig['guard_interval'], false);
					setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ReverseDirectionGrant", $arConfig['reverse_enabled'], false);
					setStr("Device.WiFi.Radio.$i.X_CISCO_COM_AggregationMSDU", $arConfig['MSDU_enabled'], false);
					setStr("Device.WiFi.Radio.$i.X_CISCO_COM_AutoBlockAck", $arConfig['blockACK_enabled'], false);
					setStr("Device.WiFi.Radio.$i.X_CISCO_COM_DeclineBARequest", $arConfig['blockBA_enabled'], false);
					//DFS_Support1 1-supported 0-not supported
					if (("2" == $i) && (getStr("Device.WiFi.Radio.$i.X_COMCAST_COM_DFSSupport") == 1)){
						setStr("Device.WiFi.Radio.$i.X_COMCAST_COM_DFSEnable", $arConfig['DFS_Selection'], false);
					}
					setStr("Device.WiFi.Radio.$i.X_COMCAST-COM_DCSEnable", $arConfig['DCS_Selection'], false);
					//setStr("Device.WiFi.Radio.$i.X_CISCO_COM_HTTxStream", $arConfig['HT_TxStream'], false);
					//setStr("Device.WiFi.Radio.$i.X_CISCO_COM_HTRxStream", $arConfig['HT_RxStream'], false);
					setStr("Device.WiFi.Radio.$i.X_CISCO_COM_STBCEnable", $arConfig['STBC_enabled'], false);
					setStr("Device.WiFi.AccessPoint.$i.UAPSDEnable", $arConfig['WMM_power_save'], true);
				}
			}
			$validation = true;
			$PossibleChannels = getStr("Device.WiFi.Radio.$i.PossibleChannels");
			if(strpos($PossibleChannels, '-') !== false){//1-11
				$PossibleChannelsRange = explode('-', $PossibleChannels);
				$PossibleChannelsArr = range($PossibleChannelsRange[0],$PossibleChannelsRange[1]);
				foreach($PossibleChannelsArr as $key => $val) $PossibleChannelsArr[$key] = (string)$val;
			}
			else {//36,40,44,48,149,153,157,161,165 or 1,2,3,4,5,6,7,8,9,10,11
				$PossibleChannelsArr = explode(',', $PossibleChannels);
			}
			if($validation) $validation = (($i==1 && isValInArray($arConfig['wireless_mode'], array("n", "g,n", "b,g,n"))) || ($i==2 && isValInArray($arConfig['wireless_mode'], array("n", "a,n", "ac", "n,ac", "a,n,ac"))));
			if ($validation && "false"==$arConfig['channel_automatic']) $validation = isValInArray($arConfig['channel_number'], $PossibleChannelsArr);
			if ($validation && ("2" != $i) && ("20MHz" != $arConfig['channel_bandwidth'])) $validation = isValInArray($arConfig['ext_channel'], array('AboveControlChannel', 'BelowControlChannel', 'Auto'));
			if($validation){
				//redio standards and green mode  must set together
				setStr("Device.WiFi.Radio.$i.OperatingStandards", $arConfig['wireless_mode'], false);
				setStr("Device.WiFi.Radio.$i.X_CISCO_COM_11nGreenfieldEnabled", $arConfig['operation_mode'], false);
				//primary channel and 2nd channel must set together
				if ("false"==$arConfig['channel_automatic']){
					if(!$Mesh_Mode) setStr("Device.WiFi.Radio.$i.Channel", $arConfig['channel_number'], false);
				}
				if (("2" != $i) && ("20MHz" != $arConfig['channel_bandwidth'])){
					setStr("Device.WiFi.Radio.$i.ExtensionChannel", $arConfig['ext_channel'], false);	
				}
				//apply once
				// setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);
				MiniApplySSID($i);
			}
		}
     if($arConfig['channel_automatic'] != $channel){
       $fh = fopen("/rdklogs/logs/Consolelog.txt.0","a");
       $data = ($arConfig['channel_automatic'] == 'true') ? "Channel is set to Auto from " .$_SESSION["loginuser"]. " for radio " .$i. "\n" : "Channel is set to Manual from ". $_SESSION["loginuser"] . " for radio " .$i. " and channel selected is " .$arConfig['channel_number']. "\n";
       fwrite($fh,$data);
       fclose($fh);
      }
		echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
	}
	else if ("wps_ssid" == $arConfig['target'])
	{
		$wps_enabled =		getStr("Device.WiFi.AccessPoint.$i.WPS.Enable");
		$wps_security =		getStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled");
		$wps_encryption =	getStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_EncryptionMethod");
		$wps_pin =			getStr("Device.WiFi.AccessPoint.$i.WPS.X_CISCO_COM_Pin");
		$wps_method =		getStr("Device.WiFi.AccessPoint.$i.WPS.ConfigMethodsEnabled");
		// $wps_enabled =		"true";	
		// $wps_security =		"WPA-WPA2-Personal";	
		// $wps_encryption =	"AES+TKIP";	
		// $wps_pin =			"12345678";	
		// $wps_method =		"PIN";	
		// $wps_method =		"PushButton";	
		$arConfig = array('wps_enabled'=>$wps_enabled, 'wps_security'=>$wps_security, 'wps_encryption'=>$wps_encryption, 
						'wps_pin'=>$wps_pin, 'wps_method'=>$wps_method);
		$jsConfig = json_encode($arConfig);
		header("Content-Type: application/json");
		echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
	}
	else if ("save_enable" == $arConfig['target'])
	{
		if ("radio_enable" == $arConfig['sub_target']) {
			//radio_enable is only for mso
			if ($_SESSION["loginuser"] == "mso"){
				// setStr("Device.WiFi.SSID.$i.Enable", $arConfig['radio_enable'], true);
				// setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);		// only primary SSID
				//do not need this again, cause BWG has define a radio.enable
				/*
				$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));		// now, for ALL SSIDs so as to disable radio
				foreach ($ssids as $j){
					if (intval($j)%2 == intval($i)%2){
						setStr("Device.WiFi.SSID.$j.Enable", $arConfig['radio_enable'], true);			
					}
				}
				*/
				setStr("Device.WiFi.Radio.$i.Enable", $arConfig['radio_enable'], false);
				setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);		
				// MiniApplySSID($i);	// if enable or disable this radio, no need to assign an SSID
			}
			else if($_SESSION["loginuser"] == "admin" && strpos($partnerId, "sky-") !== false){
				setStr("Device.WiFi.Radio.$i.Enable", $arConfig['radio_enable'], false);
				setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);
			}
		}
		else if ("wps_enabled" == $arConfig['sub_target'] && $Radio_Enable) {
			//enable or disable WPS in all SSID, GUI ensure that only change will be commit to backend
			//$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));
			//only enable wps in private 2.4GHz and private 5GHz ssids
			$ssids = array( 1 , 2 );
			foreach ($ssids as $i){
				setStr("Device.WiFi.AccessPoint.$i.WPS.Enable", $arConfig['wps_enabled'], true);
				// setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);	// all SSID, so don't put this in loop
			}
			// setStr("Device.WiFi.Radio.1.X_CISCO_COM_ApplySetting", "true", true);
			// setStr("Device.WiFi.Radio.2.X_CISCO_COM_ApplySetting", "true", true);
			MiniApplySSID(1);
			MiniApplySSID(2);
		}
		else if ("wps_method" == $arConfig['sub_target'] && $Radio_Enable) {
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
		echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
	}
	else if ("pair_client" == $arConfig['target'] && $Radio_Enable)
	{
		// $pair_num = getStr("Device.WiFi.AccessPoint.$i.AssociatedDeviceNumberOfEntries");
		// $pair_res = "fail";
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
		// for ($j=0; $j<16; $j++)
		// {
			// sleep(6);
			// if (getStr("Device.WiFi.AccessPoint.$i.AssociatedDeviceNumberOfEntries") != $pair_num)
			// {
				// $pair_res = "success";
				// break;
			// }
		// }
		// $arConfig = array('pair_res'=>$pair_res);			
		// $jsConfig = json_encode($arConfig);
		echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
	}
	else if ("pair_cancel" == $arConfig['target'] && $Radio_Enable)
	{
		setStr("Device.WiFi.AccessPoint.1.WPS.X_CISCO_COM_CancelSession", "true", true);
		setStr("Device.WiFi.AccessPoint.2.WPS.X_CISCO_COM_CancelSession", "true", true);
		echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
	}
	else if ("mac_ssid" == $arConfig['target'])
	{
		$filter_enable = getStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.Enable");
		$filter_block  = getStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.FilterAsBlackList");
		if ("true" == $filter_enable) {
			if ("true" == $filter_block) {
				$filtering_mode	= "deny";
			}
			else {
				$filtering_mode	= "allow";
			}
		}
		else {
			$filtering_mode	= "allow_all";
		}
		$ft = array();
		$id = array_filter(explode(",",getInstanceIds("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.")));
		$rootObjName    = "Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.";
		$paramNameArray = array("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.");
		$mapping_array  = array("DeviceName", "MACAddress");
		$filterTableInstance = getParaValues($rootObjName, $paramNameArray, $mapping_array);
		for ($j=0; $j<count($id); $j++)
		{
			$ft[$j][0] = $filterTableInstance["$j"]["DeviceName"];
			$ft[$j][1] = $filterTableInstance["$j"]["MACAddress"];
		}
		$at = array();
		//HotSpot clients do not exist in Host Table.
		//Device.X_COMCAST_COM_GRE.SSID.1. is for SSID-5
		//Device.X_COMCAST_COM_GRE.SSID.2. is for SSID-6
		if ("5"==$i || "6"==$i)
		{
			$id = ("5"==$i)?"1":"2";
			$clients = explode(",", getInstanceIds("Device.X_COMCAST-COM_GRE.Tunnel.1.SSID.$id.AssociatedDevice."));
			//explode on empty string returns array count as 1 [with string(0) ""]
			if($clients[0]){
				foreach($clients as $v)
				{
					array_push($at, array(getStr("Device.X_COMCAST-COM_GRE.Tunnel.1.SSID.$id.AssociatedDevice.$v.Hostname"), getStr("Device.X_COMCAST-COM_GRE.Tunnel.1.SSID.$id.AssociatedDevice.$v.MACAddress")));
				}
			}
		}
		else
		{
			$id = array_filter(explode(",", getInstanceIds("Device.Hosts.Host.")));
			$rootObjName    = "Device.Hosts.Host.";
			$paramNameArray = array("Device.Hosts.Host.");
			$mapping_array  = array("Layer1Interface", "HostName", "PhysAddress");
			$actualTableInstance = getParaValues($rootObjName, $paramNameArray, $mapping_array);
			for ($j=0; $j<count($id); $j++)
			{
				$host = explode(".", $actualTableInstance["$j"]["Layer1Interface"]);
				// $host = explode(".", "Device.WiFi.SSID.1.");
				if (in_array("WiFi", $host))
				{
					if ($i == $host[3])
					{
						array_push($at, array($actualTableInstance["$j"]["HostName"], $actualTableInstance["$j"]["PhysAddress"]));
					}
				}
			}	
		}
		$arConfig = array('filtering_mode'=>$filtering_mode, 'ft'=>$ft, 'at'=>$at);
		$jsConfig = json_encode($arConfig);
		header("Content-Type: application/json");
		echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
	}
	else if ("save_filter" == $arConfig['target'])
	{
		$ssids = array($i);
		//ssid 1,2 are for all users
		//ssid 3,4 & 5,6 are for mso only
		if(($i == 1 || $i == 2) || ($i > 2 && $_SESSION["loginuser"] == "mso")){
			$ft_config_filtered = array();
			foreach($arConfig['ft'] as $key => $value) {
				//Remove Invalid characters Less than (<), Greater than (>), Ampersand (&), Double quote ("), Single quote ('), Pipe (|).
				$ft_config_filtered['ft'][$key][0] = str_replace(str_split('<>&"\'|'), '', $value[0]);
				$ft_config_filtered['ft'][$key][1] = $value[1];
			}
			if(validFilterParam($ft_config_filtered['ft'])){
				foreach ($ssids as $i)	//incase some filter rule apply to more than one SSID (such as HotSpot)
				{
					$ft		= $ft_config_filtered['ft'];
					//get all old table instance
					$old_id = array_filter(explode(",",getInstanceIds("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.")));
					//for old table, delete which is not in new table, keep in place which is in it
					foreach ($old_id as $j)
					{
						$del_mac = true;
						$old_mac = getStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.$j.MACAddress");
						$old_DeviceName = getStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.$j.DeviceName");
						for ($k=0; $k<count($ft); $k++)
						{
							if ((strtolower($old_mac) == strtolower($ft[$k][1])) && (strtolower($old_DeviceName) == strtolower($ft[$k][0])))
							{
								$del_mac = false;
								break;
							}
						}
						if ($del_mac)
						{
							//if an old mac is not in new table, then delete it from old table
							delTblObj("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.$j.");
						}
						else
						{
							//or delete the mac from new table, and resort new table(key as 0, 1, 2...)
							array_splice($ft, $k, 1);
						}
					}
					//add enough new instance, but we can't tell which ID is added!!!
					for ($j=0; $j<count($ft); $j++)
					{
						addTblObj("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.");
					}
					//get all instance IDs, perhaps contains old IDs
					$new_id = array_filter(explode(",",getInstanceIds("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.")));
					//find the IDs in new table, but not in old table
					$id = array_diff($new_id, $old_id);
					//key the diff array as 0, 1, 2...
					sort($id);
					//add the rest
					if (count($id) > 0)
					{
						for ($j=0; $j<count($ft); $j++)
						{
							setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.$id[$j].DeviceName", $ft[$j][0], false);
							setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.$id[$j].MACAddress", $ft[$j][1], true);
						}
					}
					//MAC filter mode, else is "allow_all"
					if ("allow" == $arConfig['filtering_mode']) {
						$filter_enable = "true";
						$filter_block  = "false";
					}
					else if ("deny"  == $arConfig['filtering_mode']) {
						$filter_enable = "true";
						$filter_block  = "true";
					}	
					else {
						$filter_enable = "false";
						$filter_block  = "false";
					}
					$get_filter_enable = getStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.Enable");
					$get_filter_block  = getStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.FilterAsBlackList");
					/*------When changing from "allow_all" to "allow" go from "allow_all" to "deny" then to "allow" -----*/
					if(($get_filter_enable == "false" && $get_filter_block == "false") && ($filter_enable == "true" && $filter_block == "false")){
						//"allow_all" to "deny"
						setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.Enable", "true", false);
						setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.FilterAsBlackList", "true", true);
						//"deny" to "allow"
						setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.Enable", "true", false);
						setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.FilterAsBlackList", "false", true);
					}
					setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.Enable", $filter_enable, false);
					setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.FilterAsBlackList", $filter_block, true);	
					//Saving ACL should not set ApplySetting
					// setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);
					// echo $i;
				}
			}
		}
		echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
	}
}
// sleep(3);
?>
