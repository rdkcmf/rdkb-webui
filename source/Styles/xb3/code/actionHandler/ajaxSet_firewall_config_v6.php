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

//$_REQUEST['configInfo'] = '{"firewallLevel": "High", "block_http": "Enabled","block_icmp": "Enabled",
//                                         "block_multicast": "Disabled","block_peer": "Disabled","block_ident": "Disabled""} ';
$firewall_config = json_decode($_REQUEST['configInfo'], true);

if ( $firewall_config['firewallLevel'] == "Custom" )
{
	setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTPV6", $firewall_config['block_http'], false);
	setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTPsV6", $firewall_config['block_http'], false);

	setStr("Device.X_CISCO_COM_Security.Firewall.FilterAnonymousInternetRequestsV6", $firewall_config['block_icmp'], false);

	setStr("Device.X_CISCO_COM_Security.Firewall.FilterMulticastV6", $firewall_config['block_multicast'], false);

	setStr("Device.X_CISCO_COM_Security.Firewall.FilterP2PV6", $firewall_config['block_peer'], false);

	setStr("Device.X_CISCO_COM_Security.Firewall.FilterIdentV6", $firewall_config['block_ident'], false);
}

setStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevelV6", $firewall_config['firewallLevel'], true);
// sleep(3);
echo $_REQUEST['configInfo'];

?>
