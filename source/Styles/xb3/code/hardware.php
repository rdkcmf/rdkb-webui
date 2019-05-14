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
<!-- $Id: hardware.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Gateway > Hardware > System Hardware", "nav-system-hardware");
});
</script>
<?php
	$model_name			= getStr("Device.DeviceInfo.ModelName");
	$manufacturer		= getStr("Device.DeviceInfo.Manufacturer");
	$hardware_version	= getStr("Device.DeviceInfo.HardwareVersion");
	$serial_number		= getStr("Device.DeviceInfo.SerialNumber");
	$cisco_processor_speed = getStr("Device.DeviceInfo.X_CISCO_COM_ProcessorSpeed");
	$mem_total			= getStr("Device.DeviceInfo.MemoryStatus.Total");
	$mem_used			= getStr("Device.DeviceInfo.MemoryStatus.Used");
	$mem_free			= getStr("Device.DeviceInfo.MemoryStatus.Free");
	$hardware_mem_total	= getStr("Device.DeviceInfo.Hardware_MemUsed") + getStr("Device.DeviceInfo.Hardware_MemFree");
	$hardware_mem_used	= getStr("Device.DeviceInfo.Hardware_MemUsed");
	$hardware_mem_free	= getStr("Device.DeviceInfo.Hardware_MemFree");
?>
<div id="content">
	<h1><?php echo _('Gateway > Hardware > System Hardware')?></h1>
	<div id="educational-tip">
		<p class="tip"><?php echo _('View information about the Gateway\'s hardware.')?></p>
		<p class="hidden"><?php echo _('You may need this information for troubleshooting assistance.')?></p>
	</div>
	<div class="module forms">
		<h2><?php echo _('System Hardware')?></h2>
		<div class="form-row odd">
			<span class="readonlyLabel"<?php echo _('>Model:')?></span> <span class="value">
			<?php echo $model_name; ?></span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo _('Vendor:')?></span> <span class="value">
			<?php echo $manufacturer; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel"><?php echo _('Hardware Revision:')?></span> <span class="value">
			<?php echo $hardware_version; ?></span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo _('Serial Number:')?></span> <span class="value">
			<?php echo  $serial_number; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel"><?php echo _('Processor Speed:')?></span> <span class="value">
			<?php echo $cisco_processor_speed; ?> MHz</span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo _('DRAM Total Memory:')?></span> <span class="value">
			<?php echo $mem_total; ?> MB</span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel"><?php echo _('DRAM Used Memory:')?></span> <span class="value">
			<?php echo $mem_used; ?> MB</span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo _('DRAM Available Memory:')?></span> <span class="value">
			<?php echo $mem_free; ?> MB</span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel"><?php echo _('Flash Total Memory:')?></span> <span class="value">
			<?php echo $hardware_mem_total; ?> MB</span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo _('Flash Used Memory:')?></span> <span class="value">
			<?php echo $hardware_mem_used; ?> MB</span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel"><?php echo _('Flash Available Memory:')?></span> <span class="value">
			<?php echo $hardware_mem_free; ?> MB</span>
		</div>
	</div> <!-- end .module -->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
