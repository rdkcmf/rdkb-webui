<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: battery.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
<?php

$battery_param = array(
        "installed"   	=> "Device.X_CISCO_COM_MTA.Battery.Installed",
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
    comcast.page.init("Hardware > Battery", "nav-battery");

	if ("true" != "<?php echo $battery_value['installed']; ?>"){
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
	<h1>Hardware > Battery</h1>
	<div id="educational-tip">
		<p class="tip">View information about the Gateway's battery status. </p>
		<p class="hidden">Battery power is for voice service only.</p>
	</div>

	<div class="module forms data div_battery">
		<table cellspacing="0" cellpadding="0" class="data" summary="This table shows battery status" >
		<tr>
			<th id="battery_metric">Battery Status</th>
			<th id="battery_status">&nbsp;</th>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label">Power status:</td>
			<td headers="battery_status" id="bat_power"><?php echo $battery_value["bat_power"]; ?></td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label">Battery Installed:</td>
			<td headers="battery_status" id="bat_instal"><?php echo ("true"== $battery_value["bat_instal"] ? "Yes" : "No"); ?></td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label">Battery Condition:</td>
			<td headers="battery_status" id="bat_condition"><?php echo $battery_value["bat_condition"]; ?></td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label">Battery Status:</td>
			<td headers="battery_status" id="bat_status"><?php echo $battery_value["bat_status"]; ?></td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label">Battery Life:</td>
			<td headers="battery_status" id="bat_life"><?php echo $battery_value["bat_life"]; ?></td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label">Total Capacity:</td>
			<td headers="battery_status" id="bat_total"><?php echo $battery_value["bat_total"]; ?> mAh</td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label">Actual Capacity:</td>
			<td headers="battery_status" id="bat_actual"><?php echo $battery_value["bat_actual"]; ?> mAh</td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label">Remaining Charge:</td>
			<td headers="battery_status" id="bat_remain">Loading...</td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label">Remaining Time:</td>
			<td headers="battery_status" id="bat_hours" >Loading...</td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label">Number of Cycles to date:</td>
			<td headers="battery_status" id="bat_cycles"><?php echo $battery_value["bat_cycles"]; ?></td>
		</tr>
		<tr class="odd">
			<td headers="battery_metric" class="row-label">Battery Model Number:</td>
			<td headers="battery_status" id="bat_model"><?php echo $battery_value["bat_model"]; ?></td>
		</tr>
		<tr>
			<td headers="battery_metric" class="row-label">Battery Serial Number:</td>
			<td headers="battery_status" id="bat_serial"><?php echo $battery_value["bat_serial"]; ?></td>
		</tr>
		</table>
	</div><!-- end .module -->
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
