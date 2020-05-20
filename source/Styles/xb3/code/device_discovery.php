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
<!-- $Id: device_discovery.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php
//start by licha
$enableUPnP = getStr("Device.UPnP.Device.UPnPIGD");
$adPeriod = getStr("Device.UPnP.Device.X_CISCO_COM_IGD_AdvertisementPeriod");
$timeToLive = getStr("Device.UPnP.Device.X_CISCO_COM_IGD_TTL");
$enableZero = getStr("Device.X_CISCO_COM_DeviceControl.EnableZeroConfig");
//$qosUPnP = getStr("Device.X_CISCO_COM_DDNS.Enable"); //? R3
//end by licha
//add by shunjie
("" == $enableUPnP) && ($enableUPnP = "false");
("" == $adPeriod)   && ($adPeriod = "false");
("" == $timeToLive) && ($timeToLive = "false");
("" == $enableZero) && ($enableZero = "false");
?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Advanced > Device Discovery", "nav-device-discovery");
//start by licha
var jsEnableUPnP = <?php echo $enableUPnP ?>;
var jsAdPeriod = <?php echo $adPeriod ?>;
var jsTimeToLive = <?php echo $timeToLive ?>;
var jsEnableZero = <?php echo $enableZero ?>;
//var jsQosUPnP = <?php //echo $qosUPnP ?>;
jsAdPeriod = jsAdPeriod/60; //unit is seconds in backend implementation.
	$("#upnp_switch").radioswitch({
		id: "upnp-switch",
		radio_name: "upnp",
		id_on: "upnp_enabled",
		id_off: "upnp_disabled",
		title_on: "<?php echo _('Enable').' UPnP'?>",
		title_off: "<?php echo _('Disable').' UPnP'?>",
		state: jsEnableUPnP ? "on" : "off"
	});
	// If Enable UPnP is not checked, disable the next two form fields
	$("#upnp_switch").change(function() {
		var isUPNPDisabled = $(this).radioswitch("getState").on === false;
		if(isUPNPDisabled) {
			$("#upnp-items").find("input").attr("disabled",true).end().find("label").addClass("disabled");
		} else {
			$("#upnp-items").find("input,label").attr("disabled",false).end().find("label").removeClass("disabled");
		}
	});
	$("#zeroconfig_switch").radioswitch({
		id: "zeroconfig-switch",
		radio_name: "zeroconfig",
		id_on: "zeroconfig_enabled",
		id_off: "zeroconfig_disabled",
		title_on: "<?php echo _('Enable').' Zero Config'?>",
		title_off: "<?php echo _('Disable').' Zero Config'?>",
		state: jsEnableZero ? "on" : "off"
	});
