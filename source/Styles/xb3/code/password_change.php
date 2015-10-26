<?php include('includes/header.php'); ?>

<!-- $Id: password_change.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">

$(document).ready(function() {
    comcast.page.init("Troubleshooting > Change Password", "nav-password");

    $("#pageForm").validate({
		debug: false,
		rules: {
			oldPassword: {
				required: true
				,alphanumeric: true
				,maxlength: 63
				,minlength: 3
			}
			,userPassword: {
				required: true
				,alphanumeric: true
				,maxlength: 20
				,minlength: 8
			}
			,verifyPassword: {
				required: true
				,alphanumeric: true
				,maxlength: 20
				,minlength: 8
				,equalTo: "#userPassword"
			}
		},
		
		submitHandler:function(form){
			next_step();
		}
    });
	
	$("#oldPassword").val("");
	$("#userPassword").val("");
	$("#verifyPassword").val("");
	
 	$("#password_show").change(function() {
		var pwd_t = $(this).prop("checked") ? 'type="text"' : 'type="password"';
		$(".password").each(function(){
			var currVal = $(this).find("input").val();
			// Note: After replaced, the $(this) of input will be changed!!!
			$(this).html($(this).html().replace(/(type="text"|type="password")/g, pwd_t));
			$(this).find("input").val(currVal);		
		});
	});

});

function getInstanceNum()
{
	var thisUser = "<?php echo $_SESSION["loginuser"]; ?>";
	switch(thisUser)
	{
	case "mso":
		return 1;
	case "cusadmin":
		return 2;
	case "admin":
		return 3;
	default: return 0;
	}
}

function cancel_save(){
	window.location = "at_a_glance.php";
}

function set_config(jsConfig)
{
	jProgress('This may take several seconds...', 60);
	$.post(
		"actionHandler/ajaxSet_wizard_step1.php",
		{
			configInfo: jsConfig
		},
		function(msg)
		{
			jHide();
			if ("Match" == msg.p_status) {
				jAlert("Changes saved successfully . <br/> Please login with the new password.", "Alert",function () {
				  window.location = "home_loggedout.php";
				});
			}
			else
			{
				jAlert("Current Password Wrong!");
			}
		},
		"json"     
	);
}

function next_step()
{
	var oldPwd = $('#oldPassword').val();
	var newPwd = $('#userPassword').val();
	var intNum = getInstanceNum();
	var jsConfig = '{"newPassword": "' + newPwd + '", "instanceNum": "' + intNum + '", "oldPassword": "' + oldPwd + '", "ChangePassword": "true"}';
	
	if (oldPwd == newPwd)
	{
		jAlert("Current Password and New Password Can't Be Same!");
	}
	else
	{
		set_config(jsConfig);
	}
}

</script>

<div id="content">
    <h1>Troubleshooting > Change Password</h1>

    <div id="educational-tip">
        <p class="tip">Periodically change your Admin Tool password to protect your network.</p>
	</div>

<form method="post" id="pageForm">
	<div class="module forms">
		<h2>Password</h2>
		
		<div class="form-row password">
			<label for="oldPassword">Current Password:</label><input type="password" value="" name="oldPassword" id="oldPassword" autocomplete="off" />
		</div>
		
		<div class="form-row odd password">
			<label for="userPassword">New Password:</label> <input type="password" value="" name="userPassword" id="userPassword" autocomplete="off" />
		</div>
		
		<div class="form-row password">
			<label for="verifyPassword">Re-enter New Password:</label> <input type="password" value="" name="verifyPassword" id="verifyPassword" autocomplete="off" />
		</div>

		<div class="form-row odd">
			<label for="password_show">Show Typed Password:</label>
			<span class="checkbox"><input type="checkbox" id="password_show" name="password_show" /></span>
		</div> 			
		
		<p class="footnote">Password Must be minimum 8 characters(Alphanumeric only). No spaces. Case sensitive.</p>
	</div> <!-- end .module -->
	
	<div class="form-row form-btn">
		<input id="submit_pwd" type="submit" value="Save" class="btn" />
		<input id="cancel_pwd" type="reset" value="Cancel" onclick="cancel_save(this)" class="btn alt" />
	</div>
</form>

</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
