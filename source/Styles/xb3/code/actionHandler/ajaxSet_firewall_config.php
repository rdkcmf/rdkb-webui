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
<?php include('../includes/actionHandlerUtility.php') ?>
<?php

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
//$_POST['configInfo'] = '{"firewallLevel": "High", "block_http": "Enabled","block_icmp": "Enabled",
//                                         "block_multicast": "Disabled","block_peer": "Disabled","block_ident": "Disabled""} ';
$firewall_config = json_decode($_POST['configInfo'], true);
$validation = true;
if($validation) $validation = isValInArray($firewall_config['firewallLevel'], array('High', 'Medium', 'Low', 'Custom', 'None'));
if ( $validation && $firewall_config['firewallLevel'] == "Custom" ){
    if($validation) $validation = isValInArray($firewall_config['block_http'], array("Enabled", "Disabled"));
    if($validation) $validation = isValInArray($firewall_config['block_icmp'], array("Enabled", "Disabled"));
    if($validation) $validation = isValInArray($firewall_config['block_multicast'], array("Enabled", "Disabled"));
    if($validation) $validation = isValInArray($firewall_config['block_peer'], array("Enabled", "Disabled"));
    if($validation) $validation = isValInArray($firewall_config['block_ident'], array("Enabled", "Disabled"));
}
if($validation) {
    if ( $firewall_config['firewallLevel'] == "Custom" )
    {
        if ( $firewall_config['block_http'] == "Enabled" )
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTP", "true", true);
    		setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTPs", "true", true);
        }
        else
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTP", "false", true);
    		setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTPs", "false", true);
        }    
    	//sleep(1);
        if ( $firewall_config['block_icmp'] == "Enabled" )
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterAnonymousInternetRequests", "true", true);
        }
        else
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterAnonymousInternetRequests", "false", true);
        }
        //sleep(1);
        if ( $firewall_config['block_multicast'] == "Enabled" )
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterMulticast", "true", true);
        }
        else
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterMulticast", "false", true);
        }
        //sleep(1);
        if ( $firewall_config['block_peer'] == "Enabled" )
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterP2P", "true", true);
        }
        else
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterP2P", "false", true);
        }
        //sleep(1);
        if ( $firewall_config['block_ident'] == "Enabled" )
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterIdent", "true", true);
        }
        else
        {
            setStr("Device.X_CISCO_COM_Security.Firewall.FilterIdent", "false", true);
        } 
    	//sleep(1);   
    }
    setStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevel", $firewall_config['firewallLevel'], true);
    $_SESSION["sta_fire"] = $firewall_config['firewallLevel'];
    // sleep(3);
}
echo htmlspecialchars($_POST['configInfo'], ENT_NOQUOTES, 'UTF-8');
?>