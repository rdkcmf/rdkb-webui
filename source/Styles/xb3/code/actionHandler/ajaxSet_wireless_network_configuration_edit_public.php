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
<?php 
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="../index.php";</script>';
	exit(0);
}
$jsConfig = $_REQUEST['configInfo'];
$arConfig = json_decode($jsConfig, true);
// DHCPCircuitIDSSID	true
// DHCPRemoteID	false
// DSCPMarkPolicy	2
// KeepAliveCount	3
// KeepAliveFailInterval	5
// KeepAliveInterval	60
// KeepAliveThreshold	5
// ReconnectPrimary	300
// RemoteEndpoints	www.cisco.com,www.google.com
// broadcastSSID	true
// enableWMM	false
// encrypt_method	AES
// encrypt_mode	WPA2-Personal
// max_client	32
// network_name	xfinityWiFi
// network_password	abc123456
// radio_enable	true
// radio_freq	5
// ssid_number	6
/*********************************************************************************************/
$i = $arConfig['ssid_number'];
$r = (2 - intval($i)%2);	//1,3,5,7==1(2.4G); 2,4,6,8==2(5G)
// this method for only restart a certain SSID
function MiniApplySSID($ssid) {
	$apply_id = (1 << intval($ssid)-1);
	$apply_rf = (2  - intval($ssid)%2);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySettingSSID", $apply_id, false);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySetting", "true", true);
}
//ssid 5,6 for mso only
if (($_SESSION["loginuser"] == "mso") && ($i == 5 || $i == 6)) {
	// change SSID status first, if disable, no need to configure following
	setStr("Device.WiFi.SSID.$i.Enable", $arConfig['radio_enable'], true);
	if ("true" == $arConfig['radio_enable'] || "true" == $arConfig['radio_reset'])
	{
		if ("None" == $arConfig['encrypt_mode']) {
			setStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", $arConfig['encrypt_mode'], true);
		}
		else if ("WEP-64" == $arConfig['encrypt_mode']) {
			setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.1.WEPKey",  $arConfig['network_password'], false);
			setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.2.WEPKey",  $arConfig['network_password'], false);
			setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.3.WEPKey",  $arConfig['network_password'], false);
			setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.4.WEPKey",  $arConfig['network_password'], false);
			setStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", $arConfig['encrypt_mode'], true);
		}
		else if("WEP-128" == $arConfig['encrypt_mode']) {
			setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.1.WEPKey", $arConfig['network_password'], false);
			setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.2.WEPKey", $arConfig['network_password'], false);
			setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.3.WEPKey", $arConfig['network_password'], false);
			setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.4.WEPKey", $arConfig['network_password'], false);
			setStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", $arConfig['encrypt_mode'], true);
		}
		else {	//no open, no wep
			//bCommit false->true still do validation each, have to group set this...
			DmExtSetStrsWithRootObj("Device.WiFi.", true, array(
				array("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", "string", $arConfig['encrypt_mode']), 
				array("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_EncryptionMethod", "string", $arConfig['encrypt_method'])));
			setStr("Device.WiFi.AccessPoint.$i.Security.X_COMCAST-COM_KeyPassphrase", $arConfig['network_password'], true);
		}
		setStr("Device.WiFi.SSID.$i.SSID", $arConfig['network_name'], true);
		setStr("Device.WiFi.AccessPoint.$i.SSIDAdvertisementEnabled", $arConfig['broadcastSSID'], true);
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
		// check if the LowerLayers radio is enabled
		if ("false" == getStr("Device.WiFi.Radio.$r.Enable") && "true" == $arConfig['radio_enable']){
			setStr("Device.WiFi.Radio.$r.Enable", "true", true);
		}
		setStr("Device.WiFi.SSID.$i.Enable", $arConfig['radio_enable'], true);
		if (intval($i) >= 5 ){
			setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_BssMaxNumSta", $arConfig['max_client'], true);
			//soft-gre
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.DSCPMarkPolicy", $arConfig['DSCPMarkPolicy'], true);
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.PrimaryRemoteEndpoint", $arConfig['PrimaryRemoteEndpoint'], true);
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.SecondaryRemoteEndpoint", $arConfig['SecondaryRemoteEndpoint'], true);
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingCount", $arConfig['KeepAliveCount'], true);
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingInterval", $arConfig['KeepAliveInterval'], true);
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingFailThreshold", $arConfig['KeepAliveThreshold'], true);
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingIntervalInFailure", $arConfig['KeepAliveFailInterval'], true);
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.ReconnectToPrimaryRemoteEndpoint", $arConfig['ReconnectPrimary'], true);
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.EnableCircuitID", $arConfig['DHCPCircuitIDSSID'], true);
			setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.EnableRemoteID", $arConfig['DHCPRemoteID'], true);
			// echo $i;
		}
	}
	// if ("2.4" == $arConfig['radio_freq']){
		// setStr("Device.WiFi.Radio.1.X_CISCO_COM_ApplySetting", "true", true);
	// }
	// else{
		// setStr("Device.WiFi.Radio.2.X_CISCO_COM_ApplySetting", "true", true);
	// }
	// setStr("Device.WiFi.Radio.$r.X_CISCO_COM_ApplySetting", "true", true);
	MiniApplySSID($i);
}
?>