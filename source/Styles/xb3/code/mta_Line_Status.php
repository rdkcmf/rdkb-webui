<?php include('includes/header.php'); ?>

<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?><script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Connection > MTA > Line Status", "nav-line-status");
});
</script>

<div id="content">
	<h1>Gateway > Connection > MTA > Line Status</h1>
	<div id="educational-tip">
			<p class="tip">Information related to the MTA Line Status.</p>
	</div>
	<div class="module forms">
		<h2>MTA Line Status</h2>
		<div class="form-row">
			<span class="readonlyLabel">Line 1 Status:</span> 
			<span class="value"><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.1.Status");?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel">Line 2 Status:</span>
			<span class="value"><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.2.Status");?></span>
		</div>
	</div>
</div> <!-- end .module -->

<!-- Page Specific Script -->
<?php include('includes/footer.php'); ?>
