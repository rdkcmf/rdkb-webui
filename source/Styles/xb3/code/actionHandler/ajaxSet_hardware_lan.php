<?php

require_once("../includes/utility.php");

session_start();

$opType = $_POST['op'];
$r_enable = $_POST['enable'];

try {
	if (!in_array($opType, array('savePort4XHS'), true)
		|| ($opType === 'savePort4XHS' && !isset($r_enable))) {
		throw new Exception('Parameters are incompleted');
	}

	$response = array();

	/* get the flag path first */
	$rootObjName = "Device.X_CISCO_COM_MultiLAN.";
	$paramNameArray = array("Device.X_CISCO_COM_MultiLAN.");
	$mapping_array  = array("PrimaryLANBridge", "PrimaryLANBridgeHSPorts", "HomeSecurityBridge", "HomeSecurityBridgePorts");

	$multiLan = getParaValues($rootObjName, $paramNameArray, $mapping_array);
	if (empty($multiLan)) {
		throw new Exception('failed to fetch parameters from backend');
	}
	$pLanBridgeHSPortEnablePath = ($multiLan[0]["PrimaryLANBridge"].".Port.".$multiLan[0]["PrimaryLANBridgeHSPorts"].".Enable");
	$HSBridgePortEnablePath = ($multiLan[0]["HomeSecurityBridge"].".Port.".$multiLan[0]["HomeSecurityBridgePorts"].".Enable");

	if (empty($pLanBridgeHSPortEnablePath) || empty($HSBridgePortEnablePath)) {
		throw new Exception('failed to fetch parameters from backend');
	}

	if ($r_enable === 'true') {
		if (setStr($pLanBridgeHSPortEnablePath, "false", true) !== true
			|| setStr($HSBridgePortEnablePath, "true", true) !== true) {
			throw new Exception('failed to set parameters to backend');
		}
	}
	else {
		if (setStr($pLanBridgeHSPortEnablePath, "true", true) !== true
			|| setStr($HSBridgePortEnablePath, "false", true) !== true) {
			throw new Exception('failed to set parameters to backend');
		}
	}

	$response["status"] = "success";
	header("Content-Type: application/json");
	echo json_encode($response);
}
catch (Exception $e) {
	$response = array("status" => "Failed", "msg" => $e->getMessage());
	header("Content-Type: application/json");
	echo json_encode($response);
}

?>
