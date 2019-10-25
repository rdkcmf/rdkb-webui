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
<!-- $Id: wireless_network_configuration.usg.php 3159 2010-01-11 20:10:58Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php
	$allowEthWan= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.AllowEthernetWAN");
	if($allowEthWan != "true") die();
	$fistUSif = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");
	$WANIPv4 = getStr($fistUSif."IPv4Address.1.IPAddress");
	$WANIPv6= getStr("Device.DeviceInfo.X_COMCAST-COM_WAN_IPv6");
	$wan_enable= getStr("Device.Ethernet.X_RDKCENTRAL-COM_WAN.Enabled");
	$wan_status= getStr("Device.Ethernet.Interface.1.Status");
	$wnStatus= ($wan_enable=="true" && $wan_status=="Down") ? "true" : "false";
	$bridge_mode = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode");
	$modelName= getStr("Device.DeviceInfo.ModelName");
?>
<style type="text/css">
label{
	margin-right: 10px !important;
}
.rs_radiolist li.rs_radio_off.rs_selected {
	background: #92c700;
	border: solid 1px #6da006;
	color: #fff;
	text-shadow: 0 1px 1px #5a9007;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Gateway > Connection > WAN Network", "nav-wan-network");
	$("#wan_switch").radioswitch({
		id: "wan-switch",
		radio_name: "wan_network",
		id_on: "ethernet",
		id_off: "docsis",
		label_on:"<?php echo _("Ethernet")?>",
		label_off:"<?php echo _("Docsis")?>",
		title_on: "<?php echo _("Ethernet Mode")?>",
		title_off: "<?php echo _("Docsis Mode")?>",
		state: <?php echo ($wan_enable === "true" ? "true" : "false"); ?> ? "on" : "off"
	});
	 <?php
		if (($bridge_mode == "bridge-static") || ($modelName!="CGM4140COM") ) {
			echo '$("#wan_switch").children(".rs_radiolist").addClass("disabled_state");';
			echo '$("#wan_switch").data("radioswitchstates", "false");';
		}
	?>	
	$("#wan_switch").change(function()
	{	
		var wan_network	= $("#wan_switch").radioswitch("getState").on ? "true" : "false";
		var jsConfig='{"wan_network": "' + wan_network + '"} ';
		if ($(this).radioswitch("getState").on)
		{
			jConfirm("<?php echo _("Please note that changing the configuration to Ethernet WAN requires connection of an Ethernet cable to a service provider gateway.")?>","<?php echo _("WARNING:")?>"
				,function(ret) {
					if(ret) {
						changeMode(jsConfig);
					} //end of if ret
					else {
						$("#wan_switch").radioswitch("doSwitch", "off");
					}
				});//end of jConfirm
		}
		else{
			changeMode(jsConfig);
		}
	});
});
function changeMode(jsConfig){
	jProgress('<?php echo _("Waiting for backend to be fully executed, please be patient...")?>', 100);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_wan_network.php",
		data: { configInfo: jsConfig },
		success: function() {   
			setTimeout(function(){
				jHide();
				window.location.reload(true);
			}, 60000);
		},
		error: function(){
			jHide();
			jAlert("<?php echo _("Failure, please try again.")?>");
		}
	});
}
</script>
<div id="content" >
    <h1><?php echo _("Gateway > Connection > WAN Network")?></h1>
	<div id="educational-tip">
		<p class="tip"><?php echo _("You have the option to enable or disable the Gateway's as Ethernet WAN or DOCSIS WAN.")?> </p>
	</div>
    <form id="pageForm">
	<fieldset>
    <legend class="acs-hide"><?php echo _("WAN Network")?></legend>
    <div class="module forms enable">
        <h2>WAN Network</h2>
        <?php
        	if ("admin" == $_SESSION["loginuser"]){       	 
        ?>
		<div class="select-row">
			<label><?php echo _("WAN Network:")?></label>
			<span id="wan_switch"></span>
			<?php
				if($wnStatus=="true"){
					echo "<br><br>";
			?>
				<div class="select-row" id="noEth"><p class="error"><?php echo _("No Ethernet WAN Connection is detected on Port 1.")?></p></div>
			<?php
			}
			?>			
		</div>
		<?php
		}else{
		?>
		<div class="form-row">
		<span class="readonlyLabel"><?php echo _("WAN Network:")?></span>
		<span class="value"><?php 
		if($wan_enable=="true"){
			echo _("Active Ethernet WAN");
		}else{
			echo _("Active Docsis WAN");
		}
		?></span>
		</div>
		<?php
		}
		?>
		<?php
            if($bridge_mode=="bridge-static"){
		?>
		    <p class="error">EthernetWAN mode is not supported in bridge mode. Disable Bridge mode to enable Ethernet WAN mode.</p>
            
        <?php
           }
           ?> 
		<div class="form-row odd">
		<span class="readonlyLabel"><?php echo _("WAN IP Address (IPv4):")?></span>
		<span class="value"><?php echo $WANIPv4;?></span>
	</div>
		<div class="form-row">
		<span class="readonlyLabel"><?php echo _("WAN IP Address (IPv6):")?></span> <span class="value">
		<?php
			echo $WANIPv6;
		?>
		</span>	
		</div>
	</div> <!-- end .module -->
	</fieldset>
    </form>
</div><!-- end #content -->
<?php include('includes/footer.php'); //sleep(3);?>
