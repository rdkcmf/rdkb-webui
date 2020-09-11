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
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="../index.php";</script>';
	exit(0);
}
$jsConfig = $_POST['configInfo'];
// $jsConfig = '{"CallWaitingEnable":"true","MWIEnable":"true","ConferenceCallingEnable":"true","HoldEnable":"true","PhoneCallerIDEnable":"true","SkyEuroFlashCallWaitingEnable":"true"}';
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);

$response_message = '';
$thisUser = count($arConfig);
if($thisUser > 1){
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.X_RDK-Central_COM_NetworkDisconnect", $arConfig['Sky_Network_Disconnect'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.X_RDK-Central_COM_ConferencingURI", $arConfig['Conference_URI'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.CallWaitingEnable", $arConfig['CallWaitingEnable'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.MWIEnable", $arConfig['MWIEnable'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.X_RDK-Central_COM_ConferenceCallingEnable", $arConfig['ConferenceCallingEnable'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.X_RDK-Central_COM_HoldEnable", $arConfig['HoldEnable'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.X_RDK-Central_COM_PhoneCallerIDEnable", $arConfig['PhoneCallerIDEnable'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.DSCPMark", $arConfig['DSCPMark_sip'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.RTP.DSCPMark", $arConfig['DSCPMark_rtp'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.EthernetPriorityMark", $arConfig['PriorityMark_sip'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.RTP.EthernetPriorityMark", $arConfig['PriorityMark_rtp'], true);
	//setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.X_RDK-Central_COM_EuroFlashCallWaitingEnable", $arConfig['SkyEuroFlashCallWaitingEnable'], true);
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

