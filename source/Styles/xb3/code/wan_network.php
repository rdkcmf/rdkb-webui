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
	$modelName= getStr("Device.DeviceInfo.ModelName");
	$autoWanEnable= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_AutowanFeatureSupport");
	$allowEthWan= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.AllowEthernetWAN");
	$wanPort= getStr("Device.Ethernet.X_RDKCENTRAL-COM_WAN.Port");
	if(!((($autoWanEnable=="true") || ($allowEthWan=="true")) && (($modelName=="CGM4140COM") || ($modelName=="CGM4331COM") || ($modelName=="TG4482A"))) ){
		die();
	}
	$fistUSif = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");
	$WANIPv4 = getStr($fistUSif."IPv4Address.1.IPAddress");
	$WANIPv6= getStr("Device.DeviceInfo.X_COMCAST-COM_WAN_IPv6");
	if($autoWanEnable!="true"){
		$wan_enable= getStr("Device.Ethernet.X_RDKCENTRAL-COM_WAN.Enabled");
          	$wan_status= getStr("Device.Ethernet.Interface.".($wanPort+1).".Status");
		$wnStatus= ($wan_enable=="true" && $wan_status=="Down") ? "true" : "false";
	}
	$autowan_status= getStr("Device.Ethernet.Interface.".($wanPort+1).".Status");
	$selectedOperationalMode =getStr("Device.X_RDKCENTRAL-COM_EthernetWAN.SelectedOperationalMode");
	$bridge_mode = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode");
	$currentOpMode = getStr("Device.X_RDKCENTRAL-COM_EthernetWAN.CurrentOperationalMode");
	$autownStatus = (strtolower($currentOpMode)=="ethernet" && $autowan_status=="Down") ? "true" : "false";
	$portNo= $wanPort+1;
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
		if ($bridge_mode == "bridge-static" ) {
			echo '$("#wan_switch").children(".rs_radiolist").addClass("disabled_state");';
			echo '$("#wan_switch").data("radioswitchstates", "false");';
			echo '$("#autowan").prop("disabled", true);';
			echo '$("#save").prop("disabled", true);';
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
function changeAutoWanMode(){
	var optionNAame = $('#autowan').find(":selected").text();
	var selectedOptionName = "<?php echo strtolower($selectedOperationalMode) ?>";
    	
	if(optionNAame=="Ethernet"){
		jConfirm("<?php echo _("Please note that changing the configuration to Ethernet WAN requires connection of an Ethernet cable to a service provider gateway.")?>","<?php echo _("WARNING:")?>"
				,function(ret) {
					if(ret) {
						
						saveConfig(optionNAame);
						
					} //end of if ret
					else {
						
						$("#autowan").val(selectedOptionName);
						
					}
				});//end of jConfirm
	}else{
		saveConfig(optionNAame);
	}
}
function saveConfig(jsConfigVal){
	var jsConfig='{"wan_network": "' + jsConfigVal + '"} ';
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
        		if($autoWanEnable=="true"){
        ?>
        		<div class="select-row">
				<label><?php echo _("WAN Network:")?></label>
				<span id=""><select name="autowan" id="autowan">
				<?php
				if(strtolower($selectedOperationalMode)=="ethernet"){
					echo "<option value='auto' >Auto</option>
					<option value='docsis'>DOCSIS</option>
					<option value='ethernet' selected>Ethernet</option>";
				}else if(strtolower($selectedOperationalMode)=="docsis"){
					echo "<option value='auto' >Auto</option>
					<option value='docsis' selected>DOCSIS</option>
					<option value='ethernet'>Ethernet</option>";
				}else{
					echo "<option value='auto' selected>Auto</option>
					<option value='docsis' >DOCSIS</option>
					<option value='ethernet' >Ethernet</option>";
				}
				?>
			
			</select></span>				
			</div>
        <?php
        		}else{
        ?>
			<div class="select-row">
				<label><?php echo _("WAN Network:")?></label>
				<span id="wan_switch"></span>			
			</div>
		<?php
			} 
					if($wnStatus=="true" || $autownStatus=="true"){
						
				?>
					<div class="select-row" id="noEth"><p class="error"><?php echo _("No Ethernet WAN Connection is detected on Port $portNo.")?></p></div>
				<?php
				}
		}else{
		?>
		<div class="form-row">
		<span class="readonlyLabel"><?php echo _("WAN Network:")?></span>
		<?php
		if($autoWanEnable=="true"){
			?>
			<span class="value"><?php 
			if(strtolower($selectedOperationalMode)=="ethernet"){
				echo _("Active Ethernet WAN");
			}else if(strtolower($selectedOperationalMode)=="docsis"){
				echo _("Active Docsis WAN");
			}else{
				echo _("Active Auto WAN");
			}
			?></span>
		<?php
		}else{
		?>
			<span class="value"><?php 
			if($wan_enable=="true"){
				echo _("Active Ethernet WAN");
			}else{
				echo _("Active Docsis WAN");
			}
			?></span>
		<?php
		}
		?>
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
           <?php
		if($autoWanEnable=="true"){
			?>
        <div class="form-row odd">
		<span class="readonlyLabel"><?php echo _("Current Operational Mode:")?></span>
		<span class="value"><?php echo $currentOpMode;?></span>
	</div>
		<div class="form-row ">
		<?php
		}else{
			echo '<div class="form-row odd">';
		}
		?>
		
		<span class="readonlyLabel"><?php echo _("WAN IP Address (IPv4):")?></span>
		<span class="value"><?php echo $WANIPv4;?></span>
	</div>
	 <?php
		if($autoWanEnable=="true"){
			echo '<div class="form-row odd">';
		}else{
			echo '<div class="form-row ">';
		}
			?>
		<span class="readonlyLabel"><?php echo _("WAN IP Address (IPv6):")?></span> <span class="value">
		<?php
			echo $WANIPv6;
		?>
		</span>
	</div>	
		<?php
		if (("admin" == $_SESSION["loginuser"]) && ($autoWanEnable=="true")){ 
		?>
		<div class="form-row ">
		<div class="form-btn">
		<label for="save" class="acs-hide"></label>
			<input type="button" id="save" value="<?php echo _("Save")?>" class="btn" onclick="changeAutoWanMode();"/>
		</div>
	</div>
	<?php
	}
	?>
	</div> <!-- end .module -->
	</fieldset>
    </form>
</div><!-- end #content -->
<?php include('includes/footer.php'); //sleep(3);?>
