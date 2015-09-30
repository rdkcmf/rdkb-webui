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

function array_trim($arr){
	$ret = array();
	foreach($arr as $v){
		$v = trim($v);
		if ("" != $v){
			array_push($ret, $v);
		}
	}
	return $ret;
}

if ($_POST['telnet']!="notset")		setStr("Device.X_CISCO_COM_DeviceControl.TelnetEnable",$_POST['telnet'],true);
if ($_POST['ssh']!="notset")		setStr("Device.X_CISCO_COM_DeviceControl.SSHEnable",$_POST['ssh'],true);

if ($_POST['allowtype']!="notset")	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.FromAnyIP",$_POST['allowtype'],true);
if ($_POST['startIP']!="notset")	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.StartIp",$_POST['startIP'],true);
if ($_POST['endIP']!="notset")		setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.EndIp",$_POST['endIP'],true);
/*
if ($_POST['startIP']!="notset" || $_POST['endIP']!="notset"){
	$dat = array();
	$ids = array_trim(explode(",", getInstanceIds("Device.UserInterface.X_CISCO_COM_RemoteAccess.iprange.")));
	$tag = "";
	
	// find the webui tagged index
	foreach ($ids as $i){
		if ("WEBCFG_IP" == getStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.iprange.$i.Desp")){
			$tag = $i;
			break;
		}
	}
	
	// if no webui preset entry, have to add one
	if ("" == $tag){
		addTblObj("Device.UserInterface.X_CISCO_COM_RemoteAccess.iprange.");
		sleep(1);
		$ids = array_trim(explode(",", getInstanceIds("Device.UserInterface.X_CISCO_COM_RemoteAccess.iprange.")));
		$tag = $ids[count($ids)-1];
		setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.iprange.$tag.Desp", "WEBCFG_IP", true);
	}
	
	// now add the data to webui entry
	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.iprange.$tag.StartIP", $_POST['startIP'], false);
	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.iprange.$tag.EndIP", $_POST['endIP'], true);
}
*/
if ($_POST['startIPv6']!="notset")	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.StartIpV6",$_POST['startIPv6'],true);
if ($_POST['endIPv6']!="notset")	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.EndIpV6",$_POST['endIPv6'],true);

if ($_POST['mso_mgmt']!="notset")	setStr("Device.X_CISCO_COM_DeviceControl.EnableMsoRemoteMgmt",$_POST['mso_mgmt'],true);
if ($_POST['cus_mgmt']!="notset")	setStr("Device.X_CISCO_COM_DeviceControl.EnableCusadminRemoteMgmt",$_POST['cus_mgmt'],true);

// put change port at the end of this script
if ($_POST['https']!="notset")		setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.HttpsEnable",$_POST['https'],true);
if ($_POST['httpsport']!="notset")	setStr("Device.X_CISCO_COM_DeviceControl.HTTPSPort",$_POST['httpsport'],true);
if ($_POST['http']!="notset")		setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.HttpEnable",$_POST['http'],true);
if ($_POST['httpport']!="notset")	setStr("Device.X_CISCO_COM_DeviceControl.HTTPPort",$_POST['httpport'],true);

// sleep(10);

?>
