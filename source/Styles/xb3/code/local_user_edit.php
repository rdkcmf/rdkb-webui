<?php include('includes/header.php'); ?>

<!-- $Id: local_user_edit.php 3116 2009-10-15 20:19:45Z cporto $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">

var dest = "<?php echo $_POST["h_dest"]; ?>";
var idex = "<?php echo $_POST["h_idex"]; ?>";
var name = "<?php echo $_POST["h_name"]; ?>";
var pass = "<?php echo $_POST["h_pass"]; ?>";

$(document).ready(function() {
    comcast.page.init("Advanced > Local Users > "+ dest +" User", "nav-local-user");

    $("input.reset").click(function() {
		location.href = 'local_users.php';
    });

	$("#pageForm").validate({
        rules: {
            user_name: {
                required: true
            }
		},
		submitHandler: function() {
			btn_save();
		}
    });
	
	$("#content h1").html('Advanced > Local Users > '+dest+' User');
	$("#content h2").html(dest+' User');
	$("#user_name").val(name);
	$("#password").val(pass);
});

function btn_save()
{
	name = $("#user_name").val();
	pass = $("#password").val();

	var jsConfig 	=	'{"dest":"'+dest
		+'", "idex":"'+idex
		+'", "name":"'+name
		+'", "pass":"'+pass
		+'"}';

	jProgress('This may take several seconds...', 60);
	
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_local_users.php",
		data: { configInfo: jsConfig },
		success: function(msg) {
			jHide();
			location.href = 'local_users.php';
		},
		error: function(){            
			jHide();
			jAlert("Failure, please try again.");
		}
	});	
}
</script>

<div id="content">
	<h1>Advanced > Local Users > Edit User</h1>

    <div id="educational-tip">
        <p class="tip">TIP: This page needs some explanation text.</p>
    </div>

	<form id="pageForm" method="post" action="">

	<div class="module forms">
		<h2>Edit User</h2>
		<div class="form-row odd">
			<label for="user_name">Name:</label>
			<input type="text" id="user_name" name="user_name" class="text" value="" />
		</div>

        <div class="form-row">
			<label for="password">Password:</label>
			<input type="password" id="password" name="password" class="text" />
		</div>


	</div> <!-- End Module -->
	<div class="form-btn">
		<input class="btn" type="submit" value="Save">
		<input class="btn alt reset" type="reset" value="Cancel">
	</div>
</form>
</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