function enableHandle() {
	var isUPNPDisabled = $("#upnp_switch").radioswitch("getState").on === false;
	if(isUPNPDisabled) {
		$("#upnp-items").find("input").prop("disabled",true).end().find("label").addClass("disabled");
	} else {
		$("#upnp-items").find("input,label").prop("disabled",false).end().find("label").removeClass("disabled");
	}
}
function init() {
	enableHandle();
	$("#period").val(jsAdPeriod);
	$("#live").val(jsTimeToLive);
/*	if(jsQosUPnP == true) {
		$("#upnp1_enabled").prop("checked", true);
	} else if(jsQosUPnP == false) {
		$("#upnp1_disabled").prop("checked", true);
	}*/
}
init();
$("#pageForm").validate({
	   rules: {
	       period: {
	           required: true
	           ,digits: true
			   ,min: 1
	       }
	       ,live: {
	           required: true
	           ,digits: true
			   ,min: 1
	       }
	   }
});
$('#save_setting').click(function() {
	var isEnabledUPnP = $("#upnp_switch").radioswitch("getState").on;
	var period = $("#period").val();
	period = period*60;
	var live = $("#live").val();
	var isEnabledZero = $("#zeroconfig_switch").radioswitch("getState").on;
//	var isEnabledQosUPnP = $("#upnp1_enabled").is(":checked"); //R3
	var upnpInfo;
	upnpInfo = '{"<?php echo _('IsEnabledUPnP')?>":"'+isEnabledUPnP+'", "<?php echo _('Period')?>":"'+period+'", "<?php echo _('Live')?>":"'+live+'", "<?php echo _('IsEnabledZero')?>":"'+isEnabledZero+'"}';
//	upnpInfo = '{"IsEnabledUPnP":"'+isEnabledUPnP+'", "Period":"'+period+'", "Live":"'+live+'", "IsEnabledZero":"'+isEnabledZero+'", "IsEnabledQosUPnP":"'+isEnabledQosUPnP+'"}';
//	alert(upnpInfo);
	if(isEnabledUPnP == true){
		if($("#pageForm").valid()) {
			saveQoS(upnpInfo);
		} else {
			//alert("Not valid! Can not be saved.");
			jAlert("<?php echo _('Please enter a value greater than or equal to 1. For Advertisement Period & Time To Live.')?>");
		}
	} else {
		saveQoS(upnpInfo);
	}
});
function saveQoS(information) {
//alert(information);
	jProgress('<?php echo _("This may take several seconds")?>', 60);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_UPnP_configuration.php",
		data: { upnpInfo: information },
		success: function(){            
			jHide();
			//               alert("successful submit");
		},
		error: function(){            
			jHide();
			jAlert("<?php echo _('Failure, please try again.')?>");
		}
	});
}
//end by licha
});
</script>
<div id="content">
   	<h1><?php echo _('Advanced > Device Discovery')?></h1>
    <div id="educational-tip">
		        <p class="tip"><?php echo _('Manage UPnP network.')?></p>
		        <p class="hidden"><?php echo _('The UPnP enabled Gateway discovers all UPnP enabled client devices, such as network printers and laptops. Using UPnP, the ports are opened automatically for the appropriate services and applications. The UPnP devices will be auto configured in the network.')?></p>
				<p class="hidden"><?php echo _('<strong>Advertisement Period:</strong> The Advertisement Period is how often the gateway will advertise (broadcast) its UPnP information.')?> </p>
				<p class="hidden"><?php echo _('<strong>Time to Live:</strong> Measured in hops for each UPnP packet sent. A hop is the number of steps an UPnP advertisement is allowed to propagate before disappearing.')?></p>
				<p class="hidden"><?php echo _('<strong>Zero Config:</strong> Discovery protocol which allows devices, such as printers and computers, to connect to a network automatically.')?> </p>
    </div>
	<form action="#TBD" method="post" id="pageForm">
    <div class="module forms">
    	<h2><?php echo _('Device Discovery')?></h2>
		<div class="form-row odd">
			<label for="upnp_enabled"><?php echo _('UPnP:')?></label>
			<span id="upnp_switch"></span>
		</div>
		<div id="upnp-items">
		<div class="form-row">
			<label for="period"><?php echo _('Advertisement Period:')?></label> <input type="text" class="text smallInput" value="30" size="2" maxlength="3" name="period" id="period" /> <?php echo _('minutes')?>
		</div>
		<div class="form-row odd">
			<label for="live"><?php echo _('Time To Live:')?></label> <input type="text" class="text smallInput" value="5" size="2" maxlength="2" name="live" id="live" /> <?php echo _('hops')?>
		</div>
		</div>
		<div class="form-row">
            <label for="zeroconfig_enabled"><?php echo _('Zero Config:')?></label>
			<span id="zeroconfig_switch"></span>
		</div>
	<!--	            <div class="form-row odd">
								<label for="upnp1_enabled">QoS for UPnP:</label>
					            <ul  class="radio-btns enable">
					                <li>
					                    <input id="upnp1_enabled" name="upnp1" type="radio"  value="Enabled"/>
					                    <label for="upnp1_enabled">Enabled</label>
					                </li>
					                <li class="radio-off">
					                    <input id="upnp1_disabled" name="upnp1" type="radio" checked="checked" value="Disabled"/>
					                    <label for="upnp1_disabled">Disabled</label>
					                </li>
					            </ul>
		</div>-->
		<div class="form-btn">
			<input id="save_setting" type="button" value="<?php echo _('Save')?>" class="btn right" /> <!--//licha if type="submit", then an error occured in POST-->
		</div>
	</div> <!-- End Module -->
	</form>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
