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
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_SDigitTimer", $arConfig['SDigitTimer'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_ZDigitTimer", $arConfig['ZDigitTimer'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.Enable", $arConfig['Acc_Enabled'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.DirectoryNumber", $arConfig['Directory'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.SIP.URI", $arConfig['uri'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.SIP.AuthPassword", $arConfig['Auth_pwd'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.SIP.AuthUserName", $arConfig['Auth_usr'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.OutboundProxy", $arConfig['OutboundProxy'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.OutboundProxyPort", $arConfig['OutboundProxyPort'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.ProxyServer", $arConfig['ProxyServer'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.ProxyServerPort", $arConfig['ProxyServerPort'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.RegistrarServer", $arConfig['RegistrarServer'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.RegistrarServerPort",$arConfig['RegistrarServerPort'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.UserAgentDomain", $arConfig['UserAgentDomain'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_DigitMap", $arConfig['DigitMap'], true);
	setStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_EmergencyDigitMap", $arConfig['EmergencyDigitMap'], true);
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