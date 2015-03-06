<?php 


$jsConfig = $_REQUEST['configInfo'];
//$jsConfig = '{"newPassword": "11111111", "instanceNum": "1", "oldPassword": "111"}';

$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);

$i = $arConfig['instanceNum'];

$p_status = "MisMatch";

if (getStr("Device.Users.User.$i.X_CISCO_COM_Password") ==  $arConfig['oldPassword']) 
{
	$p_status = "Match";
	setStr("Device.Users.User.$i.X_CISCO_COM_Password", $arConfig['newPassword'], true);	
}

$arConfig = array('p_status'=>$p_status);
				
$jsConfig = json_encode($arConfig);

header("Content-Type: application/json");
echo $jsConfig;	

?>