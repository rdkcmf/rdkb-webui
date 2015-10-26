<!--
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2015 RDK Management

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
-->
﻿<?php include('../includes/utility.php') ?>
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

function time_date_conflict($TD1, $TD2) {
	$ret = false;
	$days1 = explode(",", $TD1[2]);
	$days2 = explode(",", $TD2[2]);

	foreach ($days1 as &$value) {
		if (in_array($value, $days2)) {
			//deMorgan's law - to find if ranges are overlapping
			//(StartA <= EndB)  and  (EndA >= StartB)
			if((strtotime($TD1[0]) < strtotime($TD2[1])) and (strtotime($TD1[1]) > strtotime($TD2[0]))){
	  			$ret = true;
	  			break;
			} 
		}
	}
	return $ret;
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
	$startTime=$_POST['startTime'];
	$endTime=$_POST['endTime'];
	$blockDays=$_POST['days'];

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
		$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.";
		$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
		$mapping_array  = array("Description", "StartPort", "EndPort", "Protocol", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");

		$managedServicesValues = getParaValues($rootObjName, $paramNameArray, $mapping_array);

		foreach ($managedServicesValues as $key) {

			$serviceName = $key["Description"];
			$stport = $key["StartPort"];
			$edport = $key["EndPort"];
			$ptcol_type = $key["Protocol"];
			$always_Block = $key["AlwaysBlock"];
			$start_Time = $key["StartTime"];
			$end_Time = $key["EndTime"];
			$block_Days = $key["BlockDays"];

			if ($service == $serviceName) {
				$result .= "Service Name has been used!\n";
				break;
			}			
			elseif ($type=="BOTH" || $ptcol_type=="BOTH" || $type==$ptcol_type) {
				$porttest = PORTTEST($startPort, $endPort, $stport, $edport);
				if ($porttest == 1) {
					//Check for time and day conflicts
					$TD1=array($startTime, $endTime, $blockDays);
					$TD2=array($start_Time, $end_Time, $block_Days);
					if(($always_Block == "true") || ($block == "true") || time_date_conflict($TD1, $TD2)){
						$result .= "Conflict with other service. Please check your input!";
						break;
					}
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
	$startTime=$_POST['startTime'];
	$endTime=$_POST['endTime'];
	$blockDays=$_POST['days'];

	if ($protocol == "TCP/UDP") 
		$type = "BOTH";
	else
		$type = $protocol;
	
	$ids=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
	$result="";
	$result="";
	$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.";
	$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
	$mapping_array  = array("Description", "StartPort", "EndPort", "Protocol", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");

	$managedServicesValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
	foreach ($managedServicesValues as $key) {
		$j = $key["__id"];
		if ($i==$j) continue;
		$serviceName = $key["Description"];
		$stport = $key["StartPort"];
		$edport = $key["EndPort"];
		$ptcol_type = $key["Protocol"];
		$always_Block = $key["AlwaysBlock"];
		$start_Time = $key["StartTime"];
		$end_Time = $key["EndTime"];
		$block_Days = $key["BlockDays"];

		if ($service == $serviceName) {
			$result .= "Service Name has been used!\n";
			break;
		}			
		elseif ($type=="BOTH" || $ptcol_type=="BOTH" || $type==$ptcol_type) {
			$porttest = PORTTEST($startPort, $endPort, $stport, $edport);
			if ($porttest == 1) {
				//Check for time and day conflicts
				$TD1=array($startTime, $endTime, $blockDays);
				$TD2=array($start_Time, $end_Time, $block_Days);
				if(($always_Block == "true") || ($block == "true") || time_date_conflict($TD1, $TD2)){
					$result .= "Conflict with other service. Please check your input!";
					break;
				}
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
