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

if (!isset($_SESSION["loginuser"])){
    echo '<script type="text/javascript">alert("Please Login First!"); location.href="../index.php";</script>';
    exit(0);
}
$jsConfig = $_POST['configInfo'];
// $jsConfig = '{"current_line":"true / false"}';
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);
$response_message = '';
setStr("Device.Services.VoiceService.1.X_RDK-Central_COM_DisableLoopCurrentUntilRegistered", $arConfig['current_line'], true);
if($response_message!='') {
	$response->error_message = $response_message;
	echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
}
else echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>
