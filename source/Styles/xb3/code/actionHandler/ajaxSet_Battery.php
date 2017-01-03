<?php
session_start();
if (!isset($_SESSION["loginuser"])) {
        echo '<script type="text/javascript">alert("Please Login First!"); location.href="../index.php";</script>';
        exit(0);
}
header("Content-Type: application/json");
$infoArray = json_decode($_REQUEST['batteryInfo'], true);

function discoverBattery() {

        $paramArray = array (
                        array("Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.discover", "int", "1"),
                    );

        $retStatus = DmExtSetStrsWithRootObj("Device.DeviceInfo.", TRUE, $paramArray);
        if (!$retStatus){
                echo "Success!";
        }
        else {
                echo 'Failed to add';
        }
}

switch ($infoArray[1]) {
        case "Discover" :
              discoverBattery();
              break;
        default:
              break;
}

?>
