<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2018 RDK Management

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
<!-- $Id: nav.dory.php 3155 2010-01-06 19:36:01Z slemoine $ -->
<!--Nav-->
<?php 
/*
 *  set initial value for all pages to true(display)
 */
$partnerId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
$modelName = getStr("Device.DeviceInfo.ModelName");
$local_ip_config  	= TRUE;
$firewall         	= TRUE;
$parental_control 	= TRUE;
$dmz              	= TRUE;
$port_forwarding  	= TRUE;
$port_triggering  	= TRUE;
$MoCA             	= TRUE;
$battery            	= TRUE;
$radius_servers   	= FALSE;
$local_users      	= FALSE;
$remote_management  	= TRUE;		//for xb3, all user will have this page, but different content
$eMTA               	= TRUE;		//for mso
$routing          	= TRUE;		//for mso
$hs_port_forwarding 	= TRUE;		//for mso
$dynamic_dns        	= TRUE;		//for mso
$nat		        = FALSE;	//for mso
$wifi_spectrum_analyzer = TRUE;
$password_change	= FALSE;	//for admin only
$wifi_spec_analyzer	= TRUE;
$advanced_tab		= TRUE;
$wan_network        = TRUE;
$moca_diagnostics   = TRUE;
$voice_Dig = FALSE;
$partnersId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
$voice_dev = getStr('Device.DeviceInfo.SoftwareVersion');
$modelName= getStr("Device.DeviceInfo.ModelName");
/*
 * The difference between  bridge mode and router mode
 * In bridge mode, local ip config page, firewall page, parental control pages, 
 * routing page(mso), dmz page, wizard pages, port fowarding and port triggering pages removed
 */
if (isset($_SESSION['lanMode']) && $_SESSION["lanMode"] == "bridge-static") {
	$advanced_tab		= FALSE;
	$local_ip_config  	= FALSE;
	$firewall         	= FALSE;
	$parental_control 	= FALSE;
	$routing          	= FALSE;
	$dmz              	= FALSE;
	$port_forwarding  	= FALSE;
	$port_triggering  	= FALSE;
	$hs_port_forwarding = FALSE;
	$MoCA				= TRUE;
	$moca_diagnostics   = FALSE;
	$wifi_spec_analyzer	= FALSE;
}
if (isset($_SESSION['loginuser']) && $_SESSION['loginuser'] == 'admin') {
	$eMTA 			= FALSE;	
	$routing 		= FALSE;
	$nat				= FALSE;
	$dynamic_dns 		= FALSE;
	$hs_port_forwarding	= FALSE;
	$password_change	= TRUE;
}

if (PREPAID == true){
	$eMTA = FALSE;
}

if (strpos($partnerId, "sky-") === false) {
	/* Grab XBB or other MTA Legacy Battery Install Status */
	$batteryInstalled = getStr("Device.X_CISCO_COM_MTA.Battery.Installed");

	/* Show Battery Icon based on XBB or other MTA Legacy Battery Install Status */
	if (strstr($batteryInstalled, "true"))  {
	  $battery = TRUE;
	}
	else {
	  $battery = FALSE;  
	}
}
else {
/* Turn off Battery and MoCA based on Partner devices */
	$moca_diagnostics   = FALSE;
    $MoCA = FALSE;
    $battery = FALSE;
    if(strpos($voice_dev, "_DEV_") !== false){ $voice_Dig = TRUE;}
    else{$voice_Dig = FALSE;}
}

/*
 * generate menu and submenu accroding to above configuration
 */
