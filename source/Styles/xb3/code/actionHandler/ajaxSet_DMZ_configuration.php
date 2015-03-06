<?php 

//dmzInfo = '{"IsEnabledDMZ":"'+isEnabledDMZ+'", "Host":"'+host+'"}';

$dmzInfo = json_decode($_REQUEST['dmzInfo'], true);

//var_dump($dmzInfo);
//echo $dmzInfo['IsEnabled'];
//echo "<br />";

$isEnabledDMZ = $dmzInfo['IsEnabledDMZ'];
$ip = $dmzInfo['Host'];
$hostv6 = $dmzInfo['hostv6'];

$rootObjName = "Device.NAT.X_CISCO_COM_DMZ.";

if($isEnabledDMZ == "true") {
	$paramArray = 
		array (
			array($rootObjName."InternalIP", "string", $ip),
			array($rootObjName."IPv6Host", "string", $hostv6),
			array($rootObjName."Enable", "bool", $isEnabledDMZ)
		);
	$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
}
else if($isEnabledDMZ == "false") {
	setStr($rootObjName."Enable", $isEnabledDMZ,true);
}

?>
