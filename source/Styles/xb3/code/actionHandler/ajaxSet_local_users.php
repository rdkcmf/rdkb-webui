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
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
$jsConfig = $_POST['configInfo'];
//$jsConfig = '	{"dest":"Edit", "idex":"1", "name":"tom", "pass":"11111111"}';
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);
$id = $arConfig['idex'];
if ("Edit" == $arConfig['dest'])
{
	setStr("Device.X_CISCO_COM_FileSharing.User.$id.UserName", $arConfig['name'], false);
	setStr("Device.X_CISCO_COM_FileSharing.User.$id.Password", $arConfig['pass'], true);
}
else if ("Add" == $arConfig['dest'])
{
	addTblObj("Device.X_CISCO_COM_FileSharing.User.");
	$ids = array_filter(explode(",", getInstanceIds("Device.X_CISCO_COM_FileSharing.User.")));
	$id	 = $ids[count($ids)-1];
	setStr("Device.X_CISCO_COM_FileSharing.User.$id.UserName", $arConfig['name'], false);
	setStr("Device.X_CISCO_COM_FileSharing.User.$id.Password", $arConfig['pass'], true);	
}
else if ("Delete" == $arConfig['dest'])
{
	delTblObj("Device.X_CISCO_COM_FileSharing.User.$id.");
}
sleep(6);
echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>
