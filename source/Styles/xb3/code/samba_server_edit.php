<?php include('includes/header.php'); ?>

<!-- $Id: samba_server_edit.php 3148 2009-12-15 22:23:22Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > File Sharing > Add Share", "nav-file-sharing");

    $("#visibility").change(function() {
    	var $select = $(this);

    	// Reset, hide all user rows except header
    	$("#samba_permissions tr:not(.header)").hide();

    	// Show only relevant user rows
    	if($("option:selected", $select).val() == "everyone") {
    		$("#samba_permissions tr.everyone").show();
    	} else {
    		$("#samba_permissions tr:not(.everyone)").show();
    	}
    }).trigger("change");

    $("#btn-cancel").click(function() {
    	window.location = "samba_server_config.php";
    });

	$("#browse").click(function(){
		$(window.frames["ifr_browse"].document).find("#smallbrowser").html('Loading...');
		window.frames["ifr_browse"].location.reload();
		showModalPop();
	});
	
	$("#pop_button").click(function(){
		var sel_dir	= $(window.frames["ifr_browse"].document).find(".folder.sel").attr("ref");
		$("#directory").val( sel_dir );
		hideModalPop();
	});
	
});

// function popUp(URL1,URL2) {
	// window.open('samba_server_foldertree.php', '111111', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=600,height=300,left = '+(screen.width-600)/2+',top = '+(screen.height-300)/2+'');
// }

</script>

<div id="content">
	<h1>Advanced > File Sharing > Edit Share</h1>
	<div id="educational-tip">
		<p class="tip">This page allows you to alter the permissions, accessibility (HTTP, FTP) of existing shares.</p>
	</div>
	
<form method="post" action="samba_server_config.php">
	<div class="module forms">
	<h2>Edit Share</h2>
	<div class="form-row odd">
			<label for="Device">USB Device:</label>
			<select name="Device" id="Device">
				<option selected="selected" value="USB1" >USB1</option>
				<option value="USB2" >USB2</option>
			</select>
	</div>
	<div class="form-row ">
		<label for="directory">Directory:</label> <input type="text" value="" size="39" name="directory" id="directory" class="text" /> <input type="button" class="btn" value="Browse" id="browse" />
	</div>
	<div class="form-row odd">
		<label for="share_name">Share name:</label> <input type="text" value="Tom's Share" name="share_name" id="share_name" class="text" />
	</div>
	<div class="form-row ">
			<label for="user_name_pc">Enable HTTP for this share:</label>
								<input type="checkbox" name="read_permission_everyone1" id="read_permission_everyone1" />


		</div>
		<div class="form-row odd">
			<label for="password_pc">Enable FTP for this share:</label>
								<input type="checkbox" name="read_permission_everyone2" id="read_permission_everyone2"  />

	</div>
	<div class="form-row ">
		<label for="visibility">Visibility:</label>
		<select name="visibility" id="visibility">
			<option selected="selected" value="everyone">Everyone</option>
			<option value="restricted">Restricted</option>
		</select>
	</div>

	<div class="form-row">
		<!-- <label for="users">User names to be restricted:<br />(separate by comma) </label> <textarea id="users" name="users" cols="40" rows="5" class="text"></textarea> -->
		<label for="">Permissions:</label>
		<table id="samba_permissions" class="data">
			<tr class="header">
				<th class="user">User</th>
				<th class="permissions">Permissions</th>
			</tr>
			<tr class="everyone odd">
				<td>Everyone</td>
				<td>
					<input type="checkbox" name="read_permission_everyone" id="read_permission_everyone" checked="checked" />
					Read
					<input type="checkbox" name="write_permission_everyone" id="write_permission_everyone" checked="checked" />
					Write
					<input type="checkbox" name="execute_permission_everyone" id="execute_permission_everyone" checked="checked" />
					Execute
				</td>
			</tr>
			<tr class="restricted odd">
				<td>Tom</td>
				<td>
					<input type="checkbox" name="read_permission_user1" id="read_permission_user1" checked="checked" />
					Read
					<input type="checkbox" name="write_permission_user1" id="write_permission_user1" checked="checked" />
					Write
					<input type="checkbox" name="execute_permission_user1" id="execute_permission_user1" checked="checked" />
					Execute
				</td>
			</tr>
			<tr class="restricted">
				<td>Bill</td>
				<td>
					<input type="checkbox" name="read_permission_user2" id="read_permission_user2" checked="checked" />
					Read
					<input type="checkbox" name="write_permission_user2" id="write_permission_user2" checked="checked" />
					Write
					<input type="checkbox" name="execute_permission_user2" id="execute_permission_user2" checked="checked" />
					Execute
				</td>
			</tr>
			<tr class="restricted odd">
				<td>Kate</td>
				<td>
					<input type="checkbox" name="read_permission_user3" id="read_permission_user3" checked="checked" />
					Read
					<input type="checkbox" name="write_permission_user3" id="write_permission_user3" checked="checked" />
					Write
					<input type="checkbox" name="execute_permission_user3" id="execute_permission_user3" checked="checked" />
					Execute
				</td>
			</tr>

		</table>
		<div class="footnote clearfix">
			Please visit <a href="local_users.php">local users</a> to manage accounts.</div>
		</div>
	<div class="form-row odd">
		<label for="description">Description:</label> <textarea id="users" name="users" cols="40" rows="5" class="text"></textarea>
	</div>
	</div> <!-- End Module -->

<div class="form-btn">
	<input type="button" class="btn" value="Save"/>
	<input type="button" id="btn-cancel" class="reset btn alt" value="Cancel"/>
</div>
</form>
</div><!-- end #content -->


<script language="javascript" type="text/javascript">

function showModalPop()
{	
	$("#pop_container").css({
		"position"	: "absolute",
		"width"		: "461px",
		"left"		: ($(window).width()  - $("#pop_container").outerWidth())/2, 
		"top"		: ($(window).height() - $("#pop_container").outerHeight())/2 + $(document).scrollTop() 
	});
		
	$("#pop_title").html("Folder browser");
	$("#pop_message").html("Please select a folder to share:");	
	$("#pop_mask").fadeIn();
	$("#pop_container").show();
}

function hideModalPop()
{
	$("#pop_mask").fadeOut();
	$("#pop_container").hide();
}

</script>


<style type="text/css">

#pop_mask {
    background: none repeat scroll 0 0 #000000;
    display: none;
    position: fixed;
    height: 100%;
    width: 100%;
    left: 0;
    top: 0;
}
#pop_container {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 8px solid #FFFFFF;
/*     max-width: 360px;	Dialog will wrap after this width */
/*     min-width: 300px;	Dialog will be no smaller than this */
}
#pop_title {
    background: none repeat scroll 0 0 #A46DD3;
    color: #FFFFFF;
	font-size: 16px;
    padding: 10px;
    margin-bottom: 8px;
}
#pop_message {
    background-color: #EDEDED;
	color: #000000;
    font-size: 12px;
    padding: 10px;
}

</style>

<div id="pop_mask" style="display:none; z-index:100; opacity:0.3;"></div>
<div id="pop_container" style="display:none; z-index:101;">
	<div id="pop_title">Title</div>
	<div id="pop_message">Message</div>
	<div>
		<iframe id="ifr_browse" name="ifr_browse" src="samba_server_foldertree.php" width="461" height="191" marginwidth="0px"></iframe>
	</div>
	<div>
		<input id="pop_button" type="button" value="Select" />
	</div>
</div>


<?php include('includes/footer.php'); ?>
