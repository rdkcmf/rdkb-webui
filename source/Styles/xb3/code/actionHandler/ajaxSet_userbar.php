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

session_start();

	$a = getStr("Device.X_CISCO_COM_MTA.Battery.RemainingCharge");
	$b = getStr("Device.X_CISCO_COM_MTA.Battery.ActualCapacity");
	$sta_batt = ($a<=$b && $a && $b) ? round(100*$a/$b) : 0;
	
	//$sta_batt = "61";
	//find battery class manually	
	if($sta_batt > 90) { $battery_class = "bat-100"; }
	elseif($sta_batt > 60) { $battery_class = "bat-75"; }
	elseif($sta_batt > 39) { $battery_class = "bat-50"; }
	elseif($sta_batt > 18) { $battery_class = "bat-25"; }
	elseif($sta_batt > 8) { $battery_class = "bat-10"; }
	else { $battery_class = "bat-0"; }

	$fistUSif = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");

	$WANIPv4 = getStr($fistUSif."IPv4Address.1.IPAddress");

	$ids = explode(",", getInstanceIds($fistUSif."IPv6Address."));
	foreach ($ids as $i){
		$val = getStr($fistUSif."IPv6Address.$i.IPAddress");
		if (!strstr($val, "fe80::")){
			$WANIPv6 = $val;
			break;
		}
	}

	$sta_inet = ($WANIPv4 != "0.0.0.0" || strlen($WANIPv6) > 0) ? "true" : "false";

	//in Bridge mode > Internet connectivity status is always active
	$sta_inet = ($_SESSION["lanMode"] == "bridge-static") ? "true" : $sta_inet ;

	$sta_wifi = "false";
	if("Disabled"==$_SESSION["psmMode"]){
		$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));
		foreach ($ssids as $i){
			$r = (2 - intval($i)%2);	//1,3,5,7==1(2.4G); 2,4,6,8==2(5G)
			if ("true" == getStr("Device.WiFi.Radio.$r.Enable") && "true" == getStr("Device.WiFi.SSID.$i.Enable")){	//bwg has radio.enable, active status is “at least one SSID and its Radio is enabled”
				$sta_wifi = "true";
				break;
			}
		}	
	}
	
	if("Disabled"==$_SESSION["psmMode"]) { $sta_moca = getStr("Device.MoCA.Interface.1.Enable"); }
	$sta_dect = getStr("Device.X_CISCO_COM_MTA.Dect.Enable");
	$sta_fire = getStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevel");
	
	$arConfig = array(
		"target" => "sta_inet,sta_wifi,sta_moca,sta_fire",
		"status" => $sta_inet.",".$sta_wifi.",".$sta_moca.",".$sta_fire,
	);

	//to update main status
	$mainStatus = array($sta_inet,$sta_wifi,$sta_moca,$sta_fire,$sta_batt,$battery_class);
	$_SESSION['sta_inet'] = $sta_inet;
	$_SESSION['sta_wifi'] = $sta_wifi;
	$_SESSION['sta_moca'] = $sta_moca;
	$_SESSION['sta_fire'] = $sta_fire;
	$_SESSION['sta_batt'] = $sta_batt;
	$_SESSION['battery_class'] = $battery_class;

function get_tips($target, $status)
{
	$tip = "No Tips!";

	switch($target)
	{
		case "sta_inet":{
			if ("true"==$status){
				$tip = 'Status: Connected<br/>'.getStr("Device.Hosts.X_CISCO_COM_ConnectedDeviceNumber").' computers connected';
			}
			else{
				$tip = 'Status: Unconnected<br/>no computers';
			}
		}break;

		case "sta_wifi":{
			if ("true"==$status){
				$sum = 0;
				$ids = explode(",", getInstanceIds("Device.WiFi.AccessPoint."));
				foreach($ids as $i){
					$sum += getStr("Device.WiFi.AccessPoint.$i.AssociatedDeviceNumberOfEntries");
				}
				$tip = 'Status: Connected<br/>'.$sum.' computers connected';
			}
			else{
				$tip = 'Status: Unconnected<br/>no computers';
			}
		}break;

		case "sta_moca":{
			if ("true"==$status && "Up"==getStr("Device.MoCA.Interface.1.Status")){
				$tip = 'Status: Connected<br/>'.getStr("Device.MoCA.Interface.1.X_CISCO_COM_NumberOfConnectedClients").' computers connected';
			}
			else{
				$tip = 'Status: Unconnected<br/>no computers';
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

$arConfig = array('tags'=>$tags, 'tips'=>$tips, 'mainStatus'=>$mainStatus);

$jsConfig = json_encode($arConfig);

echo $jsConfig;

?>
