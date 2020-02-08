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
if (isset($_POST['set'])){
	$validation = true;
	if($validation) $validation = isValInArray($_POST['UMDStatus'], array('Enabled', 'Disabled'));
	if($validation) {
		$UMDStatus=(($_POST['UMDStatus']=="Enabled")?"true":"false");
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Enable",$UMDStatus,true);
	}
	$UMDStatus=getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Enable");
	$UMDStatus=($UMDStatus=="true")?"Enabled":"Disabled";
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($UMDStatus), ENT_NOQUOTES, 'UTF-8');
//	echo json_encode("Disabled");
}
if (isset($_POST['allow_block'])){
	$validation = true;
	if($validation) $validation = isValInArray($_POST['AllowBlock'], array('allow_all', 'block_all'));
	if($validation) {
		$AllowBlock=(($_POST['AllowBlock']=="allow_all")?"true":"false");
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.AllowAll",$AllowBlock,true);
	}
	$AllowBlock=getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.AllowAll");
	$AllowBlock=($AllowBlock=="true")?"Allow All":"Block All";
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($AllowBlock), ENT_NOQUOTES, 'UTF-8');
//	echo json_encode("Disabled");
}
if (isset($_POST['add'])){
	$validation = true;
	if($validation) $validation = isValInArray($_POST['type'], array('Allow', 'Block'));
	if($validation) $validation = printableCharacters($_POST['name']);
	if($validation) $validation = is_allowed_string($_POST['name']);
	if($validation) $validation = validMAC($_POST['mac']);
	if($validation) $validation = isValInArray($_POST['block'], array('true', 'false'));
	if($validation && $_POST['block'] == 'false'){
		if($validation) $validation = validTime($_POST['startTime'], $_POST['endTime']);
		if($validation) $validation = validDays($_POST['days']);
	}
	$result = ($validation)?'':_('Invalid Inputs!');
	if($validation) {
		$type=$_POST['type'];
		$name=$_POST['name'];
		$mac=$_POST['mac'];
		$block=$_POST['block'];
		$startTime=$_POST['startTime'];
		$endTime=$_POST['endTime'];
		$blockDays=$_POST['days'];
		$ids=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
		if (count($ids)==0) {	//no table, need test whether it equals 0
			if ($UTC_local_Time_conversion) $timeData = days_time_conversion_set($startTime, $endTime, $blockDays);
			else $timeData = array($startTime, $endTime, $blockDays, false);
			//for the first rule
			addTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
			$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
			$i=$IDs[count($IDs)-1];
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Type",$type,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Description",$name,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".MACAddress",$mac,false);		
			if($block == "false") {
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".StartTime",$timeData[0],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".EndTime",$timeData[1],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".BlockDays",$timeData[2],false);
			}
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".AlwaysBlock",$block,true);
			if($timeData[3]){
				//for the second rule
				addTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
				$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
				$i=$IDs[count($IDs)-1];
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Type",$type,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Description",$name,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".MACAddress",$mac,false);		
				if($block == "false") {
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".StartTime",$timeData[4],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".EndTime",$timeData[5],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".BlockDays",$timeData[6],false);
				}
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".AlwaysBlock",$block,true);
			}
			$result = _("Success!");
		} 
		else {
			$result="";
			$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.";
			$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
			$mapping_array  = array("Description", "Type", "MACAddress", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");
			$managedDevicesValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
			if($UTC_local_Time_conversion) $managedDevicesValues = days_time_conversion_get($managedDevicesValues, array('Type', 'MACAddress'));
			foreach ($managedDevicesValues as $key) {
				$deviceName = $key["Description"];
				$accessType = $key["Type"];	
				$MACAddress = $key["MACAddress"];	
				$always_Block = $key["AlwaysBlock"];
				$start_Time = $key["StartTime"];
				$end_Time = $key["EndTime"];
				$block_Days = $key["BlockDays"];
				if (($type == $accessType) && (!strcasecmp($mac, $MACAddress))) {
					//Check for time and day conflicts
					$TD1=array($startTime, $endTime, $blockDays);
					$TD2=array($start_Time, $end_Time, $block_Days);
					if(($always_Block == "true") || ($block == "true") || time_date_conflict($TD1, $TD2)){
						$result .= _("Conflict with other device. Please check your input!");
						break;
					}
				}
			}
			if ($result=="") {
				if ($UTC_local_Time_conversion) $timeData = days_time_conversion_set($startTime, $endTime, $blockDays);
				else $timeData = array($startTime, $endTime, $blockDays, false);
				//for the first rule
				addTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
				$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
				$i=$IDs[count($IDs)-1];
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Type",$type,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Description",$name,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".MACAddress",$mac,false);
				if($block == "false") {
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".StartTime",$timeData[0],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".EndTime",$timeData[1],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".BlockDays",$timeData[2],false);
				}
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".AlwaysBlock",$block,true);
				if($timeData[3]){
					//for the second rule
					addTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
					$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
					$i=$IDs[count($IDs)-1];
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Type",$type,false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Description",$name,false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".MACAddress",$mac,false);
					if($block == "false") {
						setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".StartTime",$timeData[4],false);
						setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".EndTime",$timeData[5],false);
						setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".BlockDays",$timeData[6],false);
					}
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".AlwaysBlock",$block,true);
				}
				$result=_("Success!");
			}
		}
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
}
if (isset($_POST['edit'])){
	$validation = true;
	if($validation) $validation = validId_PC($_POST['ID']);
	if($validation) $validation = printableCharacters($_POST['name']);
	if($validation) $validation = is_allowed_string($_POST['name']);
	if($validation) $validation = validMAC($_POST['mac']);
	if($validation) $validation = isValInArray($_POST['block'], array('true', 'false'));
	if($validation && $_POST['block'] == 'false'){
		if($validation) $validation = validTime($_POST['startTime'], $_POST['endTime']);
		if($validation) $validation = validDays($_POST['days']);
	}
	$result = ($validation)?'':_('Invalid Inputs!');
	if($validation) {
		$i=$_POST['ID'];
		$name=$_POST['name'];
		$mac=$_POST['mac'];
		$block=$_POST['block'];
		$startTime=$_POST['startTime'];
		$endTime=$_POST['endTime'];
		$blockDays=$_POST['days'];
		$ID = explode('_', $i)[0];
		$type = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.$ID.Type");
		$result="";
		$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.";
		$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
		$mapping_array  = array("Description", "Type", "MACAddress", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");
		$managedDevicesValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
		if($UTC_local_Time_conversion) $managedDevicesValues = days_time_conversion_get($managedDevicesValues, array('Type', 'MACAddress'));
		foreach ($managedDevicesValues as $key) {
			$j = $key["__id"];
			if ($i==$j) continue;
			$deviceName = $key["Description"];
			$accessType = $key["Type"];	
			$MACAddress = $key["MACAddress"];	
			$always_Block = $key["AlwaysBlock"];
			$start_Time = $key["StartTime"];
			$end_Time = $key["EndTime"];
			$block_Days = $key["BlockDays"];
			if (($type == $accessType) && (!strcasecmp($mac, $MACAddress))) {
				//Check for time and day conflicts
				$TD1=array($startTime, $endTime, $blockDays);
				$TD2=array($start_Time, $end_Time, $block_Days);
				if(($always_Block == "true") || ($block == "true") || time_date_conflict($TD1, $TD2)){
					$result .= _("Conflict with other device. Please check your input!");
					break;
				}
			}
		}
		$i = explode('_', $i);
		if ($result=="") {
			if ($UTC_local_Time_conversion) $timeData = days_time_conversion_set($startTime, $endTime, $blockDays);
			else $timeData = array($startTime, $endTime, $blockDays, false);
			//for the first rule
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i[0].".Type",$type,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i[0].".Description",$name,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i[0].".MACAddress",$mac,false);
			if($block == "false") {
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i[0].".StartTime",$timeData[0],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i[0].".EndTime",$timeData[1],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i[0].".BlockDays",$timeData[2],false);
			}
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i[0].".AlwaysBlock",$block,true);
			if(($block == "true") && array_key_exists(1, $i)){
				delTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i[1].".");
			}
			if($timeData[3]){
				//for the second rule
				if(!array_key_exists(1, $i)){
					addTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
					$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
					$index=$IDs[count($IDs)-1];
				}
				else $index = $i[1];
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index.".Type",$type,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index.".Description",$name,false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index.".MACAddress",$mac,false);
				if($block == "false") {
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index.".StartTime",$timeData[4],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index.".EndTime",$timeData[5],false);
					setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index.".BlockDays",$timeData[6],false);
				}
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index.".AlwaysBlock",$block,true);
			}
			else {
                        	$j=1;
				while(array_key_exists($j, $i)){ 
					delTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i[$j].".");
					$j=$j+1;  
                                }
			}
			$result=_("Success!");
		}
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
}
if (isset($_POST['del'])){
	foreach (explode('_', $_POST['del']) as $key => $value) {
		delTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$value.".");
	}
}
?>