$allowEthWan= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.AllowEthernetWAN");
$autoWanEnable= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_AutowanFeatureSupport");
echo '<div id="nav">';
echo '<ul>';
echo '<li class="nav-gateway">';
	echo '<a role="menuitem"  title="'._("click to toggle sub menu").'" class="top-level" href="at_a_glance.php">'._("Gateway").'</a>';
	echo '<ul>';
	echo '<li class="nav-at-a-glance"><a role="menuitem"  href="at_a_glance.php">'._("At a Glance").'</a></li>';
	echo '<li class="nav-connection"><a role="menuitem"  title="'._("click to toggle sub menu").'"  href="javascript:;">'._("Connection").'</a>';
		echo '<ul>';
		echo '<li class="nav-connection-status"><a role="menuitem"  href="connection_status.php">'._("Status").'</a></li>';
		$Connection_MSOmenu = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.Connection.MSOmenu");
		echo '<li class="nav-gateway-network"><a role="menuitem"  href="network_setup.php">'.$Connection_MSOmenu.'</a></li>';
		if($local_ip_config) echo '<li class="nav-local-ip-network"><a role="menuitem"  href="local_ip_configuration.php">'._("Local IP Network").'</a></li>';
		echo '<li class="nav-wifi-config"><a role="menuitem"  href="wireless_network_configuration.php">'._("Wi-Fi").'</a></li>';
		if ($eMTA) {
			echo '<li class="nav-mta"><a role="menuitem"  title="'._("click to toggle sub menu").'"  href="javascript:;">'._("MTA").'</a>';
				echo '<ul style="padding-left:10px">';
				echo '<li class="nav-line-status"><a role="menuitem"  href="mta_Line_Status.php">'._("Line Status").'</a></li>';
				echo '<li class="nav-mta-line-diagnostics"><a role="menuitem"  href="mta_Line_Diagnostics.php">'._("Line Diagnostics").'</a></li>';
				echo '<li class="nav-service-sip"><a role="menuitem"  href="mta_sip_packet_log.php">'._("SIP Packet Log").'</a></li>';
				echo '</ul>';
			echo '</li>';
			echo '<li class="nav-qos"><a role="menuitem"  href="qos.php">'._("CallP/QoS").'</a></li>';
			echo '<li class="nav-gateway-voice"><a role="menuitem"  href="voice_quality_metrics.php">'._("VQM").'</a></li>';
		}
		if ($MoCA) {
		  echo '<li class="nav-moca"><a role="menuitem"  href="moca.php">'._("MoCA").'</a></li>';
		}
		if((($autoWanEnable=="true") || ($allowEthWan=="true")) && (($modelName=="CGM4140COM") || ($modelName=="CGM4331COM") || ($modelName=="TG4482A"))){
			if($wan_network) echo '<li class="nav-wan-network"><a role="menuitem"  href="wan_network.php">'._("WAN Network").'</a></li>';
		}
		echo '</ul>';
	echo '</li>';
	/*if($firewall) echo '<li class="nav-firewall"><a role="menuitem"  href="firewall_settings.php">Firewall</a></li>';*/
	if($firewall) echo '<li class="nav-firewall"><a role="menuitem"  title="'._("click to toggle sub menu").'"  href="javascript:;">'._("Firewall").'</a>
			<ul>
				<li class="nav-firewall-ipv4"><a role="menuitem"  href="firewall_settings_ipv4.php">'._("IPv4").'</a></li>
				<li class="nav-firewall-ipv6"><a role="menuitem"  href="firewall_settings_ipv6.php">'._("IPv6").'</a></li>
			</ul>	
		</li>';
	echo '<li class="nav-software"><a role="menuitem"  href="software.php">'._("Software").'</a></li>';
	echo '<li class="nav-hardware"><a role="menuitem"  title="'._("click to toggle sub menu").'"  href="javascript:;">'._("Hardware").'</a>';
		echo '<ul>';
		echo '<li class="nav-system-hardware"><a role="menuitem"  href="hardware.php">'._("System Hardware").'</a></li>';
		if($battery) echo '<li class="nav-battery"><a role="menuitem"  href="battery.php">'._("Battery").'</a></li>';
		echo '<li class="nav-lan"><a role="menuitem"  href="lan.php">'._("LAN").'</a></li>';
		echo '<li class="nav-wifi"><a role="menuitem"  href="wifi.php">'._("Wireless").'</a></li>';
		echo '</ul>';
	echo '</li>';
	echo '</ul>';
echo '</li>';
echo '<li class="nav-connected-devices">';
	echo '<a role="menuitem"  title="'._("click to toggle sub menu").'"  class="top-level" href="connected_devices_computers.php">'._("Connected Devices").'</a>';
	echo '<ul>';
	echo '<li class="nav-cdevices"><a role="menuitem"  href="connected_devices_computers.php">'._("Devices").'</a></li>';
	echo '</ul>';
