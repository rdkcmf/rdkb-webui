<!-- $Id: nav.dory.php 3155 2010-01-06 19:36:01Z slemoine $ -->

<!--Nav-->

<?php 
/*
 *  set initial value for all pages to true(display)
 */
$local_ip_config  	= TRUE;
$firewall         	= TRUE;
$parental_control 	= TRUE;
$dmz              	= TRUE;
$port_forwarding  	= TRUE;
$port_triggering  	= TRUE;
$MoCA             	= TRUE;
$battery            	= TRUE;
$cordless_handset 	= FALSE;
$USB              	= FALSE;
$network_storage  	= FALSE;
$range_extenders  	= TRUE;
$radius_servers   	= FALSE;
$media_sharing    	= FALSE;
$file_sharing     	= FALSE;
$local_users      	= FALSE;
$remote_management  	= TRUE;  //for xb3, all user will have this page, but different content
$eMTA               	= TRUE;  //for mso/cusadmin
$routing          	= TRUE;  //for mso only
$email_notify		= TRUE;  //for mso only
$hs_port_forwarding 	= TRUE; //for mso only
$dynamic_dns        	= TRUE;  //for mso/cusadmin
$nat		        = FALSE;  //for mso/cusadmin
$password_change	= FALSE;  //for admin only
$wizard        		= TRUE;

if ($_DEBUG) {
	$media_sharing = TRUE;
}

/*
 * The difference between  bridge mode and router mode
 * In bridge mode, local ip config page, firewall page, parental control pages, 
 * routing page(mso), dmz page, wizard pages, port fowarding and port triggering pages removed
 */
if (isset($_SESSION['lanMode']) && $_SESSION["lanMode"] == "bridge-static") {
	$local_ip_config  	= FALSE;
	$firewall         	= FALSE;
	$parental_control 	= FALSE;
	$routing          	= FALSE;
	$dmz              	= FALSE;
	$port_forwarding  	= FALSE;
	$port_triggering  	= FALSE;
	$hs_port_forwarding = FALSE;
	$wizard		 		= FALSE;
}

if (isset($_SESSION['loginuser']) && $_SESSION['loginuser'] == 'cusadmin') {
	$routing		= FALSE;
	$email_notify		= FALSE;
	$hs_port_forwarding 	= FALSE;
}
elseif (isset($_SESSION['loginuser']) && $_SESSION['loginuser'] == 'admin') {
	$eMTA 			= FALSE;	
	$routing 		= FALSE;
	$email_notify		= FALSE;
	$dynamic_dns 		= FALSE;
	$hs_port_forwarding	= FALSE;
	$password_change	= TRUE;
}

/*
 * generate menu and submenu accroding to above configuration
 */
