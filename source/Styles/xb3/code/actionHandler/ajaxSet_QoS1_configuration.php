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