echo '</li>';
if($parental_control){
 echo '<li class="nav-parental-control">';
	if($partnerId =="cox"){
		echo '<a role="menuitem"  title="'._("click to toggle sub menu").'"  class="top-level" href="managed_services.php">'._("Parental Control").'</a>';
	} else {
		echo '<a role="menuitem"  title="'._("click to toggle sub menu").'"  class="top-level" href="managed_sites.php">'._("Parental Control").'</a>';
	}
	echo '<ul>';
	if($partnerId !="cox"){
		echo '<li class="nav-sites"><a role="menuitem"  href="managed_sites.php">'._("Managed Sites").'</a></li>';
	}
		echo '<li class="nav-services"><a role="menuitem"  href="managed_services.php">'._("Managed Services").'</a></li>';
		echo '<li class="nav-devices"><a role="menuitem"  href="managed_devices.php">'._("Managed Devices").'</a></li>';
		echo '<li class="nav-parental-reports"><a role="menuitem"  href="parental_reports.php">'._("Reports").'</a></li>';
	echo '</ul>';
echo '</li>';
}
if($advanced_tab) {
	echo '<li class="nav-advanced">';
		echo '<a role="menuitem"  title="'._("click to toggle sub menu").'"  class="top-level" href="port_forwarding.php">'._("Advanced").'</a>';
		echo '<ul>';
		if($port_forwarding) echo '<li class="nav-port-forwarding"><a role="menuitem"  href="port_forwarding.php">'._("Port Forwarding").'</a></li>';
		if($hs_port_forwarding) echo '<li class="nav-HS-port-forwarding"><a role="menuitem"  href="hs_port_forwarding.php">'._("HS Port Forwarding").'</a></li>';
		if($port_triggering) echo '<li class="nav-port-triggering"><a role="menuitem"  href="port_triggering.php">'._("Port Triggering").'</a></li>';
		if($remote_management) echo '<li class="nav-remote-management"><a role="menuitem"  href="remote_management.php">'._("Remote Management").'</a></li>';
		echo '<!--li class="nav-qos1"><a role="menuitem"  href="qos1.php">'._("QoS").'</a></li-->';
		if($dmz) echo '<li class="nav-dmz"><a role="menuitem"  href="dmz.php">'._("DMZ").'</a></li>';
		if($nat) echo '<li class="nav-nat"><a role="menuitem"  href="nat.php">'._("NAT").'</a></li>';
		if($routing) echo '<li class="nav-routing"><a role="menuitem"  href="routing.php">'._("Routing").'</a></li>';
		if($dynamic_dns) echo '<li class="nav-Dynamic-dns"><a role="menuitem"  href="dynamic_dns.php">'._("Dynamic DNS").'</a></li>';
		echo '<li class="nav-device-discovery"><a role="menuitem"  href="device_discovery.php">'._("Device Discovery").'</a></li>';
		if($radius_servers) echo '<li class="nav-radius-servers"><a role="menuitem"  href="radius_servers.php">'._("Radius Servers").'</a></li>';
		if($local_users)  echo '<li class="nav-local-users"><a role="menuitem"  href="local_users.php">'._("Local Users").'</a></li>';
		echo '</ul>';
	echo '</li>';
}
echo '<li class="nav-troubleshooting">';
	echo '<a role="menuitem"  title="'._("click to toggle sub menu").'"  class="top-level" href="troubleshooting_logs.php">'._("Troubleshooting").'</a>';
	echo '<ul>';
		echo '<li class="nav-logs"><a role="menuitem"  href="troubleshooting_logs.php">'._("Logs").'</a></li>';
		echo '<li class="nav-diagnostic-tools"><a role="menuitem"  href="network_diagnostic_tools.php">'._("Diagnostic Tools").'</a></li>';
		if($wifi_spec_analyzer) echo '<li class="nav-wifi-spectrum-analyzer"><a role="menuitem"  href="wifi_spectrum_analyzer.php">'._("Wi-Fi Spectrum Analyzer").'</a></li>';
		if($moca_diagnostics) echo '<li class="nav-moca-diagnostics"><a role="menuitem"  href="moca_diagnostics.php">'._("MoCA Diagnostics").'</a></li>';
		echo '<li class="nav-restore-reboot"><a role="menuitem"  href="restore_reboot.php">'._("Reset/Restore Gateway").'</a></li>';
		if($password_change) echo '<li class="nav-password"><a role="menuitem"  href="password_change.php">'._("Change Password").'</a></li>';
	echo '</ul>';
		echo '</li>';

		if($voice_Dig){
		echo '<li class="nav-voice">';
		echo '<a role="menuitem"  title="click to toggle sub menu" class="top-level" href="Voip_SipBasic_GlobalParamaters.php">'._("Voice Diagnostics").'</a>';
		echo '<ul>';
			echo '<li class="nav-voice-sip"><a role="menuitem"  title="click to toggle sub menu"  href="javascript:;">SIP Basic Setup</a>';
			    echo '<ul>';
					echo '<li class="nav-voice-sip-global"><a role="menuitem"  href="Voip_SipBasic_GlobalParamaters.php">Global Parameters</a></li>';
					echo '<li class="nav-voice-sip-service"><a role="menuitem"  href="Voip_SipBasic_ServiceProvider.php">SIP Service Provider</a></li>';
				echo '</ul>';
			echo '</li>';
			echo '<li class="nav-voice-advance"><a role="menuitem"  title="click to toggle sub menu"  href="javascript:;">Advance Setup</a>
					<ul>
						<li class="nav-voice-advance-service"><a role="menuitem"  href="Voip_SipAdvanced_ServiceProvider.php">Adv Service Provider</a></li>
					</ul>
				</li>';
			echo '<li class="nav-voice-debug"><a role="menuitem"  title="click to toggle sub menu"  href="javascript:;">Debug</a>';
			    echo '<ul>';
					echo '<li class="nav-voice-debug-global"><a role="menuitem"  href="Voip_Debug_GlobalParameter.php">Global Parameters</a></li>';
					echo '<li class="nav-voice-debug-service"><a role="menuitem"  href="Voip_Debug_ServiceProvider.php">Service Provider</a></li>';
				echo '</ul>';
			echo '</li>';
		echo '</ul>';
                echo '</li>';
              }
echo '</ul>';
echo '</div>';
?>
