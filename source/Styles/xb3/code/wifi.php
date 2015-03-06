<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: wifi.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Hardware > WiFi", "nav-wifi");
});
</script>

<div id="content">
	<h1>Gateway > Hardware > Wireless</h1>

	<div id="educational-tip">
		<p class="tip">View information about the Gateway's wireless components.</p>
		<p class="hidden"><strong>Wi-Fi:</strong> The Gateway provides concurrent 2.4 GHz and 5 GHz for Wi-Fi connections.</p>
		<p class="hidden"><strong>DECT:</strong> Provides details of the cordless phone base built into the Gateway.</p>
	</div>

	<div class="module forms block">
		<h2>Wi-Fi LAN port (2.4 GHZ)</h2>
		<div class="form-row">
			<span class="readonlyLabel">Wi-Fi link status:</span>
			<span class="value"><?php echo ("true"==php_getstr("Device.WiFi.SSID.1.Enable"))?"Active":"Inactive";?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">MAC Address:</span>
			<span class="value"><?php echo php_getstr("Device.WiFi.SSID.1.BSSID");?></span>
		</div>
	</div> <!-- end .module -->

	<div class="module forms block">
		<h2>Wi-Fi LAN port (5 GHZ)</h2>
		<div class="form-row">
			<span class="readonlyLabel">Wi-Fi link status:</span>
			<span class="value"><?php echo ("true"==php_getstr("Device.WiFi.SSID.2.Enable"))?"Active":"Inactive";?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">MAC Address:</span>
			<span class="value"><?php echo php_getstr("Device.WiFi.SSID.2.BSSID");?></span>
		</div>
	</div> <!-- end .module -->

	<div class="module forms block">
		<h2>DECT Base</h2>
		<div class="form-row">
			<span class="readonlyLabel">Status:</span>
			<span class="value"><?php echo ("true"==php_getstr("Device.X_CISCO_COM_MTA.Dect.Enable"))?"Active":"Inactive";?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">DECT Module HW Version:</span>
			<span class="value"><?php echo php_getstr("Device.X_CISCO_COM_MTA.Dect.HardwareVersion");?></span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel">RFPI:</span>
			<span class="value"><?php echo php_getstr("Device.X_CISCO_COM_MTA.Dect.RFPI");?></span>
		</div>
	</div> <!-- end .module -->

</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
