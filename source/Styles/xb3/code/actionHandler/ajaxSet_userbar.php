<?php

$jsConfig = $_REQUEST['configInfo'];
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
				$tip = 'Status: Connected<br/>' . (1 + intval(getStr("Device.MoCA.Interface.1.AssociatedDeviceNumberOfEntries"))) . ' nodes connected';
			}
			else{
				$tip = 'Status: Unconnected<br/>no nodes';
			}	
		}break;

		case "sta_dect":{
			if ("true"==$status){
				$tip = getStr("Device.X_CISCO_COM_MTA.Dect.HandsetsNumberOfEntries").' Handsets connected';
			}
			else{
				$tip = 'no Handsets';
			}
		}break;
		
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

echo $jsConfig;

?>
