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

header("Content-Type: application/json");

$infoArray = json_decode($_REQUEST['resetInfo'], true);
// sleep(10);
$thisUser = $infoArray[2];

ob_implicit_flush(true);
ob_end_flush();

$ret = array();

switch ($infoArray[0]) {
	case "btn1" :
		$ret["reboot"] = true;
		echo json_encode($ret);
		setStr("Device.X_CISCO_COM_DeviceControl.RebootDevice", $infoArray[1],true);
		exit(0);
	case "btn2" :
		$ret["wifi"] = true;
		echo json_encode($ret);	
		setStr("Device.X_CISCO_COM_DeviceControl.RebootDevice", $infoArray[1],true);
		//force to restart radio even no change
		setStr("Device.WiFi.X_CISCO_COM_ResetRadios", "true", true);
		exit(0);
	case "btn3" :
		$ret["wifi"] = true;
		echo json_encode($ret);	
		setStr("Device.X_CISCO_COM_DeviceControl.RebootDevice", $infoArray[1],true);
		//force to restart radio even no change
		setStr("Device.WiFi.X_CISCO_COM_ResetRadios", "true", true);
		exit(0);
	case "btn4" :
		$ret["wifi"] = true;
		echo json_encode($ret);	
		setStr("Device.X_CISCO_COM_DeviceControl.FactoryReset", $infoArray[1],true);
		//when restore, radio can be restart, but also need to force it when no change
		setStr("Device.WiFi.X_CISCO_COM_ResetRadios", "true", true);
		exit(0);
	case "btn5" :
		$ret["reboot"] = true;
		echo json_encode($ret);
		setStr("Device.X_CISCO_COM_DeviceControl.FactoryReset", $infoArray[1],true);
		exit(0);
	case "btn6" :
		//"mso" and "cusadmin" required to reset password of "admin"
		if ("mso"==$thisUser) {
			setStr("Device.Users.User.1.X_CISCO_COM_Password", "pod", true);
			setStr("Device.Users.User.3.X_CISCO_COM_Password", "password", true);
			echo "mso";
		}
		elseif ("cusadmin"==$thisUser) {
			setStr("Device.Users.User.2.X_CISCO_COM_Password", "Xfinity", true);
			setStr("Device.Users.User.3.X_CISCO_COM_Password", "password", true);
			echo "cusadmin";
		}
		else {
			setStr("Device.Users.User.3.X_CISCO_COM_Password", "password", true);
			echo "admin";
		}
		break;
	default:
		break;
}

echo json_encode($ret);
?>
