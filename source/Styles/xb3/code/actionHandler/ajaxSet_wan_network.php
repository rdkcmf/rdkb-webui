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
$allowEthWan= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.AllowEthernetWAN");
$autoWanEnable= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_AutowanFeatureSupport");
$modelName= getStr("Device.DeviceInfo.ModelName");
if(!((($autoWanEnable=="true") || ($allowEthWan=="true")) && (($modelName=="CGM4140COM") || ($modelName=="CGM4331COM") || ($modelName=="TG4482A"))) ){
		die();
}
$jsConfig = $_POST['configInfo'];
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);
$thisUser = $_SESSION["loginuser"];
if($thisUser=="admin"){
	if($autoWanEnable=="true"){
		setStr("Device.X_RDKCENTRAL-COM_EthernetWAN.SelectedOperationalMode", $arConfig['wan_network'], true);
	}else{
		setStr("Device.Ethernet.X_RDKCENTRAL-COM_WAN.Enabled", $arConfig['wan_network'], true);
	}
	
}

echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>
