<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2018 RDK Management

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
$infoArray = json_decode($_REQUEST['batteryInfo'], true);

function discoverBattery() {

        $paramArray = array (
                        array("Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.discover", "int", "300"),
                    );

        $retStatus = DmExtSetStrsWithRootObj("Device.DeviceInfo.", TRUE, $paramArray);
        if (!$retStatus){
                echo _("Success!");
        }
        else {
                echo _('Failed to add');
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
