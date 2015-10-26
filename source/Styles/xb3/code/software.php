<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: software.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Software", "nav-software");
});
</script>
<?php
	$deviceInfo_param = array(
        "version"   	=> "Device.DeviceInfo.SoftwareVersion",
	"FirmwareName"	=> "Device.DeviceInfo.X_CISCO_COM_FirmwareName",
	"ModelName"	=> "Device.DeviceInfo.ModelName",
	);
    $deviceInfo_value = KeyExtGet("Device.DeviceInfo.", $deviceInfo_param);
?>
<div id="content">
	<h1>Gateway > Software</h1>
	<div id="educational-tip">
		<p class="tip">View details about the Gateway's software.</p>
		<p class="hidden">You may need this information if you contact Comcast for troubleshooting assistance.</p>
	</div>
	<div class="module forms">
		<h2>System Software Version</h2>
		<div class="form-row">
			<span class="readonlyLabel">eMTA & DOCSIS Software Version:</span> <span class="value">
			<?php echo $deviceInfo_value['version']; ?></span>
		</div>
		<!--div class="form-row odd">
			<span class="readonlyLabel">DECT Software Version:</span> <span class="value">
			<?php //echo getStr("Device.X_CISCO_COM_MTA.Dect.SoftwareVersion"); ?></span>
		</div-->
		<div class="form-row odd">
			<span class="readonlyLabel">Software Image Name:</span> <span class="value">
			<?php echo $deviceInfo_value['FirmwareName']; ?></span>
		</div>
		<div class="form-row ">
			<span class="readonlyLabel">Advanced Services:</span> <span class="value">
			<?php echo $deviceInfo_value['ModelName']; ?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Packet Cable:</span> <span class="value">
			<?php echo getStr("Device.X_CISCO_COM_MTA.PCVersion"); ?></span>
		</div>
	</div> <!-- end .module -->
</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
