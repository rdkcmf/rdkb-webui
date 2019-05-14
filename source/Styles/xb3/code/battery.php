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
<!-- $Id: battery.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php
$battery_param = array(
        "bat_instal"   	=> "Device.X_CISCO_COM_MTA.Battery.Installed",
        "bat_remain"   	=> "Device.X_CISCO_COM_MTA.Battery.RemainingCharge",
	"remain_time"   => "Device.X_CISCO_COM_MTA.Battery.RemainingTime",
	"bat_power"	=> "Device.X_CISCO_COM_MTA.Battery.PowerStatus",
	"bat_condition"	=> "Device.X_CISCO_COM_MTA.Battery.Condition",
	"bat_status"	=> "Device.X_CISCO_COM_MTA.Battery.Status",
	"bat_life"	=> "Device.X_CISCO_COM_MTA.Battery.Life",
	"bat_total"	=> "Device.X_CISCO_COM_MTA.Battery.TotalCapacity",
	"bat_actual"	=> "Device.X_CISCO_COM_MTA.Battery.ActualCapacity",
	"bat_cycles"	=> "Device.X_CISCO_COM_MTA.Battery.NumberofCycles",
	"bat_model"	=> "Device.X_CISCO_COM_MTA.Battery.ModelNumber",
	"bat_serial"	=> "Device.X_CISCO_COM_MTA.Battery.SerialNumber",
	);
    $battery_value = KeyExtGet("Device.X_CISCO_COM_MTA.Battery.", $battery_param);
?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Hardware > Battery", "nav-battery");
	if ("true" != "<?php echo $battery_value['bat_instal']; ?>"){
		$(".div_battery [id^='bat_']").text("");
		$("#bat_power").text("AC");
		$("#bat_instal").text("No");
		return;
	}
	var percent	= $("#sta_batt").text().replace("Battery", "");
	var bat_remain	= "<?php echo $battery_value['bat_remain']; ?>";
	$("#bat_remain").text(bat_remain + ' mAh ('+percent+')');
	var remain_time		= "<?php echo $battery_value['remain_time']; ?>";
	var bat_hours	= Math.round(parseInt(remain_time)/6).toString();
	if (bat_hours.length <=1)
	{
		bat_hours = '0'+bat_hours;
	}
	$("#bat_hours").text(bat_hours.slice(0, -1) + "."+bat_hours.slice(-1) + ' hours');
});
</script>
<div id="content">
	<h1><?php echo _("Hardware > Battery")?></h1>
	<div id="educational-tip">
		<p class="tip"><?php echo _("View information about the Gateway's battery status.")?> </p>
		<p class="hidden"><?php echo _("Battery power is for voice service only.")?></p>
		<p class="hidden"><?php echo _("<strong>Number of Cycles to date:</strong> Indicates how many discharge and charge cycles the battery has gone through from the day it was inserted.")?></p>
	</div>
	<div class="module forms data div_battery">
		<table cellspacing="0" cellpadding="0" class="data" summary="<?php echo _('This table shows battery status')?>" >
		<tr>
			<th id="battery_metric"><?php echo _("Battery Status")?></th>
			<th id="battery_status">&nbsp;</th>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label"><?php echo _("Power Status:")?></td>
			<td headers="battery_status" id="bat_power"><?php echo $battery_value["bat_power"]; ?></td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label"><?php echo _("Battery Installed:")?></td>
			<td headers="battery_status" id="bat_instal"><?php echo ("true"== $battery_value["bat_instal"] ? _("Yes") : _("No")); ?></td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label"><?php echo _("Battery Condition:")?></td>
			<td headers="battery_status" id="bat_condition"><?php echo $battery_value["bat_condition"]; ?></td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label"><?php echo _("Battery Status:")?></td>
			<td headers="battery_status" id="bat_status"><?php echo $battery_value["bat_status"]; ?></td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label"><?php echo _("Battery Life:")?></td>
			<td headers="battery_status" id="bat_life"><?php echo $battery_value["bat_life"]; ?></td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label"><?php echo _("Total Capacity:")?></td>
			<td headers="battery_status" id="bat_total"><?php echo $battery_value["bat_total"]; ?> mAh</td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label"><?php echo _("Actual Capacity:")?></td>
			<td headers="battery_status" id="bat_actual"><?php echo $battery_value["bat_actual"]; ?> mAh</td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label"><?php echo _("Remaining Charge:")?></td>
			<td headers="battery_status" id="bat_remain"><?php echo _("Loading...")?></td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label"><?php echo _("Remaining Time:")?></td>
			<td headers="battery_status" id="bat_hours" ><?php echo _("Loading...")?></td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label"><?php echo _("Number of Cycles to date:")?></td>
			<td headers="battery_status" id="bat_cycles"><?php echo $battery_value["bat_cycles"]; ?></td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label"><?php echo _("Battery Model Number:")?></td>
			<td headers="battery_status" id="bat_model"><?php echo $battery_value["bat_model"]; ?></td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label"><?php echo _("Battery Serial Number:")?></td>
			<td headers="battery_status" id="bat_serial"><?php echo $battery_value["bat_serial"]; ?></td>
		</tr>
		</table>
	</div><!-- end .module -->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
