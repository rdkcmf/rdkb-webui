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
<!-- $Id: network_storage.php 3159 2010-01-11 20:10:58Z slemoine $ -->
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Connected Devices > Network Storage", "nav-network-storage");
});
</script>
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<div id="content">
	<h1><?php echo _("Connected Devices > Network Storage")?></h1>
    <div id="educational-tip">
		<p class="tip"><?php echo _("TIP: This page displays the space available on the attached drive.")?></p>
		<p class="hidden"><?php echo _("To EDIT the Device Name Settings hover the mouse over the respective row and Click on the edit button to  change Device Name.")?></p>
		<p class="hidden"><?php echo _("A screen with the current settings will show up.")?></p>
    </div>
<div class="module data">
	<h2><?php echo _("Storage Attached to Gateway")?></h2>
<table id="network-storage" class="data" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th scope="col"><?php echo _("Storage Name")?></th>
			<th scope="col"><?php echo _("File System")?></th>
			<th scope="col"><?php echo _("Space Available")?></th>
			<th scope="col"><?php echo _("Total Space")?></th>
			<th scope="col"><?php echo _("Location")?></th>
			<th scope="col">&nbsp;</th>
			<th scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="storage-name row-label"><?php echo _("LaCie 500 GB USB")?></th>
			<td>FAT 32</td>
			<td>5 gb</td>
			<td>500 gb</td>
			<td><?php echo _("USB port 2")?></td>
			<td class="delete"><a href="network_storage_edit.php" class="btn"><?php echo _("edit")?></a></td>
			<td class="delete"><a href="#" title="<?php echo _("delete the Network Storage Device (LaCie 500 GB USB)")?>" class="btn alt confirm">x</a></td>
		</tr>
	</tbody>
</table>
</div> <!-- end .module -->
<!--
	<div class="form-btn">
		<a href="index.php" class="btn confirm" title="New driver found for {storagename}. Download and update driver?">Check Driver Updates</a>
	</div>
-->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
