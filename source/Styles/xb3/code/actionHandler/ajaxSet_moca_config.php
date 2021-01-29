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
$jsConfig = $_POST['configInfo'];
//$jsConfig = '{"moca_enable": "true", "scan_method": "true", "channel": "0000000001000000", "beacon_power": "0", "taboo_enable": "false", "taboo_freq": "00000003ffffc000", "nc_enable": "false", "privacy_enable": "false", "net_password": "", "qos_enable": "false"}'; 
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);
$thisUser = $_SESSION["loginuser"];
$validation = true;
if ($_SESSION["lanMode"] != "router") $validation = false;
if($validation) $validation = isValInArray($arConfig['moca_enable'], array('true', 'false'));
if ($validation && "true" == $arConfig['moca_enable'] && "admin" != $thisUser){
	if($validation) $validation = isValInArray($arConfig['scan_method'], array('true', 'false'));
	if($validation) $validation = isValInArray($arConfig['channel'], array("0000000000004000", "0000000000008000", "0000000000010000", "0000000000020000", "0000000000040000", "0000000000080000", "0000000000100000", "0000000000200000", "0000000000400000", "0000000000800000", "0000000001000000", "0000000002000000", "0000000004000000", "0000000008000000", "0000000010000000", "0000000020000000", "0000000040000000", "0000000080000000", "0000000100000000", "0000000200000000", ));
	if($validation) $validation = isValInArray($arConfig['beacon_power'], array('0', '3', '6', '9', '12', '15'));
	if($validation) $validation = (preg_match("/^0{7}[0-9a-f]{7}00$|^0{16}$/i", $arConfig['taboo_freq'])==1);
	if($validation) $validation = isValInArray($arConfig['nc_enable'], array('true', 'false'));
	if($validation) $validation = isValInArray($arConfig['privacy_enable'], array('true', 'false'));
	//if ("true" == $arConfig['privacy_enable'])
		//if($validation) $validation = (preg_match("/^\d{12,17}$/", $arConfig['net_password'])==1);
}
if($validation) {
	if ("true" == $arConfig['moca_enable']){
		if ("admin" != $thisUser){
			setStr("Device.MoCA.Interface.1.X_CISCO_COM_ChannelScanning", $arConfig['scan_method'], true);
			if ("false" == $arConfig['scan_method']){
				setStr("Device.MoCA.Interface.1.FreqCurrentMaskSetting", $arConfig['channel'], true);
			}
			setStr("Device.MoCA.Interface.1.BeaconPowerLimit", $arConfig['beacon_power'], true);
			// GUI version 3.0 removed Taboo enable option
			// setStr("Device.MoCA.Interface.1.X_CISCO_COM_EnableTabooBit", $arConfig['taboo_enable'], false);
			// if ("true" == $arConfig['taboo_enable']){
				setStr("Device.MoCA.Interface.1.NodeTabooMask", $arConfig['taboo_freq'], true);
			// }
			setStr("Device.MoCA.Interface.1.PreferredNC", $arConfig['nc_enable'], true);
			// GUI version 3.0 removed QoS option
			// setStr("Device.MoCA.Interface.1.QoS.X_CISCO_COM_Enabled", $arConfig['qos_enable'], false);
		}
		// GUI version 3.0 don't allowd home user to set MoCA privacy
		if ("admin" != $thisUser){
			if ("true" == $arConfig['privacy_enable']){
				setStr("Device.MoCA.Interface.1.KeyPassphrase", $arConfig['net_password'], true);
			}
			setStr("Device.MoCA.Interface.1.PrivacyEnabledSetting", $arConfig['privacy_enable'], true);
		}
	}
	setStr("Device.MoCA.Interface.1.Enable", $arConfig['moca_enable'], true);
}
echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>
