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
<!-- $Id: password_change.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Troubleshooting > Change Password", "nav-password");
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
	jProgress('<?php echo _("This may take several seconds...")?>', 60);
	$.post(
		"actionHandler/ajaxSet_wizard_step1.php",
		{
			configInfo: jsConfig
		},
		function(msg)
		{
			jHide();
			//msg.p_status >> Good_PWD, Default_PWD, Invalid_PWD
			if ("Good_PWD" == msg.p_status) {
				jAlert("<?php echo _("Changes saved successfully. <br/> Please login with the new password.")?>", "<?php echo _("Alert") ?>",function () {
				  window.location = "home_loggedout.php";
				});
			}
			else
			{
				jAlert("<?php echo _("Current Password Wrong!")?>");
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
		jAlert("<?php echo _("Current Password and New Password Can't Be Same!")?>");
	}
	else
	{
		set_config(jsConfig);
	}
}
</script>
<div id="content">
    <h1><?php echo _("Troubleshooting > Change Password")?></h1>
    <div id="educational-tip">
        <p class="tip"><?php echo _("Periodically change your Admin Tool password to protect your network.")?></p>
	</div>
<form method="post" id="pageForm">
	<div class="module forms">
		<h2>Password</h2>
		<div class="form-row password">
			<label for="oldPassword"><?php echo _("Current Password:")?></label><input type="password" value="" name="oldPassword" id="oldPassword" autocomplete="off" />
		</div>
		<div class="form-row odd password">
			<label for="userPassword"><?php echo _("New Password:")?></label> <input type="password" value="" name="userPassword" id="userPassword" autocomplete="off" />
		</div>
		<div class="form-row password">
			<label for="verifyPassword"><?php echo _("Re-enter New Password:")?></label> <input type="password" value="" name="verifyPassword" id="verifyPassword" autocomplete="off" />
		</div>
		<div class="form-row odd">
			<label for="password_show"><?php echo _("Show Typed Password:")?></label>
			<span class="checkbox"><input type="checkbox" id="password_show" name="password_show" /></span>
		</div> 			
		<p class="footnote"><?php echo _("Password Must be minimum 8 characters(Alphanumeric only). No spaces. Case sensitive.")?></p>
	</div> <!-- end .module -->
	<div class="form-row form-btn">
		<input id="submit_pwd" type="submit" value="<?php echo _("Save")?>" class="btn" />
		<input id="cancel_pwd" type="reset" value="<?php echo _("Cancel")?>" onclick="cancel_save(this)" class="btn alt" />
	</div>
</form>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
