<?php 

$devBlockInfo = json_decode($_REQUEST['BlockInfo'], true);

if (! array_key_exists('XfinitySSID', $devBlockInfo)) {
	
	$exist = false;
    $macAddr = $devBlockInfo['macAddr'];
    $idArr = explode(",", getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));

    foreach ($idArr as $key => $value) {
        if ( $macAddr == getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.$value.MACAddress")){
            $exist = true;
            break;
        }
    }

    if(! $exist){

		addTblObj("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
		$IDs  = getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
		$idArr = explode(",", $IDs);
		$instanceid = array_pop($idArr);

		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device." .$instanceid. ".Type", "Block", false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device." .$instanceid. ".Description", $devBlockInfo['hostName'], false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device." .$instanceid. ".MACAddress", $devBlockInfo['macAddr'], false);
		setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device." .$instanceid. ".AlwaysBlock", "true", true);
	
	}

	/*
	* if managed device is disabled, enable it
	*/
    $enableFlag =  getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Enable");
    if ( !strcasecmp($enableFlag, "false") ) {
    	setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Enable", "true", true);
    }

   	setStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.AllowAll", "true", true);

}
else {
	//this is going to set add XfinitySSID connected device to MAC filter list
	//wi-fi ssie 5 and 6 mapped to XfinitySSID
	setStr("Device.WiFi.AccessPoint.5.X_CISCO_COM_MACFilter.Enable", 'true', true);
	setStr("Device.WiFi.AccessPoint.5.X_CISCO_COM_MACFilter.FilterAsBlackList", 'true', true);
	addTblObj("Device.WiFi.AccessPoint.5.X_CISCO_COM_MacFilterTable.");
	$idArr = explode(",", getInstanceIds("Device.WiFi.AccessPoint.5.X_CISCO_COM_MacFilterTable."));
	$id = array_pop($idArr);

	setStr("Device.WiFi.AccessPoint.5.X_CISCO_COM_MacFilterTable.$id.DeviceName", $devBlockInfo['hostName'], false);
	setStr("Device.WiFi.AccessPoint.5.X_CISCO_COM_MacFilterTable.$id.MACAddress", $devBlockInfo['macAddr'], true);

	setStr("Device.WiFi.AccessPoint.6.X_CISCO_COM_MACFilter.Enable", 'true', true);
	setStr("Device.WiFi.AccessPoint.6.X_CISCO_COM_MACFilter.FilterAsBlackList", 'true', true);
	addTblObj("Device.WiFi.AccessPoint.6.X_CISCO_COM_MacFilterTable.");
	$idArr = explode(",", getInstanceIds("Device.WiFi.AccessPoint.6.X_CISCO_COM_MacFilterTable."));
	$id = array_pop($idArr);

	setStr("Device.WiFi.AccessPoint.6.X_CISCO_COM_MacFilterTable.$id.DeviceName", $devBlockInfo['hostName'], false);
	setStr("Device.WiFi.AccessPoint.6.X_CISCO_COM_MacFilterTable.$id.MACAddress", $devBlockInfo['macAddr'], true);
	//For WECB
	setStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.X_CISCO_COM_SSID_Updated", "true", true);
}

?>
