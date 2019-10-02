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
$modelName = getStr("Device.DeviceInfo.ModelName");

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
//$_POST['configInfo'] = {"firewallLevel": "Default", "block_http": "false", "block_icmp": "false", "block_multicast": "false", "block_peer": "false", "block_ident": "false"}
$firewall_config = json_decode($_POST['configInfo'], true);
$validation = true;
if($validation) $validation = isValInArray($firewall_config['firewallLevel'], array('None', 'Default', 'Custom'));
if ( $validation && $firewall_config['firewallLevel'] == "Custom" ){
    if($validation) $validation = isValInArray($firewall_config['block_http'], array("true", "false"));
      if($modelName != "X5001")
         {
       if($validation){
             $validation = isValInArray($firewall_config['block_icmp'], array("true", "false"));
                      }
         }
    if($validation) $validation = isValInArray($firewall_config['block_multicast'], array("true", "false"));
    if($validation) $validation = isValInArray($firewall_config['block_peer'], array("true", "false"));
    if($validation) $validation = isValInArray($firewall_config['block_ident'], array("true", "false"));
}
if($validation) {
	if ( $firewall_config['firewallLevel'] == "Custom" )
	{
		setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTPV6", $firewall_config['block_http'], true);
		setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTPsV6", $firewall_config['block_http'], true);
            if($modelName != "X5001") {
		setStr("Device.X_CISCO_COM_Security.Firewall.FilterAnonymousInternetRequestsV6", $firewall_config['block_icmp'], true);
                 }
		setStr("Device.X_CISCO_COM_Security.Firewall.FilterMulticastV6", $firewall_config['block_multicast'], true);
		setStr("Device.X_CISCO_COM_Security.Firewall.FilterP2PV6", $firewall_config['block_peer'], true);
		setStr("Device.X_CISCO_COM_Security.Firewall.FilterIdentV6", $firewall_config['block_ident'], true);
	}
	setStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevelV6", $firewall_config['firewallLevel'], true);
	// sleep(3);
}
echo htmlspecialchars($_POST['configInfo'], ENT_NOQUOTES, 'UTF-8');
?>
