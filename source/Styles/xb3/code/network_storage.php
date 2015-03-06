<?php include('includes/header.php'); ?>

<!-- $Id: network_storage.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Connected Devices > Network Storage", "nav-network-storage");
});
</script>

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<div id="content">
	<h1>Connected Devices > Network Storage</h1>

    <div id="educational-tip">
		<p class="tip">TIP: This page displays the space available on the attached drive.</p>
		<p class="hidden">To EDIT the Device Name Settings hover the mouse over the respective row and Click on the edit button to  change Device Name.</p>
		<p class="hidden">A screen with the current settings will show up.</p>
    </div>

<div class="module data">
	<h2>Storage Attached to Gateway</h2>
<table id="network-storage" class="data" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th scope="col">Storage Name</th>
			<th scope="col">File System</th>
			<th scope="col">Space Available</th>
			<th scope="col">Total Space</th>
			<th scope="col">Location</th>
			<th scope="col">&nbsp;</th>
			<th scope="col">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="storage-name row-label">LaCie 500 GB USB</th>
			<td>FAT 32</td>
			<td>5 gb</td>
			<td>500 gb</td>
			<td>USB port 2</td>
			<td class="delete"><a href="network_storage_edit.php" class="btn">edit</a></td>
			<td class="delete"><a href="#" title="delete the Network Storage Device (LaCie 500 GB USB)" class="btn alt confirm">x</a></td>
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
