<?php

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
	$UMSStatus=(($_POST['UMSStatus']=="Enabled")?"true":"false");
	setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Enable",$UMSStatus,true);
	$UMSStatus=getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Enable");
	$UMSStatus=($UMSStatus=="true")?"Enabled":"Disabled";
	header("Content-Type: application/json");
	echo json_encode($UMSStatus);
//	echo json_encode("Disabled");
}

if (isset($_POST['trust_not'])){
	$ID=$_POST['ID'];
	setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.".$ID.".Trusted",$_POST['status'],true);
	$status=getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.".$ID.".Trusted");
	$status=($status=="true")?"Trusted":"Not trusted";
	header("Content-Type: application/json");
	echo json_encode($status);
//	echo json_encode("Disabled");
}

if (isset($_POST['add'])){

	$service=$_POST['service'];
	$protocol=$_POST['protocol'];
	$startPort=$_POST['startPort'];
	$endPort=$_POST['endPort'];
	$block=$_POST['block'];

	if ($protocol == "TCP/UDP") 
		$type = "BOTH";
	else
		$type = $protocol;
	
	$ids=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
	if (count($ids)==0) {	//no table, need test whether it equals 0
		addTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
		$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
		$i=$IDs[count($IDs)-1];
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Description",$service,false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Protocol",$protocol,false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartPort",$startPort,false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndPort",$endPort,false);
		if($block == "false") {
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartTime",$_POST['startTime'],false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndTime",$_POST['endTime'],false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".BlockDays",$_POST['days'],false);
		}
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".AlwaysBlock",$block,true);
		header("Content-Type: application/json");
		echo json_encode("Success!");
	} 
	else {
		$result="";
		foreach ($ids as $key=>$j) {
			$serviceName = getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.$j.Description");
			$stport = getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.$j.StartPort");
			$edport = getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.$j.EndPort");
			$ptcol_type = getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.$j.Protocol");

			if ($service == $serviceName) {
				$result .= "Service Name has been used!\n";
				break;
			}			
			elseif ($type=="BOTH" || $ptcol_type=="BOTH" || $type==$ptcol_type) {
				$porttest = PORTTEST($startPort, $endPort, $stport, $edport);
				if ($porttest == 1) {
					$result .= "Conflict with other service. Please check your input!";
					break;
				}
			}
		}
		
		if ($result=="") {
			addTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
			$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
			$i=$IDs[count($IDs)-1];
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Description",$service,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Protocol",$protocol,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartPort",$startPort,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndPort",$endPort,false);
			if($block == "false") {
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartTime",$_POST['startTime'],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndTime",$_POST['endTime'],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".BlockDays",$_POST['days'],false);
			}
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".AlwaysBlock",$block,true);
			$result="Success!";
		}
		header("Content-Type: application/json");
		echo json_encode($result);
	}
}

if (isset($_POST['edit'])){
	$i=$_POST['ID'];
	$service=$_POST['service'];
	$protocol=$_POST['protocol'];
	$startPort=$_POST['startPort'];
	$endPort=$_POST['endPort'];
	$block=$_POST['block'];

	if ($protocol == "TCP/UDP") 
		$type = "BOTH";
	else
		$type = $protocol;
	
	$ids=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
	$result="";
	foreach ($ids as $key=>$j) {
		if ($i==$j) continue;
		
		$serviceName = getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.$j.Description");
		$stport = getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.$j.StartPort");
		$edport = getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.$j.EndPort");
		$ptcol_type = getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.$j.Protocol");

		if ($service == $serviceName) {
			$result .= "Service Name has been used!\n";
			break;
		}			
		elseif ($type=="BOTH" || $ptcol_type=="BOTH" || $type==$ptcol_type) {
			$porttest = PORTTEST($startPort, $endPort, $stport, $edport);
			if ($porttest == 1) {
				$result .= "Conflict with other service. Please check your input!";
				break;
			}
		}
	}
	
	if ($result=="") {
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Description",$service,false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Protocol",$protocol,false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartPort",$startPort,false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndPort",$endPort,false);
		if($block == "false") {
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartTime",$_POST['startTime'],false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndTime",$_POST['endTime'],false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".BlockDays",$_POST['days'],false);
		}
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".AlwaysBlock",$block,true);
		$result="Success!";
	}
	header("Content-Type: application/json");
	echo json_encode($result);
}

if (isset($_GET['del'])){
	delTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$_GET['del'].".");
	Header("Location:../managed_services.php");
	exit;
}
?>
