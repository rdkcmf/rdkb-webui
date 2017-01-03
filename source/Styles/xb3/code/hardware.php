<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: hardware.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Hardware > System Hardware", "nav-system-hardware");
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
	$hardware			= getStr("Device.DeviceInfo.Hardware");
	$hardware_mem_used	= getStr("Device.DeviceInfo.Hardware_MemUsed");
	$hardware_mem_free	= getStr("Device.DeviceInfo.Hardware_MemFree");
?>
<div id="content">
	<h1>Gateway > Hardware > System Hardware</h1>
	<div id="educational-tip">
		<p class="tip">View information about the Gateway's hardware.</p>
		<p class="hidden">You may need this information if you contact Comcast for troubleshooting assistance.</p>
	</div>
	<div class="module forms">
		<h2>System Hardware</h2>
		<div class="form-row odd">
			<span class="readonlyLabel">Model:</span> <span class="value">
			<?php echo $model_name; ?></span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">Vendor:</span> <span class="value">
			<?php echo $manufacturer; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Hardware Revision:</span> <span class="value">
			<?php echo $hardware_version; ?></span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">Serial Number:</span> <span class="value">
			<?php echo  $serial_number; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Processor Speed:</span> <span class="value">
			<?php echo $cisco_processor_speed; ?> MHz</span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">DRAM Total Memory:</span> <span class="value">
			<?php echo $mem_total; ?> MB</span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">DRAM Used Memory:</span> <span class="value">
			<?php echo $mem_used; ?> MB</span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">DRAM Available Memory:</span> <span class="value">
			<?php echo $mem_free; ?> MB</span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Flash Total Memory:</span> <span class="value">
			<?php echo $hardware; ?> MB</span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">Flash Used Memory:</span> <span class="value">
			<?php echo $hardware_mem_used; ?> MB</span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Flash Available Memory:</span> <span class="value">
			<?php echo $hardware_mem_free; ?> MB</span>
		</div>
	</div> <!-- end .module -->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
