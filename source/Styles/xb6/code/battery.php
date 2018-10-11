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
        "bat_capacity"  	    => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.batteryCapacity",
        "bat_health"                => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.batteryHealth",
        "bat_status"      	    => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.batteryStatus",
        "bat_chargestatus"          => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.chargingStatus",
        "bat_currtemp"              => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.currentTemperature",
        "bat_chargeremain"  	    => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.estimatedChargeRemaining",
	"bat_timeremain"   	    => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.estimatedMinutesRemaining",
	"bat_chargehealth"	    => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.healthOfChargingSystem",
        "bat_hwversion"             => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.hwVersion",
        "bat_manufacturer"          => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.manufacturerName",
        "bat_maxtemp"               => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.maxTempExperienced",
        "bat_mintemp"		    => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.minTempExperienced",
	"bat_model"                 => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.modelIdentifier",
        "bat_idlepower1"            => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.poweredDeviceIdlePower1",
        "bat_idlepower2"            => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.poweredDeviceIdlePower2",
        "bat_lowbattime"            => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.configLowBattTime",
        "bat_lowtempthresh"         => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.lowTempThreshold",
        "bat_hightempthresh"        => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.highTempThreshold",
        "bat_lowtempdwelltripsecs"  => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.lowTempDwellTripPointSeconds",
        "bat_hightempdwelltripsecs" => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.highTempDwellTripPointSeconds",
        "bat_secsonbatt"            => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.secondsOnBattery",
        "bat_serial"                => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.serialNumber",
        "bat_swversion"             => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.softwareVersion",
	"bat_teststate"		    => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.testingState",
        "bat_teststatus"            => "Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.testingStatus",
	);
    $battery_value = KeyExtGet("Device.DeviceInfo.X_RDKCENTRAL-COM_batteryBackup.", $battery_param);
