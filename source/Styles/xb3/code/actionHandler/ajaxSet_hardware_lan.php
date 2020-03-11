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
require_once("../includes/utility.php");
$opType = $_POST['op'];
$r_enable = $_POST['enable'];
try {
	if(PREPAID == true){
		throw new Exception(_('Home Security not supported'));
	}
	if ($opType !== 'savePort4XHS' || !($r_enable == 'true' || $r_enable == 'false')) {
		throw new Exception(_('Parameters are invalid'));
	}
	$response = array();
	/* get the flag path first */
	$rootObjName = "Device.X_CISCO_COM_MultiLAN.";
	$paramNameArray = array("Device.X_CISCO_COM_MultiLAN.");
	$mapping_array  = array("PrimaryLANBridge", "PrimaryLANBridgeHSPorts", "HomeSecurityBridge", "HomeSecurityBridgePorts");
	$multiLan = getParaValues($rootObjName, $paramNameArray, $mapping_array);
	if (empty($multiLan)) {
		throw new Exception(_('failed to fetch parameters from backend'));
	}
	$pLanBridgeHSPortEnablePath = ($multiLan[0]["PrimaryLANBridge"].".Port.".$multiLan[0]["PrimaryLANBridgeHSPorts"].".Enable");
	$HSBridgePortEnablePath = ($multiLan[0]["HomeSecurityBridge"].".Port.".$multiLan[0]["HomeSecurityBridgePorts"].".Enable");
	if (empty($pLanBridgeHSPortEnablePath) || empty($HSBridgePortEnablePath)) {
		throw new Exception(_('failed to fetch parameters from backend'));
	}
	if ($r_enable === 'true') {
		if (setStr($pLanBridgeHSPortEnablePath, "false", true) !== true
			|| setStr($HSBridgePortEnablePath, "true", true) !== true) {
			throw new Exception(_('failed to set parameters to backend'));
		}
	}
	else {
		#zqiu: port should be removed first and then added to another vlan
		#if (setStr($pLanBridgeHSPortEnablePath, "true", true) !== true
		#	|| setStr($HSBridgePortEnablePath, "false", true) !== true) {
		if (setStr($HSBridgePortEnablePath, "false", true) !== true
                        || setStr($pLanBridgeHSPortEnablePath, "true", true) !== true) {
			throw new Exception(_('failed to set parameters to backend'));
		}
	}
	$response["status"] = _("success");
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
}
catch (Exception $e) {
	$response = array("status" => _("Failed"), "msg" => $e->getMessage());
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
}
?>
