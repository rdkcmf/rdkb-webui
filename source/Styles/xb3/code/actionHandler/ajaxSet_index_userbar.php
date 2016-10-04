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
<?php
$jsConfig = $_POST['configInfo'];
//$jsConfig = '{"status":"true", "target":"sta_inet"}';
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);
function get_tips($target, $status)
{
	$tip = "No Tips!";
	switch($target)
	{
		case "sta_inet":{
			if ("true"==$status){
				$tip = 'Status: Connected-'.getStr("Device.Hosts.X_CISCO_COM_ConnectedDeviceNumber").' computers connected';
			}
			else{
				$tip = 'Status: Unconnected-no computers';
			}
		}break;
		case "sta_wifi":{
			if ("true"==$status){
				$sum = 0;
				$ids = explode(",", getInstanceIds("Device.WiFi.AccessPoint."));
				foreach($ids as $i){
					$sum += getStr("Device.WiFi.AccessPoint.$i.AssociatedDeviceNumberOfEntries");
				}
				$tip = 'Status: Connected-'.$sum.' computers connected';
			}
			else{
				$tip = 'Status: Unconnected-no computers';
			}
		}break;
		case "sta_moca":{
			if ("true"==$status && "Up"==getStr("Device.MoCA.Interface.1.Status")){
				$tip = 'Status: Connected-'.getStr("Device.MoCA.Interface.1.X_CISCO_COM_NumberOfConnectedClients").' computers connected';
			}
			else{
				$tip = 'Status: Unconnected-no computers';
			}	
		}break;
		/*case "sta_dect":{
			if ("true"==$status){
				$tip = getStr("Device.X_CISCO_COM_MTA.Dect.HandsetsNumberOfEntries").' Handsets connected';
			}
			else{
				$tip = 'no Handsets';
			}
		}break;*/
		case "sta_fire":{
			$tip = 'Firewall is set to '.$status;
		}break;
		default:{
			$tip = "No Tips!";
		}break;
	}
	return $tip;
}
$tags = explode(',', $arConfig['target']);
$stas = explode(',', $arConfig['status']);
$tips = array();
for ($i=0; $i<count($tags); $i++) {
	array_push($tips, get_tips($tags[$i], $stas[$i]));
}
$arConfig = array('tags'=>$tags, 'tips'=>$tips);			
$jsConfig = json_encode($arConfig);
echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>
