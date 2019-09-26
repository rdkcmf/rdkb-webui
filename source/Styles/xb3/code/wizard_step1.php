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
<!-- $Id: wizard_step1.php 2943 2009-08-25 20:58:43Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php $partnerId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId"); ?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Gateway > Home Network Wizard - Step 1", "nav-wizard");
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
	//Fix for IE8 browser issue, IE8 changeing type from "text" to "password" is not supported
 	$("#password_show").change(function() {
		if ($("#password_show").is(":checked")) {
			document.getElementById("password_field_1").innerHTML = 
			'<input type="text"     size="23" id="oldPassword" name="oldPassword" class="text" value="' + $("#oldPassword").val() + '" />';
			document.getElementById("password_field_2").innerHTML = 
			'<input type="text"     size="23" id="userPassword" name="userPassword" class="text" value="' + $("#userPassword").val() + '" />';
			document.getElementById("password_field_3").innerHTML = 
			'<input type="text"     size="23" id="verifyPassword" name="verifyPassword" class="text" value="' + $("#verifyPassword").val() + '" />';
		}
		else {
			document.getElementById("password_field_1").innerHTML = 
			'<input type="password" size="23" id="oldPassword" name="oldPassword" class="text" value="' + $("#oldPassword").val() + '" />';
			document.getElementById("password_field_2").innerHTML = 
			'<input type="password" size="23" id="userPassword" name="userPassword" class="text" value="' + $("#userPassword").val() + '" />';
			document.getElementById("password_field_3").innerHTML = 
			'<input type="password" size="23" id="verifyPassword" name="verifyPassword" class="text" value="' + $("#verifyPassword").val() + '" />';
		}
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
			setTimeout(function(){ 
				jHide();
				//msg.p_status >> Good_PWD, Default_PWD, Invalid_PWD
				if ("Good_PWD" == msg.p_status) {
					//window.location = "wizard_step2.php";
					document.getElementById("pageForm").submit();
				}
				else
				{
					jAlert("<?php echo _("Current Password Wrong!")?>");
				}
			 }, 5000);
		},
		"json"     
	);
}
function next_step()
{
	var oldPwd = $('#oldPassword').val();
	var newPwd = $('#userPassword').val();
	var intNum = getInstanceNum();
	var jsConfig = '{"newPassword": "' + newPwd + '", "instanceNum": "' + intNum + '", "oldPassword": "' + oldPwd + '"}';
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
	<h1><?php echo _("Gateway > Home Network Wizard - Step 1")?></h1>
	<div id="educational-tip">
		<p class="tip"><?php echo _("The Home Network Wizard walks you through settings you may want to change for better network security.")?></p>
                <p class="hidden"><?php if(strpos($partnerId, "sky-") !== false){echo _("If you have never changed the default information, the <strong>Current Password </strong>is the <i>password</i> which is on the router rear side. Step 1 changes the Admin Tool password (the password to log into this site in the future)."); } else {echo "If you have never changed the default information, the <strong>Current Password </strong>is <i>password</i>. Step 1 changes the Admin Tool password (the password to log into this site in the future).";} ?> </p>

	</div>
	<div class="module forms">
		<!--form action="wizard_step2.php" method="post" id="pageForm"-->
		<form action="wizard_step2.php" method="post" id="pageForm">
			<h2>Step 1 of 2</h2>
			<p class="summary"><?php echo _("To configure your home network, we need some basic information")?></p>
			<div class="form-row password">
				<label for="oldPassword"><?php echo _("Current Password:")?></label>
				<span id="password_field_1"><input type="password" size="23" id="oldPassword" name="oldPassword" class="text" value=""></span>
   			</div>
			<div class="form-row odd password">
				<label for="userPassword"><?php echo _("New Password:")?></label>
				<span id="password_field_2"><input type="password" size="23" id="userPassword" name="userPassword" class="text" value=""></span>
			</div>
			<div class="form-row password">
				<label for="verifyPassword"><?php echo _("Re-enter New Password:")?></label>
				<span id="password_field_3"><input type="password" size="23" id="verifyPassword" name="verifyPassword" class="text" value=""></span>
			</div>
			<div class="form-row odd">
				<label for="password_show"><?php echo _("Show Typed Password:")?></label>
				<span class="checkbox"><input type="checkbox" id="password_show" name="password_show" /></span>
			</div> 
			<p class="footnote"><?php echo _("8-20 characters. Alphanumeric only. No spaces. Case sensitive.")?></p>
			<div class="form-row form-btn">
				<input id="submit_pwd" type="submit" value="<?php echo _("Next Step")?>" class="btn" />
			</div>
		</form>
	</div> <!-- end .module -->	
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
