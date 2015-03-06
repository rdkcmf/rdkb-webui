<?php 

//upnpInfo = '{"IsEnabledUPnP":"'+isEnabledUPnP+'", "Period":"'+period+'", "Live":"'+live+'", "IsEnabledZero":"'+isEnabledZero+'", "IsEnabledQosUPnP":"'+isEnabledQosUPnP+'"}';

$upnpInfo = json_decode($_REQUEST['upnpInfo'], true);

//var_dump($upnpInfo);
//echo $ddnsInfo['IsEnabled'];
//echo "<br />";

$isEnabledUPnP = $upnpInfo['IsEnabledUPnP'];
	
if(!strcmp($isEnabledUPnP, "true")) {
	setStr("Device.UPnP.Device.UPnPIGD", $upnpInfo['IsEnabledUPnP'],true);
	setStr("Device.UPnP.Device.X_CISCO_COM_IGD_AdvertisementPeriod", $upnpInfo['Period'],true);
	setStr("Device.UPnP.Device.X_CISCO_COM_IGD_TTL", $upnpInfo['Live'],true);
} else if(!strcmp($isEnabledUPnP, "false")) {
	setStr("Device.UPnP.Device.UPnPIGD", $upnpInfo['IsEnabledUPnP'],true);
}

setStr("Device.X_CISCO_COM_DeviceControl.EnableZeroConfig", $upnpInfo['IsEnabledZero'],true);
//setStr("", $upnpInfo['IsEnabledQosUPnP']); //? R3

?>
