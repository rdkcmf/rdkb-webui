<?php 

//$_REQUEST['configInfo'] = '{"firewallLevel": "High", "block_http": "Enabled","block_icmp": "Enabled",
//                                         "block_multicast": "Disabled","block_peer": "Disabled","block_ident": "Disabled""} ';
$firewall_config = json_decode($_REQUEST['configInfo'], true);

if ( $firewall_config['firewallLevel'] == "Custom" )
{
    if ( $firewall_config['block_http'] == "Enabled" )
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTP", "true", false);
		setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTPs", "true", false);
    }
    else
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTP", "false", false);
		setStr("Device.X_CISCO_COM_Security.Firewall.FilterHTTPs", "false", false);
    }    

    if ( $firewall_config['block_icmp'] == "Enabled" )
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterAnonymousInternetRequests", "true", false);
    }
    else
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterAnonymousInternetRequests", "false", false);
    }
    
    if ( $firewall_config['block_multicast'] == "Enabled" )
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterMulticast", "true", false);
    }
    else
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterMulticast", "false", false);
    }
    
    if ( $firewall_config['block_peer'] == "Enabled" )
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterP2P", "true", false);
    }
    else
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterP2P", "false", false);
    }
    
    if ( $firewall_config['block_ident'] == "Enabled" )
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterIdent", "true", false);
    }
    else
    {
        setStr("Device.X_CISCO_COM_Security.Firewall.FilterIdent", "false", false);
    }    
}

setStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevel", $firewall_config['firewallLevel'], true);
// sleep(3);
echo $_REQUEST['configInfo'];

?>
