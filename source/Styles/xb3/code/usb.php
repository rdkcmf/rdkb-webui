<?php include('includes/header.php'); ?>

<!-- $Id: usb.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Hardware > USB", "nav-usb");
});
</script>

<div id="content">
	<h1>Gateway > Hardware > USB</h1>

	<div id="educational-tip">
		<p class="tip">View information about connected USB devices.</p>
	</div>

	<?php
	$ids = array_filter(explode(",", getInstanceIds("Device.USB.USBHosts.Host.1.Device.")));
	// print_r($ids);
	
	foreach ($ids as $id)
	{
		$dm = array(
		array("Status:", "", "Active"),
		array("Description:", "Device.USB.USBHosts.Host.1.Device.$id.ProductClass", ""),
		array("Serial Number:", "Device.USB.USBHosts.Host.1.Device.$id.SerialNumber", ""),
		array("Speed:", "Device.USB.USBHosts.Host.1.Device.$id.Rate", " Mbps"),
		array("Manufacturer:", "Device.USB.USBHosts.Host.1.Device.$id.Manufacturer", ""),
		);
		
		echo '<div class="module forms block">';
		echo '<h2>USB Port '.$id.'</h2>';
	
		for ($m=0, $i=0; $i<count($dm); $i++)
		{
			echo '<div class="form-row '.(($m++ % 2)?'odd':'').'" >';
			echo '<span class="readonlyLabel">'.$dm[$i][0].'</span>';
			echo '<span class="value">'.getStr($dm[$i][1]).$dm[$i][2].'</span>';
			echo '</div>';
		}
		echo '</div>';
	}
	?>
	
</div><!-- end #content -->
<?php require_once('includes/footer.php'); ?>
