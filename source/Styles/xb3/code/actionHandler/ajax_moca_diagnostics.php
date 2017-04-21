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
<?php include('../includes/utility.php'); ?>
<?php
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="../index.php";</script>';
	exit(0);
}
$MeshTxNodeTableEntries	= getStr("Device.MoCA.Interface.1.X_RDKCENTRAL-COM_MeshTable.MeshTxNodeTableNumberOfEntries");
if($MeshTxNodeTableEntries != '0'){
	/*--
	AssociatedDeviceNumberOfEntries is the number of other MoCA nodes in the network (not including the XB3).
	MeshTxNodeTableNumberOfEntries is the total number of MoCA nodes in the network (including the XB3 itself).
	The MeshTable has TxRates from each node to every other node, 
	whereas Device.MoCA.Interface.1.AssociatedDevice only has information about the other nodes in the network.
	*/
	$MeshTxNodeArray = array();
	for ($i=1; $i <= $MeshTxNodeTableEntries; $i++) {
		$rootObjName	= "Device.MoCA.Interface.1.X_RDKCENTRAL-COM_MeshTable.MeshTxNodeTable.$i.MeshRxNodeTable.";
		$paramNameArray	= array("Device.MoCA.Interface.1.X_RDKCENTRAL-COM_MeshTable.MeshTxNodeTable.$i.MeshRxNodeTable.");
		$mapping_array	= array("MeshRxNodeId", "MeshPHYTxRate");
		$MeshTxNodes	= getParaValues($rootObjName, $paramNameArray, $mapping_array);
		array_push($MeshTxNodeArray, array(getStr("Device.MoCA.Interface.1.X_RDKCENTRAL-COM_MeshTable.MeshTxNodeTable.$i.MeshTxNodeId") => $MeshTxNodes));
	}
	$rootObjName	= "Device.MoCA.Interface.1.AssociatedDevice.";
	$paramNameArray	= array("Device.MoCA.Interface.1.AssociatedDevice.");
	$mapping_array	= array("NodeID", "HighestVersion");
	$HighestVersion	= getParaValues($rootObjName, $paramNameArray, $mapping_array);
	//For the XB3
	$HighestVersion_Modem = getStr("Device.MoCA.Interface.1.HighestVersion");
	$HighestVersion_Modem = str_replace('.', '', $HighestVersion_Modem);
	$NodeID_Modem = getStr("Device.MoCA.Interface.1.NodeID");
	foreach ($HighestVersion as $key => $value) {
		$Mesh_HighestVersion[$value["NodeID"]] = $value["HighestVersion"];
	}
	$Mesh_HighestVersion[$NodeID_Modem] = $HighestVersion_Modem;
	$MeshTxNodeArray['Mesh_HighestVersion'] = $Mesh_HighestVersion;
}
else {
	$MeshTxNodeArray = array();
	array_push($MeshTxNodeArray, array('MeshTxNodeTableEntries' => '0'));
}
header("Content-Type: application/json");
echo htmlspecialchars(json_encode($MeshTxNodeArray), ENT_NOQUOTES, 'UTF-8');
?>