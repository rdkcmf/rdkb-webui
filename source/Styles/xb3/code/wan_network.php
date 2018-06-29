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
	$partnerId=getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
	if($partnerId != "cox") die();
	$fistUSif = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");
	$WANIPv4 = getStr($fistUSif."IPv4Address.1.IPAddress");
	$ids = explode(",", getInstanceIds($fistUSif."IPv6Address."));
	foreach ($ids as $i){
		$val = getStr($fistUSif."IPv6Address.$i.IPAddress");
		if (!strstr($val, "fe80::")){
			$WANIPv6 = $val;
		}
	}
	$wan_enable= getStr("Device.Ethernet.X_RDKCENTRAL-COM_WAN.Enabled");
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
		label_on:"Ethernet",
		label_off:"Docsis",
		title_on: "Ethernet Mode",
		title_off: "Docsis Mode",
		state: <?php echo ($wan_enable === "true" ? "true" : "false"); ?> ? "on" : "off"
	});
	$("#wan_switch").change(function()
	{
		if ($(this).radioswitch("getState").on)
		{
			$("#docMode").hide();
		}
		else{
			$("#docMode").show();
		}
	});
    $("#pageForm").validate({
		submitHandler:function(form){
			next_step();
		}
    });
});
function next_step() 
{
	var wan_network	= $("#wan_switch").radioswitch("getState").on ? "true" : "false";
	var jsConfig='{"wan_network": "' + wan_network + '"} ';
	console.log(jsConfig);
	jProgress('Waiting for backend to be fully executed, please be patient...', 100);
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
			jAlert("Failure, please try again.");
		}
	});
}
</script>
<div id="content" >
    <h1>Gateway > Connection > WAN Network</h1>
	<div id="educational-tip">
		<p class="tip">You have the option to enable or disable the Gateway's as Ethernet WAN or DOCSIS WAN. </p>
	</div>
    <form id="pageForm">
	<fieldset>
    <legend class="acs-hide">WAN Network</legend>
    <div class="module forms enable">
        <h2>WAN Network</h2>
        <?php
        	if ("admin" == $_SESSION["loginuser"]){       	 
        ?>
		<div class="select-row">
			<label>WAN Network:</label>
			<span id="wan_switch"></span>
			<?php
			if($wan_enable=="false"){
			?>
				<div class="select-row" id="docMode"><p class="error">Please note that changing the configuration to Ethernet WAN requires connection of an Ethernet cable to a service provider gateway.</p></div>
			<?php
			}
			?>			
		</div>
		<?php
		}else{
		?>
		<div class="form-row">
		<span class="readonlyLabel">WAN Network:</span>
		<span class="value"><?php 
		if($wan_enable=="true"){
			echo "Active Ethernet WAN";
		}else{
			echo "Active Docsis WAN";
		}
		?></span>
		</div>
		<?php
		}
		?>
		<div class="form-row odd">
		<span class="readonlyLabel">WAN IP Address (IPv4):</span>
		<span class="value"><?php echo $WANIPv4;?></span>
	</div>
		<div class="form-row">
		<span class="readonlyLabel">WAN IP Address (IPv6):</span> <span class="value">
		<?php
			echo $WANIPv6;
		?>
		</span>
	</div>	
		</div>
		 <?php
        	if ("admin" == $_SESSION["loginuser"]){
       	 ?>
		<div class="form-btn">
			<input id="submit_wan" type="submit" value="Save" class="btn" />
		</div>
		<?php
			}
		?>
    </div> <!-- end .module -->
	</fieldset>
    </form>
</div><!-- end #content -->
<?php include('includes/footer.php'); //sleep(3);?>
