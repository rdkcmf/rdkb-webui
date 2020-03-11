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
<?php include('includes/header.php'); 
if(PREPAID == TRUE){
	echo '<script type="text/javascript">alert("'._("No MTA support for this device").'"); window.history.back(); </script>';
	exit(0);
}
?>
<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?><script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Gateway > Connection > MTA > Line Status", "nav-line-status");
});
</script>
<div id="content">
	<h1><?php echo _("Gateway > Connection > MTA > Line Status")?></h1>
	<div id="educational-tip">
			<p class="tip"><?php echo _("Information related to the MTA Line Status.")?></p>
	</div>
	<div class="module forms">
		<h2><?php echo _("MTA Line Status")?></h2>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo _("Line 1 Status:")?></span> 
			<span class="value"><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.1.Status");?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel"><?php echo _("Line 2 Status:")?></span>
			<span class="value"><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.2.Status");?></span>
		</div>
	</div>
</div> <!-- end .module -->
<!-- Page Specific Script -->
<?php include('includes/footer.php'); ?>
