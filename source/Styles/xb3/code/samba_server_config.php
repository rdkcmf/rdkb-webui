 <?php include('includes/header.php'); ?>

<!-- $Id: samba_server_config.php 3146 2009-12-11 20:08:47Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > File Sharing", "nav-file-sharing");

	$("#sharing-switch").change(function() {
		if($("#filesharing_disabled").is(":checked")) {
			$("#sharing-items").prop("disabled",true).addClass("disabled");
		}
		else {
			$("#sharing-items").prop("disabled",false).removeClass("disabled");
			$("btn").removeClass("disabled");
		}
	});

	$(".radio-btns").radioToButton();
});
</script>

<div id="content">
	<h1>Advanced > File Sharing</h1>

    <div id="educational-tip">
	        <p class="tip">Manage File Sharing options.</p>
	        <p class="hidden">Select <strong>Enable</strong> to allow File Sharing on your network.</p>
	        <p class="hidden">Click <strong> + ADD SHARE</strong> to add new shares to your network.</p>
	        <p class="hidden">Click <strong> MANAGE USERS</strong>  to add, delete, or edit users for shared folders access.</p>
    </div>

    <div class="module ">
    	<h2>File Sharing</h2>
        <div class="select-row">
    		<span class="readonlyLabel label">File Sharing:</span>
            <ul class="radio-btns enable" id="sharing-switch">
                <li>
                    <input id="filesharing_enabled" name="filesharing" type="radio" checked="checked" value="Enabled"/>
                    <label for="filesharing_enabled">Enable</label>
                </li>
                <li class="radio-off">
                    <input id="filesharing_disabled" name="filesharing" type="radio" value="Disabled"/>
                    <label for="filesharing_disabled">Disable</label>
                </li>
            </ul>
    	</div>
    	<div class="select-row">
		    			<span class="readonlyLabel label">Network Device Name:</span>
                <input type="text" size="15" maxlength="8" id="authentication_key" name="authentication_key" class="authentication_key" value="HomeShare" /></div>
    </div> <!-- end .module -->

	<div class="module data" id="sharing-items">
		<h2>Shares</h2>
		<p class="button"><a href="samba_server_add.php" class="btn" id="add_share">+ Add Share</a></p>
		<table id="samba_shares" cellspacing="0" cellpadding="0" class="data">
			<thead>
				<tr>
					<th scope="col">Directory</th>
					<th scope="col">Share name</th>
					<th scope="col">Visibility</th>
					<th scope="col">Permissions</th>
					<th scope="col">&nbsp;</th>
					<th scope="col">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr class="odd">
					<td>\\HomeShare\USB1\Folder1</td>
					<td>Tom's Share <small><br />(With latest songs)</small></td>
					<td>Everyone</td>
					<td> Read-Write-Execute</td>
			        <td class="edit"><a href="samba_server_edit.php" class="btn" title="edit this share" id="edit1">Edit</a></td>
					<td class="delete"><a href="samba_server_config.php#" class="btn confirm" title="delete this share" id="delete1">x</a></td>
				</tr>
				<tr>
					<td>\\HomeShare\USB2\Folder2</td>
					<td>
						Kate's Share
						<small><br />(vacation photos)</small>
					</td>
					<td>Restricted</td>
					<td class="permissions">
						Tom(Read-Write-Execute)<br />
						Bill(Read-Write-Execute)<br />
						Kate(Read-Write)<br />
					</td>
			        <td class="edit"><a href="samba_server_edit1.php" class="btn" title="edit this share"id="edit2">Edit</a></td>
	        		<td class="delete"><a href="samba_server_config.php#" class="btn confirm" title="delete this share" id="delete2">x</a></td>
				</tr>
				<tr class="odd">
					<td>\\HomeShare\USB1\Folder3</td>
					<td>Bill's share<small><br />(Important documents)</small></td>
					<td>Read</td>
					<td>Restricted</td>
			        <td class="edit"><a href="samba_server_edit2.php" class="btn" title="edit this share" id="edit3">Edit</a></td>
			        <td class="delete"><a href="samba_server_config.php#" class="btn confirm" title="delete this share" id="delete3">x</a></td>
				</tr>
			</tbody>
		</table>
		</div> <!-- End Module -->

		<div class="form-btn">
			<a href="local_users.php" class="btn" id="Manage_users">Manage Users</a>
		</div>

</div><!-- end #content -->


<?php include('includes/footer.php'); ?>