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
<?php include('../includes/utility.php') ?>
<?php include('../includes/actionHandlerUtility.php') ?>
<?php
session_start();
if (!isset($_SESSION["loginuser"]) || $_SESSION['loginuser'] != 'mso') {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="../index.php";</script>';
	exit(0);
}
$result="";
function PORTTEST($sport,$eport,$arraySPort,$arrayEPort) {
	//echo $sport."  ".$eport."  ".$arraySPort."  ".$arrayEPort."<hr/>";
	if ( ($sport>=$arraySPort) && ($sport<=$arrayEPort) ){
		return 1;
	}
	elseif ( ($eport>=$arraySPort) && ($eport<=$arrayEPort) ){
		return 1;
	}
	elseif ( ($sport<$arraySPort) && ($eport>$arrayEPort) ){
		return 1;
	}
	else 
		return 0;
}
if (isset($_POST['set'])){
	if (isValInArray($_POST['UHSPStatus'], array("Enabled", "Disabled"))){
		$UHSPStatus=(($_POST['UHSPStatus']=="Enabled")?"true":"false");
		setStr("Device.NAT.X_Comcast_com_EnableHSPortMapping",$UHSPStatus,true);
		while(getStr("Device.NAT.X_Comcast_com_EnableHSPortMapping")!=$UHSPStatus) sleep(2);
		// $UHSPStatus=(getStr("Device.NAT.X_Comcast_com_EnableHSPortMapping")=="true"?"Enabled":"Disabled");
		// echo json_encode($UHSPStatus);
	}
}
if (isset($_POST['add'])) {
	$validation = true;
	if($validation) $validation = printableCharacters($_POST['name']);
	if($validation) $validation = is_allowed_string($_POST['name']);
	if($validation) $validation = isValInArray($_POST['type'], array('TCP', 'UDP', 'TCP/UDP'));
	if($validation) $validation = validIPAddr($_POST['ip']);
	if($validation) $validation = validPort($_POST['startport']);
	if($validation) $validation = validPort($_POST['endport']);
	if($validation) $validation = validPort($_POST['priport']);
	if($validation) $validation = isValInArray($_POST['enportrange'], array('true', 'false'));
	if($validation) {
		$name=$_POST['name'];
		$type=$_POST['type'];
		if ($type=="TCP/UDP") $type="BOTH";
		$ip=$_POST['ip'];
		$startport=$_POST['startport'];
		$endport=$_POST['endport'];
		$priport=$_POST['priport'];
		$enportrange=$_POST['enportrange']; // string "true" / "false"
		$enableHSEntry = 'true';  // set 'true' to default value
		if (getStr("Device.NAT.PortMappingNumberOfEntries")==0) {	//no table, need test whether it equals 0
			addTblObj("Device.NAT.PortMapping.");
			$IDs=explode(",",getInstanceIDs("Device.NAT.PortMapping."));
			$i=$IDs[count($IDs)-1];
			$rootObjName ="Device.NAT.PortMapping.";
			$paramArray = 
				array (
					array("Device.NAT.PortMapping.".$i.".Enable", "bool", $enableHSEntry),
					array("Device.NAT.PortMapping.".$i.".InternalClient", "string", $ip),
					array("Device.NAT.PortMapping.".$i.".InternalPort", "uint", $priport),
					array("Device.NAT.PortMapping.".$i.".ExternalPort", "uint", $startport),
					array("Device.NAT.PortMapping.".$i.".ExternalPortEndRange", "uint", $endport),
					array("Device.NAT.PortMapping.".$i.".Protocol", "string", $type),
					array("Device.NAT.PortMapping.".$i.".Description", "string", $name),
				);
			$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);
			if (!$retStatus){$result="Success!";}	
		} 
		else {
			//$result="";
			$rootObjName    = "Device.NAT.PortMapping.";
			$paramNameArray = array("Device.NAT.PortMapping.");
			$mapping_array  = array("LeaseDuration", "Description", "InternalClient", "Protocol", "ExternalPort", "ExternalPortEndRange", "InternalPort");
			$portMappingValues = getParaValues($rootObjName, $paramNameArray, $mapping_array);
			foreach ($portMappingValues as $key) {
				if ($key["LeaseDuration"]==0){
					$arrayName = $key["Description"];
					$arrayIP = $key["InternalClient"];
					$arrayType = $key["Protocol"];
					$arraySPort = $key["ExternalPort"];
					$arrayEPort = $key["ExternalPortEndRange"];
					$InternalPort = $key["InternalPort"];
					if($name==$arrayName) {
						if($InternalPort !=0){
							$result.="Service name has been used in HS Port Forwarding service!\n";
							break;
						} else {
							$result.="Service name has been used in Port Forwarding service!\n";
							break;
						}
					} else if($type=="BOTH"||$arrayType=="BOTH"||$type==$arrayType){
						if($arrayIP==$ip && $InternalPort==$priport){
							if($InternalPort !=0){
								$result.="Conflict with other HS Port Forwarding service. Please check Private Port(s) and IP!";
								break;
							} else {
								$result.="Conflict with other Port Forwarding service. Please check port and IP!";
								break;
							}
						}
						else if($arrayIP==$ip) {
							$porttest=PORTTEST($startport,$endport,$arraySPort,$arrayEPort);
							if ($porttest==1) {
								if($InternalPort !=0){
									$result.="Conflict with other HS Port Forwarding service. Please check Public port and IP!";
									break;
								} else {
									$result.="Conflict with other Port Forwarding service. Please check Start/End port and IP!";
									break;
								}
							}
						}
					}
				}
			}		
			if ($result=="") {
				addTblObj("Device.NAT.PortMapping.");
				$IDs=explode(",",getInstanceIDs("Device.NAT.PortMapping."));
				$i=$IDs[count($IDs)-1];
				$rootObjName ="Device.NAT.PortMapping.";
				$paramArray = 
					array (
						array("Device.NAT.PortMapping.".$i.".Enable", "bool", $enableHSEntry),
						array("Device.NAT.PortMapping.".$i.".InternalClient", "string", $ip),
						array("Device.NAT.PortMapping.".$i.".InternalPort", "uint", $priport),
						array("Device.NAT.PortMapping.".$i.".ExternalPort", "uint", $startport),
						array("Device.NAT.PortMapping.".$i.".ExternalPortEndRange", "uint", $endport),
						array("Device.NAT.PortMapping.".$i.".Protocol", "string", $type),
						array("Device.NAT.PortMapping.".$i.".Description", "string", $name),
					);
				$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);
				if (!$retStatus){$result="Success!";}	
			}
		}
	}
}
if (isset($_POST['edit'])){
	$validation = true;
	if($validation) $validation = validId($_POST['id']);
	if($validation) $validation = printableCharacters($_POST['name']);
	if($validation) $validation = is_allowed_string($_POST['name']);
	if($validation) $validation = isValInArray($_POST['type'], array('TCP', 'UDP', 'TCP/UDP'));
	if($validation) $validation = validIPAddr($_POST['ip']);
	if($validation) $validation = validPort($_POST['startport']);
	if($validation) $validation = validPort($_POST['endport']);
	if($validation) $validation = validPort($_POST['priport']);
	if($validation) $validation = isValInArray($_POST['enportrange'], array('true', 'false'));
	if($validation) {
		//data:{edit:"true",id:id,name:name,type:type,ip:ip,startport:startport,endport:endport,priport:priport,enportrange:enportrange},
		$i=$_POST['id'];
		$name=$_POST['name'];
		$type=$_POST['type'];
		if ($type=="TCP/UDP") $type="BOTH";
		$ip=$_POST['ip'];
		$sport=$_POST['startport'];
		$eport=$_POST['endport'];
		$priport=$_POST['priport'];
		$enportrange=$_POST['enportrange'];
		// $result="";
		$rootObjName    = "Device.NAT.PortMapping.";
		$paramNameArray = array("Device.NAT.PortMapping.");
		$mapping_array  = array("LeaseDuration", "Description", "InternalClient", "Protocol", "ExternalPort", "ExternalPortEndRange", "InternalPort");
		$portMappingValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
		foreach ($portMappingValues as $key) {
			$j = $key["__id"];
			if ($i==$j) continue;
			if ($key["LeaseDuration"]==0){
				$arrayName 		= $key["Description"];
				$arrayIP 		= $key["InternalClient"];
				$arrayType 		= $key["Protocol"];
				$arraySPort 	= $key["ExternalPort"];
				$arrayEPort 	= $key["ExternalPortEndRange"];
				$InternalPort 	= $key["InternalPort"];
				if($name==$arrayName) { 
					if($InternalPort !=0){
						$result.="Service name has been used in HS Port Forwarding service!\n";
						break;
					} else {
						$result.="Service name has been used in Port Forwarding service!\n";
						break;
					}
				}
				else if($type=="BOTH"||$arrayType=="BOTH"||$type==$arrayType){
					if($arrayIP==$ip && $InternalPort==$priport){
						if($InternalPort !=0){
							$result.="Conflict with other HS Port Forwarding service. Please check Private Port(s) and IP!";
							break;
						} else {
							$result.="Conflict with other Port Forwarding service. Please check port and IP!";
							break;
						}
					}
					else if($arrayIP==$ip) {
						$porttest=PORTTEST($startport,$endport,$arraySPort,$arrayEPort);
						if ($porttest==1) {
							if($InternalPort !=0){
								$result.="Conflict with other HS Port Forwarding service. Please check Public port and IP!";
								break;
							} else {
								$result.="Conflict with other Port Forwarding service. Please check Start/End port and IP!";
								break;
							}
						}
					}
				}
			}
		}
		if ($result=="") {
			$rootObjName ="Device.NAT.PortMapping.";
			$paramArray = 
				array (
					array("Device.NAT.PortMapping.".$i.".Enable", "bool", $enportrange),
					array("Device.NAT.PortMapping.".$i.".InternalClient", "string", $ip),
					array("Device.NAT.PortMapping.".$i.".InternalPort", "uint", $priport),
					array("Device.NAT.PortMapping.".$i.".ExternalPort", "uint", $sport),
					array("Device.NAT.PortMapping.".$i.".ExternalPortEndRange", "uint", $eport),
					array("Device.NAT.PortMapping.".$i.".Protocol", "string", $type),
					array("Device.NAT.PortMapping.".$i.".Description", "string", $name),
				);
			$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
			if (!$retStatus){$result="Success!";}
		}
	}
}
if (isset($_POST['active'])){
	$validation = true;
	if($validation) $validation = isValInArray($_POST['isChecked'], array('true', 'false'));
	if($validation) $validation = validId($_POST['id']);
	if($validation) {
		//this is to enable/disable PortActive
		$isChecked=$_POST['isChecked'];
		$i=$_POST['id'];
		if (setStr("Device.NAT.PortMapping.$i.Enable",$isChecked,true) === true) {
			$result="Success!";
		}
	}
}
if (isset($_POST['del'])){
	$validation = true;
	if($validation) $validation = validId($_POST['del']);
	if($validation) delTblObj("Device.NAT.PortMapping.".$_POST['del'].".");
}
if ($result=="") {
//the set operation failure due to conflict with port trigger rules or ...
//so need to remove the '0.0.0.0' entry
$ids=explode(",",getInstanceIDs("Device.NAT.PortMapping."));
	foreach ($ids as $key=>$j) {
        if (getStr("Device.NAT.PortMapping.$j.InternalClient") == "0.0.0.0") {
        	delTblObj("Device.NAT.PortMapping.$j.");
        }
	} //end of foreach
} //end of if
header("Content-Type: application/json");
echo json_encode($result);
?>
