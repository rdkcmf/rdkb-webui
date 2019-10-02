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
<?php include('../includes/actionHandlerUtility.php') ?>
<?php

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
//dmzInfo = '{"IsEnabledDMZ":"'+isEnabledDMZ+'", "Host":"'+host+'"}';
$dmzInfo = json_decode($_POST['dmzInfo'], true);
$validation = true;
if($validation) $validation = isValInArray($dmzInfo['IsEnabledDMZ'], array('true', 'false'));
if($validation) $validation = validIPAddr($dmzInfo['Host']);
if($validation)
	if (!($dmzInfo['hostv6']=='x'))
		$validation = validIPAddr($dmzInfo['hostv6']);
//echo $dmzInfo['IsEnabled'];
//echo "<br />";
if($validation){
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
}
?>
