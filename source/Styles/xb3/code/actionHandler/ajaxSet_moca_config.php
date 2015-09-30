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

$jsConfig = $_REQUEST['configInfo'];
//$jsConfig = '{"moca_enable": "true", "scan_method": "true", "channel": "0000000001000000", "beacon_power": "0", "taboo_enable": "false", "taboo_freq": "00000003ffffc000", "nc_enable": "false", "privacy_enable": "false", "net_password": "", "qos_enable": "false"}'; 

$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);

$thisUser = $arConfig['thisUser'];

if ("true" == $arConfig['moca_enable']){

	if ("admin" != $thisUser){
		setStr("Device.MoCA.Interface.1.X_CISCO_COM_ChannelScanning", $arConfig['scan_method'], false);
		if ("false" == $arConfig['scan_method']){
			setStr("Device.MoCA.Interface.1.FreqCurrentMaskSetting", $arConfig['channel'], false);
		}
		setStr("Device.MoCA.Interface.1.BeaconPowerLimit", $arConfig['beacon_power'], false);

		// GUI version 3.0 removed Taboo enable option
		// setStr("Device.MoCA.Interface.1.X_CISCO_COM_EnableTabooBit", $arConfig['taboo_enable'], false);
		// if ("true" == $arConfig['taboo_enable']){
			setStr("Device.MoCA.Interface.1.NodeTabooMask", $arConfig['taboo_freq'], false);
		// }

		setStr("Device.MoCA.Interface.1.PreferredNC", $arConfig['nc_enable'], false);
		
		// GUI version 3.0 removed QoS option
		// setStr("Device.MoCA.Interface.1.QoS.X_CISCO_COM_Enabled", $arConfig['qos_enable'], false);
	}
	
	// GUI version 3.0 don't allowd home user to set MoCA privacy
	if ("admin" != $thisUser){
		if ("true" == $arConfig['privacy_enable']){
			setStr("Device.MoCA.Interface.1.KeyPassphrase", $arConfig['net_password'], false);
		}
		setStr("Device.MoCA.Interface.1.PrivacyEnabledSetting", $arConfig['privacy_enable'], false);
	}
}

setStr("Device.MoCA.Interface.1.Enable", $arConfig['moca_enable'], true);
echo $jsConfig;

?>
