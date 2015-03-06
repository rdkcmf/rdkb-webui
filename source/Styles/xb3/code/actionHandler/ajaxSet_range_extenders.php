<?php

$jsConfig = $_REQUEST['configInfo'];
//$jsConfig = '{"ext_mac":"10:11:22:33:44:66", "dis_mac":"20:11:22:33:44:77,00:11:22:33:44:55,10:11:22:33:44:66"}';

$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);

setStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.X_CISCO_COM_DISCONNECT_CLIENT", $arConfig['dis_mac'], true);

echo $jsConfig;	

?>
