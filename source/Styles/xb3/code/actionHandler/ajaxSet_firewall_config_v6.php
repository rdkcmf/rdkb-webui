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
