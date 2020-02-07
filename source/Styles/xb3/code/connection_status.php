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
<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: connection_status.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php $ForceDisable = getStr("Device.WiFi.X_RDK-CENTRAL_COM_ForceDisable"); ?>
<?php  
    $interface = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstDownstreamIpInterface");
    // $interface = "Device.IP.Interface.2.";
	// initial some variable to suppress some error
    $ipv6_local_addr = "";
    $ipv6_global_addr = "";
    $ipv6_DNS = "";
	$DNSv6Index = "";
    /*local ipv6 address */
    $idArr = explode(",", getInstanceIds($interface."IPv6Address."));
    foreach ($idArr as $key => $value) {
        $ipv6addr = getStr($interface."IPv6Address.$value.IPAddress");
        if (stripos($ipv6addr, "fe80::") !== false) {
          $ipv6_local_addr = $ipv6addr;
        }
        else{
          $ipv6_global_addr = $ipv6addr;
        }
    }
	/*ipv6 dns*/
	$idArr = explode(",", getInstanceIds("Device.DNS.Client.Server."));
    foreach ($idArr as $key => $value) {
        if ( !strcasecmp(php_getstr("Device.DNS.Client.Server.$value.Type"), "DHCPv6") ) {
            $DNSv6Index = $value;
            break;
        }
    }
    $ipv6_DNS = php_getstr("Device.DNS.Client.Server.$DNSv6Index.DNSServer");
    // $ipv6_local_addr = "fe80::250:f1ff:fe80:0";
    // $ipv6_global_addr = "2018:cafe:aaaa::fccc";
    // $ipv6_DNS = "2018:cafe::20c:29ff:fe97:fccc";
	// !!! move "get hotspot status" to edit_public.php, no change on the "Edit" link 
  function php_KeyExtGet($root, $params)
  {
    if ("Enabled" == $_SESSION["psmMode"])
    {
      if ((strstr($root, "WiFi")) || (strstr($root, "MoCA"))){
          foreach($params as $i=>$key)
          {
            $params["$key"] = "";
          }
          return $params;
      }
    }
    return KeyExtGet($root, $params);
  }
      $wifi_param = array(
      "SSID1"               => "Device.WiFi.SSID.1.SSID",
      "SSID2"               => "Device.WiFi.SSID.2.SSID",
      "SSID3"               => "Device.WiFi.SSID.3.SSID",
      "SSID4"               => "Device.WiFi.SSID.4.SSID",
      "SSID5"               => "Device.WiFi.SSID.5.SSID",
      "SSID6"               => "Device.WiFi.SSID.6.SSID",
      "Enable1"             => "Device.WiFi.SSID.1.Enable",
      "Enable2"             => "Device.WiFi.SSID.2.Enable",
      "Enable3"             => "Device.WiFi.SSID.3.Enable",
      "Enable4"             => "Device.WiFi.SSID.4.Enable",
      "Enable5"             => "Device.WiFi.SSID.5.Enable",
      "Enable6"             => "Device.WiFi.SSID.6.Enable",
      "ModeEnabled1"        => "Device.WiFi.AccessPoint.1.Security.ModeEnabled",
      "ModeEnabled2"        => "Device.WiFi.AccessPoint.2.Security.ModeEnabled",
      "ModeEnabled3"        => "Device.WiFi.AccessPoint.3.Security.ModeEnabled",
      "ModeEnabled4"        => "Device.WiFi.AccessPoint.4.Security.ModeEnabled",
      "ModeEnabled5"        => "Device.WiFi.AccessPoint.5.Security.ModeEnabled",
      "ModeEnabled6"        => "Device.WiFi.AccessPoint.6.Security.ModeEnabled",
      "NumberOfEntries1"    => "Device.WiFi.AccessPoint.1.AssociatedDeviceNumberOfEntries",
      "NumberOfEntries2"    => "Device.WiFi.AccessPoint.2.AssociatedDeviceNumberOfEntries",
      "NumberOfEntries3"    => "Device.WiFi.AccessPoint.3.AssociatedDeviceNumberOfEntries",
      "NumberOfEntries4"    => "Device.WiFi.AccessPoint.4.AssociatedDeviceNumberOfEntries",
      "NumberOfEntries5"    => "Device.WiFi.AccessPoint.5.AssociatedDeviceNumberOfEntries",
      "NumberOfEntries6"    => "Device.WiFi.AccessPoint.6.AssociatedDeviceNumberOfEntries",
      "EncryptionMethod1"   => "Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_EncryptionMethod",
      "EncryptionMethod2"   => "Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_EncryptionMethod",
      "EncryptionMethod3"   => "Device.WiFi.AccessPoint.3.Security.X_CISCO_COM_EncryptionMethod",
      "EncryptionMethod4"   => "Device.WiFi.AccessPoint.4.Security.X_CISCO_COM_EncryptionMethod",
      "EncryptionMethod5"   => "Device.WiFi.AccessPoint.5.Security.X_CISCO_COM_EncryptionMethod",
      "EncryptionMethod6"   => "Device.WiFi.AccessPoint.6.Security.X_CISCO_COM_EncryptionMethod",
      "OperatingStandards1" => "Device.WiFi.Radio.1.OperatingStandards",
      "OperatingStandards2" => "Device.WiFi.Radio.2.OperatingStandards",
      "OperatingFrequencyBand1" => "Device.WiFi.Radio.1.OperatingFrequencyBand",
      "OperatingFrequencyBand2" => "Device.WiFi.Radio.2.OperatingFrequencyBand",
    );
    $wifi_value = php_KeyExtGet("Device.WiFi.", $wifi_param);
    $OperatingFrequencyBand1  = $wifi_value['OperatingFrequencyBand1'];
    $OperatingFrequencyBand2  = $wifi_value['OperatingFrequencyBand2'];
    $radioband1               = (strstr($OperatingFrequencyBand1,"5G")) ? "5" : "2.4";
    $radioband2               = (strstr($OperatingFrequencyBand2,"5G")) ? "5" : "2.4";
    $device_ctrl_param = array(
        "LanIPAddress"    => "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress",
        "LanSubnetMask"   => "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask" ,
        "WanAddressMode"  => "Device.X_CISCO_COM_DeviceControl.WanAddressMode",
      );
    $device_ctrl_value = php_KeyExtGet("Device.X_CISCO_COM_DeviceControl.", $device_ctrl_param);
    $dhcpv4_param = array(
        "Enable"                => "Device.DHCPv4.Server.Enable",
        "LeaseTime"             => "Device.DHCPv4.Server.Pool.1.LeaseTime",
        "LeaseTimeRemaining"    => "Device.DHCPv4.Client.1.LeaseTimeRemaining",
      );
    $dhcpv4_value = php_KeyExtGet("Device.DHCPv4.", $dhcpv4_param);
    
    $partnerId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
    /* Turn off MoCA based on Partner devices */
    if (strpos($partnerId, "sky-") !== false) {
        $MoCA = FALSE;
    }
