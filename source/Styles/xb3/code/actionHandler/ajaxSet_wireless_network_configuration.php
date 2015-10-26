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
<?php include('../includes/utility.php'); ?>
<?php

$jsConfig = $_REQUEST['configInfo'];
//$jsConfig = '{"ssid_number":"1", "ft":[["1","2"],["c","d"]], "target":"save_filter"}';

$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);

$i = $arConfig['ssid_number'];

// this method for only restart a certain SSID
function MiniApplySSID($ssid) {
	$apply_id = (1 << intval($ssid)-1);
	$apply_rf = (2  - intval($ssid)%2);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySettingSSID", $apply_id, false);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySetting", "true", true);
}

if ("save_config" == $arConfig['target'])
{
	if ("save_basic" == $arConfig['sub_target'])
	{
		setStr("Device.WiFi.Radio.$i.TransmitPower", $arConfig['transmit_power'], false);
		setStr("Device.WiFi.Radio.$i.AutoChannelEnable", $arConfig['channel_automatic'], false);	
	}
	else if ("save_advance" == $arConfig['sub_target'])
	{
		setStr("Device.WiFi.Radio.$i.X_CISCO_COM_CTSProtectionMode", $arConfig['BG_protect_mode'], false);
		setStr("Device.WiFi.Radio.$i.X_COMCAST_COM_IGMPSnoopingEnable", $arConfig['IGMP_Snooping'], false);
		setStr("Device.WiFi.Radio.$i.OperatingChannelBandwidth", $arConfig['channel_bandwidth'], false);
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
		setStr("Device.WiFi.Radio.$i.X_CISCO_COM_HTTxStream", $arConfig['HT_TxStream'], false);
		setStr("Device.WiFi.Radio.$i.X_CISCO_COM_HTRxStream", $arConfig['HT_RxStream'], false);
		setStr("Device.WiFi.Radio.$i.X_CISCO_COM_STBCEnable", $arConfig['STBC_enabled'], false);
		setStr("Device.WiFi.AccessPoint.$i.UAPSDEnable", $arConfig['WMM_power_save'], true);	
	}
	
	//redio standards and green mode  must set together
	setStr("Device.WiFi.Radio.$i.OperatingStandards", $arConfig['wireless_mode'], false);
	setStr("Device.WiFi.Radio.$i.X_CISCO_COM_11nGreenfieldEnabled", $arConfig['operation_mode'], false);

	//primary channel and 2nd channel must set together
	if ("false"==$arConfig['channel_automatic']){
		setStr("Device.WiFi.Radio.$i.Channel", $arConfig['channel_number'], false);
	}
	if (("2" != $i) && ("20MHz" != $arConfig['channel_bandwidth'])){
		setStr("Device.WiFi.Radio.$i.ExtensionChannel", $arConfig['ext_channel'], false);	
	}
	
	//apply once
	// setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);
	MiniApplySSID($i);
	echo $jsConfig;
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
	echo $jsConfig;	
}
else if ("save_enable" == $arConfig['target'])
{
	if ("radio_enable" == $arConfig['sub_target']) {
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
	else if ("wps_enabled" == $arConfig['sub_target']) {
		//enable or disable WPS in all SSID, GUI ensure that only change will be commit to backend
		$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));
		foreach ($ssids as $i){
			setStr("Device.WiFi.AccessPoint.$i.WPS.Enable", $arConfig['wps_enabled'], true);
			// setStr("Device.WiFi.Radio.$i.X_CISCO_COM_ApplySetting", "true", true);	// all SSID, so don't put this in loop
		}
		// setStr("Device.WiFi.Radio.1.X_CISCO_COM_ApplySetting", "true", true);
		// setStr("Device.WiFi.Radio.2.X_CISCO_COM_ApplySetting", "true", true);
		MiniApplySSID(1);
		MiniApplySSID(2);
	}
	else if ("wps_method" == $arConfig['sub_target']) {
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

	echo $jsConfig;	
}
else if ("pair_client" == $arConfig['target'])
{
	// $pair_num = getStr("Device.WiFi.AccessPoint.$i.AssociatedDeviceNumberOfEntries");
	// $pair_res = "fail";
	
	if ("PushButton" == $arConfig['pair_method']) 
	{
		setStr("Device.WiFi.AccessPoint.1.WPS.X_CISCO_COM_ActivatePushButton", "true", true);
		setStr("Device.WiFi.AccessPoint.2.WPS.X_CISCO_COM_ActivatePushButton", "true", true);
	}
	else 
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
	echo $jsConfig;	
}
else if ("pair_cancel" == $arConfig['target'])
{
	setStr("Device.WiFi.AccessPoint.1.WPS.X_CISCO_COM_CancelSession", "true", true);
	setStr("Device.WiFi.AccessPoint.2.WPS.X_CISCO_COM_CancelSession", "true", true);
	echo $jsConfig;	
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
	echo $jsConfig;	
}
else if ("save_filter" == $arConfig['target'])
{
	$ssids = array($i);

	//xfinitywifi[HotSpot] filter rule apply to both 5 & 6 SSID
	if ("5"==$i || "6"==$i){
		$ssids = array("5","6");
	}
	
	foreach ($ssids as $i)	//incase some filter rule apply to more than one SSID (such as HotSpot)
	{
		$ft		= $arConfig['ft'];
		//get all old table instance
		$old_id = array_filter(explode(",",getInstanceIds("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.")));
		
		//for old table, delete which is not in new table, keep in place which is in it
		foreach ($old_id as $j)
		{
			$del_mac = true;
			$old_mac = getStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.$j.MACAddress");
			
			for ($k=0; $k<count($ft); $k++)
			{
				if ($old_mac == $ft[$k][1])
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
	//For WECB
	setStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.X_CISCO_COM_SSID_Updated", "true", true);
	echo $jsConfig;	
}

// sleep(3);

?>
