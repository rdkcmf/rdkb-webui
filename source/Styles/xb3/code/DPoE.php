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
		$(".div_dpoe").remove();
	}
	else if ("admin" == "<?php echo $_SESSION["loginuser"]; ?>"){
		$(".div_dpoe").remove();
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
	$WAN_Status = getStr($fistUSif."Status");
	$WANIPv4 = getStr($fistUSif."IPv4Address.1.IPAddress");
	$ids = explode(",", getInstanceIds($fistUSif."IPv6Address."));
	foreach ($ids as $i){
		$val = getStr($fistUSif."IPv6Address.$i.IPAddress");
		if (!strstr($val, "fe80::")){
			$WANIPv6 = $val;
				//DHCP Lease Expire Time (IPv6):
				// echo $fistUSif."IPv6Address.$i.X_Comcast_com_LeaseTime";
				$sec = getStr($fistUSif."IPv6Address.$i.X_CISCO_COM_PreferredLifetime");
				$tmp = div_mod($sec, 24*60*60);
				$day = $tmp[0];
				$tmp = div_mod($tmp[1], 60*60);
				$hor = $tmp[0];
				$tmp = div_mod($tmp[1],    60);
				$min = $tmp[0];
				$DHCP_LET_IPv6 = $day."d:".$hor."h:".$min."m";
		}
		if (strstr($val, "fe80::")){
			$WANIPv6LinkLocal = $val;
		}
	}
	$sta_inet = ($WAN_Status == "Up") ? "true" : "false";
	//in Bridge mode > Internet connectivity status is always active
	$sta_inet = ($_SESSION["lanMode"] == "bridge-static") ? "true" : $sta_inet ;
?>
<div id="content">
<h1>Gateway > Connection > XFINITY Network</h1>
<div id="educational-tip">
	<p class="tip">View technical information related to your XFINITY network connection.</p>
	<p class="hidden">You may need this information if you contact Comcast for troubleshooting assistance.</p>
</div>
<div class="module forms">
	<h2>XFINITY Network (DPoE)</h2>
	<div class="form-row">
		<span class="readonlyLabel">Internet:</span>
		<span class="value"><?php echo ($sta_inet=="true") ? "Active" : "Inactive";?></span>
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
		<span class="value"><?php echo $WANIPv4;?></span>
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
			echo $WANIPv6;
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
			echo $WANIPv6LinkLocal;
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
			echo $DHCP_LET_IPv6;
		?>		
		</span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">WAN MAC:</span>
		<span class="value">
			<?php echo strtoupper(getStr(getStr(getStr($fistUSif."LowerLayers").".LowerLayers").".MACAddress")); ?>
		</span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">eMTA MAC:</span>
		<span class="value"><?php echo strtoupper(getStr("Device.X_CISCO_COM_MTA.MACAddress"));?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel">DPoE MAC:</span>
		<span class="value"><?php echo strtoupper(getStr("Device.DPoE.Mac_address"));?></span>
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
	"PrimaryDHCPServer"		=> "Device.X_CISCO_COM_MTA.PrimaryDHCPServer",
	"SecondaryDHCPServer"	=> "Device.X_CISCO_COM_MTA.SecondaryDHCPServer",
	);
