<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<style type="text/css">

#content {
	display: none;
}

</style>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Connection > XFINITY Network", "nav-comcast-network");

	if ("cusadmin" == "<?php echo $_SESSION["loginuser"]; ?>"){
		$(".div_cm").remove();
	}
	else if ("admin" == "<?php echo $_SESSION["loginuser"]; ?>"){
		$(".div_cm").remove();
		$(".div_mta").remove();
	}
	
	// now we can show target content
	$("#content").show();

});
</script>

<?php

function div_mod($n, $m)
{
	if (!is_numeric($n) || !is_numeric($m) || (0==$m)){
		return array(0, 0);
	}	
	for($i=0; $n >= $m; $i++){
		$n = $n - $m;
	}	
	return array($i, $n);
}

function sec2dhms($sec)
{
	$tmp = div_mod($sec, 24*60*60);
	$day = $tmp[0];
	$tmp = div_mod($tmp[1], 60*60);
	$hor = $tmp[0];
	$tmp = div_mod($tmp[1],    60);
	$min = $tmp[0];
	return "D: $day H: $hor M: $min S: $tmp[1]";
}

$fistUSif = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");
$cmStatus = getStr("Device.X_CISCO_COM_CableModem.CMStatus");

?>

<div id="content">

<h1>Gateway > Connection > XFINITY Network</h1>

<div id="educational-tip">
	<p class="tip">View technical information related to your XFINITY network connection.</p>
	<p class="hidden">You may need this information if you contact Comcast for troubleshooting assistance.</p>
</div>

