<?php 

//$_REQUEST['configInfo'] = '{"SSID": "HOME-1FD9-5", "Channel": "3","SecurityMode": "WPA-PSK (TKIP)", Password": "12345678"}';
$wifi24G_config = json_decode($_REQUEST['configInfo'], true);

setStr("Device.WiFi.SSID.1.SSID", $wifi24G_config['SSID'], false);
setStr("Device.WiFi.Radio.1.Channel", $wifi24G_config['Channel'], false);
setStr("Device.WiFi.AccessPoint.1.Security.ModeEnabled", $wifi24G_config['SecurityMode'], false);
setStr("Device.WiFi.AccessPoint.1.Security.KeyPassphrase", $wifi24G_config['Password'], true);


?>