?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init('Gateway > Connection > Status', "nav-connection-status");
    $ForceDisable = '<?php echo $ForceDisable; ?>';
    var isBridge = "<?php echo $_SESSION["lanMode"]; ?>";
    if(isBridge == 'bridge-static'){
        $('.localIPNetwork *').addClass('disabled');
        $('.localBtn').click(function(e){
            e.preventDefault();
        }) ;
        //$('.localIPNetwork').remove();
        $('.private-wifi *').addClass('disabled');
        $('.private-wifi .btn').click(function(e) {
            e.preventDefault();
        });
        $('.moca_section *').addClass('disabled');
        $('.moca_section .btn').click(function(e) {
            e.preventDefault();
        });
    };
      if($ForceDisable == "true") {
     $('.private-wifi *').addClass('disabled');
        $('.private-wifi .btn').click(function(e) {
            e.preventDefault();
        });
     $('.tr_hotspot *').addClass('disabled');
     $('.home_network *').addClass('disabled');
     };

	if("Enabled"=="<?php echo $_SESSION["psmMode"]; ?>") {
		$(".wifi_section").remove();
		$(".moca_section").remove();
		$(".wifi_text").show();
		$(".moca_text").show();
	}
	/* Remove Moca based on partner support */
	var partnerId = "<?php echo $partnerId;?>";
	if (partnerId.indexOf("sky-") !== -1){
		$(".moca_section").remove();
		$(".moca_text").hide();
	}
  /** tweak ipv6 address field according to its length **/
  var ipv6_max_len = 25;
  var local_v6 = "<?php echo $ipv6_local_addr; ?>";
  var global_v6 = "<?php echo $ipv6_global_addr; ?>";
  var ipv6_DNS = "<?php echo $ipv6_DNS; ?>";
  // console.log(local_v6.length);
  // console.log(global_v6.length);
  // console.log(ipv6_DNS.length);
  if(local_v6.length > ipv6_max_len){
    $('#local-v6-addr').removeClass('value').addClass('ipv6-style');
  }
  if(global_v6.length > ipv6_max_len){
    $('#global-v6-addr').removeClass('value').addClass('ipv6-style');
  }
  if(ipv6_DNS.length > ipv6_max_len){
    $('#v6-dns').removeClass('value').addClass('ipv6-style');
  }
});
</script>
<style type="text/css">
.ipv6-style{
  font-weight: bold;
  margin-left: 45px;
}
</style>
<?php
  $NetworkName = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.HelpTip.NetworkName");
  $allowEthWan= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.AllowEthernetWAN");
  $wan_enable= getStr("Device.Ethernet.X_RDKCENTRAL-COM_WAN.Enabled");
