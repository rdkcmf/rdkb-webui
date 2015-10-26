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
	$deviceinfo_param = array(
	"model_name"		=> "Device.DeviceInfo.ModelName",
	"manufacturer"		=> "Device.DeviceInfo.Manufacturer",
	"hardware_version"	=> "Device.DeviceInfo.HardwareVersion",
	"serial_number"		=> "Device.DeviceInfo.SerialNumber",
	"cisco_processor_speed" => "Device.DeviceInfo.X_CISCO_COM_ProcessorSpeed",
	"mem_total"		=> "Device.DeviceInfo.MemoryStatus.Total",
	"mem_used"		=> "Device.DeviceInfo.MemoryStatus.Used",
	"mem_free"		=> "Device.DeviceInfo.MemoryStatus.Free",
	"hardware"		=> "Device.DeviceInfo.Hardware",
	"hardware_mem_used"	=> "Device.DeviceInfo.Hardware_MemUsed",
	"hardware_mem_free"	=> "Device.DeviceInfo.Hardware_MemFree"
	);
	$deviceinfo_value = KeyExtGet("Device.DeviceInfo.", $deviceinfo_param);
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
			<?php echo $deviceinfo_value["model_name"]; ?></span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">Vendor:</span> <span class="value">
			<?php echo $deviceinfo_value["manufacturer"]; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Hardware Revision:</span> <span class="value">
			<?php echo $deviceinfo_value["hardware_version"]; ?></span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">Serial Number:</span> <span class="value">
			<?php echo  $deviceinfo_value["serial_number"]; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Processor Speed:</span> <span class="value">
			<?php echo $deviceinfo_value["cisco_processor_speed"]; ?> MHz</span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">DRAM Total Memory:</span> <span class="value">
			<?php echo $deviceinfo_value["mem_total"]; ?> MB</span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">DRAM Used Memory:</span> <span class="value">
			<?php echo $deviceinfo_value["mem_used"]; ?> MB</span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">DRAM Available Memory:</span> <span class="value">
			<?php echo $deviceinfo_value["mem_free"]; ?> MB</span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Flash Total Memory:</span> <span class="value">
			<?php echo $deviceinfo_value["hardware"]; ?> MB</span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">Flash Used Memory:</span> <span class="value">
			<?php echo $deviceinfo_value["hardware_mem_used"]; ?> MB</span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Flash Available Memory:</span> <span class="value">
			<?php echo $deviceinfo_value["hardware_mem_free"]; ?> MB</span>
		</div>
	</div> <!-- end .module -->
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
