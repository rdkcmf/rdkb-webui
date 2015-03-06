<?php

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
	$UHSPStatus=(($_POST['UHSPStatus']=="Enabled")?"true":"false");
	setStr("Device.NAT.X_Comcast_com_EnableHSPortMapping",$UHSPStatus,true);
	while(getStr("Device.NAT.X_Comcast_com_EnableHSPortMapping")!=$UHSPStatus) sleep(2);
	// $UHSPStatus=(getStr("Device.NAT.X_Comcast_com_EnableHSPortMapping")=="true"?"Enabled":"Disabled");
	// echo json_encode($UHSPStatus);
}

if (isset($_POST['add'])) {

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
		$ids=explode(",",getInstanceIDs("Device.NAT.PortMapping."));
		foreach ($ids as $key=>$j) {
			if (getStr("Device.NAT.PortMapping.".$j.".LeaseDuration")==0 && getStr("Device.NAT.PortMapping.".$j.".InternalPort")!=0){
				$arrayName=getStr("Device.NAT.PortMapping.".$j.".Description");
				$arrayIP=getStr("Device.NAT.PortMapping.".$j.".InternalClient");
				$arrayType=getStr("Device.NAT.PortMapping.".$j.".Protocol");
				$arraySPort=getStr("Device.NAT.PortMapping.".$j.".ExternalPort");
				$arrayEPort=getStr("Device.NAT.PortMapping.".$j.".ExternalPortEndRange");
				$arrayPPort=getStr("Device.NAT.PortMapping.".$j.".InternalPort");
				if($name==$arrayName) { 
					$result.="Service name has been used!\n";
					break;
				} else if($type=="BOTH"||$arrayType=="BOTH"||$type==$arrayType){
					$porttest=PORTTEST($startport,$endport,$arraySPort,$arrayEPort);
					if ($porttest==1) {
						$result.="Conflict with other service. Please check port and IP!";
						break;
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

if (isset($_POST['active'])){
	//this is to enable/disable PortActive
	$isChecked=$_POST['isChecked'];
	$i=$_POST['id'];
	setStr("Device.NAT.PortMapping.".$i.".Enable",$isChecked,true);
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