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

if (!isset($_SESSION["loginuser"]) || $_SESSION['loginuser'] != 'mso') {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
//data:{line:$("#line_number").val(),call:$("#call_number").val(),action:$("#action").val()},
$line = $_POST['line'];
$call = $_POST['call'];
$action = $_POST['action'];
if("display" == $action){
	//display part is handled in main page
}
if("clear_line" == $action){
	setStr("Device.X_CISCO_COM_MTA.ClearLineStats",$line,true);
}
else if ("clear_all" == $action){
	setStr("Device.X_CISCO_COM_MTA.ClearLineStats",1,true);
	setStr("Device.X_CISCO_COM_MTA.ClearLineStats",2,true);
}
?>