?>
<div id="content" style="margin-bottom:100px;">
	<h1><?php echo _("Gateway > Connection > Status")?></h1>
	<div id="educational-tip">
			<p class="tip"><?php echo _("View information about your network connections.")?></p>
			<p class="hidden"><?php echo sprintf(_("View and manage the settings for your local IP, Wi-Fi, MoCA and %ss."), $NetworkName)?></p>
	</div>
        <?php
            if($ForceDisable == "true") {
         ?>
                      <div class= "error" style="text-align: right;" >
                             <h3 style="width:92%"><?php echo _("WiFi is configured to be disabled");?></h3>
                          </div>
              <?php
             }
           ?>

  <div style="width:360px;float:left;"><!-- contain local ip, gateway network, Moca -->
    <div class="module forms block localIPNetwork">
        <h2><?php echo _("Local IP Network")?></h2>
        <p class="button"><a tabindex='0' href="local_ip_configuration.php" class="btn localBtn"><?php echo _("Edit")?></a></p>
                <div class="form-row ">
                    <span class="readonlyLabel"><?php echo _("IP Address (IPv4):")?></span> <span class="value"> 
                         <?php echo $device_ctrl_value["LanIPAddress"]; ?>
                    </span>
                </div>
                <div class="form-row odd" >
                    <span class="readonlyLabel"><?php echo _("Subnet mask:")?></span> <span class="value">
                     <?php echo $device_ctrl_value["LanSubnetMask"]; ?>
                    </span>
                </div>
                <div class="form-row ">
                        <span class="readonlyLabel"><?php echo _("DHCPv4 Server:")?></span> <span class="value"> 
                  <?php if ( !strcasecmp("true", $dhcpv4_value["Enable"])) 
                          echo _("Enabled");
                          else  echo _("Disabled");
                  ?> 
                        </span>
                </div>
                <div class="form-row odd">
                    <span class="readonlyLabel"><?php echo _("DHCPv4 Lease Time:")?></span> 
                    <span class="value" >
                      <?php 
                      function div_mod($n, $m)
                      {
                        if (!is_numeric($n) || !is_numeric($m) || (0==$m))
                        {
                          return array(0, 0);
                        }
                        for($i=0; $n >= $m; $i++)
                        {
                          $n = $n - $m;
                        }
                        return array($i, $n);
                      }
                      function sec2dhms($sec)
                      {
			if($sec == "-1") return _("Forever");
			(!is_numeric($sec)) && ($sec = 0);
                        if($sec >= 604800 && $sec % 604800 == 0) return $sec/(604800)._(" Week");
			$tmp = div_mod($sec, 24*60*60);
                        $day = $tmp[0];
                        $tmp = div_mod($tmp[1], 60*60);
                        $hor = $tmp[0];
                        $tmp = div_mod($tmp[1],    60);
                        $min = $tmp[0];
                        return "${day}d:${hor}h:${min}m";
                      }
                      $dhcp_lease_time = $dhcpv4_value["LeaseTime"];      
                      echo sec2dhms($dhcp_lease_time);
                      ?>
                    </span>
                </div>                
                <div class="form-row ">
                  <span class="readonlyLabel"><?php echo _("Link Local Gateway Address (IPv6):")?></span> 
                  <span id="local-v6-addr" class="value"> 
                   <?php  echo $ipv6_local_addr; ?>
                  </span>
              </div>
              <div class="form-row odd">
                  <span class="readonlyLabel"><?php echo _("Global Gateway Address (IPv6):")?></span> 
                  <span id="global-v6-addr" class="value">
                  <?php 
                      echo $ipv6_global_addr;
                  ?>
                </span>
              </div>    
              <div class="form-row ">
                  <span class="readonlyLabel"><?php echo _("Delegated prefix:")?></span> <span class="value">
                   <?php 
                    echo php_getstr("Device.IP.Interface.1.IPv6Prefix.1.Prefix");
                   ?> 
                  </span>
              </div>
              <div class="form-row odd">
                  <span class="readonlyLabel"><?php echo _("DHCPv6 Lease Time:")?></span> <span class="value">
                    <?php 
                       $dhcpV6_lease_time = php_getstr("Device.DHCPv6.Server.Pool.1.LeaseTime");      
                       echo sec2dhms($dhcpV6_lease_time);
                    ?>
                  </span>
              </div>
              <div class="form-row ">
                  <span class="readonlyLabel"><?php echo _("IPV6 DNS:")?></span> 
                  <span id="v6-dns" class="value" >
                    <?php 
                       echo $ipv6_DNS;
                    ?>
                  </span>
              </div>            
              <div class="form-row odd">
                  <span class="readonlyLabel"><?php echo _("No of Clients connected:")?></span> 
                  <span class="value">
                      <?php
                         echo php_getstr("Device.Hosts.X_CISCO_COM_ConnectedDeviceNumber"); 
                      ?>
                  </span>
              </div>
    </div><!-- end .module local ip network-->
