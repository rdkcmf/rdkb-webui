<?php 

$ripInfo = json_decode($_REQUEST['ripInfo'], true);

function setRIPconfig($ripInfo){

	$authType = $ripInfo['AuthType'];

	setStr("Device.Routing.RIP.Enable", "true", false);
	setStr("Device.Routing.RIP.InterfaceSetting.1.Enable", "true", false);	

	setStr("Device.Routing.RIP.InterfaceSetting.1.Interface", $ripInfo['IfName'], false);	

	if($ripInfo['SendVer'] == "NA") {
		setStr("Device.Routing.RIP.InterfaceSetting.1.SendRA", "false", false);	
	} 
	else {
		setStr("Device.Routing.RIP.InterfaceSetting.1.SendRA", "true", false);	
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_SendVersion", $ripInfo['SendVer'], false);	
	}

	if($ripInfo['RecVer'] == "NA") {
		setStr("Device.Routing.RIP.InterfaceSetting.1.AcceptRA", "false", false);	
	} 
	else {
		setStr("Device.Routing.RIP.InterfaceSetting.1.AcceptRA", "true", false);	
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_ReceiveVersion", $ripInfo['RecVer'], false);	
	}
	
	setStr("Device.Routing.RIP.X_CISCO_COM_UpdateInterval", $ripInfo['Interval'], false);
	setStr("Device.Routing.RIP.X_CISCO_COM_DefaultMetric", $ripInfo['Metric'], false);

	if(!strcasecmp($authType, "SimplePassword")) {
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_SimplePassword", $ripInfo['auth_key'], false);
	}
	elseif (!strcasecmp($authType, "MD5")) {
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_Md5KeyValue", $ripInfo['auth_key'], false);
		setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_Md5KeyID", $ripInfo['auth_id'], false);		//doesn't work?
	}

	setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_AuthenticationType", $ripInfo['AuthType'], false);
	setStr("Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_Neighbor", $ripInfo['NeighborIP'], true);

}

setRIPconfig($ripInfo);

	
?>
