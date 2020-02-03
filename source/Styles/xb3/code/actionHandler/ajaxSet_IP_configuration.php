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
$partnerId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
$ip_config = json_decode($_POST['configInfo'], true);
function isValidIP($ip, $ipRange){
	$longIP = ip2long($ip);
	if ($longIP == -1 || $longIP === FALSE) return false;
	else return ($longIP > ip2long($ipRange[0]) && $longIP < ip2long($ipRange[1]));
}
function ipRange($gwAddress, $subnetMask){
	$subnetMask_nr		= ip2long($subnetMask);
	$gateway_ip_start	= long2ip((ip2long($gwAddress) & $subnetMask_nr) + 1);
	$gateway_ip_end		= long2ip((ip2long($gwAddress) | ~$subnetMask_nr) - 1);
	return array($gateway_ip_start, $gateway_ip_end);
}
function valid_GW_IP($beginAddress, $endAddress, $gwAddress){
	if(explode('.', $gwAddress)[3] != '1') return false;
	$min	= ip2long($beginAddress);
	$max	= ip2long($endAddress);
	return ((ip2long($gwAddress) >= $min) && (ip2long($gwAddress) <= $max));
}
function isValidGW($gwAddress, $subnetMask, $beginAddress, $endAddress){
	//RFC1918 >> valid private IP range [10.0.0.1 ~ 10.255.255.253,\n172.16.0.1 ~ 172.31.255.253,\n192.168.0.1 ~ 192.168.255.253]
	//subnetMask
	if (ip2long($gwAddress) == -1 || ip2long($gwAddress) === FALSE) return false;
	if (explode('.', $gwAddress)[0] == '10') {
		//for classA network only these subnetMask are allowed
		if(!isValInArray($subnetMask, array("255.255.255.0", "255.255.0.0", "255.255.255.128", "255.0.0.0"))) return false;
		if(!valid_GW_IP('10.0.0.1', '10.255.255.1', $gwAddress)) return false;
		$ipRange = ipRange($gwAddress, $subnetMask);
	}
	else if (explode('.', $gwAddress)[0] == '172') {
		//for classB network only these subnetMask are allowed
		if(!isValInArray($subnetMask, array("255.255.255.0", "255.255.255.128", "255.255.0.0"))) return false;
		if(!valid_GW_IP('172.16.0.1', '172.31.255.1', $gwAddress)) return false;
		$ipRange = ipRange($gwAddress, $subnetMask);
	}
	else if (explode('.', $gwAddress)[0] == '192') {
		//for classC network only these subnetMask are allowed
		if(!isValInArray($subnetMask, array("255.255.255.0", "255.255.255.128"))) return false;
		//192.168.0.1 ~ 192.168.146.1 || 192.168.148.1 ~ 192.168.255.1
		if(!(valid_GW_IP('192.168.0.1', '192.168.146.1', $gwAddress) || valid_GW_IP('192.168.148.1', '192.168.244.1', $gwAddress) || valid_GW_IP('192.168.246.1', '192.168.255.1', $gwAddress))) return false;
		$ipRange = ipRange($gwAddress, $subnetMask);
	}
	else return false;
	//beginAddress
	if(!isValidIP($beginAddress, $ipRange)) return false;
	//endAddress
	if(!isValidIP($endAddress, $ipRange)) return false;
	return true;
}
if(!array_key_exists('IPv6', $ip_config)){
	$validation = true;
	if($validation) $validation = isValidGW($ip_config['Ipaddr'], $ip_config['Subnet_mask'], $ip_config['Dhcp_begin_addr'], $ip_config['Dhcp_end_addr']);
	if($validation) $validation = (isValInRange($ip_config['Dhcp_lease_time'], 120, 604195200) || $ip_config['Dhcp_lease_time'] == '-1');
	if($ip_config['Dhcp_begin_addr'] == $ip_config['Dhcp_end_addr']){
		$validation = false;
	}
	if($validation){
		//set ipv4 part
		setStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress", $ip_config['Ipaddr'], true);
		setStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask", $ip_config['Subnet_mask'], true);
		//20140523
		//set LanManagementEntry_ApplySettings after change LanManagementEntry table
		setStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry_ApplySettings", "true", true);
		setStr("Device.DHCPv4.Server.Pool.1.MinAddress", $ip_config['Dhcp_begin_addr'], false);
		setStr("Device.DHCPv4.Server.Pool.1.MaxAddress", $ip_config['Dhcp_end_addr'], false);
		setStr("Device.DHCPv4.Server.Pool.1.LeaseTime" , $ip_config['Dhcp_lease_time'], true);
	}
}
else{
	//set ipv6 part
	$state = $ip_config['Stateful'];
	$restore = $ip_config['restore'];
	if ($state == 'true') {//stateful
		$validation = true;
		if($validation) $validation = (preg_match("/^([0-9a-f]{1,4}:){3}[0-9a-f]{1,4}$/i", $ip_config['dhcpv6_begin_addr'])==1);
		if($validation) $validation = (preg_match("/^([0-9a-f]{1,4}:){3}[0-9a-f]{1,4}$/i", $ip_config['dhcpv6_end_addr'])==1);
		if($validation) $validation = (isValInRange($ip_config['dhcpv6_lease_time'], 120, 604195200) || $ip_config['dhcpv6_lease_time'] == '-1');
		if($validation){
	        getStr("Device.IP.Interface.1.IPv6Prefix.1."); //this line if a trick fix for Yan's framework bug, may delete in future 
			setStr("Device.RouterAdvertisement.InterfaceSetting.1.AdvManagedFlag", "true", true);
			setStr("Device.DHCPv6.Server.X_CISCO_COM_Type", "Stateful", true);
			setStr("Device.DHCPv6.Server.Pool.1.PrefixRangeBegin", $ip_config['dhcpv6_begin_addr'], false);
			setStr("Device.DHCPv6.Server.Pool.1.PrefixRangeEnd", $ip_config['dhcpv6_end_addr'], false);
			setStr("Device.DHCPv6.Server.Pool.1.LeaseTime", $ip_config['dhcpv6_lease_time'], true);
		}
	}
	else{//stateless
		setStr("Device.RouterAdvertisement.InterfaceSetting.1.AdvManagedFlag", "false", true);  
		setStr("Device.DHCPv6.Server.X_CISCO_COM_Type", "Stateless", true);
		if(strpos($partnerId, "sky-") !== false){
          		setStr("Device.X_RDKCENTRAL-COM_DeviceControl.LanManagementEntry.LanIpv6UlaEnable",$ip_config['ula_enable'], false);
          		setStr("Device.X_RDKCENTRAL-COM_DeviceControl.LanManagementEntry.LanIpv6UlaPrefix",$ip_config['ula_prefix'],false);
          }
	}
	if(strpos($partnerId, "sky-") !== false){
    		setStr("Device.X_RDKCENTRAL-COM_DeviceControl.LanManagementEntry.LanIpv6Enable",$ip_config['ipv6_enable'], true);
	}
	if ($restore == 'true'){
		$validation = true;
		if($validation) $validation = (preg_match("/^([0-9a-f]{1,4}:){3}[0-9a-f]{1,4}$/i", $ip_config['dhcpv6_begin_addr'])==1);
		if($validation) $validation = (preg_match("/^([0-9a-f]{1,4}:){3}[0-9a-f]{1,4}$/i", $ip_config['dhcpv6_end_addr'])==1);
		if($validation) $validation = (isValInRange($ip_config['dhcpv6_lease_time'], 120, 604195200) || $ip_config['dhcpv6_lease_time'] == '-1');
		if($validation){
			setStr("Device.RouterAdvertisement.InterfaceSetting.1.AdvManagedFlag", "false", true);
			if(strpos($partnerId, "sky-") !== false){
				setStr("Device.DHCPv6.Server.X_CISCO_COM_Type", "Stateless", true);
			}else{
				setStr("Device.DHCPv6.Server.X_CISCO_COM_Type", "Stateful", true);		
			}
			setStr("Device.DHCPv6.Server.Pool.1.PrefixRangeBegin", $ip_config['dhcpv6_begin_addr'], false);
			setStr("Device.DHCPv6.Server.Pool.1.PrefixRangeEnd", $ip_config['dhcpv6_end_addr'], false);
			setStr("Device.DHCPv6.Server.Pool.1.LeaseTime", $ip_config['dhcpv6_lease_time'], true);
		}
	}
}
?>
