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
