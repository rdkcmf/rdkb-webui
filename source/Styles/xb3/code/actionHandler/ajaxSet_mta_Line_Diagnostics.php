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

if (!isset($_SESSION["loginuser"]) || (!isset($_POST['restore_reboot']) && $_SESSION['loginuser'] != 'mso')) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
function TransEmpty($v)
{
	return (""==$v) ? "Failed" : $v;
}
if (isset($_POST['get_statusx']))
{
	$line1hook	= getStr("Device.X_CISCO_COM_MTA.LineTable.1.Status");
	$line2hook	= getStr("Device.X_CISCO_COM_MTA.LineTable.2.Status");
	$linexhook	= "On-Hook";
	sleep(2);
	if ("Off-Hook"==$line1hook || "Off-Hook"==$line2hook){
		$linexhook = "Off-Hook";
	}
	// $linexhook	= "Off-Hook";
	$arConfig	= array('linexhook'=>$linexhook);
}
else if (isset($_POST['get_status1']))
{
	$line1hook	= getStr("Device.X_CISCO_COM_MTA.LineTable.1.Status");
	sleep(2);
	// $line1hook	= "On-Hook";
	$arConfig	= array('line1hook'=>$line1hook);
}
else if (isset($_POST['get_status2']))
{
	$line2hook	= getStr("Device.X_CISCO_COM_MTA.LineTable.2.Status");
	sleep(2);
	// $line2hook	= "Off-Hook";
	$arConfig	= array('line2hook'=>$line2hook);
}
else if (isset($_POST['start_diagnostics1']))
{
	setStr("Device.X_CISCO_COM_MTA.LineTable.1.TriggerDiagnostics", "true", true);
	sleep(15);
	$line1hp 	= getStr("Device.X_CISCO_COM_MTA.LineTable.1.HazardousPotential");
	$line1femf 	= getStr("Device.X_CISCO_COM_MTA.LineTable.1.ForeignEMF");
	$line1rf 	= getStr("Device.X_CISCO_COM_MTA.LineTable.1.ResistiveFaults");
	$line1roh 	= getStr("Device.X_CISCO_COM_MTA.LineTable.1.ReceiverOffHook");
	$line1re 	= getStr("Device.X_CISCO_COM_MTA.LineTable.1.RingerEquivalency");	
	$arConfig	= array('line1hp'=>$line1hp, 'line1femf'=>$line1femf, 'line1rf'=>$line1rf, 'line1roh'=>$line1roh, 'line1re'=>$line1re);
	$arConfig	= array_map("TransEmpty", $arConfig);
}
else if (isset($_POST['start_diagnostics2']))
{
	setStr("Device.X_CISCO_COM_MTA.LineTable.2.TriggerDiagnostics", "true", true);
	sleep(15);
	$line2hp 	= getStr("Device.X_CISCO_COM_MTA.LineTable.2.HazardousPotential");
	$line2femf 	= getStr("Device.X_CISCO_COM_MTA.LineTable.2.ForeignEMF");
	$line2rf 	= getStr("Device.X_CISCO_COM_MTA.LineTable.2.ResistiveFaults");
	$line2roh 	= getStr("Device.X_CISCO_COM_MTA.LineTable.2.ReceiverOffHook");
	$line2re 	= getStr("Device.X_CISCO_COM_MTA.LineTable.2.RingerEquivalency");
	$arConfig	= array('line2hp'=>$line2hp, 'line2femf'=>$line2femf, 'line2rf'=>$line2rf, 'line2roh'=>$line2roh, 'line2re'=>$line2re);
	$arConfig	= array_map("TransEmpty", $arConfig);
}
header("Content-Type: application/json");
$jsConfig	= json_encode($arConfig);
echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>