<?php
if($allowEthWan=="true"){
?>
    <div class="module forms block" style="margin-bottom:0px"><!--- WAN Network -->
      <h2><?php echo _("WAN Network")?></h2>
        <p class="button"><a tabindex='0' href="wan_network.php" class="btn localBtn">Edit</a></p>
                <div class="form-row ">
                    <span class="readonlyLabel"><?php echo _("WAN Network Status:")?></span> <span class="value">
                         <?php
                          if($wan_enable=="true")
                              echo _("Active Ethernet WAN");
                          else
                              echo _("Active Docsis WAN");
                         ?>
                    </span>
                </div>
                <div class="form-row ">
                    <span class="readonlyLabel"><?php echo _("WAN IP Address (IPv4):")?></span> <span class="value">
                         <?php
                          $fistUSif = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");
                          $WANIPv4 = getStr($fistUSif."IPv4Address.1.IPAddress");
                          $WANIPv6 = getStr("Device.DeviceInfo.X_COMCAST-COM_WAN_IPv6");
                          echo $WANIPv4;
                         ?>
                    </span>
                </div>
                <div class="form-row ">
                    <span class="readonlyLabel"><?php echo _("WAN IP Address (IPv6):")?></span> <span class="ipv6-style">
                        <?php echo $WANIPv6; ?>
                    </span>
                </div>
    </div><!-- End of Module -->
<?php
}
?>
    <div class="module forms block" style="margin-bottom:0px">
        <h2><?php echo $NetworkName; ?></h2>
        <p class="button"><a tabindex='0' href="network_setup.php" class="btn"><?php echo _("View")?></a></p>
        <div class="form-row">
        <span class="readonlyLabel"><?php echo _("Internet:")?></span> <span class="value">
              <?php 
              if($wan_enable=="true"){
                $wanipv4_status = php_getstr("Device.DeviceInfo.X_COMCAST-COM_WAN_IP");
                $wanipv6_status = php_getstr("Device.DeviceInfo.X_COMCAST-COM_WAN_IPv6");
                if( ($wanipv4_status!="0.0.0.0") || ($wanipv6_status!="")){
                    echo _("Active");
                }else{
                   echo _("Inactive"); 
                }
              }else{
                $partnerId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
                if (strpos($partnerId, "sky-") !== false) {
                   $fistUSif = php_getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");
                   $WANIPv4 = php_getStr($fistUSif."IPv4Address.1.IPAddress");
                   if ( strcasecmp($WANIPv4, "0.0.0.0") || (strlen($ipv6_global_addr) > 0) ){
                      echo _("Active");
                   }
                   else{
                      echo _("Inactive");
                   }
                }
                else{
                   $status = php_getstr("Device.X_CISCO_COM_CableModem.CMStatus"); 
                   if ( !strcasecmp($status, "Operational") ){
                      echo _("Active");
                   }
                   else{
                      echo _("Inactive");                   
                   }
                }
              }
              ?>
        </span>
        </div>
        <div class="form-row odd">
              <span class="readonlyLabel"><?php echo _("WAN IP Address:")?></span> <span class="value">
              <?php
                  $interface = php_getstr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");
                  echo php_getstr( $interface . "IPv4Address.1.IPAddress" );
              ?>
             </span>
        </div>
        <?php
        	if($ipv6_global_addr!=""){
        ?>
        <div class="form-row odd">
              <span class="readonlyLabel"><?php echo _("WAN IPv6 Address:")?></span> <span class="value">
              <?php
                  echo _($ipv6_global_addr);
              ?>
             </span>
        </div>
        <?php
    		}
    	?>
        <div class="form-row">
        <span class="readonlyLabel"><?php echo _("DHCP Client:")?></span> <span class="value"> 
        <?php 
         if("DHCP" == $device_ctrl_value["WanAddressMode"]) 
             echo _("Enabled");
         else
             echo _("Disabled");
        ?>
        </span>
        </div>
        <div class="form-row odd">
          <span class="readonlyLabel"><?php echo _("DHCP Expire Time:")?></span> <span class="value">
                    <?php
                    $expire_time = $dhcpv4_value["LeaseTimeRemaining"];
                    echo sec2dhms($expire_time);
                    ?>
                </span>
        </div>
    </div><!-- end .module gateway network-->
    <div class="module forms block moca_section" style="position:relative;top:7px;right:0px;">
            <h2><?php echo _("MoCA")?></h2>
            <p class="button"><a tabindex='0' href="moca.php" class="btn"><?php echo _("Edit")?></a></p>
            <div class="form-row odd">
              <span class="readonlyLabel"><?php echo _("MoCA Network:")?></span> <span class="value">
                <?php 
                    if("true" == php_getstr("Device.MoCA.Interface.1.Enable")) 
                    echo _("Active");
                    else echo _("Inactive");
                ?>
              </span>
            </div>
            <div class="form-row">
              <span class="readonlyLabel"><?php echo _("MoCA Privacy:")?></span> 
              <span class="value">
                <?php 
                    if("true" == php_getstr("Device.MoCA.Interface.1.PrivacyEnabledSetting")) 
                    echo _("Enabled");
                    else echo _("Disabled");
                ?>
              </span>
            </div>
            <div class="form-row odd ">
              <span class="readonlyLabel"><?php echo _("MoCA Channel:")?></span> <span class="value">
			<?php
			$channel		= php_getstr("Device.MoCA.Interface.1.CurrentOperFreq");			
			switch ($channel)
			{
			//MoCA 1.1
			case "1150":
				echo "D1 (1150 MHz)";
				break;  
			case "1200":
				echo "D2 (1200 MHz)";
				break;  
			case "1250":
				echo "D3 (1250 MHz)";
				break;
			case "1300":
				echo "D4 (1300 MHz)";
				break;
			case "1350":
				echo "D5 (1350 MHz)";
				break;
			case "1400":
				echo "D6 (1400 MHz)";
				break;
			case "1450":
				echo "D7 (1450 MHz)";
				break;
			case "1500":
				echo "D8 (1500 MHz)";
				break;
			case "1550":
				echo "D9 (1550 MHz)";
				break;
			case "1600":
				echo "D10 (1600 MHz)";
				break;
			//MoCA 2.0
			case "1175":
				echo "D1a (1175 MHz)";
				break;
			case "1225":
				echo "D2a (1225 MHz)";
				break;
			case "1275":
				echo "D3a (1275 MHz)";
				break;
			case "1325":
				echo "D4a (1325 MHz)";
				break;
			case "1375":
				echo "D5a (1375 MHz)";
				break;
			case "1425":
				echo "D6a (1425 MHz)";
				break;
			case "1475":
				echo "D7a (1475 MHz)";
				break;
			case "1525":
				echo "D8a (1525 MHz)";
				break;
			case "1575":
				echo "D9a (1575 MHz)";
				break;
			case "1625":
				echo "D10a (1625 MHz)";
				break;
			default:
				echo "D1 (1150 MHz)";
			}
			?>
            </span>
            </div>
            <div class="form-row ">
              <span class="readonlyLabel"><?php echo _("No of Nodes:")?></span> <span class="value">
               <?php echo intval(php_getstr("Device.MoCA.Interface.1.AssociatedDeviceNumberOfEntries"))+1; ?>
             </span>
            </div>
            <div class="form-row odd">
              <span class="readonlyLabel"><?php echo _("No of Clients Connected:")?></span> <span class="value">
               <?php echo php_getstr("Device.MoCA.Interface.1.X_CISCO_COM_NumberOfConnectedClients"); ?>
              </span>
            </div>
    </div><!-- end .module MoCA -->
  </div>
  <div style="width:355px;float:left;position:relative;left:5px;" class="wifi_section"><!-- contain private and public Wi-Fi -->
    <div class="module forms block private-wifi">
        <h2 style="white-space: pre-wrap;"><?php echo sprintf(_("Private Wi-Fi Network-%s"), htmlspecialchars($wifi_value["SSID1"], ENT_NOQUOTES, 'UTF-8')); ?></h2>
        <p class="button"><a tabindex='0' href="wireless_network_configuration_edit.php?id=1" class="btn"><?php echo _("Edit")?></a></p>
        <div class="form-row">
          <span class="readonlyLabel"><?php echo sprintf(_("Wireless Network (Wi-Fi 2.4 GHz):"), $radioband); ?></span> <span class="value">
          <?php 
              if("true" == $wifi_value["Enable1"]) 
                echo _("Active");
              else
                echo _("Inactive");
          ?>
          </span>
        </div>
        <div class="form-row odd">
          <span class="readonlyLabel"><?php echo _("Supported Protocols:")?></span> <span class="value">
          <?php echo strtoupper($wifi_value["OperatingStandards1"]); ?>
          </span>
        </div>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo _("Security:")?></span> <span class="value">
			<?php 
				//echo php_getstr("Device.WiFi.AccessPoint.2.Security.ModeEnabled");
				$encrypt_mode	= $wifi_value["ModeEnabled1"];
				$encrypt_method	= $wifi_value["EncryptionMethod1"];
				echo _(encrypt_map($encrypt_mode, $encrypt_method));
			?>
			</span>
		</div>
        <div class="form-row odd">
          <span class="readonlyLabel"><?php echo _("No of Clients connected:")?></span> <span class="value">
          <?php echo php_getstr("Device.WiFi.AccessPoint.1.AssociatedDeviceNumberOfEntries"); ?>
          </span>
        </div>
    </div><!-- end .module private wifi 2.4-->  
    <div class="module forms block private-wifi" style="position:relative;top:0px;right:0px;">
        <h2 style="white-space: pre-wrap;"><?php echo sprintf(_("Private Wi-Fi Network-%s"), htmlspecialchars($wifi_value["SSID2"], ENT_NOQUOTES, 'UTF-8')); ?> </h2>
        <p class="button"><a tabindex='0' href="wireless_network_configuration_edit.php?id=2" class="btn"><?php echo _("Edit")?></a></p>
        <div class="form-row">
          <span class="readonlyLabel"><?php echo sprintf(_("Wireless Network (Wi-Fi 5 GHz):"), $radioband2); ?></span> <span class="value">
          <?php 
              if("true" == $wifi_value["Enable2"]) 
                echo _("Active");
              else
                echo _("Inactive");
          ?>
          </span>
        </div>
        <div class="form-row odd">
          <span class="readonlyLabel"><?php echo _("Supported Protocols:")?></span> <span class="value">
          <?php echo strtoupper($wifi_value["OperatingStandards2"]); ?>
          </span>
        </div>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo _("Security:")?></span> <span class="value">
			<?php 
				//echo php_getstr("Device.WiFi.AccessPoint.2.Security.ModeEnabled");
				$encrypt_mode	= $wifi_value["ModeEnabled2"];
				$encrypt_method	= $wifi_value["EncryptionMethod2"];
				echo _(encrypt_map($encrypt_mode, $encrypt_method));
			?>
			</span>
		</div>
        <div class="form-row odd">
          <span class="readonlyLabel"><?php echo _("No of Clients connected:")?></span> <span class="value">
          <?php echo $wifi_value["NumberOfEntries2"]; ?>
          </span>
        </div>
    </div><!-- end .module private wifi 5 --> 
