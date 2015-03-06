<?php 

//$_REQUEST['configInfo'] = '{"SSID": "HOME-1FD9-5", "Channel": "3","SecurityMode": "WPA-PSK (TKIP)", Password": "12345678"}';
$wifi5G_config = json_decode($_REQUEST['configInfo'], true);

setStr("Device.WiFi.SSID.2.SSID", $wifi5G_config['SSID'], false);
setStr("Device.WiFi.Radio.2.Channel", $wifi5G_config['Channel'], false);
setStr("Device.WiFi.AccessPoint.2.Security.ModeEnabled", $wifi5G_config['SecurityMode'], false);
setStr("Device.WiFi.AccessPoint.2.Security.KeyPassphrase", $wifi5G_config['Password'], true);


?>
