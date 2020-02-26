<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:
 Copyright 2016 RDK Management
 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at
 http://www.apache.org/licenses/LICENSE-2.0
 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/
?>
<?php include('../includes/utility.php') ?>
<?php include('../includes/actionHandlerUtility.php') ?>
<?php

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
$result='';
function PORTTEST($port,$arraySp,$arrayEp){
	if ( ($port>=$arraySp) && ($port<=$arrayEp) ) return 1;
	
	else return 0;
}
$validation = true;
if($validation) $validation = isValInArray($_POST['allowtype'], array('true', 'false', 'notset'));
if($validation)
	if(!($_POST['startIP'] == 'x' || $_POST['startIP'] == 'notset'))
		$validation = validIPAddr($_POST['startIP']);
if($validation)
	if(!($_POST['endIP'] == 'x' || $_POST['endIP'] == 'notset'))
		$validation = validIPAddr($_POST['endIP']);
//IPv6 can be 'x' or 'notset' as well
if($validation)
	if(!($_POST['startIPv6'] == 'x' || $_POST['startIPv6'] == 'notset'))
		$validation = validIPAddr($_POST['startIPv6']);
if($validation)
	if(!($_POST['endIPv6'] == 'x' || $_POST['endIPv6'] == 'notset'))
		$validation = validIPAddr($_POST['endIPv6']);
//if($validation) $validation = isValInArray($_POST['mso_mgmt'], array('true', 'false', 'notset'));
//if($validation) $validation = isValInArray($_POST['cus_mgmt'], array('true', 'false', 'notset'));
if($validation) $validation = isValInArray($_POST['https'], array('true', 'false', 'notset'));
//httpsport can only be 1025 ~ 65535
if($validation && ($_POST['httpsport'] != 'notset') && !($_POST['httpsport'] >= 1025 && $_POST['httpsport'] <= 65535)) 
	$validation = false;
if($validation) $validation = isValInArray($_POST['http'], array('true', 'false', 'notset'));
//httpsport can only be 1025 ~ 65535
if($validation && ($_POST['httpport'] != 'notset') && !($_POST['httpport'] >= 1025 && $_POST['httpport'] <= 65535)) 
	$validation = false;
if($validation) {
	//if(validatePort()){
	$httpsport=$_POST['httpsport'];
	$httpport=$_POST['httpport'];
	$rootObjName    = "Device.NAT.PortMapping.";
	$paramNameArray = array("Device.NAT.PortMapping.");                                                                                    
    $mapping_array  = array("Enable","ExternalPort", "ExternalPortEndRange");                                                              
    $portMappingValues = getParaValues($rootObjName, $paramNameArray, $mapping_array);                                                     
    foreach ($portMappingValues as $key) {                                                                                                 
        if($key["Enable"]=="true"){     
            $arraySPort = $key["ExternalPort"];                                                                                    
            $arrayEPort = $key["ExternalPortEndRange"];    
            $httpsportvalidation = PORTTEST($httpsport,$arraySPort,$arrayEPort);
            $httpportvalidation = PORTTEST($httpport,$arraySPort,$arrayEPort);
            if(($httpsportvalidation == 1) || ($httpportvalidation == 1) ){
            	$result =_("Conflict with other port. Please use a different port!");  
                break;                                           
                                                                                    
            }                                                                    
                                                                                     
    	}
    }
    $rootObjName    = "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.";
			$paramNameArray = array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.");
			$mapping_array  = array("Description", "TriggerProtocol", "TriggerPortStart", "TriggerPortEnd", "ForwardPortStart", "ForwardPortEnd");
			$portTriggerValues = getParaValues($rootObjName, $paramNameArray, $mapping_array);
			//$ids=explode(",",getInstanceIDs("Device.NAT.X_CISCO_COM_PortTriggers.Trigger."));
			foreach ($portTriggerValues as $key) {
				$arrayFsp = $key["TriggerPortStart"];
				$arrayFep = $key["TriggerPortEnd"];
				$arrayTsp = $key["ForwardPortStart"];
				$arratTep = $key["ForwardPortEnd"];
				$fphttptest=PORTTEST($httpport,$arrayFsp,$arrayFep);
				$tphttptest=PORTTEST($httpport,$arrayTsp,$arratTep);
				$fphttpstest=PORTTEST($httpsport,$arrayFsp,$arrayFep);
				$tphttpstest=PORTTEST($httpsport,$arrayTsp,$arratTep);
				//echo $fptest;

				if ($fphttptest==1 || $tphttptest==1 || $fphttpstest==1 ||  $tphttpstest==1) {
					$result =_("Conflict with other port. Please use a different port!");
					break;
				}
				
			}
	if($result==""){
		if ($_POST['allowtype']!="notset")	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.FromAnyIP",$_POST['allowtype'],true);
		if ($_POST['startIP']!="notset")	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.StartIp",$_POST['startIP'],true);
		if ($_POST['endIP']!="notset")		setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.EndIp",$_POST['endIP'],true);
		if ($_POST['startIPv6']!="notset")	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.StartIpV6",$_POST['startIPv6'],true);
		if ($_POST['endIPv6']!="notset")	setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.EndIpV6",$_POST['endIPv6'],true);
		//if ($_POST['mso_mgmt']!="notset")	setStr("Device.X_CISCO_COM_DeviceControl.EnableMsoRemoteMgmt",$_POST['mso_mgmt'],true);
		// put change port at the end of this script
		if ($_POST['https']!="notset")		setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.HttpsEnable",$_POST['https'],true);
		//if ($_POST['httpsport']!="notset")	setStr("Device.X_CISCO_COM_DeviceControl.HTTPSPort",$_POST['httpsport'],true);
		if ($_POST['http']!="notset")		setStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.HttpEnable",$_POST['http'],true);
		//if ($_POST['httpport']!="notset")	setStr("Device.X_CISCO_COM_DeviceControl.HTTPPort",$_POST['httpport'],true);
	        if($_POST['httpsport']!="notset" && $_POST['httpport']!="notset") {
	          setStr("Device.X_CISCO_COM_DeviceControl.HTTPPort",$_POST['httpport'],false);
	          setStr("Device.X_CISCO_COM_DeviceControl.HTTPSPort",$_POST['httpsport'],true);
	        }  
	       if($_POST['httpport']!="notset" && $_POST['httpsport']=="notset"){
	         setStr("Device.X_CISCO_COM_DeviceControl.HTTPPort",$_POST['httpport'],true);
	       }
	       if($_POST['httpsport']!="notset" && $_POST['httpport']=="notset"){
	         setStr("Device.X_CISCO_COM_DeviceControl.HTTPSPort",$_POST['httpsport'],true);
	       }
	}
	
}
echo htmlspecialchars(json_encode(trim($result)), ENT_NOQUOTES, 'UTF-8');
// sleep(10);
/*
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
?>
