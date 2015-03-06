<?php

$result="";

function PORTTEST($sp,$ep,$arraySp,$arrayEp){

	if ( $sp>=$arraySp && $sp<=$arrayEp ) return 1;
	else if ( $ep>=$arraySp && $ep<=$arrayEp ) return 1;
	else if ( $sp<$arraySp && $ep>$arrayEp ) return 1;
	else return 0;

}

if (isset($_POST['set'])){
	$UPTRStatus=(($_POST['UPTRStatus']=="Enabled")?"true":"false");
	setStr("Device.NAT.X_CISCO_COM_PortTriggers.Enable",$UPTRStatus,true);
	while(getStr("Device.NAT.X_CISCO_COM_PortTriggers.Enable")!=$UPTRStatus) sleep(2);
	//$UPTRStatus=((getStr("Device.NAT.X_CISCO_COM_PortTriggers.Enable")=="true")?"Enabled":"Disabled");
	//echo json_encode($UPTRStatus);
}

if (isset($_POST['add'])){
	
	$name=$_POST['name'];
	$type=$_POST['type'];
	if ($type=="TCP/UDP") $type="BOTH";
	$fsp=$_POST['fsp'];
	$fep=$_POST['fep'];
	$tsp=$_POST['tsp'];
	$tep=$_POST['tep'];
	
	if (getStr("Device.NAT.X_CISCO_COM_PortTriggers.TriggerNumberOfEntries")==0) { //need to test
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
		if (!$retStatus){$result="Success!";}
		
		// echo json_encode("Success!");
	} else {
		// $result="";
		$ids=explode(",",getInstanceIDs("Device.NAT.X_CISCO_COM_PortTriggers.Trigger."));
		foreach ($ids as $key=>$j) {
			$arrayName=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".Description");
			$arrayType=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".TriggerProtocol");
			$arrayFsp=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".TriggerPortStart");
			$arrayFep=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".TriggerPortEnd");
			$arrayTsp=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".ForwardPortStart");
			$arratTep=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".ForwardPortEnd");
			if(!strcmp($name, $arrayName)) {
				$result.="Service name has been used!\n";
				break;
			} else if($type=="BOTH"||$arrayType=="BOTH"||$type==$arrayType){
				$fptest=PORTTEST($fsp,$fep,$arrayFsp,$arrayFep);
				$tptest=PORTTEST($tsp,$tep,$arrayTsp,$arratTep);
				if ($fptest==1 || $tptest==1) {
					$result.="Conflict with other service. Please check Trigger and Target Ports!";
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
							$result.="Conflict with other service. Please check port and IP!";
							break;
						}
					}
				}
			} //end of foreach		
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
			if (!$retStatus){$result="Success!";}
		}
		// echo json_encode($result);
	}
}

if (isset($_POST['edit'])){
	$i=$_POST['ID'];
	$name=$_POST['name'];
	$type=$_POST['type'];
	if ($type=="TCP/UDP") $type="BOTH";
	$fsp=$_POST['fsp'];
	$fep=$_POST['fep'];
	$tsp=$_POST['tsp'];
	$tep=$_POST['tep'];
	
	$results="";
	$ids=explode(",",getInstanceIDs("Device.NAT.X_CISCO_COM_PortTriggers.Trigger."));
	foreach ($ids as $key=>$j) {
		if ($i==$j) continue;
		$arrayName=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".Description");
		$arrayType=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".TriggerProtocol");
		$arrayFsp=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".TriggerPortStart");
		$arrayFep=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".TriggerPortEnd");
		$arrayTsp=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".ForwardPortStart");
		$arratTep=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$j.".ForwardPortEnd");
		if(!strcmp($name, $arrayName)) {
			$result.="Service name has been used!\n";
			break;
		} else if($type=="BOTH"||$arrayType=="BOTH"||$type==$arrayType){
			$fptest=PORTTEST($fsp,$fep,$arrayFsp,$arrayFep);
			$tptest=PORTTEST($tsp,$tep,$arrayTsp,$arratTep);
			if ($fptest==1 || $tptest==1) {
				$result.="Conflict with other service. Please check Trigger and Target Ports!";
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
						$result.="Conflict with other service. Please check port and IP!";
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
		if (!$retStatus){$result="Success!";}
	}
	// echo json_encode($result);
}

if (isset($_POST['active'])){
	$isChecked=$_POST['isChecked'];
	$i=$_POST['id'];
	setStr("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Enable",$isChecked,true);
}

if (isset($_GET['del'])){
	delTblObj("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$_GET['del'].".");
	Header("Location:../port_triggering.php");
	exit;
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

echo json_encode($result);

?>