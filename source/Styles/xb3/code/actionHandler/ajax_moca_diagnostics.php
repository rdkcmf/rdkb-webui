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
<?php include('../includes/utility.php'); ?>
<?php

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
$MeshTableEntries	= getStr("Device.MoCA.Interface.1.X_RDKCENTRAL-COM_MeshTableNumberOfEntries");
if($MeshTableEntries != '0'){
	/*--
	AssociatedDeviceNumberOfEntries is the number of other MoCA nodes in the network (not including the XB3).
	MeshTableNumberOfEntries is the total number of MoCA nodes in the network (including the XB3 itself).
	The MeshTable has TxRates from each node to every other node, 
	whereas Device.MoCA.Interface.1.AssociatedDevice only has information about the other nodes in the network.
	*/
	//MoCA Nodes Table
	$MeshNodeArray = array();
	for ($i=1; $i <= $MeshTableEntries; $i++) {
		$rootObjName	= "Device.MoCA.Interface.1.X_RDKCENTRAL-COM_MeshTable.$i.";
		$paramNameArray	= array("Device.MoCA.Interface.1.X_RDKCENTRAL-COM_MeshTable.$i.");
		$mapping_array	= array("MeshTxNodeId", "MeshRxNodeId", "MeshPHYTxRate");
		$MeshNodes	= getParaValues($rootObjName, $paramNameArray, $mapping_array);
		array_push($MeshNodeArray, $MeshNodes);
	}
	//for AssociatedDevices
	$rootObjName	= "Device.MoCA.Interface.1.AssociatedDevice.";
	$paramNameArray	= array("Device.MoCA.Interface.1.AssociatedDevice.");
	$mapping_array	= array("MACAddress", "NodeID", "HighestVersion", "PreferredNC", "TxPowerControlReduction", "RxPowerLevel");
	$AssociatedDevices	= getParaValues($rootObjName, $paramNameArray, $mapping_array);
	//for Modem NC
	$MACAddress_Modem	= getStr("Device.MoCA.Interface.1.MACAddress");
	$PreferredNC_Modem	= getStr("Device.MoCA.Interface.1.PreferredNC");
	$BackupNC_Modem		= getStr("Device.MoCA.Interface.1.BackupNC");
	$HighestVersion_Modem = getStr("Device.MoCA.Interface.1.HighestVersion");
	$HighestVersion_Modem = str_replace('.', '', $HighestVersion_Modem);
	$NodeID_Modem 	= getStr("Device.MoCA.Interface.1.NodeID");
	foreach ($AssociatedDevices as $key => $value) {
		$Mesh_HighestVersion[$value["NodeID"]] = $value["HighestVersion"];
	}
	$Mesh_HighestVersion[$NodeID_Modem] = $HighestVersion_Modem;
	$MeshNodeArray['Mesh_HighestVersion'] = $Mesh_HighestVersion;
	$AssociatedDevices['GW']['MACAddress']		= $MACAddress_Modem;
	$AssociatedDevices['GW']['PreferredNC']		= $PreferredNC_Modem;
	$AssociatedDevices['GW']['BackupNC']		= $BackupNC_Modem;
	$AssociatedDevices['GW']['NodeID']			= $NodeID_Modem;
	$MeshNodeArray['AssociatedDevices'] = $AssociatedDevices;
	$MeshNodeArray['MeshTableEntries'] = $MeshTableEntries;
}
else {
	$MeshNodeArray = array();
	$MeshNodeArray['MeshTableEntries'] = '0';
}
header("Content-Type: application/json");
echo htmlspecialchars(json_encode($MeshNodeArray), ENT_NOQUOTES, 'UTF-8');
?>