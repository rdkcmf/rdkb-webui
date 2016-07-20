<?php
$CloudUIEnable = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CloudUIEnable");
	if($CloudUIEnable == "false"){
		header('Location:at_a_glance.php');
		exit;
	}
?>
<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: managed_devices.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div  id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<div  id="content" class="main_content">
	<br><h1>Managed Devices and Port Forwarding pages are moved to cloud.</h1>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>