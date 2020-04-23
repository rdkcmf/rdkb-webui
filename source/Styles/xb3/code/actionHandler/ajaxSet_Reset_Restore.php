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
header("Content-Type: application/json");
$infoArray = json_decode($_POST['resetInfo'], true);
// sleep(10);
$thisUser = $_SESSION["loginuser"];
ob_implicit_flush(true);
ob_end_flush();
$ret = array();
//>>zqiu
function delMacFilterTable( $ssid_list ) {
	$ssids = explode(" ", $ssid_list);
	foreach ($ssids as $i)	{
		$old_id = array_filter(explode(",",getInstanceIds("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.")));
		foreach ($old_id as $j) {
			delTblObj("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MacFilterTable.$j.");
		}
		setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.Enable", false, true);
		setStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.FilterAsBlackList", false, true);
	}
}
function delMacFilterTables(  ) {
	delMacFilterTable("1 2 3 5 6");
}
//Implement Validation of the input parameter(s)
//NOT USED radioIndex+";"+apIndex >> '1;1', '2;2', '1;3', '2;4'
$validInputs = array("Router,Wifi,VoIP,Dect,MoCA", "Device", "Wifi,Router", "Wifi", "password");
if (!in_array($infoArray[1], $validInputs)) {
	$infoArray[0] = 'InvalidInputs';
	$ret['status'] = 'InvalidInputs';
}
switch ($infoArray[0]) {
	case "btn1" :
		$ret["reboot"] = true;
		echo htmlspecialchars(json_encode($ret), ENT_NOQUOTES, 'UTF-8');
		setStr("Device.DeviceInfo.X_RDKCENTRAL-COM_UI_ACCESS","reboot_device",true);
		setStr("Device.X_CISCO_COM_DeviceControl.RebootDevice", $infoArray[1],true);
		exit(0);
	case "btn2" :
		$ret["wifi"] = true;
		echo htmlspecialchars(json_encode($ret), ENT_NOQUOTES, 'UTF-8');
		setStr("Device.X_CISCO_COM_DeviceControl.RebootDevice", $infoArray[1],true);
		//force to restart radio even no change
		setStr("Device.WiFi.X_CISCO_COM_ResetRadios", "true", true);
		exit(0);
	case "btn3" :
		$ret["wifi"] = true;
		echo htmlspecialchars(json_encode($ret), ENT_NOQUOTES, 'UTF-8');
		setStr("Device.X_CISCO_COM_DeviceControl.RebootDevice", $infoArray[1],true);
		//force to restart radio even no change
		setStr("Device.WiFi.X_CISCO_COM_ResetRadios", "true", true);
		exit(0);
	case "btn4" :
		$ret["wifi"] = true;
		echo htmlspecialchars(json_encode($ret), ENT_NOQUOTES, 'UTF-8');
		delMacFilterTable("1 2");
		//setStr("Device.X_CISCO_COM_DeviceControl.FactoryReset", $infoArray[1],true);
		//when restore, radio can be restart, but also need to force it when no change
		//setStr("Device.WiFi.X_CISCO_COM_ResetRadios", "true", true);
		setStr("Device.WiFi.X_CISCO_COM_FactoryResetRadioAndAp", "1,2;1,2",true);	//radio 1, radio 2; Ap 1, Ap 2
                setStr("Device.WiFi.X_CISCO_COM_FactoryResetRadioAndAp", "1,2;3,4",true);       //radio 1, radio 2; Ap 3, Ap 4
		exit(0);
	case "FactoryResetRadioAndAp" :
		$ret["wifi"] = true;
		echo htmlspecialchars(json_encode($ret), ENT_NOQUOTES, 'UTF-8');
		$idxArr = explode(";", $infoArray[1]);
		//$radioIndex=$idxArr[0];
		$apIndex=$idxArr[1];		
		if($apIndex != "0") {
			delMacFilterTable( "$apIndex" );
		}		
		setStr("Device.WiFi.X_CISCO_COM_FactoryResetRadioAndAp", $infoArray[1],true);		
		exit(0);
	case "btn5" :
		$ret["reboot"] = true;
		echo htmlspecialchars(json_encode($ret), ENT_NOQUOTES, 'UTF-8');
		setStr("Device.DeviceInfo.X_RDKCENTRAL-COM_UI_ACCESS","factory_reset",true);
		setStr("Device.X_CISCO_COM_DeviceControl.FactoryReset", $infoArray[1],true);
		exit(0);
	case "btn6" :
		//"mso" required to reset password of "admin"
		if($_SESSION["loginuser"] == "mso"){
			if ("mso"==$thisUser) {
				setStr("Device.Users.User.1.X_CISCO_COM_Password", "pod", true);
				setStr("Device.Users.User.3.X_RDKCENTRAL-COM_PasswordReset", true, true);
				echo "mso";
			}
			else {
				setStr("Device.Users.User.3.X_RDKCENTRAL-COM_PasswordReset", "true", true);
				echo "admin";
			}
		}
		break;
	default:
		break;
}
echo htmlspecialchars(json_encode($ret), ENT_NOQUOTES, 'UTF-8');
?>
