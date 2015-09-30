<!--
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2015 RDK Management

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
-->
<?php 

//$_REQUEST['configInfo'] = '{"SSID": "HOME-1FD9-5", "Channel": "3","SecurityMode": "WPA-PSK (TKIP)", Password": "12345678"}';
$wifi24G_config = json_decode($_REQUEST['configInfo'], true);

setStr("Device.WiFi.SSID.1.SSID", $wifi24G_config['SSID'], false);
setStr("Device.WiFi.Radio.1.Channel", $wifi24G_config['Channel'], false);
setStr("Device.WiFi.AccessPoint.1.Security.ModeEnabled", $wifi24G_config['SecurityMode'], false);
setStr("Device.WiFi.AccessPoint.1.Security.KeyPassphrase", $wifi24G_config['Password'], true);


?>