<div class="module forms">
	<h2>XFINITY Network</h2>
	<div class="form-row">
		<span class="readonlyLabel">Internet:</span>
		<span class="value"><?php echo ("OPERATIONAL"==$cmStatus) ? "Active" : "Inactive";?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Local time:</span>
		<span class="value"><?php echo getStr("Device.Time.CurrentLocalTime");?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">System Uptime:</span>
		<span class="value">
		<?php
			$sec = getStr("Device.DeviceInfo.UpTime");
			$tmp = div_mod($sec, 24*60*60);
			$day = $tmp[0];
			$tmp = div_mod($tmp[1], 60*60);
			$hor = $tmp[0];
			$tmp = div_mod($tmp[1],    60);
			$min = $tmp[0];
			echo $day." days ".$hor."h: ".$min."m: ".$tmp[1]."s";
		?>
		</span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">WAN IP Address (IPv4):</span>
		<span class="value"><?php echo getStr($fistUSif."IPv4Address.1.IPAddress");?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">WAN Default Gateway Address (IPv4):</span> <span class="value">
		<?php
			//echo getStr("Device.Routing.Router.1.IPv4Forwarding.1.GatewayIPAddress");
			/* For BWG, we just use the DHCP GW received from upstream as the wan side GW */
			echo getStr("Device.DHCPv4.Client.1.IPRouters");
		?>
		</span>
	</div>		
	<div class="form-row odd">
		<span class="readonlyLabel">WAN IP Address (IPv6):</span> <span class="value">
		<?php
		$ids = explode(",", getInstanceIds($fistUSif."IPv6Address."));
		foreach ($ids as $i){
			$val = getStr($fistUSif."IPv6Address.$i.IPAddress");
			if (!strstr($val, "fe80::")){
				echo $val;
				break;
			}
		}
		?>
		</span>
	</div>	
	<div class="form-row ">
		<span class="readonlyLabel">WAN Default Gateway Address (IPv6):</span> <span class="value">
		<?php
		$ids = explode(",", getInstanceIds("Device.Routing.Router.1.IPv6Forwarding."));
		foreach ($ids as $i){
			$val1 = getStr("Device.Routing.Router.1.IPv6Forwarding.$i.DestIPPrefix");
			$val2 = getStr("Device.Routing.Router.1.IPv6Forwarding.$i.Interface");
			if ("::/0"==$val1 && $fistUSif.""==$val2){
				echo getStr("Device.Routing.Router.1.IPv6Forwarding.$i.NextHop");
				break;
			}
		}
		?>		
		</span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Delegated prefix (IPv6):</span> <span class="value">
		<?php
		$ids = explode(",", getInstanceIds($fistUSif."IPv6Prefix."));
		echo getStr($fistUSif."IPv6Prefix.$ids[0].Prefix");
		?>		
		</span>
	</div>			
	<div class="form-row ">
		<span class="readonlyLabel">Primary DNS Server (IPv4):</span> <span class="value">
		<?php
		$ids    = explode(",", getInstanceIds("Device.DNS.Client.Server."));
		$dns_v4 = array();
		$dns_v6 = array();
		foreach ($ids as $i){
			$val = getStr("Device.DNS.Client.Server.$i.DNSServer");
			if (strstr($val, ".")){
				if(strstr($val, "127.0.0.1")) continue;
				array_push($dns_v4, $val);
			}
			else{
				array_push($dns_v6, $val);
			}
		}
		if (isset($dns_v4[0])) echo $dns_v4[0];
		?>
		</span>
	</div>	
	<div class="form-row odd">
		<span class="readonlyLabel">Secondary DNS Server (IPv4):</span> <span class="value"><?php if (isset($dns_v4[1])) echo $dns_v4[1];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">Primary DNS Server (IPv6):</span> <span class="value"><?php if (isset($dns_v6[0])) echo $dns_v6[0];?></span>
	</div>	
	<div class="form-row odd">
		<span class="readonlyLabel">Secondary DNS Server (IPv6):</span> <span class="value"><?php if (isset($dns_v6[1])) echo $dns_v6[1];?></span>
	</div>		
	<div class="form-row ">
		<span class="readonlyLabel">WAN Link Local Address (IPv6):</span>
		<span class="value">
		<?php
		$ids = explode(",", getInstanceIds($fistUSif."IPv6Address."));
		foreach ($ids as $i){
			$val = getStr($fistUSif."IPv6Address.$i.IPAddress");
			if (strstr($val, "fe80::")){
				echo $val;
				break;
			}
		}
		?>
		</span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">DHCP Client (IPv4):</span>
		<span class="value"><?php echo ("DHCP"==getStr("Device.X_CISCO_COM_DeviceControl.WanAddressMode")) ? "Enabled" : "Disabled";?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">DHCP Client (IPv6):</span> <span class="value">
		<?php echo ("true"==getStr("Device.DHCPv6.Client.1.Enable")) ? "Enabled" : "Disabled";?>
		</span>
	</div>	
	<div class="form-row odd">
		<span class="readonlyLabel">DHCP Lease Expire Time (IPv4):</span>
		<span class="value">
		<?php
			$sec = getStr("Device.DHCPv4.Client.1.LeaseTimeRemaining");
			$tmp = div_mod($sec, 24*60*60);
			$day = $tmp[0];
			$tmp = div_mod($tmp[1], 60*60);
			$hor = $tmp[0];
			$tmp = div_mod($tmp[1],    60);
			$min = $tmp[0];
			echo $day."d:".$hor."h:".$min."m";
		?>
		</span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">DHCP Lease Expire Time (IPv6):</span> <span class="value">
		<?php
		$ids = explode(",", getInstanceIds($fistUSif."IPv6Address."));
		foreach ($ids as $i){
			$val = getStr($fistUSif."IPv6Address.$i.IPAddress");
			if (!strstr($val, "fe80::")){
				$sec = getStr($fistUSif."IPv6Address.$i.X_CISCO_COM_PreferredLifetime");
				$tmp = div_mod($sec, 24*60*60);
				$day = $tmp[0];
				$tmp = div_mod($tmp[1], 60*60);
				$hor = $tmp[0];
				$tmp = div_mod($tmp[1],    60);
				$min = $tmp[0];
				echo $day."d:".$hor."h:".$min."m";
				break;
			}
		}
		// echo $fistUSif."IPv6Address.$i.X_Comcast_com_LeaseTime";
		?>		
		</span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">WAN MAC:</span>
		<span class="value"><?php echo strtoupper(getStr(getStr($fistUSif."LowerLayers").".MACAddress")); ?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">eMTA MAC:</span>
		<span class="value"><?php echo strtoupper(getStr("Device.X_CISCO_COM_MTA.MACAddress"));?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">CM MAC:</span>
		<span class="value"><?php echo strtoupper(getStr("Device.X_CISCO_COM_CableModem.MACAddress"));?></span>
	</div>