<!--HomeSecurity wifi start-->    
	<?php
	$ssids 		= explode(",", php_getinstanceids("Device.WiFi.SSID."));
	$public_v	= array();
	$odd 		= true;
	// xb3-1.6 remove homesecurity wifi for now
	// $ssids 		= explode(",", "");
	if ("mso" != $_SESSION["loginuser"]) {
		$ssids	= array();
	}
	foreach ($ssids as $i)
	{
		if (intval($i)!=3){		//SSID 1,2 for Private, 3,4 for Home Security, 5,6 for Hot Spot
			continue;
		}
		$freq_id = strpos(php_getstr("Device.WiFi.SSID.$i.LowerLayers"), "Radio.1") ? "1" : "2";
		array_push($public_v, array(
			'ssid_id'		=> $i,
			'ssid_enable'	=> $wifi_value["Enable".$i],
			'ssid_name'		=> htmlspecialchars($wifi_value["SSID".$i], ENT_NOQUOTES, 'UTF-8'),
			'radio_mode'	=> strtoupper(php_getstr("Device.WiFi.Radio.$freq_id.OperatingStandards")),
			'radio_freq'	=> ("1"==$freq_id) ? "2.4" : "5",
			'client_cnt'	=> $wifi_value["NumberOfEntries".$i],
			'security'		=> encrypt_map($wifi_value["ModeEnabled".$i], $wifi_value["EncryptionMethod".$i])
		));
	}
	for ($j=0; $j<count($public_v); $j++)
	{
		$wifi_enable = "Inactive";
		if("true" == $public_v[$j]['ssid_enable']) 
		        $wifi_enable = _("Active");
		else
		        $wifi_enable = _("Inactive");
		echo '<div class="module forms block home_network" style="position:relative;top:0px;right:0px;">';
		echo '<h2 style="white-space: pre-wrap;">'.sprintf(_("HomeSecurityNetwork-%s"),$public_v[$j]['ssid_name']).'</h2>';
		// !!!dont goto edit_public page!!! thant page just for hotspot tunnel configuration
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'.sprintf(_("Wireless Network (Wi-Fi %s GHz):"),$public_v[$j]['radio_freq']).'</span> <span class="value">'.$wifi_enable.'</span></div>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'._("Supported Protocols:").'</span> <span class="value">'.$public_v[$j]['radio_mode'].'</span></div>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'._("Security:").'</span> <span class="value">'.$public_v[$j]['security'].'</span></div>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'._("No of Clients connected:").'</span> <span class="value">'.$public_v[$j]['client_cnt'].'</span></div>';
		echo '</div>';
	}
	?>  
