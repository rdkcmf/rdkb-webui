<?php

$jsConfig = $_REQUEST['configInfo'];
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
echo $jsConfig;

?>
