<?php
header("Content-Type: application/json");

if (isset($_POST['Bridge'])){
	$isBridgeModel=$_POST['isBridgeModel'];
	
	if($isBridgeModel=="Enabled"){
		setStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode","bridge-static",true);
	}else{
		setStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode","router",true);
	}
	
	//20140523
	//set LanManagementEntry_ApplySettings after change LanManagementEntry table
	setStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry_ApplySettings", "true", true);
	
	$bridgeModel=getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode");
	
	if($bridgeModel=="bridge-static"){
		echo json_encode("Enabled");
	}else{
		echo json_encode("Disabled");
	}
}

if (isset($_POST['IGMP'])){
	$IGMPEnable=($_POST['IGMPEnable']=="Enabled"?"true":"false");
	setStr("Device.X_CISCO_COM_DeviceControl.IGMPSnoopingEnable",$IGMPEnable,true);
	$IGMPModel=(getStr("Device.X_CISCO_COM_DeviceControl.IGMPSnoopingEnable")=="true"?"Enabled":"Disabled");
	echo json_encode($IGMPModel);
}
?>
