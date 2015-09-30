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
<?php

if (isset($_POST['set'])){
	$UMDStatus=(($_POST['UMDStatus']=="Enabled")?"true":"false");
	setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Enable",$UMDStatus,true);
	$UMDStatus=getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Enable");
	$UMDStatus=($UMDStatus=="true")?"Enabled":"Disabled";
	header("Content-Type: application/json");
	echo json_encode($UMDStatus);
//	echo json_encode("Disabled");
}

if (isset($_POST['allow_block'])){
	$AllowBlock=(($_POST['AllowBlock']=="allow_all")?"true":"false");
	setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.AllowAll",$AllowBlock,true);

	$AllowBlock=getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.AllowAll");
	$AllowBlock=($AllowBlock=="true")?"Allow All":"Block All";
	header("Content-Type: application/json");
	echo json_encode($AllowBlock);
//	echo json_encode("Disabled");
}

if (isset($_POST['add'])){

	$type=$_POST['type'];
	$name=$_POST['name'];
	$mac=$_POST['mac'];
	$block=$_POST['block'];
	
	$ids=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
	if (count($ids)==0) {	//no table, need test whether it equals 0
		addTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
		$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
		$i=$IDs[count($IDs)-1];
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Type",$type,false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Description",$name,false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".MACAddress",$mac,false);		
		if($block == "false") {
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".StartTime",$_POST['startTime'],false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".EndTime",$_POST['endTime'],false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".BlockDays",$_POST['days'],false);
		}
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".AlwaysBlock",$block,true);
		header("Content-Type: application/json");
		echo json_encode("Success!");
	} 
	else {
		
		$result="";

		foreach ($ids as $key=>$j) {

			$deviceName = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.$j.Description");
			$accessType = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.$j.Type");	
			$MACAddress = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.$j.MACAddress");	

			if (($type == $accessType) && (!strcasecmp($mac, $MACAddress))) {
				$result .= "This device is already in $type list.";
				break;
			}
		}

		if ($result=="") {
			addTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
			$IDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
			$i=$IDs[count($IDs)-1];
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Type",$type,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Description",$name,false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".MACAddress",$mac,false);
			if($block == "false") {
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".StartTime",$_POST['startTime'],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".EndTime",$_POST['endTime'],false);
				setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".BlockDays",$_POST['days'],false);
			}
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".AlwaysBlock",$block,true);
			$result="Success!";
		}
		header("Content-Type: application/json");
		echo json_encode($result);
	}
}

if (isset($_POST['edit'])){
	$i=$_POST['ID'];
	$name=$_POST['name'];
	$mac=$_POST['mac'];
	$block=$_POST['block'];

	$type = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.$i.Type");
	$result="";
	
	$ids=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
	foreach ($ids as $key=>$j) {
		if ($i==$j) continue;

		$deviceName = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.$j.Description");
	    $accessType = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.$j.Type");	
	    $MACAddress = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.$j.MACAddress");	
		
		if (($type == $accessType) && (!strcasecmp($mac, $MACAddress))) {
			$result .= "This device is already in $type list.";
			break;
		}
	}

	if ($result=="") {
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".Description",$name,false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".MACAddress",$mac,false);
		if($block == "false") {
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".StartTime",$_POST['startTime'],false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".EndTime",$_POST['endTime'],false);
			setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".BlockDays",$_POST['days'],false);
		}
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$i.".AlwaysBlock",$block,true);
		$result="Success!";
	}
	header("Content-Type: application/json");
	echo json_encode($result);
}

if (isset($_GET['del'])){
	delTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$_GET['del'].".");
	Header("Location:../managed_devices.php");
	exit;
}
?>
