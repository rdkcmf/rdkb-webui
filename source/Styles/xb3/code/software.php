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
<!-- $Id: software.php 3159 2010-01-11 20:10:58Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Gateway > Software", "nav-software");
});
</script>
<?php
	$version		= getStr("Device.DeviceInfo.AdditionalSoftwareVersion");
	$FirmwareName	= getStr("Device.DeviceInfo.X_CISCO_COM_FirmwareName");
	$ModelName		= getStr("Device.DeviceInfo.ModelName");
	$wanType = get_wan_type();
 	if ($wanType == "EPON") {
	   $BackendSoftwareVersion = "EPON";
 	} elseif ($wanType == "DSL") {
	   $BackendSoftwareVersion = "DSL";
	} else {
	   $BackendSoftwareVersion = "eMTA & DOCSIS";
	}
?>
<div id="content">
	<h1><?php echo _("Gateway > Software")?></h1>
	<div id="educational-tip">
		<p class="tip"><?php echo _("View details about the Gateway's software.")?></p>
		<p class="hidden"><?php echo _("You may need this information for troubleshooting assistance.")?></p>
	</div>
	<div class="module forms">
		<h2><?php echo _("System Software Version")?></h2>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo $BackendSoftwareVersion." "._("Software Version:")?></span> <span class="value">
			<?php echo $version; ?></span>
		</div>
		<!--div class="form-row odd">
			<span class="readonlyLabel">DECT Software Version:</span> <span class="value">
			<?php //echo getStr("Device.X_CISCO_COM_MTA.Dect.SoftwareVersion"); ?></span>
		</div-->
		<div class="form-row odd">
			<span class="readonlyLabel"><?php echo _("Software Image Name:")?></span> <span class="value">
			<?php echo $FirmwareName; ?></span>
		</div>
		<div class="form-row ">
			<span class="readonlyLabel"><?php echo _("Advanced Services:")?></span> <span class="value">
			<?php echo $ModelName; ?></span>
		</div>
		<?php 
		if ($wanType !== "DSL") {
		  echo '<div class="form-row odd">
                  <span class="readonlyLabel">'._("Packet Cable:").'</span> <span class="value">
			      '.getStr("Device.X_CISCO_COM_MTA.PCVersion").'</span>
		        </div>';
        } 
		?>
	</div> <!-- end .module -->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
