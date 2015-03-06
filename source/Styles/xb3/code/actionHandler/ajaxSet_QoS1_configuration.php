<?php

//qosInfo = '{"IsEnabledWMM":"'+isEnabledWMM+'", "IsEnabledMoCA":"'+isEnabledMoCA+'", "IsEnabledLAN":"'+isEnabledLAN+'", "IsEnabledUPnP":"'+isEnabledUPnP+'"}';

$qosInfo = json_decode($_REQUEST['qosInfo'], true);

//var_dump($ddnsInfo);

$APIDs=explode(",",getInstanceIDs("Device.WiFi.AccessPoint."));
for($i=0;$i<count($APIDs);$i++)
{
	if ("false" == $qosInfo['IsEnabledWMM']) {
		setStr("Device.WiFi.AccessPoint.".$APIDs[$i].".UAPSDEnable", "false", true);
	}	
	setStr("Device.WiFi.AccessPoint.".$APIDs[$i].".WMMEnable", $qosInfo['IsEnabledWMM'],true);
	setStr("Device.WiFi.Radio.".$APIDs[$i].".X_CISCO_COM_ApplySetting", "true", true);
}	


$MoCAIDs=explode(",",getInstanceIDs("Device.MoCA.Interface."));
for($i=0;$i<count($MoCAIDs);$i++)
{
	setStr("Device.MoCA.Interface.".$MoCAIDs[$i].".QoS.X_CISCO_COM_Enabled", $qosInfo['IsEnabledMoCA'],true);
}	
//setStr("", $qosInfo['IsEnabledLAN']);
//setStr("", $qosInfo['IsEnabledUPnP']);

?>
