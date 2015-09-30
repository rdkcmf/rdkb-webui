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

//ddnsInfo = '{"Index":"'+index+'", "SpName":"'+spName+'", "User:"'+user+'", "Passwd":"'+passwd+'", "Host":"'+host+'"}';
//ddnsInfo = '{"IsEnabled":"'+isEnabled+'", "SpName":"'+spName+'", "User":"'+user+'", "Passwd":"'+passwd+'", "Host":"'+host+'"}';

$ddnsInfo = json_decode($_REQUEST['ddnsInfo'], true);

//var_dump($ddnsInfo);
//echo $ddnsInfo['Index'];
//echo $ddnsInfo['IsEnabled'];
//echo "<br />";
//echo $ddnsInfo['SpName'];
//echo "<br />";
//echo $ddnsInfo['User'];
//echo "<br />";
//echo $ddnsInfo['Passwd'];

//$index = $ddnsInfo['Index'];;
$index = 1;
$isEnabled = $ddnsInfo['IsEnabled'];
$spName = $ddnsInfo['SpName'];
	
if($isEnabled == "true") {
	setStr("Device.X_CISCO_COM_DDNS.Enable", $isEnabled, false);
	if($spName == "dyndns.org") {
		$index = 1;
		setStr("Device.X_CISCO_COM_DDNS.Service.2.Enable", "false", false);
	} else {
		$index = 2;
		setStr("Device.X_CISCO_COM_DDNS.Service.1.Enable", "false", false);
	}
	setStr("Device.X_CISCO_COM_DDNS.Service."."$index".".ServiceName", $ddnsInfo['SpName'], false);
	setStr("Device.X_CISCO_COM_DDNS.Service."."$index".".Username", $ddnsInfo['User'], false);	
	setStr("Device.X_CISCO_COM_DDNS.Service."."$index".".Password", $ddnsInfo['Passwd'], false);	
	setStr("Device.X_CISCO_COM_DDNS.Service."."$index".".Domain", $ddnsInfo['Host'],false);
	setStr("Device.X_CISCO_COM_DDNS.Service."."$index".".Enable", $isEnabled, true);
} else if($isEnabled == "false") {
	setStr("Device.X_CISCO_COM_DDNS.Enable", $isEnabled, false);
}


?>