</div>

<?php

$initStatus = array();
if ("NOT_READY" == $cmStatus) {
	$initStatus = array("NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted");
}
elseif ("NOT_SYNCHRONIZED" == $cmStatus) {
	$initStatus = array("Complete", "NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted");
}
elseif ("EAE_IN_PROGRESS" == $cmStatus) {
	$initStatus = array("Complete", "InProgress", "NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted");
}
elseif ("US_PARAMETERS_ACQUIRED" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted");
}
elseif ("RANGING_IN_PROGRESS" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "InProgress", "NotStarted", "NotStarted", "NotStarted", "NotStarted");
}
elseif ("RANGING_COMPLETE" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "Complete", "NotStarted", "NotStarted", "NotStarted", "NotStarted");
}
elseif ("DHCPV4_IN_PROGRESS" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "Complete", "InProgress", "NotStarted", "NotStarted", "NotStarted");
}
elseif ("DHCPV4_COMPLETE" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "Complete", "Complete", "NotStarted", "NotStarted", "NotStarted");
}
elseif ("BPI_INIT" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "Complete", "Complete", "InProgress", "NotStarted", "NotStarted");
}
elseif ("TOD_ESTABLISHED" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "Complete", "Complete", "Complete", "NotStarted", "NotStarted");
}
elseif ("DS_TOPOLOGY_RESOLUTION_IN_PROGRESS" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "Complete", "Complete", "Complete", "InProgress", "NotStarted");
}
elseif ("CONFIG_FILE_DOWNLOAD_COMPLETE" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "Complete", "Complete", "Complete", "Complete", "NotStarted");
}
elseif ("RANGING_IN_PROGRESS" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "Complete", "Complete", "Complete", "Complete", "InProgress");
}
elseif ("REGISTRATION_COMPLETE" == $cmStatus || "OPERATIONAL" == $cmStatus) {
	$initStatus = array("Complete", "Complete", "Complete", "Complete", "Complete", "Complete", "Complete");
}
else {
	$initStatus = array("NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted", "NotStarted");
}

?>

<div class="module forms">
	<h2>Initialization Procedure</h2>
	<div class="form-row ">
		<span class="readonlyLabel">Initialize Hardware:</span>
		<span class="value"><?php echo $initStatus[0];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Acquire Downstream Channel:</span>
		<span class="value"><?php echo $initStatus[1];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">Upstream Ranging:</span>
		<span class="value"><?php echo $initStatus[2];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">DHCP bound:</span>
		<span class="value"><?php echo $initStatus[3];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">Set Time-of-Day:</span>
		<span class="value"><?php echo $initStatus[4];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Configuration File Download:</span>
		<span class="value"><?php echo $initStatus[5];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">Registration:</span>
		<span class="value"><?php echo $initStatus[6];?></span>
	</div>
</div>

<?php
$cm_param = array(
	"IPAddress"				=> "Device.X_CISCO_COM_CableModem.IPAddress",
	"SubnetMask"			=> "Device.X_CISCO_COM_CableModem.SubnetMask",
	"Gateway"				=> "Device.X_CISCO_COM_CableModem.Gateway",
	"TFTPServer"			=> "Device.X_CISCO_COM_CableModem.TFTPServer",
	"TimeServer"			=> "Device.X_CISCO_COM_CableModem.TimeServer",
	"TimeOffset"			=> "Device.X_CISCO_COM_CableModem.TimeOffset",
	"BootFileName"			=> "Device.X_CISCO_COM_CableModem.BootFileName",
	"MDDIPOverride"			=> "Device.X_CISCO_COM_CableModem.MDDIPOverride",
	"LeaseTimeRemaining"	=> "Device.X_CISCO_COM_CableModem.LeaseTimeRemaining",
	"RebindTimeRemaining"	=> "Device.X_CISCO_COM_CableModem.RebindTimeRemaining",
	"RenewTimeRemaining"	=> "Device.X_CISCO_COM_CableModem.RenewTimeRemaining",
	"PrimaryDHCPServer"		=> "Device.X_CISCO_COM_CableModem.PrimaryDHCPServer",
	"SecondaryDHCPServer"	=> "Device.X_CISCO_COM_CableModem.SecondaryDHCPServer",
	);
