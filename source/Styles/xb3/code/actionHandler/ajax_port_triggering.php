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

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
$result="";
function PORTTEST($sp,$ep,$arraySp,$arrayEp){
	if ( $sp>=$arraySp && $sp<=$arrayEp ) return 1;
	else if ( $ep>=$arraySp && $ep<=$arrayEp ) return 1;
	else if ( $sp<$arraySp && $ep>$arrayEp ) return 1;
	else return 0;
}
if (isset($_POST['set'])){
	if (isValInArray($_POST['UPTRStatus'], array("Enabled", "Disabled"))){
		$UPTRStatus=(($_POST['UPTRStatus']=="Enabled")?"true":"false");
		setStr("Device.NAT.X_CISCO_COM_PortTriggers.Enable",$UPTRStatus,true);
		while(getStr("Device.NAT.X_CISCO_COM_PortTriggers.Enable")!=$UPTRStatus) sleep(2);
		//$UPTRStatus=((getStr("Device.NAT.X_CISCO_COM_PortTriggers.Enable")=="true")?"Enabled":"Disabled");
		//echo json_encode($UPTRStatus);
	}
}
if (isset($_POST['add'])){
	$validation = true;
	if($validation) $validation = printableCharacters($_POST['name']);
	if($validation) $validation = is_allowed_string($_POST['name']);
	if($validation) $validation = isValInArray($_POST['type'], array('TCP', 'UDP', 'TCP/UDP'));
	if($validation) $validation = validPort($_POST['fsp']);
	if($validation) $validation = validPort($_POST['fep']);
	if($validation) $validation = validPort($_POST['tsp']);
	if($validation) $validation = validPort($_POST['tep']);
	if($validation) {
		$name=$_POST['name'];
		$type=$_POST['type'];
		if ($type=="TCP/UDP") $type="BOTH";
		$fsp=$_POST['fsp'];
		$fep=$_POST['fep'];
		$tsp=$_POST['tsp'];
		$tep=$_POST['tep'];
		if (getStr("Device.NAT.X_CISCO_COM_PortTriggers.TriggerNumberOfEntries")==0) {
                  
			addTblObj("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.");
			$IDs=explode(",",getInstanceIDs("Device.NAT.X_CISCO_COM_PortTriggers.Trigger."));
			$i=$IDs[count($IDs)-1];
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortStart",$fsp,false);//from start port
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortEnd",$fep,false);
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerProtocol",$type,false);
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardProtocol",$type,false);//need to ask wu
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortStart",$tsp,false);//to start port
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortEnd",$tep,false);
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Description",$name,false);
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Enable","true",true);
			$rootObjName ="Device.NAT.X_CISCO_COM_PortTriggers.Trigger.";
			$paramArray =
				array (
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortStart", "uint",   $fsp),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortEnd",   "uint",   $fep),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerProtocol",  "string", $type),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardProtocol",  "string", $type),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortStart", "uint",   $tsp),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortEnd",   "uint",   $tep),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Description",      "string", $name),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Enable",           "bool",   "true"),
				);
			$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);
			if (!$retStatus){$result=_("Success!");}
			// echo json_encode("Success!");
		} else {
			// $result="";
			$rootObjName    = "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.";
			$paramNameArray = array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.");
			$mapping_array  = array("Description", "TriggerProtocol", "TriggerPortStart", "TriggerPortEnd", "ForwardPortStart", "ForwardPortEnd");
			$portTriggerValues = getParaValues($rootObjName, $paramNameArray, $mapping_array);
			//$ids=explode(",",getInstanceIDs("Device.NAT.X_CISCO_COM_PortTriggers.Trigger."));
			foreach ($portTriggerValues as $key) {
				$arrayName = $key["Description"];
				$arrayType = $key["TriggerProtocol"];
				$arrayFsp = $key["TriggerPortStart"];
				$arrayFep = $key["TriggerPortEnd"];
				$arrayTsp = $key["ForwardPortStart"];
				$arratTep = $key["ForwardPortEnd"];
				if(!strcasecmp($name, $arrayName)) {
					$result.=_("Service name has been used!\n");
					break;
				} else if($type=="BOTH"||$arrayType=="BOTH"||$type==$arrayType){
					$fptest=PORTTEST($fsp,$fep,$arrayFsp,$arrayFep);
					$tptest=PORTTEST($tsp,$tep,$arrayTsp,$arratTep);
					if ($fptest==1 || $tptest==1) {
						$result.=_("Conflict with other service. Please check Trigger and Target Ports!");
						break;
					}
				}
			}
                  
			if ($result=="") {
				addTblObj("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.");
				$IDs=explode(",",getInstanceIDs("Device.NAT.X_CISCO_COM_PortTriggers.Trigger."));
				$i=$IDs[count($IDs)-1];
				// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortStart",$fsp,false);//from start port
				// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortEnd",$fep,false);
				// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerProtocol",$type,false);
				// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardProtocol",$type,false);//need to ask wu
				// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortStart",$tsp,false);//to start port
				// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortEnd",$tep,false);
				// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Description",$name,false);
				// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Enable","true",true);
				// $result="Success!";
				$rootObjName ="Device.NAT.X_CISCO_COM_PortTriggers.Trigger.";
				$paramArray = 
					array (
						array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortStart", "uint",   $fsp),
						array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortEnd",   "uint",   $fep),
						array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerProtocol",  "string", $type),
						array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardProtocol",  "string", $type),
						array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortStart", "uint",   $tsp),
						array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortEnd",   "uint",   $tep),
						array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Description",      "string", $name),
						array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Enable",           "bool",   "true"),
					);
				$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
				if (!$retStatus){$result=_("Success!");}
			}
			// echo json_encode($result);
                     if ($result=="") {
			/*
			* this piece of code is going to check forward start port and end port not overlapped with port forwarding entry
			*/
			$ids=explode(",",getInstanceIDs("Device.NAT.PortMapping."));
			for(var $key in $ids) { var $j=$ids[$key];/*p2j-foreach*/
				if (getStr("Device.NAT.PortMapping."+$j+".LeaseDuration")==0 && getStr("Device.NAT.PortMapping."+$j+".InternalPort")==0){
					$portMappingType=getStr("Device.NAT.PortMapping."+$j+".Protocol");
					$arraySPort=getStr("Device.NAT.PortMapping."+$j+".ExternalPort");
					$arrayEPort=getStr("Device.NAT.PortMapping."+$j+".ExternalPortEndRange");
					if($type=="BOTH" || $portMappingType=="BOTH" || $type==$portMappingType){
						$porttest=PORTTEST($tsp,$tep,$arraySPort,$arrayEPort);
						if ($porttest==1) {
							$result+=_("Failure! As Port Triggering/Port Forwarding rule exists for the same port.");
							break;
						}
					}
				}
			} //end of foreach		
		    }  
		}

	}
}
if (isset($_POST['edit'])){
	$validation = true;
	if($validation) $validation = validId($_POST['ID']);
	if($validation) $validation = printableCharacters($_POST['name']);
	if($validation) $validation = is_allowed_string($_POST['name']);
	if($validation) $validation = isValInArray($_POST['type'], array('TCP', 'UDP', 'TCP/UDP'));
	if($validation) $validation = validPort($_POST['fsp']);
	if($validation) $validation = validPort($_POST['fep']);
	if($validation) $validation = validPort($_POST['tsp']);
	if($validation) $validation = validPort($_POST['tep']);
	if($validation) {
		$i=$_POST['ID'];
		$name=$_POST['name'];
		$type=$_POST['type'];
		if ($type=="TCP/UDP") $type="BOTH";
		$fsp=$_POST['fsp'];
		$fep=$_POST['fep'];
		$tsp=$_POST['tsp'];
		$tep=$_POST['tep'];
		$results="";
		//$ids=explode(",",getInstanceIDs("Device.NAT.X_CISCO_COM_PortTriggers.Trigger."));
		$rootObjName    = "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.";
			$paramNameArray = array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.");
			$mapping_array  = array("Description", "TriggerProtocol", "TriggerPortStart", "TriggerPortEnd", "ForwardPortStart", "ForwardPortEnd");
			$portTriggerValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
		foreach ($portTriggerValues as $key) {
			$j = $key["__id"];
			if ($i==$j) continue;
			$arrayName = $key["Description"];
				$arrayType = $key["TriggerProtocol"];
				$arrayFsp = $key["TriggerPortStart"];
				$arrayFep = $key["TriggerPortEnd"];
				$arrayTsp = $key["ForwardPortStart"];
				$arratTep = $key["ForwardPortEnd"];
			if(!strcasecmp($name, $arrayName)) {
				$result.=_("Service name has been used!\n");
				break;
			} else if($type=="BOTH"||$arrayType=="BOTH"||$type==$arrayType){
				$fptest=PORTTEST($fsp,$fep,$arrayFsp,$arrayFep);
				$tptest=PORTTEST($tsp,$tep,$arrayTsp,$arratTep);
				if ($fptest==1 || $tptest==1) {
					$result.=_("Conflict with other service. Please check Trigger and Target Ports!");
					break;
				}
			}
		}

	    if ($result=="") {
			/*
			* this piece of code is going to check forward start port and end port not overlapped with port forwarding entry
			*/
			$ids=explode(",",getInstanceIDs("Device.NAT.PortMapping."));
			foreach ($ids as $key=>$j) {
				if (getStr("Device.NAT.PortMapping.".$j.".LeaseDuration")==0 && getStr("Device.NAT.PortMapping.".$j.".InternalPort")==0){
				$portMappingType=getStr("Device.NAT.PortMapping.".$j.".Protocol");
					$arraySPort=getStr("Device.NAT.PortMapping.".$j.".ExternalPort");
					$arrayEPort=getStr("Device.NAT.PortMapping.".$j.".ExternalPortEndRange");
					if($type=="BOTH" || $portMappingType=="BOTH" || $type==$portMappingType){
						$porttest=PORTTEST($tsp,$tep,$arraySPort,$arrayEPort);
						if ($porttest==1) {
							$result.=_("Failure! As Port Triggering/Port Forwarding rule exists for the same port.");
							break;
						}
					}
				}
			} //end of foreach		
		}


		if ($result=="") {
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortStart",$fsp,false);//from start port
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortEnd",$fep,false);
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerProtocol",$type,false);
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardProtocol",$type,false);//need to ask wu
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortStart",$tsp,false);//to start port
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortEnd",$tep,false);
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Description",$name,false);
			// setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Enable","true",true);
			// $result="Success!";
			$rootObjName ="Device.NAT.X_CISCO_COM_PortTriggers.Trigger.";
			$paramArray = 
				array (
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortStart", "uint",   $fsp),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortEnd",   "uint",   $fep),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerProtocol",  "string", $type),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardProtocol",  "string", $type),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortStart", "uint",   $tsp),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortEnd",   "uint",   $tep),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Description",      "string", $name),
					array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Enable",           "bool",   "true"),
				);
			$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
			if (!$retStatus){$result=_("Success!");}
		}

		// echo json_encode($result);
	}
}
if (isset($_POST['active'])){
	$validation = true;
	if($validation) $validation = isValInArray($_POST['isChecked'], array('true', 'false'));
	if($validation) $validation = validId($_POST['id']);
	if($validation) {
		$isChecked=$_POST['isChecked'];
		$i=$_POST['id'];
		setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Enable",$isChecked,true);
	}
}
if (isset($_POST['del'])){
	$validation = true;
	if($validation) $validation = validId($_POST['del']);
	if($validation) delTblObj("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$_POST['del'].".");
}
if ($result=="") { 
//the set operation failure due to conflict with port forwarding rules or ...
//so need to remove the '0~0,0~0' entry
$ids=explode(",",getInstanceIDs("Device.NAT.X_CISCO_COM_PortTriggers.Trigger."));
	foreach ($ids as $key=>$j) {
		$tport_start = getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.$j.TriggerPortStart");
		$fport_start = getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.$j.ForwardPortStart");
        if ( ($tport_start == 0) && ($tport_start == $fport_start) ) {
        	delTblObj("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.$j.");
        }
	} //end of foreach
} //end of if
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
?>
