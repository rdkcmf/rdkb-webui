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
	$UFWDStatus=(($_POST['UFWDStatus']=="Enabled")?"true":"false");
	setStr("Device.NAT.X_Comcast_com_EnablePortMapping",$UFWDStatus,true);
	while(getStr("Device.NAT.X_Comcast_com_EnablePortMapping")!=$UFWDStatus) sleep(2);
	//$UFWDStatus=(getStr("Device.NAT.X_Comcast_com_EnablePortMapping")=="true"?"Enabled":"Disabled");
	//echo json_encode($UFWDStatus);
}

if (isset($_POST['add'])){

	$name=$_POST['name'];
	$type=$_POST['type'];
	if ($type=="TCP/UDP") $type="BOTH";
	$ip=$_POST['ip'];
	$ip6 = $_POST['ipv6addr'];
	$sport=$_POST['startport'];
	$eport=$_POST['endport'];
	
	if (getStr("Device.NAT.PortMappingNumberOfEntries")==0) {	
		addTblObj("Device.NAT.PortMapping.");
		$IDs=explode(",",getInstanceIDs("Device.NAT.PortMapping."));
		$i=$IDs[count($IDs)-1];

		$rootObjName ="Device.NAT.PortMapping.";
		$paramArray = 
			array (
				array("Device.NAT.PortMapping.".$i.".Enable", "bool", "true"),
				array("Device.NAT.PortMapping.".$i.".InternalClient", "string", $ip),
				array("Device.NAT.PortMapping.".$i.".X_CISCO_COM_InternalClientV6", "string", $ip6),
				array("Device.NAT.PortMapping.".$i.".InternalPort", "uint", "0"),
				array("Device.NAT.PortMapping.".$i.".ExternalPort", "uint", $sport),
				array("Device.NAT.PortMapping.".$i.".ExternalPortEndRange", "uint", $eport),
				array("Device.NAT.PortMapping.".$i.".Protocol", "string", $type),
				array("Device.NAT.PortMapping.".$i.".Description", "string", $name),
			);
		$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
		if (!$retStatus){$result="Success!";}
	} 
	else {
		// $result="";
		$ids=explode(",",getInstanceIDs("Device.NAT.PortMapping."));
		foreach ($ids as $key=>$j) {
			if (getStr("Device.NAT.PortMapping.".$j.".LeaseDuration")==0 && getStr("Device.NAT.PortMapping.".$j.".InternalPort")==0){
				$arrayName  = getStr("Device.NAT.PortMapping.".$j.".Description");
				$arrayIP    = getStr("Device.NAT.PortMapping.".$j.".InternalClient");
				$arrayType  = getStr("Device.NAT.PortMapping.".$j.".Protocol");
				$arraySPort = getStr("Device.NAT.PortMapping.".$j.".ExternalPort");
				$arrayEPort = getStr("Device.NAT.PortMapping.".$j.".ExternalPortEndRange");
				if($name==$arrayName) { 
					$result.="Service name has been used!\n";
					break;
				} 
				else if($type=="BOTH"||$arrayType=="BOTH"||$type==$arrayType){
					$porttest=PORTTEST($sport,$eport,$arraySPort,$arrayEPort);
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
					array("Device.NAT.PortMapping.".$i.".Enable", "bool", "true"),
					array("Device.NAT.PortMapping.".$i.".InternalClient", "string", $ip),
					array("Device.NAT.PortMapping.".$i.".X_CISCO_COM_InternalClientV6", "string", $ip6),
					array("Device.NAT.PortMapping.".$i.".InternalPort", "uint", "0"),
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

if (isset($_POST['edit'])){
	$i=$_POST['ID'];
	$name=$_POST['name'];
	$type=$_POST['type'];
	if ($type=="TCP/UDP") $type="BOTH";
	$ip=$_POST['ip'];
	$ip6 = $_POST['ipv6addr'];
	$sport=$_POST['startport'];
	$eport=$_POST['endport'];
	
	// $result="";
	$ids=explode(",",getInstanceIDs("Device.NAT.PortMapping."));
	foreach ($ids as $key=>$j) {
		if ($i==$j) continue;
		if (getStr("Device.NAT.PortMapping.".$j.".LeaseDuration")==0 && getStr("Device.NAT.PortMapping.".$j.".InternalPort")==0){
			$arrayName  = getStr("Device.NAT.PortMapping.".$j.".Description");
			$arrayIP    = getStr("Device.NAT.PortMapping.".$j.".InternalClient");
			$arrayType  = getStr("Device.NAT.PortMapping.".$j.".Protocol");
			$arraySPort = getStr("Device.NAT.PortMapping.".$j.".ExternalPort");
			$arrayEPort = getStr("Device.NAT.PortMapping.".$j.".ExternalPortEndRange");
			if($name==$arrayName) { 
				$result.="Service name has been used!\n";
				break;
			}
			else if($type=="BOTH"||$arrayType=="BOTH"||$type==$arrayType){
				$porttest=PORTTEST($sport,$eport,$arraySPort,$arrayEPort);
				if ($porttest==1) {
					$result.="Conflict with other service. Please check port and IP!";
					break;
				}
			}
		}
	}

	if ($result=="") {
		$rootObjName ="Device.NAT.PortMapping.";
		$paramArray = 
			array (
				array("Device.NAT.PortMapping.".$i.".Enable", "bool", "true"),
				array("Device.NAT.PortMapping.".$i.".InternalClient", "string", $ip),
				array("Device.NAT.PortMapping.".$i.".X_CISCO_COM_InternalClientV6", "string", $ip6),
				array("Device.NAT.PortMapping.".$i.".InternalPort", "uint", "0"),
				array("Device.NAT.PortMapping.".$i.".ExternalPort", "uint", $sport),
				array("Device.NAT.PortMapping.".$i.".ExternalPortEndRange", "uint", $eport),
				array("Device.NAT.PortMapping.".$i.".Protocol", "string", $type),
				array("Device.NAT.PortMapping.".$i.".Description", "string", $name),
			);
		$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
		if (!$retStatus){$result="Success!";}
	}
}


if (isset($_POST['active'])){
	$isChecked=$_POST['isChecked'];
	$i=$_POST['id'];
	if (setStr("Device.NAT.PortMapping.$i.Enable",$isChecked,true) === true) {
		
		$result="Success!";
	}
}

if (isset($_REQUEST['del'])){
	delTblObj("Device.NAT.PortMapping.".$_REQUEST['del'].".");
	
	Header("Location:../port_forwarding.php");
	
	exit;
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

echo json_encode($result);

?>