$mta_value = KeyExtGet("Device.X_CISCO_COM_MTA.", $mta_param);
?>
<div class="module forms div_dpoe">
	<h2>DPoE PacketCable Options</h2>
	<div class="form-row ">
		<span class="readonlyLabel">Sub-option 1 Service Provider's Primary DHCP:</span>
		<span class="value"><?php echo $mta_value['PrimaryDHCPServer'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel">Sub-option 1 Service Provider's Secondary DHCP:</span>
		<span class="value"><?php echo $mta_value['SecondaryDHCPServer'];?></span>
	</div>
</div>
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
$device_value["HardwareVersion"] 			= getStr("Device.DPoE.DPoE_ManufacturerInfo.info");
$device_value["Manufacturer"] 				= getStr("Device.DPoE.DPoE_ManufacturerInfo.organizationName");
$device_value["BootloaderVersion"] 			= getStr("Device.DeviceInfo.X_CISCO_COM_BootloaderVersion");
$device_value["ModelName"] 					= getStr("Device.DeviceInfo.ModelName");
$device_value["ProductClass"] 				= "XF3";
$device_value["SoftwareVersion"]		 	= getStr("Device.DeviceInfo.SoftwareVersion");
$device_value["SerialNumber"] 				= getStr("Device.DeviceInfo.SerialNumber");
$device_value["MFI_Date"]					= getStr("Device.DPoE.DPoE_ManufacturerInfo.manufacturerDate");
?>
<div class="module forms">
	<h2>DPoE Modem Info</h2>
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
	<div class="form-row ">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Model:</span>
		<span class="value"><?php echo $device_value['ModelName'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Product Type:</span>
		<span class="value"><?php echo $device_value['ProductClass'];?></span>
	</div>
	<div class="form-row odd">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Download Version:</span>
		<span class="value"><?php echo $device_value['SoftwareVersion'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Serial Number:</span>
		<span class="value"><?php echo $device_value['SerialNumber'];?></span>
	</div>
	<div class="form-row ">
		<span class="readonlyLabel" style="text-align:left; color:#333333">Manufacturer Date:</span>
		<span class="value"><?php echo $device_value['MFI_Date']; ?></span>
	</div>
</div>

<?php
$DPoE_param = array(
//Device.DPoE.
    "DPoE_NOfNetworkPorts"  => "Device.DPoE.NumberOfNetworkPorts",
    "DPoE_NOfS1Interfaces"  => "Device.DPoE.NumberOfS1Interfaces",
    "DPoE_ResetOnu"         => "Device.DPoE.ResetOnu",
    "DPoE_DMacAddrAgeLimit" => "Device.DPoE.DynamicMacAddressAgeLimit",
    "DPoE_DMacLTableSize"   => "Device.DPoE.DynamicMacLearningTableSize",
    "DPoE_MacLAL"           => "Device.DPoE.MacLearningAggregateLimit",
    "DPoE_COLStatistics"    => "Device.DPoE.ClearOnuLinkStatistics",

    //Device.DPoE.DPoE_FirmwareInfo.
        "FWI_bootVersion"   => "Device.DPoE.DPoE_FirmwareInfo.bootVersion",
        "FWI_bootCrc32"     => "Device.DPoE.DPoE_FirmwareInfo.bootCrc32",
        "FWI_appVersion"    => "Device.DPoE.DPoE_FirmwareInfo.appVersion",
        "FWI_appCrc32"      => "Device.DPoE.DPoE_FirmwareInfo.appCrc32",

    //Device.DPoE.DPoE_ChipInfo.
        "CI_jedecId"        => "Device.DPoE.DPoE_ChipInfo.jedecId",
        "CI_chipModel"      => "Device.DPoE.DPoE_ChipInfo.chipModel",
        "CI_chipVersion"    => "Device.DPoE.DPoE_ChipInfo.chipVersion",

    //Device.DPoE.DPoE_ManufacturerInfo.
        "MFI_info"      => "Device.DPoE.DPoE_ManufacturerInfo.info",
        "MFI_orgName"   => "Device.DPoE.DPoE_ManufacturerInfo.organizationName",
        "MFI_Date"      => "Device.DPoE.DPoE_ManufacturerInfo.manufacturerDate",

    //Device.DPoE.DPoE_DeviceSysDescrInfo.
        "DSDI_vendorName"   => "Device.DPoE.DPoE_DeviceSysDescrInfo.vendorName",
        "DSDI_modelNumber"  => "Device.DPoE.DPoE_DeviceSysDescrInfo.modelNumber",
        "DSDI_HW_Version"   => "Device.DPoE.DPoE_DeviceSysDescrInfo.hardwareVersion",

    //Device.DPoE.DPoE_OnuPacketBufferCapabilities.
        "OPBC_upstreamQueues"       => "Device.DPoE.DPoE_OnuPacketBufferCapabilities.upstreamQueues",
        "OPBC_upQueuesMaxPerLink"   => "Device.DPoE.DPoE_OnuPacketBufferCapabilities.upQueuesMaxPerLink",
        "OPBC_upQueueIncrement"     => "Device.DPoE.DPoE_OnuPacketBufferCapabilities.upQueueIncrement",
        "OPBC_downstreamQueues"     => "Device.DPoE.DPoE_OnuPacketBufferCapabilities.downstreamQueues",
        "OPBC_dnQueuesMaxPerPort"   => "Device.DPoE.DPoE_OnuPacketBufferCapabilities.dnQueuesMaxPerPort",
        "OPBC_dnQueueIncrement"     => "Device.DPoE.DPoE_OnuPacketBufferCapabilities.dnQueueIncrement",
        "OPBC_totalPacketBuffer"    => "Device.DPoE.DPoE_OnuPacketBufferCapabilities.totalPacketBuffer",
        "OPBC_dnPacketBuffer"       => "Device.DPoE.DPoE_OnuPacketBufferCapabilities.dnPacketBuffer"
);
    $DPoE_value = KeyExtGet("Device.DPoE.", $DPoE_param);

    //DPoE_LlidForwardingState    [Table]
    $rootObjName    = "Device.DPoE.DPoE_LlidForwardingState.";
    $paramNameArray = array("Device.DPoE.DPoE_LlidForwardingState.");
    $mapping_array  = array("forwardingState", "link");
    $DPoE_LFWState_Values = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);

    //DPoE_OamFrameRate           [Table]
    $rootObjName    = "Device.DPoE.DPoE_OamFrameRate.";
    $paramNameArray = array("Device.DPoE.DPoE_OamFrameRate.");
    $mapping_array  = array("maxRate","link", "minRate");
    $DPoE_OFR_Values = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);

    //DPoE_DynamicMacTable        [Table]
    $rootObjName    = "Device.DPoE.DPoE_DynamicMacTable.";
    $paramNameArray = array("Device.DPoE.DPoE_DynamicMacTable.");
    $mapping_array  = array("link", "macAddress");
    $DPoE_DMacTable_Values = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);

    //DPoE_StaticMacTable         [Table]
    $rootObjName    = "Device.DPoE.DPoE_StaticMacTable.";
    $paramNameArray = array("Device.DPoE.DPoE_StaticMacTable.");
    $mapping_array  = array("link", "macAddress");
    $DPoE_SMacTable_Values = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);

    //DPoE_OnuLinkStatistics      [Table]
    $rootObjName    = "Device.DPoE.DPoE_OnuLinkStatistics.";
    $paramNameArray = array("Device.DPoE.DPoE_OnuLinkStatistics.");
    $mapping_array  = array("rxUnicastFrames", "txUnicastFrames", "rxFrameTooShort", "rxFrame64", "rxFrame65_127", "rxFrame128_255", "rxFrame256_511", "rxFrame512_1023", "rxFrame1024_1518", "rxFrame1519_Plus", "txFrame64", "txFrame65_127", "txFrame128_255", "txFrame256_511", "txFrame512_1023", "txFrame_1024_1518", "txFrame_1519_Plus", "framesDropped", "bytesDropped", "opticalMonVcc", "opticalMonTxBiasCurrent", "opticalMonTxPower", "opticalMonRxPower");
    $DPoE_OLS_Values = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