echo '<div id="nav">';
echo '<ul>';
echo '<li class="nav-gateway">';
	echo '<a role="menuitem"  title="click to toggle sub menu" class="top-level" href="at_a_glance.php">Gateway</a>';
	echo '<ul>';
	echo '<li class="nav-at-a-glance"><a role="menuitem"  href="at_a_glance.php">At a Glance</a></li>';
	if($email_notify) echo '<li class="nav-email-notification"><a role="menuitem"  href="email_notification.php">Email Notification</a></li>';
	echo '<li class="nav-connection"><a role="menuitem"  title="click to toggle sub menu"  href="javascript:;">Connection</a>';
		echo '<ul>';
		echo '<li class="nav-connection-status"><a role="menuitem"  href="connection_status.php">Status</a></li>';
		echo '<li class="nav-comcast-network"><a role="menuitem"  href="comcast_network.php">XFINITY Network</a></li>';
		if($local_ip_config) echo '<li class="nav-local-ip-network"><a role="menuitem"  href="local_ip_configuration.php">Local IP Network</a></li>';
		echo '<li class="nav-wifi-config"><a role="menuitem"  href="wireless_network_configuration.php">Wi-Fi</a></li>';
		if ($eMTA) {
			echo '<li class="nav-mta"><a role="menuitem"  title="click to toggle sub menu"  href="javascript:;">MTA</a>';
				echo '<ul style="padding-left:10px">';
				echo '<li class="nav-line-status"><a role="menuitem"  href="mta_Line_Status.php">Line Status</a></li>';
				echo '<li class="nav-mta-line-diagnostics"><a role="menuitem"  href="mta_Line_Diagnostics.php">Line Diagnostics</a></li>';
				echo '<li class="nav-service-sip"><a role="menuitem"  href="mta_sip_packet_log.php">SIP Packet Log</a></li>';
				echo '</ul>';
			echo '</li>';
			echo '<li class="nav-qos"><a role="menuitem"  href="qos.php">CallP/QoS</a></li>';
			echo '<li class="nav-comcast-voice"><a role="menuitem"  href="voice_quality_metrics.php">VQM</a></li>';
		}
		if($MoCA) echo '<li class="nav-moca"><a role="menuitem"  href="moca.php">MoCA</a></li>';
		echo '</ul>';
	echo '</li>';
	/*if($firewall) echo '<li class="nav-firewall"><a role="menuitem"  href="firewall_settings.php">Firewall</a></li>';*/
	if($firewall) echo '<li class="nav-firewall"><a role="menuitem"  title="click to toggle sub menu"  href="javascript:;">Firewall</a>
			<ul>
				<li class="nav-firewall-ipv4"><a role="menuitem"  href="firewall_settings_ipv4.php">IPv4</a></li>
				<li class="nav-firewall-ipv6"><a role="menuitem"  href="firewall_settings_ipv6.php">IPv6</a></li>
			</ul>	
		</li>';
	echo '<li class="nav-software"><a role="menuitem"  href="software.php">Software</a></li>';
	echo '<li class="nav-hardware"><a role="menuitem"  title="click to toggle sub menu"  href="javascript:;">Hardware</a>';
		echo '<ul>';
		echo '<li class="nav-system-hardware"><a role="menuitem"  href="hardware.php">System Hardware</a></li>';
		if($battery) echo '<li class="nav-battery"><a role="menuitem"  href="battery.php">Battery</a></li>';
		echo '<li class="nav-lan"><a role="menuitem"  href="lan.php">LAN</a></li>';
		echo '<li class="nav-wifi"><a role="menuitem"  href="wifi.php">Wireless</a></li>';
		if($USB) echo '<li class="nav-usb"><a role="menuitem"  href="usb.php">USB</a></li>';
		echo '</ul>';
	echo '</li>';
	if($wizard){
		if($password_change) echo '<li class="nav-wizard"><a role="menuitem"  href="wizard_step1.php">Wizard</a></li>';
		else echo '<li class="nav-wizard"><a role="menuitem"  href="wizard_step2.php">Wizard</a></li>';
	}
	echo '</ul>';
echo '</li>';

echo '<li class="nav-connected-devices">';
	echo '<a role="menuitem"  title="click to toggle sub menu"  class="top-level" href="connected_devices_computers.php">Connected Devices</a>';
	echo '<ul>';
	echo '<li class="nav-cdevices"><a role="menuitem"  href="connected_devices_computers.php">Devices</a></li>';
	if($cordless_handset) echo '<li class="nav-cordless-handsets"><a role="menuitem"  href="cordless_handsets.php">Cordless Handsets</a></li>';
	if($network_storage)  echo '<li class="nav-network-storage"><a role="menuitem"  href="network_storage.php">Network Storage</a></li>';
	if($range_extenders)  echo '<li class="nav-range-extenders"><a role="menuitem"  href="range_extenders.php">Range Extenders</a></li>';
	echo '</ul>';
echo '</li>';
	
if($parental_control){
 echo '<li class="nav-parental-control">';
	echo '<a role="menuitem"  title="click to toggle sub menu"  class="top-level" href="managed_sites.php">Parental Control</a>';
	echo '<ul>';
		echo '<li class="nav-sites"><a role="menuitem"  href="managed_sites.php">Managed Sites</a></li>';
		echo '<li class="nav-services"><a role="menuitem"  href="managed_services.php">Managed Services</a></li>';
		echo '<li class="nav-devices"><a role="menuitem"  href="managed_devices.php">Managed Devices</a></li>';
		echo '<li class="nav-parental-reports"><a role="menuitem"  href="parental_reports.php">Reports</a></li>';
	echo '</ul>';
echo '</li>';
}

