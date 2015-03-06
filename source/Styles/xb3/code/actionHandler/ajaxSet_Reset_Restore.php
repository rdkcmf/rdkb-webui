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