?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Hardware > Battery", "nav-battery");
/*	if ("true" != "<?php echo $battery_value['installed']; ?>"){
		$(".div_battery [id^='bat_']").text("");
		$("#bat_power").text("AC");
		$("#bat_instal").text("No");
		return;
	}*/
	//var percent	= $("#sta_batt").text().replace("Battery", "");
         if ("0" == "<?php echo $battery_value['bat_status']; ?>"){
		//return without populating the table
		$(".div_battery [id^='bat_']").text("");
		return;
         }
	var bat_chargeremain	= "<?php echo $battery_value['bat_chargeremain']; ?>";
	$("#bat_chargeremain").text(bat_chargeremain + ' % ');
	var bat_timeremain	= "<?php echo $battery_value['bat_timeremain']; ?>";
	var bat_hours	= Math.round(parseInt(bat_timeremain)/6).toString();
	if (bat_hours.length <=1)
	{
		bat_hours = '0'+bat_hours;
	}
	$("#bat_hours").text(bat_hours.slice(0, -1) + "."+bat_hours.slice(-1) + ' hours');

        var bat_health = "<?php echo $battery_value['bat_health']; ?>";
        if("0" == bat_health)
          $("#bat_health").text("Good");
        else if ("1" == bat_health)
          $("#bat_health").text("Fair");
        else if ("2" == bat_health)
          $("#bat_health").text("Poor");
        else if ("3" == bat_health)
          $("#bat_health").text("Failure");
        else
          $("#bat_health").text("Unknown");

        var bat_teststate = "<?php echo $battery_value['bat_teststate']; ?>";
        if("0" == bat_teststate)
          $("#bat_teststate").text("Not Discharging");
        else if ("1" == bat_teststate)
          $("#bat_teststate").text("Discharging");
        else if ("2" == bat_teststate)
          $("#bat_teststate").text("Charging");
        else
          $("#bat_teststate").text("Unknown");

        var bat_chargehealth = "<?php echo $battery_value['bat_chargehealth']; ?>";
        if("0" == bat_chargehealth)
          $("#bat_chargehealth").text("Good");
        else if ("1" == bat_chargehealth)
          $("#bat_chargehealth").text("Voltage High");
        else if ("2" == bat_chargehealth)
          $("#bat_chargehealth").text("Current High");
	else if ("3" == bat_chargehealth)
          $("#bat_chargehealth").text("Current Low");
        else if ("4" == bat_chargehealth)
          $("#bat_chargehealth").text("DischargingOrTestCurrentFailure");
        else
          $("#bat_chargehealth").text("Unknown");

        var bat_chargestatus = "<?php echo $battery_value['bat_chargestatus']; ?>";
        if ("true" == bat_chargestatus)
          $("#bat_chargestatus").text("Charging");
        else
          $("#bat_chargestatus").text("Not Charging");

        var bat_status = "<?php echo $battery_value['bat_status']; ?>";
        if("0" == bat_status)
          $("#bat_status").text("Unknown");
        else if ("1" == bat_status)
          $("#bat_status").text("Normal");
        else if ("2" == bat_status)
          $("#bat_status").text("Low");
        else if ("3" == bat_status)
          $("#bat_status").text("Depleted");
        else
          $("#bat_status").text("Unknown");
});
</script>
<div id="content">
        <h1>Hardware > Battery</h1>
        <div id="educational-tip">
                <p class="tip">View information about the Gateway's battery status. </p>
                <p class="hidden">Battery power is for voice service only.</p>
                <p class="hidden"><strong>Number of Cycles to date:</strong> Indicates how many discharge and charge cycles the battery has gone through from the day it was inserted.</p>
        </div>
        <div class="module forms data div_battery">
                <table cellspacing="0" cellpadding="0" class="data" summary="This table shows battery status" >
                <tr>
                        <th id="battery_metric">Battery Status:</th>
                        <th id="battery_status">&nbsp;</th>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Battery Life:</td>
                        <td headers="battery_status" id="bat_capacity"><?php echo $battery_value["bat_capacity"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Battery Health:</td>
                        <td headers="battery_status" id="bat_health"><?php echo $battery_value["bat_health"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Battery Status:</td>
                        <td headers="battery_status" id="bat_status"><?php echo $battery_value["bat_status"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Battery Charge Status:</td>
                        <td headers="battery_status" id="bat_chargestatus"><?php echo $battery_value["bat_chargestatus"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Current Temperature:</td>
                        <td headers="battery_status" id="bat_currtemp"><?php echo $battery_value["bat_currtemp"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Charging System Health:</td>
                        <td headers="battery_status" id="bat_chargehealth"><?php echo $battery_value["bat_chargehealth"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Remaining Charge:</td>
                        <td headers="battery_status" id="bat_chargeremain">Loading...</td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Remaining Time:</td>
                        <td headers="battery_status" id="bat_hours" >Loading...</td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Hardware Version:</td>
                        <td headers="battery_status" id="bat_hwversion"><?php echo $battery_value["bat_hwversion"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Manufacturer Name:</td>
                        <td headers="battery_status" id="bat_manufacturer"><?php echo $battery_value["bat_manufacturer"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Max Temp Experienced:</td>
                        <td headers="battery_status" id="bat_maxtemp"><?php echo $battery_value["bat_maxtemp"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Min Temp Experienced:</td>
                        <td headers="battery_status" id="bat_mintemp"><?php echo $battery_value["bat_mintemp"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Battery Model Number:</td>
                        <td headers="battery_status" id="bat_model"><?php echo $battery_value["bat_model"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Power Consumed In Initial 8 Hours:</td>
                        <td headers="battery_status" id="bat_idlepower1"><?php echo $battery_value["bat_idlepower1"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Power Consumed In Final 16 hours:</td>
                        <td headers="battery_status" id="bat_idlepower2"><?php echo $battery_value["bat_idlepower2"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Low Battery Time:</td>
                        <td headers="battery_status" id="bat_lowbattime"><?php echo $battery_value["bat_lowbattime"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Low Temperature Threshold:</td>
                        <td headers="battery_status" id="bat_lowtempthresh"><?php echo $battery_value["bat_lowtempthresh"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">High Temperature Threshold:</td>
                        <td headers="battery_status" id="bat_hightempthresh"><?php echo $battery_value["bat_hightempthresh"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Low Temperature Dwell TripPoint Seconds:</td>
                        <td headers="battery_status" id="bat_lowtempdwelltripsecs"><?php echo $battery_value["bat_lowtempdwelltripsecs"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">High Temperature Dwell TripPoint Seconds:</td>
                        <td headers="battery_status" id="bat_hightempdwelltripsecs"><?php echo $battery_value["bat_hightempdwelltripsecs"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Seconds On Battery:</td>
                        <td headers="battery_status" id="bat_secsonbatt"><?php echo $battery_value["bat_secsonbatt"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Battery Serial Number:</td>
                        <td headers="battery_status" id="bat_serial"><?php echo $battery_value["bat_serial"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Battery Software Version:</td>
                        <td headers="battery_status" id="bat_swversion"><?php echo $battery_value["bat_swversion"]; ?></td>
                </tr>
                <tr class="odd">
                        <td headers="battery_metric" class="row-label">Battery Testing State:</td>
                        <td headers="battery_status" id="bat_teststate"><?php echo $battery_value["bat_teststate"]; ?></td>
                </tr>
                <tr>
                        <td headers="battery_metric" class="row-label">Battery Testing Status:</td>
                        <td headers="battery_status" id="bat_teststatus"><?php echo $battery_value["bat_teststatus"]; ?></td>
                </tr>
                </table>
        </div><!-- end .module -->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