echo '<li class="nav-advanced">';
	if ($_SESSION["lanMode"] == "bridge-static") echo '<a role="menuitem"  title="click to toggle sub menu"  class="top-level" href="dynamic_dns.php">Advanced</a>';
		else echo '<a role="menuitem"  title="click to toggle sub menu"  class="top-level" href="port_forwarding.php">Advanced</a>';
	echo '<ul>';
	if($port_forwarding) echo '<li class="nav-port-forwarding"><a role="menuitem"  href="port_forwarding.php">Port Forwarding</a></li>';
	if($hs_port_forwarding) echo '<li class="nav-HS-port-forwarding"><a role="menuitem"  href="hs_port_forwarding.php">HS Port Forwarding</a></li>';
	if($port_triggering) echo '<li class="nav-port-triggering"><a role="menuitem"  href="port_triggering.php">Port Triggering</a></li>';
	if($remote_management) echo '<li class="nav-remote-management"><a role="menuitem"  href="remote_management.php">Remote Management</a></li>';
	echo '<!--li class="nav-qos1"><a role="menuitem"  href="qos1.php">QoS</a></li-->';
	if($dmz) echo '<li class="nav-dmz"><a role="menuitem"  href="dmz.php">DMZ</a></li>';
	if($nat) echo '<li class="nav-nat"><a role="menuitem"  href="nat.php">NAT</a></li>';
	if($routing) echo '<li class="nav-routing"><a role="menuitem"  href="routing.php">Routing</a></li>';
	if($dynamic_dns) echo '<li class="nav-Dynamic-dns"><a role="menuitem"  href="dynamic_dns.php">Dynamic DNS</a></li>';
	echo '<li class="nav-device-discovery"><a role="menuitem"  href="device_discovery.php">Device Discovery</a></li>';
	if($radius_servers) echo '<li class="nav-radius-servers"><a role="menuitem"  href="radius_servers.php">Radius Servers</a></li>';
	
	if($media_sharing) {
		echo '<li class="nav-media-sharing"><a role="menuitem"  title="click to toggle sub menu"  href="javascript:;">Media Sharing</a>';
		echo '<ul>';
			echo '<li class="nav-dlna"><a role="menuitem"  title="click to toggle sub menu"  href="javascript:;">DLNA</a>';
				echo '<ul>';
					echo '<li class="nav-dlna-settings"><a role="menuitem"  href="dlna_settings.php">DLNA Settings</a></li>';
					echo '<li class="nav-dlna-media-servers"><a role="menuitem"  href="digital_media_servers.php">Digital Media Servers</a></li>';
					echo '<li class="nav-dlna-media-players"><a role="menuitem"  href="digital_media_players.php">Digital Media Players</a></li>';
					echo '<li class="nav-dlna-media-index"><a role="menuitem"  href="digital_media_index.php">Media Index</a></li>';
				echo '</ul>';
			echo '</li>';
		echo '</ul>';
		echo '</li>';
	}

	if($file_sharing) echo '<li class="nav-file-sharing"><a role="menuitem"  href="samba_server_config.php">File Sharing</a></li>';
	if($local_users)  echo '<li class="nav-local-users"><a role="menuitem"  href="local_users.php">Local Users</a></li>';
	echo '</ul>';
echo '</li>';

echo '<li class="nav-troubleshooting">';
	echo '<a role="menuitem"  title="click to toggle sub menu"  class="top-level" href="troubleshooting_logs.php">Troubleshooting</a>';
	echo '<ul>';
		echo '<li class="nav-logs"><a role="menuitem"  href="troubleshooting_logs.php">Logs</a></li>';
		echo '<li class="nav-diagnostic-tools"><a role="menuitem"  href="network_diagnostic_tools.php">Diagnostic Tools</a></li>';
		echo '<li class="nav-restore-reboot"><a role="menuitem"  href="restore_reboot.php">Reset/Restore Gateway</a></li>';
		if($password_change) echo '<li class="nav-password"><a role="menuitem"  href="password_change.php">Change Password</a></li>';
	echo '</ul>';
echo '</li>';
echo '</ul>';
echo '</div>';

?>
