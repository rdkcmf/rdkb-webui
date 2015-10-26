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
$i = $_POST['ssid_number'];
$r = (2 - intval($i)%2);	//1,3,5,7==1(2.4G); 2,4,6,8==2(5G)

// this method for only restart a certain SSID
function MiniApplySSID($ssid) {
	$apply_id = (1 << intval($ssid)-1);
	$apply_rf = (2  - intval($ssid)%2);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySettingSSID", $apply_id, false);
	setStr("Device.WiFi.Radio.$apply_rf.X_CISCO_COM_ApplySetting", "true", true);
}

// change SSID status first, if disable, no need to configure following
// setStr("Device.WiFi.SSID.$i.Enable", $_POST['radio_enable'], true);

if ("true" == $_POST['radio_enable'] || "true" == $_POST['radio_reset'])
{
	if ("None" == $_POST['encrypt_mode']) {
		setStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", $_POST['encrypt_mode'], true);
	}
	else if ("WEP-64" == $_POST['encrypt_mode']) {
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.1.WEPKey",  $_POST['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.2.WEPKey",  $_POST['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.3.WEPKey",  $_POST['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey64Bit.4.WEPKey",  $_POST['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", $_POST['encrypt_mode'], true);
	}
	else if("WEP-128" == $_POST['encrypt_mode']) {
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.1.WEPKey", $_POST['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.2.WEPKey", $_POST['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.3.WEPKey", $_POST['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_WEPKey128Bit.4.WEPKey", $_POST['network_password'], false);
		setStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", $_POST['encrypt_mode'], true);
	}
	else {	//no open, no wep
		//bCommit false->true still do validation each, have to group set this...
		DmExtSetStrsWithRootObj("Device.WiFi.", true, array(
			array("Device.WiFi.AccessPoint.$i.Security.ModeEnabled", "string", $_POST['encrypt_mode']), 
			array("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_EncryptionMethod", "string", $_POST['encrypt_method'])));
		setStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_KeyPassphrase", $_POST['network_password'], true);
	}

	setStr("Device.WiFi.SSID.$i.SSID", $_POST['network_name'], true);
	setStr("Device.WiFi.AccessPoint.$i.SSIDAdvertisementEnabled", $_POST['broadcastSSID'], true);
	
	// if ("false" == $_POST['enableWMM']){
		// setStr("Device.WiFi.AccessPoint.$i.UAPSDEnable", "false", true);
	// }
	// setStr("Device.WiFi.AccessPoint.$i.WMMEnable", $_POST['enableWMM'], true);
	
	//when disable WMM, make sure UAPSD is disabled as well, have to use group set		
	if (getStr("Device.WiFi.AccessPoint.$i.WMMEnable") != $_POST['enableWMM']) {
		DmExtSetStrsWithRootObj("Device.WiFi.", true, array(
			array("Device.WiFi.AccessPoint.$i.UAPSDEnable", "bool", "false"),
			array("Device.WiFi.AccessPoint.$i.WMMEnable",   "bool", $_POST['enableWMM'])));			
	}
}

	// check if the LowerLayers radio is enabled
	if ("false" == getStr("Device.WiFi.Radio.$r.Enable") && "true" == $_POST['radio_enable']){
		setStr("Device.WiFi.Radio.$r.Enable", "true", true);
	}
	setStr("Device.WiFi.SSID.$i.Enable", $_POST['radio_enable'], true);

	if (intval($i) >= 5 ){
		setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_BssMaxNumSta", $_POST['max_client'], true);
		//soft-gre
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.DSCPMarkPolicy", $_POST['DSCPMarkPolicy'], true);
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.PrimaryRemoteEndpoint", $_POST['PrimaryRemoteEndpoint'], true);
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.SecondaryRemoteEndpoint", $_POST['SecondaryRemoteEndpoint'], true);
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingCount", $_POST['KeepAliveCount'], true);
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingInterval", $_POST['KeepAliveInterval'], true);
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingFailThreshold", $_POST['KeepAliveThreshold'], true);
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.RemoteEndpointHealthCheckPingIntervalInFailure", $_POST['KeepAliveFailInterval'], true);
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.ReconnectToPrimaryRemoteEndpoint", $_POST['ReconnectPrimary'], true);
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.EnableCircuitID", $_POST['DHCPCircuitIDSSID'], true);
		setStr("Device.X_COMCAST-COM_GRE.Tunnel.1.EnableRemoteID", $_POST['DHCPRemoteID'], true);
		// echo $i;
	}
}

// if ("2.4" == $_POST['radio_freq']){
	// setStr("Device.WiFi.Radio.1.X_CISCO_COM_ApplySetting", "true", true);
// }
// else{
	// setStr("Device.WiFi.Radio.2.X_CISCO_COM_ApplySetting", "true", true);
// }

// setStr("Device.WiFi.Radio.$r.X_CISCO_COM_ApplySetting", "true", true);
MiniApplySSID($i);

?>