<!--HomeSecurity wifi end-->
<!--HotSpot wifi start-->    
	<?php
	$ssids 		= explode(",", php_getinstanceids("Device.WiFi.SSID."));
	$public_v	= array();
	$odd 		= true;
	// xb3-1.6 add hotspot wifi
	// $ssids 		= explode(",", "");
	if ("mso" != $_SESSION["loginuser"]) {
		$ssids	= array();
	}
	foreach ($ssids as $i)
	{
		if (intval($i)<5 || intval($i)>6){		//SSID 1,2 for Private, 3,4 for Home Security, 5,6 for Hot Spot
			continue;
		}
		$freq_id = strpos(php_getstr("Device.WiFi.SSID.$i.LowerLayers"), "Radio.1") ? "1" : "2";
		$PrimaryRemoteEndpoint	= php_getstr("Device.X_COMCAST-COM_GRE.Tunnel.1.PrimaryRemoteEndpoint");	// 2.4G and 5G share one gre tunnel
		$SecondaryRemoteEndpoint = php_getstr("Device.X_COMCAST-COM_GRE.Tunnel.1.SecondaryRemoteEndpoint");	// 2.4G and 5G share one gre tunnel
        $RemoteEndpointsV4  = array();
        array_push($RemoteEndpointsV4, $PrimaryRemoteEndpoint);
        array_push($RemoteEndpointsV4, $SecondaryRemoteEndpoint);
        $wlan_gw = ""; 
        if (isset($RemoteEndpointsV4[0])) {
            $wlan_gw = $RemoteEndpointsV4[0];
            $wlan_gw = $wlan_gw."/".$RemoteEndpointsV4[1];
        }      
		array_push($public_v, array(
			'ssid_id'		=> $i,
			'ssid_enable'	=> $wifi_value["Enable".$i],
			'ssid_name'		=> htmlspecialchars($wifi_value["SSID".$i], ENT_NOQUOTES, 'UTF-8'),
			'xf_capable'	=> php_getstr("Device.DeviceInfo.X_COMCAST-COM_xfinitywifiCapableCPE"),
			'time_last'		=> sec2dhms(php_getstr("Device.X_COMCAST-COM_GRE.Tunnel.1.LastChange")),	// 2.4G and 5G share one gre tunnel
			'wlan_gw'		=> $wlan_gw,
			'radio_mode'	=> strtoupper(php_getstr("Device.WiFi.Radio.$freq_id.OperatingStandards")),
			'radio_freq'	=> ("1"==$freq_id) ? "2.4" : "5",
			'client_cnt'	=> $wifi_value["NumberOfEntries".$i],
			'security'		=> encrypt_map($wifi_value["ModeEnabled".$i], $wifi_value["EncryptionMethod".$i])
		));
	}
	for ($j=0; $j<count($public_v); $j++)
	{
		echo '<div class="module forms block tr_hotspot" style="position:relative;top:0px;right:0px;">';
		echo '<h2 style="white-space: pre-wrap;">'.sprintf(_("Public Wi-Fi Network-%s"),$public_v[$j]['ssid_name']).'</h2>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'._("Public Wi-Fi Capable:").'</span> <span class="value">'.("true"==$public_v[$j]['xf_capable']?_("Yes"):_("No")).'</span></div>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'.sprintf(_("Wireless Network (Wi-Fi %s GHz):"),$public_v[$j]['radio_freq']).'</span> <span class="value">'.("true"==$public_v[$j]['ssid_enable']?_("Active"):_("Inactive")).'</span></div>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'._("Time Since Last Status:").'</span> <span class="value">'.$public_v[$j]['time_last'].'</span></div>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'._("WLAN Gateway:").'</span> <span class="value">'.$public_v[$j]['wlan_gw'].'</span></div>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'._("Supported Protocols:").'</span> <span class="value">'.$public_v[$j]['radio_mode'].'</span></div>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'._("Security:").'</span> <span class="value">'.$public_v[$j]['security'].'</span></div>';
		echo '<div class="form-row '.(($odd=!$odd)?'odd':'').'"><span class="readonlyLabel">'._("No of Clients connected:").'</span> <span class="value">'.$public_v[$j]['client_cnt'].'</span></div>';
		echo '</div>';
	}
	?>  
<!--HotSpot wifi end-->
	</div>
	<div class="module forms block wifi_text" style="display: none;">
		<h2><?php echo _("No Wi-Fi information available")?></h2>
		<strong><?php echo _("Gateway operating in battery mode.")?></strong>
	</div> <!-- end .module -->	
	<div class="module forms block moca_text" style="display: none;">
		<h2><?php echo _("No MoCA information available")?></h2>
		<strong><?php echo _("Gateway operating in battery mode.")?></strong>
	</div> <!-- end .module -->	  
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
