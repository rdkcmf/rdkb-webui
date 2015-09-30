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

if ("save_iq" == $_POST['target'])
{
	setStr("Device.X_CISCO_COM_MTA.Dect.Enable", $_POST['cat_iq'], true);
}
else if ("save_pin" == $_POST['target'])
{
	setStr("Device.X_CISCO_COM_MTA.Dect.PIN", $_POST['cat_pin'], true);
}
else if ("save_tn" == $_POST['target'])
{
	$arConfig = json_decode($_POST['cat_tn'], true);
	foreach($arConfig as $val){
		setStr("Device.X_CISCO_COM_MTA.Dect.Handsets.$val[0].SupportedTN", $val[1], true);
	}
}
else if ("register" == $_POST['target'])
{
	if ("noChange" != $_POST['reg_mode']){
		setStr("Device.X_CISCO_COM_MTA.Dect.RegistrationMode", $_POST['reg_mode'], true);
	}
	echo $_POST['reg_mode'];
}
else if ("deregister" == $_POST['target'])
{
	setStr("Device.X_CISCO_COM_MTA.Dect.DeregisterDectHandset", $_POST['dereg_id'], true);
}

// print_r($arConfig);

?>
