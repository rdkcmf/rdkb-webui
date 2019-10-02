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

if (!isset($_SESSION["loginuser"]) || $_SESSION['loginuser'] != 'mso') {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
$ripInfo = json_decode($_POST['ripInfo'], true);
function setRIPconfig($ripInfo){
	$authType = $ripInfo['AuthType'];
	setStr("Device.Routing.RIP.Enable", "true", false);
	setStr("Device.Routing.RIP.InterfaceSetting.1.Enable", "true", false);	
	setStr("Device.Routing.RIP.InterfaceSetting.1.Interface", $ripInfo['IfName'], false);	
	if($ripInfo['SendVer'] == "NA") {
		setStr("Device.Routing.RIP.InterfaceSetting.1.SendRA", "false", false);	
	} 
	else {
		setStr("Device.Routing.RIP.InterfaceSetting.1.SendRA", "true", false);	
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_SendVersion", $ripInfo['SendVer'], false);	
	}
	if($ripInfo['RecVer'] == "NA") {
		setStr("Device.Routing.RIP.InterfaceSetting.1.AcceptRA", "false", false);	
	} 
	else {
		setStr("Device.Routing.RIP.InterfaceSetting.1.AcceptRA", "true", false);	
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_ReceiveVersion", $ripInfo['RecVer'], false);	
	}
	setStr("Device.Routing.RIP.X_CISCO_COM_UpdateInterval", $ripInfo['Interval'], false);
	setStr("Device.Routing.RIP.X_CISCO_COM_DefaultMetric", $ripInfo['Metric'], false);
	if(!strcasecmp($authType, "SimplePassword")) {
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_SimplePassword", $ripInfo['auth_key'], false);
	}
	elseif (!strcasecmp($authType, "MD5")) {
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_Md5KeyValue", $ripInfo['auth_key'], false);
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_Md5KeyID", $ripInfo['auth_id'], false);		//doesn't work?
	}
	setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_AuthenticationType", $ripInfo['AuthType'], false);
	setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_Neighbor", $ripInfo['NeighborIP'], true);
}
$validation = true;
if($validation) $validation = isValInArray($ripInfo['AuthType'], array('NoAuth', 'SimplePassword', 'MD5'));
if($validation) $validation = isValInArray($ripInfo['IfName'], array('Cable', 'Ethernet'));
if($validation) $validation = isValInArray($ripInfo['SendVer'], array('NA', 'RIP1'));
if($validation) $validation = isValInArray($ripInfo['RecVer'], array('NA', 'RIP1'));
if($validation) $validation = isValInRange($ripInfo['Interval'], 5, 2147483647);
if($validation) $validation = isValInRange($ripInfo['Metric'], 1, 15);
if($validation) $validation = validIPAddr($ripInfo['NeighborIP']);
$authType = $ripInfo['AuthType'];
if($validation && ($authType == "SimplePassword")){
	if($validation) $validation = printableCharacters(array($ripInfo['auth_key'], 1, 32));
	if($validation) $validation = is_allowed_string($ripInfo['auth_key']);
}
if($validation && ($authType == "MD5")){
	if($validation) $validation = printableCharacters(array($ripInfo['auth_key'], 1, 32));
	if($validation) $validation = is_allowed_string($ripInfo['auth_key']);
	if($validation) $validation = isValInRange($ripInfo['auth_id'], 0, 999);
}
if($validation) setRIPconfig($ripInfo);
?>