$cm_value = KeyExtGet("Device.X_CISCO_COM_CableModem.", $cm_param);
?>

<div class="module forms div_cm">
	<h2>CM DHCP Parameters</h2>
	<div class="form-row ">
		<span class="readonlyLabel">CM IP Address:</span>
		<span class="value"><?php echo $cm_value['IPAddress'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">CM Subnet Mask:</span>
		<span class="value"><?php echo $cm_value['SubnetMask'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">CM IP Gateway:</span>
		<span class="value"><?php echo $cm_value['Gateway'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">CM TFTP Server:</span>
		<span class="value"><?php echo $cm_value['TFTPServer'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">CM Time Server:</span>
		<span class="value"><?php echo $cm_value['TimeServer'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">CM Time Offset:</span>
		<span class="value"><?php echo $cm_value['TimeOffset'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">CM Boot File:</span>
		<span class="value"><?php echo $cm_value['BootFileName'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">MDD IP Mode Override:</span>
		<span class="value"><?php echo $cm_value['MDDIPOverride'];?></span>
	</div>
</div>

<div class="module forms div_cm">
	<h2>CM IP Time Remaining</h2>
	<div class="form-row ">
		<span class="readonlyLabel">DHCP Lease Time:</span>
		<span class="value"><?php echo sec2dhms($cm_value['LeaseTimeRemaining']);?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">DHCP Rebind Time:</span>
		<span class="value"><?php echo ($cm_value['RebindTimeRemaining']);?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">DHCP Renew Time:</span>
		<span class="value"><?php echo ($cm_value['RenewTimeRemaining']);?></span>
	</div>
</div>

<div class="module forms div_cm">
	<h2>CM PacketCable Options</h2>
	<div class="form-row ">
		<span class="readonlyLabel">Sub-option 1 Service Provider's Primary DHCP:</span>
		<span class="value"><?php echo $cm_value['PrimaryDHCPServer'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Sub-option 1 Service Provider's Secondary DHCP:</span>
		<span class="value"><?php echo $cm_value['SecondaryDHCPServer'];?></span>
	</div>
</div>

<?php
$mta_param = array(
	"FQDN"					=> "Device.X_CISCO_COM_MTA.FQDN",
	"IPAddress"				=> "Device.X_CISCO_COM_MTA.IPAddress",
	"SubnetMask"			=> "Device.X_CISCO_COM_MTA.SubnetMask",
	"Gateway"				=> "Device.X_CISCO_COM_MTA.Gateway",
	"BootFileName"			=> "Device.X_CISCO_COM_MTA.BootFileName",
	"LeaseTimeRemaining"	=> "Device.X_CISCO_COM_MTA.LeaseTimeRemaining",
	"RebindTimeRemaining"	=> "Device.X_CISCO_COM_MTA.RebindTimeRemaining",
	"RenewTimeRemaining"	=> "Device.X_CISCO_COM_MTA.RenewTimeRemaining",
	"PrimaryDNS"			=> "Device.X_CISCO_COM_MTA.PrimaryDNS",
	"SecondaryDNS"			=> "Device.X_CISCO_COM_MTA.SecondaryDNS",
	"DHCPOption3"			=> "Device.X_CISCO_COM_MTA.DHCPOption3",
	"DHCPOption6"			=> "Device.X_CISCO_COM_MTA.DHCPOption6",
	"DHCPOption7"			=> "Device.X_CISCO_COM_MTA.DHCPOption7",
	"DHCPOption8"			=> "Device.X_CISCO_COM_MTA.DHCPOption8",
	);
$mta_value = KeyExtGet("Device.X_CISCO_COM_MTA.", $mta_param);
?>

<div class="module forms div_mta">
	<h2>MTA DHCP Parameters</h2>
	<div class="form-row ">
		<span class="readonlyLabel">MTA FQDN:</span>
		<span class="value"><?php echo $mta_value['FQDN'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">MTA IP Address:</span>
		<span class="value"><?php echo $mta_value['IPAddress'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">MTA IP Subnet Mask:</span>
		<span class="value"><?php echo $mta_value['SubnetMask'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">MTA IP Gateway:</span>
		<span class="value"><?php echo $mta_value['Gateway'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">MTA Bootfile:</span>
		<span class="value"><?php echo $mta_value['BootFileName'];?></span>
	</div>
</div>

<div class="module forms div_mta">
	<h2>MTA IP Time Remaining</h2>
	<div class="form-row ">
		<span class="readonlyLabel">DHCP Lease Time:</span>
		<span class="value"><?php echo sec2dhms($mta_value['LeaseTimeRemaining']);?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">DHCP Rebind Time:</span>
		<span class="value"><?php echo sec2dhms($mta_value['RebindTimeRemaining']);?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">DHCP Renew Time:</span>
		<span class="value"><?php echo sec2dhms($mta_value['RenewTimeRemaining']);?></span>
	</div>
</div>

<div class="module forms div_mta">
	<h2>MTA DHCP Option 6</h2>
	<div class="form-row ">
		<span class="readonlyLabel">Network Primary DNS:</span>
		<span class="value"><?php echo $mta_value['PrimaryDNS'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Network Secondary DNS:</span>
		<span class="value"><?php echo $mta_value['SecondaryDNS'];?></span>
	</div>
</div>

<div class="module forms div_mta">
	<h2>MTA PacketCable Options(Option 122)</h2>
	<div class="form-row ">
		<span class="readonlyLabel">Sub-option 3:</span>
		<span class="value"><?php echo $mta_value['DHCPOption3'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Sub-option 6:</span>
		<span class="value"><?php echo $mta_value['DHCPOption6'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">Sub-option 7:</span>
		<span class="value"><?php echo $mta_value['DHCPOption7'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Sub-option 8:</span>
		<span class="value"><?php echo $mta_value['DHCPOption8'];?></span>
	</div>
</div>

<?php
// $device_param = array(
	// "HardwareVersion"			=> "Device.DeviceInfo.HardwareVersion",
	// "Manufacturer"				=> "Device.DeviceInfo.Manufacturer",
	// "BootloaderVersion"			=> "Device.DeviceInfo.X_CISCO_COM_BootloaderVersion",
	// "ModelName"					=> "Device.DeviceInfo.ModelName",
	// "ProductClass"				=> "Device.DeviceInfo.ProductClass",
	// "Hardware"					=> "Device.DeviceInfo.Hardware",
	// "AdditionalSoftwareVersion"	=> "Device.DeviceInfo.AdditionalSoftwareVersion",
	// "SerialNumber"				=> "Device.DeviceInfo.SerialNumber",
	// );
// $device_value = KeyExtGet("Device.DeviceInfo.", $device_param);

// there are bugs in native DmExtGetStrsWithRootObj when geting DeviceInfo, have to get one by one
$device_value["HardwareVersion"] 			= getStr("Device.DeviceInfo.HardwareVersion");
$device_value["Manufacturer"] 				= getStr("Device.DeviceInfo.Manufacturer");
$device_value["BootloaderVersion"] 			= getStr("Device.DeviceInfo.X_CISCO_COM_BootloaderVersion");
$device_value["ModelName"] 					= getStr("Device.DeviceInfo.ModelName");
$device_value["ProductClass"] 				= getStr("Device.DeviceInfo.ProductClass");
$device_value["Hardware"] 					= getStr("Device.DeviceInfo.Hardware");
$device_value["AdditionalSoftwareVersion"] 	= getStr("Device.DeviceInfo.AdditionalSoftwareVersion");
$device_value["SerialNumber"] 				= getStr("Device.DeviceInfo.SerialNumber");

?>

<div class="module forms">
	<h2>Cable Modem</h2>
	<div class="form-row ">
		<span class="readonlyLabel" style="text-align:left; color:#333333">HW Version:</span>
		<span class="value"><?php echo $device_value['HardwareVersion'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Vendor:</span>
		<span class="value"><?php echo $device_value['Manufacturer'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel" style="text-align:left; color:#333333">BOOT Version:</span>
		<span class="value"><?php echo $device_value['BootloaderVersion'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Core Version:</span>
		<span class="value"><?php echo getStr("Device.X_CISCO_COM_CableModem.CoreVersion");?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Model:</span>
		<span class="value"><?php echo $device_value['ModelName'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Product Type:</span>
		<span class="value"><?php echo $device_value['ProductClass'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Flash Part:</span>
		<span class="value"><?php echo $device_value['Hardware'];?> MB</span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Download Version:</span>
		<span class="value"><?php echo $device_value['AdditionalSoftwareVersion'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Serial Number:</span>
		<span class="value"><?php echo $device_value['SerialNumber'];?></span>
	</div>
</div>

<?php
$ds_obj = "Device.X_CISCO_COM_CableModem.DownstreamChannel.";
$ds_val = DmExtGetStrsWithRootObj($ds_obj, array($ds_obj));
$ds_ids = DmExtGetInstanceIds($ds_obj);
$ds_tab = array();
for ($i=1, $j=1; $i<count($ds_ids); $i++)
{
	$ds_tab[$i]['ChannelID']		= $ds_val[$j++][1];
	$ds_tab[$i]['Frequency']		= $ds_val[$j++][1];
	$ds_tab[$i]['PowerLevel']		= $ds_val[$j++][1];
	$ds_tab[$i]['SNRLevel']			= $ds_val[$j++][1];
	$ds_tab[$i]['Modulation']		= $ds_val[$j++][1];
	$ds_tab[$i]['Octets']			= $ds_val[$j++][1];
	$ds_tab[$i]['Correcteds']		= $ds_val[$j++][1];
	$ds_tab[$i]['Uncorrectables']	= $ds_val[$j++][1];
	$ds_tab[$i]['LockStatus']		= $ds_val[$j++][1];
}
?>

<div class="module" style="overflow:auto">
	<table class="data" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td class="row-label acs-th"><div style="width: 100px">Downstream</div></td>
			<td class="row-label acs-th" colspan="16">Channel Bonding Value</td>
		</tr>
	</thead>
	<tbody>
		<tr class="">
			<td class="row-label "><div style="width: 100px">Index</div></td>
			<?php for ($i=1; $i<count($ds_ids); $i++) echo '<td><div style="width: 100px">'.$i.'</div></td>';?>
		</tr>
		<tr class="odd">
			<td class="row-label "><div style="width: 100px">Lock Status</div></td>
			<?php for ($i=1; $i<count($ds_ids); $i++) echo '<td><div style="width: 100px">'.$ds_tab[$i]['LockStatus'].'</div></td>';?>
		</tr>
		<tr class="">
			<td class="row-label "><div style="width: 100px">Frequency</div></td>
			<?php for ($i=1; $i<count($ds_ids); $i++) echo '<td><div style="width: 100px">'.$ds_tab[$i]['Frequency'].'</div></td>';?>
		</tr>
		<tr class="odd">
			<td class="row-label "><div style="width: 100px">SNR</div></td>
			<?php for ($i=1; $i<count($ds_ids); $i++) echo '<td><div style="width: 100px">'.$ds_tab[$i]['SNRLevel'].'</div></td>';?>
		</tr>
		<tr class="">
			<td class="row-label "><div style="width: 100px">Power</div></td>
			<?php for ($i=1; $i<count($ds_ids); $i++) echo '<td><div style="width: 100px">'.$ds_tab[$i]['PowerLevel'].'</div></td>';?>
		</tr>
		<tr class="odd">
			<td class="row-label "><div style="width: 100px">Modulation</div></td>
			<?php for ($i=1; $i<count($ds_ids); $i++) echo '<td><div style="width: 100px">'.$ds_tab[$i]['Modulation'].'</div></td>';?>
		</tr>
	</tbody>
	</table>
</div>

<?php
$us_obj = "Device.X_CISCO_COM_CableModem.UpstreamChannel.";
$us_val = DmExtGetStrsWithRootObj($us_obj, array($us_obj));
$us_ids = DmExtGetInstanceIds($us_obj);
$us_tab = array();
for ($i=1, $j=1; $i<count($us_ids); $i++)
{
	$us_tab[$i]['ChannelID']		= $us_val[$j++][1];
	$us_tab[$i]['Frequency']		= $us_val[$j++][1];
	$us_tab[$i]['PowerLevel']		= $us_val[$j++][1];
	$us_tab[$i]['ChannelType']		= $us_val[$j++][1];
	$us_tab[$i]['SymbolRate']		= $us_val[$j++][1];
	$us_tab[$i]['Modulation']		= $us_val[$j++][1];
	$us_tab[$i]['LockStatus']		= $us_val[$j++][1];
}
?>

<div class="module" style="overflow:auto">
	<table class="data" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td class="row-label acs-th"><div style="width: 100px">Upstream</div></td>
			<td class="row-label acs-th" colspan="16">Channel Bonding Value</td>
		</tr>
	</thead>
	<tbody>
		<tr class="">
			<td class="row-label "><div style="width: 100px">Index</div></td>
			<?php for ($i=1; $i<count($us_ids); $i++) echo '<td><div style="width: 100px">'.$i.'</div></td>';?>
		</tr>
		<tr class="odd">
			<td class="row-label "><div style="width: 100px">Lock Status</div></td>
			<?php for ($i=1; $i<count($us_ids); $i++) echo '<td><div style="width: 100px">'.$us_tab[$i]['LockStatus'].'</div></td>';?>
		</tr>
		<tr class="">
			<td class="row-label "><div style="width: 100px">Frequency</div></td>
			<?php for ($i=1; $i<count($us_ids); $i++) echo '<td><div style="width: 100px">'.$us_tab[$i]['Frequency'].'</div></td>';?>
		</tr>
		<tr class="odd">
			<td class="row-label "><div style="width: 100px">Symbol Rate</div></td>
			<?php for ($i=1; $i<count($us_ids); $i++) echo '<td><div style="width: 100px">'.$us_tab[$i]['SymbolRate'].'</div></td>';?>
		</tr>
		<tr class="">
			<td class="row-label "><div style="width: 100px">Power Level</div></td>
			<?php for ($i=1; $i<count($us_ids); $i++) echo '<td><div style="width: 100px">'.$us_tab[$i]['PowerLevel'].'</div></td>';?>
		</tr>
		<tr class="odd">
			<td class="row-label "><div style="width: 100px">Modulation</div></td>
			<?php for ($i=1; $i<count($us_ids); $i++) echo '<td><div style="width: 100px">'.$us_tab[$i]['Modulation'].'</div></td>';?>
		</tr>
		<tr class="odd">
			<td class="row-label "><div style="width: 100px">Channel ID</div></td>
			<?php for ($i=1; $i<count($us_ids); $i++) echo '<td><div style="width: 100px">'.$us_tab[$i]['ChannelID'].'</div></td>';?>
		</tr>
	</tbody>
	</table>
</div>

<div class="module" style="overflow:auto">
	<table class="data" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td class="row-label acs-th" colspan="16">CM Error Codewords</td>
		</tr>
	</thead>
	<tbody>
		<tr class="">
			<td class="row-label "><div style="width: 100px">Unerrored Codewords</div></td>
			<?php for ($i=1; $i<count($ds_ids); $i++) echo '<td><div style="width: 100px">'.$ds_tab[$i]['Octets'].'</div></td>';?>
		</tr>
		<tr class="odd">
			<td class="row-label "><div style="width: 100px">Correctable Codewords</div></td>
			<?php for ($i=1; $i<count($ds_ids); $i++) echo '<td><div style="width: 100px">'.$ds_tab[$i]['Correcteds'].'</div></td>';?>
		</tr>
		<tr class="">
			<td class="row-label "><div style="width: 100px">Uncorrectable Codewords</div></td>
			<?php for ($i=1; $i<count($ds_ids); $i++) echo '<td><div style="width: 100px">'.$ds_tab[$i]['Uncorrectables'].'</div></td>';?>
		</tr>
	</tbody>
	</table>
</div>
</div> <!-- end #container -->

<?php include('includes/footer.php'); ?>
