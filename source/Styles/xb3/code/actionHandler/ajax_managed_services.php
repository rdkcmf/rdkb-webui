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
	$validation = true;
	if($validation) $validation = isValInArray($_POST['UMSStatus'], array('Enabled', 'Disabled'));
	if($validation) {
		$UMSStatus=(($_POST['UMSStatus']=="Enabled")?"true":"false");
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Enable",$UMSStatus,true);
	}
	$UMSStatus=getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Enable");
	$UMSStatus=($UMSStatus=="true")?"Enabled":"Disabled";
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($UMSStatus), ENT_NOQUOTES, 'UTF-8');
//	echo json_encode("Disabled");
}
if (isset($_POST['trust_not'])){
	$validation = true;
	if($validation) $validation = validId_PC($_POST['ID']);
	if($validation) $validation = isValInArray($_POST['status'], array('true', 'false'));
	if($validation) {
		$ID=$_POST['ID'];
		setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.".$ID.".Trusted",$_POST['status'],true);
		$status=getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.".$ID.".Trusted");
		$status=($status=="true")?_("Trusted"):_("Not trusted");
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($status), ENT_NOQUOTES, 'UTF-8');
//	echo json_encode("Disabled");
}
if (isset($_POST['add'])){
	$validation = true;
	if($validation) $validation = noSpace($_POST['service']);
	if($validation) $validation = !empty($_POST['service']);
	if($validation) $validation = printableCharacters($_POST['service']);
	if($validation) $validation = is_allowed_string($_POST['service']);
	if($validation) $validation = isValInArray($_POST['protocol'], array('TCP', 'UDP', 'BOTH'));
	if($validation) $validation = validPort($_POST['startPort']);
	if($validation) $validation = validPort($_POST['endPort']);
	if($validation) $validation = ($_POST['startPort'] <= $_POST['endPort']);
	if($validation) $validation = isValInArray($_POST['block'], array('true', 'false'));
	if($validation && $_POST['block'] == 'false'){
		if($validation) $validation = validTime($_POST['startTime'], $_POST['endTime']);
		if($validation) $validation = validDays($_POST['days']);
	}
	$result = ($validation)?'':_('Invalid Inputs!');
	if($validation) {
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
			if ($UTC_local_Time_conversion) $timeData = days_time_conversion_set($startTime, $endTime, $blockDays);
			else $timeData = array($startTime, $endTime, $blockDays, false);
			//for the first rule
			addTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
			$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
			$i=$IDs[count($IDs)-1];
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Description",$service,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Protocol",$protocol,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartPort",$startPort,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndPort",$endPort,false);
			if($block == "false") {
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartTime",$timeData[0],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndTime",$timeData[1],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".BlockDays",$timeData[2],false);
			}
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".AlwaysBlock",$block,true);
			if($timeData[3]){
				//for the second rule
				addTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
				$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
				$i=$IDs[count($IDs)-1];
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Description",$service,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Protocol",$protocol,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartPort",$startPort,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndPort",$endPort,false);
				if($block == "false") {
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartTime",$timeData[4],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndTime",$timeData[5],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".BlockDays",$timeData[6],false);
				}
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".AlwaysBlock",$block,true);
			}
			$result=_("Success!");
		}
		else {
			$result="";
			$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.";
			$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
			$mapping_array  = array("Description", "StartPort", "EndPort", "Protocol", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");
			$managedServicesValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
			if($UTC_local_Time_conversion) $managedServicesValues = days_time_conversion_get($managedServicesValues, 'Description');
			foreach ($managedServicesValues as $key) {
				$serviceName = $key["Description"];
				$stport = $key["StartPort"];
				$edport = $key["EndPort"];
				$ptcol_type = $key["Protocol"];
				$always_Block = $key["AlwaysBlock"];
				$start_Time = $key["StartTime"];
				$end_Time = $key["EndTime"];
				$block_Days = $key["BlockDays"];
				if (strcasecmp($service,$serviceName) == 0) {
					$result .= _("Service Name has been used!\n");
					break;
				}			
				elseif ($type=="BOTH" || $ptcol_type=="BOTH" || $type==$ptcol_type) {
					$porttest = PORTTEST($startPort, $endPort, $stport, $edport);
					if ($porttest == 1) {
						//Check for time and day conflicts
						$TD1=array($startTime, $endTime, $blockDays);
						$TD2=array($start_Time, $end_Time, $block_Days);
						if(($always_Block == "true") || ($block == "true") || time_date_conflict($TD1, $TD2)){
							$result .= _("Conflict with other service. Please check your input!");
							break;
						}
					}
				}
			}
			if ($result=="") {
				if ($UTC_local_Time_conversion) $timeData = days_time_conversion_set($startTime, $endTime, $blockDays);
				else $timeData = array($startTime, $endTime, $blockDays, false);
				//for the first rule
				addTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
				$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
				$i=$IDs[count($IDs)-1];
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Description",$service,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Protocol",$protocol,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartPort",$startPort,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndPort",$endPort,false);
				if($block == "false") {
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartTime",$timeData[0],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndTime",$timeData[1],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".BlockDays",$timeData[2],false);
				}
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".AlwaysBlock",$block,true);
				if($timeData[3]){
					//for the second rule
					addTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
					$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
					$i=$IDs[count($IDs)-1];
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Description",$service,false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Protocol",$protocol,false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartPort",$startPort,false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndPort",$endPort,false);
					if($block == "false") {
						setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartTime",$timeData[4],false);
						setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndTime",$timeData[5],false);
						setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".BlockDays",$timeData[6],false);
					}
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".AlwaysBlock",$block,true);
				}
				$result = _("Success!");
			}
		}
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
}
if (isset($_POST['edit'])){
	$validation = true;
	if($validation) $validation = validId_PC($_POST['ID']);
	if($validation) $validation = noSpace($_POST['service']);
	if($validation) $validation = !empty($_POST['service']);
	if($validation) $validation = printableCharacters($_POST['service']);
	if($validation) $validation = is_allowed_string($_POST['service']);
	if($validation) $validation = isValInArray($_POST['protocol'], array('TCP', 'UDP', 'BOTH'));
	if($validation) $validation = validPort($_POST['startPort']);
	if($validation) $validation = validPort($_POST['endPort']);
	if($validation) $validation = ($_POST['startPort'] <= $_POST['endPort']);
	if($validation) $validation = isValInArray($_POST['block'], array('true', 'false'));
	if($validation && $_POST['block'] == 'false'){
		if($validation) $validation = validTime($_POST['startTime'], $_POST['endTime']);
		if($validation) $validation = validDays($_POST['days']);
	}
	$result = ($validation)?'':_('Invalid Inputs!');
	if($validation) {
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
		$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.";
		$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
		$mapping_array  = array("Description", "StartPort", "EndPort", "Protocol", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");
		$managedServicesValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
		if($UTC_local_Time_conversion) $managedServicesValues = days_time_conversion_get($managedServicesValues, 'Description');
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
			if (strcasecmp($service,$serviceName) == 0) {
				$result .= _("Service Name has been used!\n");
				break;
			}			
			elseif ($type=="BOTH" || $ptcol_type=="BOTH" || $type==$ptcol_type) {
				$porttest = PORTTEST($startPort, $endPort, $stport, $edport);
				if ($porttest == 1) {
					//Check for time and day conflicts
					$TD1=array($startTime, $endTime, $blockDays);
					$TD2=array($start_Time, $end_Time, $block_Days);
					if(($always_Block == "true") || ($block == "true") || time_date_conflict($TD1, $TD2)){
						$result .= _("Conflict with other service. Please check your input!");
						break;
					}
				}
			}
		}
		$i = explode('_', $i);
		if ($result=="") {
			if ($UTC_local_Time_conversion) $timeData = days_time_conversion_set($startTime, $endTime, $blockDays);
			else $timeData = array($startTime, $endTime, $blockDays, false);
			//for the first rule
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[0].".Description",$service,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[0].".Protocol",$protocol,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[0].".StartPort",$startPort,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[0].".EndPort",$endPort,false);
			if($block == "false") {
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[0].".StartTime",$timeData[0],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[0].".EndTime",$timeData[1],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[0].".BlockDays",$timeData[2],false);
			}
			setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[0].".AlwaysBlock",$block,true);
			if(($block == "true") && array_key_exists(1, $i)){
				delTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[1].".");
			}
			if($timeData[3]){
				//for the second rule
				if(!array_key_exists(1, $i)){
					addTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
					$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
					$index=$IDs[count($IDs)-1];
				}
				else $index = $i[1];
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$index.".Description",$service,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$index.".Protocol",$protocol,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$index.".StartPort",$startPort,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$index.".EndPort",$endPort,false);
				if($block == "false") {
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$index.".StartTime",$timeData[4],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$index.".EndTime",$timeData[5],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$index.".BlockDays",$timeData[6],false);
				}
				setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$index.".AlwaysBlock",$block,true);
			}
			else {
				if(array_key_exists(1, $i)) delTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i[1].".");
			}
			$result=_("Success!");
		}
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
}
if (isset($_POST['del'])){
	if(validId_PC($_POST['del']))
	foreach (explode('_', $_POST['del']) as $key => $value) {
		delTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$value.".");
	}
}
?>
