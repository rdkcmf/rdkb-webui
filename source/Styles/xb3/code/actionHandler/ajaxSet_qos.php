<?php

$jsConfig = $_REQUEST['configInfo'];
//$jsConfig = '{"ssid_number":"1", "ft":[["1","2"],["c","d"]], "target":"save_filter"}';

$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);

if ("switch_callsignallog" == $arConfig['target'])
{
	setStr("Device.X_CISCO_COM_MTA.CallSignallingLogEnable", $arConfig['value'], true);
}
else if ("clear_callsignallog" == $arConfig['target'])
{
	setStr("Device.X_CISCO_COM_MTA.ClearCallSignallingLog", "true", true);
}
else if ("switch_DSXlog" == $arConfig['target'])
{
	setStr("Device.X_CISCO_COM_MTA.DSXLogEnable", $arConfig['value'], true);
}
else if ("clear_DSXlog" == $arConfig['target'])
{
	setStr("Device.X_CISCO_COM_MTA.ClearDSXLog", "true", true);
}

echo $jsConfig;	

?>