?>
	<div class="module forms div_dpoe">
		<h2>DPoE Network</h2>
		<div class="form-row odd">
			<span class="readonlyLabel">Number Of Network Ports:</span>
			<span class="value"><?php echo $DPoE_value['DPoE_NOfNetworkPorts']; ?></span>
		</div>
		<div class="form-row ">
			<span class="readonlyLabel">Number Of S1 Interfaces:</span>
			<span class="value"><?php echo $DPoE_value['DPoE_NOfS1Interfaces']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Reset Onu:</span>
			<span class="value"><?php echo $DPoE_value['DPoE_ResetOnu']; ?></span>
		</div>
		<div class="form-row ">
			<span class="readonlyLabel">Dynamic MAC Address Age Limit:</span>
			<span class="value"><?php echo $DPoE_value['DPoE_DMacAddrAgeLimit']; ?></span>
		</div>		
		<div class="form-row odd">
			<span class="readonlyLabel">Dynamic MAC Learning Table Size:</span>
			<span class="value"><?php echo $DPoE_value['DPoE_DMacLTableSize']; ?></span>
		</div>	
		<div class="form-row ">
			<span class="readonlyLabel">MAC Learning Aggregate Limit:</span>
			<span class="value"><?php echo $DPoE_value['DPoE_MacLAL']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Clear Onu Link Statistics:</span>
			<span class="value"><?php echo $DPoE_value['DPoE_COLStatistics']; ?></span>
		</div>
	</div>
	<div class="module forms div_dpoe">
		<h2>DPoE Firmware Info</h2>
		<div class="form-row ">
			<span class="readonlyLabel">Boot Version:</span>
			<span class="value"><?php echo $DPoE_value['FWI_bootVersion']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Boot Crc32:</span>
			<span class="value"><?php echo $DPoE_value['FWI_bootCrc32']; ?></span>
		</div>
		<div class="form-row ">
			<span class="readonlyLabel">App Version:</span>
			<span class="value"><?php echo $DPoE_value['FWI_appVersion']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">App Crc32:</span>
			<span class="value"><?php echo $DPoE_value['FWI_appCrc32']; ?></span>
		</div>
	</div>
	<div class="module forms div_dpoe">
		<h2>DPoE Chip Info</h2>
		<div class="form-row ">
			<span class="readonlyLabel">JEDEC ID:</span>
			<span class="value"><?php echo $DPoE_value['CI_jedecId']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Chip Model:</span>
			<span class="value"><?php echo $DPoE_value['CI_chipModel']; ?></span>
		</div>
		<div class="form-row ">
			<span class="readonlyLabel">Chip Version:</span>
			<span class="value"><?php echo $DPoE_value['CI_chipVersion']; ?></span>
		</div>
	</div>
	<div class="module forms div_dpoe">
		<h2>DPoE OnuPacketBufferCapabilities</h2>
		<div class="form-row ">
			<span class="readonlyLabel">Upstream Queues:</span>
			<span class="value"><?php echo $DPoE_value['OPBC_upstreamQueues']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Up Queues Max Per Link:</span>
			<span class="value"><?php echo $DPoE_value['OPBC_upQueuesMaxPerLink']; ?></span>
		</div>
		<div class="form-row ">
			<span class="readonlyLabel">Up Queue Increment:</span>
			<span class="value"><?php echo $DPoE_value['OPBC_upQueueIncrement']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Downstream Queues:</span>
			<span class="value"><?php echo $DPoE_value['OPBC_downstreamQueues']; ?></span>
		</div>
		<div class="form-row ">
			<span class="readonlyLabel">Dn Queues MaxPer Port:</span>
			<span class="value"><?php echo $DPoE_value['OPBC_dnQueuesMaxPerPort']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Dn Queue Increment:</span>
			<span class="value"><?php echo $DPoE_value['OPBC_dnQueueIncrement']; ?></span>
		</div>
		<div class="form-row ">
			<span class="readonlyLabel">Total Packet Buffer:</span>
			<span class="value"><?php echo $DPoE_value['OPBC_totalPacketBuffer']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Dn Packet Buffer:</span>
			<span class="value"><?php echo $DPoE_value['OPBC_dnPacketBuffer']; ?></span>
		</div>	
	</div>
	<div class="module" style="overflow:auto">
		<h2>DPoE Llid Forwarding State</h2>
		<table class="data">
		<tbody>
			<tr class="">
				<th class="row-label ">Index</th>
				<?php foreach ($DPoE_LFWState_Values as $key => $value) echo '<td><div style="width: 100px">'.$key.'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Forwarding State</th>
				<?php foreach ($DPoE_LFWState_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["forwardingState"].'</div></td>';?>
			</tr>
			<tr>
				<th class="row-label ">Link</th>
				<?php foreach ($DPoE_LFWState_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["link"].'</div></td>';?>
			</tr>
		</tbody>
		</table>
	</div>
	<div class="module" style="overflow:auto">
		<h2>DPoE Oam Frame Rate</h2>
		<table class="data">	
		<tbody>
			<tr class="">
				<th class="row-label ">Index</th>
				<?php foreach ($DPoE_OFR_Values as $key => $value) echo '<td><div style="width: 100px">'.$key.'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Max Rate</th>
				<?php foreach ($DPoE_OFR_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["maxRate"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Min Rate</th>
				<?php foreach ($DPoE_OFR_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["minRate"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Link</th>
				<?php foreach ($DPoE_OFR_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["link"].'</div></td>';?>
			</tr>
		</tbody>
		</table>
	</div>
	<div class="module form div_dpoe" style="overflow:auto">
		<h2>DPoE DynamicMACTable</h2>
		<table class="data">	
		<tbody>
			<tr class="">
				<th class="row-label ">Index</th>
				<?php foreach ($DPoE_DMacTable_Values as $key => $value) echo '<td><div style="width: 100px">'.$key.'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Link</th>
				<?php foreach ($DPoE_DMacTable_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["link"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">MAC Address</th>
				<?php foreach ($DPoE_DMacTable_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["macAddress"].'</div></td>';?>
			</tr>
		</tbody>
		</table>
	</div><div class="module form div_dpoe" style="overflow:auto">
		<h2>DPoE StaticMACTable</h2>
		<table class="data">	
		<tbody>
			<tr class="">
				<th class="row-label ">Index</th>
				<?php foreach ($DPoE_SMacTable_Values as $key => $value) echo '<td><div style="width: 100px">'.$key.'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Link</th>
				<?php foreach ($DPoE_SMacTable_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["link"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">MAC Address</th>
				<?php foreach ($DPoE_SMacTable_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["macAddress"].'</div></td>';?>
			</tr>
		</tbody>
		</table>
	</div>
	<div class="module form div_dpoe" style="overflow:auto">
		<h2>DPoE OnuLinkStatistics</h2>
		<table class="data">	
		<tbody>
			<tr class="">
				<th class="row-label ">Index</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$key.'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Rx Unicast Frames</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["rxUnicastFrames"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Tx Unicast Frames</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["txUnicastFrames"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Rx Frame Too Short</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["rxFrameTooShort"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Rx Frame 64</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["rxFrame64"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Rx Frame 65-127</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["rxFrame65_127"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Rx Frame 128-255</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["rxFrame128_255"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Rx Frame 256-511</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["rxFrame256_511"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Rx Frame 512-1023</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["rxFrame512_1023"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Rx Frame 1024-1518</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["rxFrame1024_1518"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Rx Frame 1519-Plus</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["rxFrame1519_Plus"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Tx Frame 64</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["txFrame64"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Tx Frame 65-127</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["txFrame65_127"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Tx Frame 128-255</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["txFrame128_255"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Tx Frame 256-511</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["txFrame256_511"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Tx Frame 512-1023</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["txFrame512_1023"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Tx Frame 1024-1518</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["txFrame_1024_1518"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Tx Frame 1519-Plus</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["txFrame_1519_Plus"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Frames Dropped</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["framesDropped"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Bytes Dropped</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["bytesDropped"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Optical Mon Vcc</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["opticalMonVcc"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Optical Mon Tx Bias Current</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["opticalMonTxBiasCurrent"].'</div></td>';?>
			</tr>
			<tr class="">
				<th class="row-label ">Optical Mon Tx Power</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["opticalMonTxPower"].'</div></td>';?>
			</tr>
			<tr class="odd">
				<th class="row-label ">Optical Mon Rx Power</th>
				<?php foreach ($DPoE_OLS_Values as $key => $value) echo '<td><div style="width: 100px">'.$value["opticalMonRxPower"].'</div></td>';?>
			</tr>
		</tbody>
		</table>
	</div>
</div> <!-- end #container -->
<?php include('includes/footer.php'); ?>