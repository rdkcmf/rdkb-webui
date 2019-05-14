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
$thisUser = count($arConfig);
$response_message = '';
if($thisUser > 1){
setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.VoiceProcessing.ReceiveGain", $arConfig['Ingress'], true);
setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.VoiceProcessing.TransmitGain", $arConfig['Egress'], true);
setStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_LogServerPort", $arConfig['server_port'], true);
setStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_LogServer", $arConfig['server_IP_addr'], true);
}
else{
setStr("Device.Services.VoiceService.1.X_RDK-Central_COM_VoiceProcessState",$arConfig['SIP_val'],true);
}

if($response_message!='') {
	$response->error_message = $response_message;
	echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
}
else echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>

