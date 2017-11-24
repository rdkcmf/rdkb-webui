<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
	session_start();
	if (!isset($_SESSION["password_change"])) {
		echo '<script type="text/javascript">alert("Please Login First!"); location.href="home_loggedout.php";</script>';
		exit(0);
	}
?>
<head>
	<title>Xfinity</title>
	<!--CSS-->
	<link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/common-min.css" />
	<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="./cmn/css/ie6-min.css" />
	<![endif]-->
	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="./cmn/css/ie7-min.css" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" media="print" href="./cmn/css/print.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/lib/jquery.radioswitch.css" />
	<!--Character Encoding-->
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <script type="text/javascript" src="./cmn/js/lib/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery-migrate-1.2.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.validate.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.alerts.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.alerts.progress.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.ciscoExt.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.highContrastDetect.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.radioswitch.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.virtualDialog.js"></script>
	<script type="text/javascript" src="./cmn/js/utilityFunctions.js"></script>
    <script type="text/javascript" src="./cmn/js/comcast.js"></script>
	<style>
	#div-skip-to {
		position:relative; 
		left: 150px;
		top: -300px;
	}
	#div-skip-to a {
		position: absolute;
		top: 0;
	}
	#div-skip-to a:active, #div-skip-to a:focus {
		top: 300px;
		color: #0000FF;		
		/*background-color: #b3d4fc;*/
	}
	.form-btn{
			margin-left: 107px;
	}
	</style>	
</head>
<script type="text/javascript">
	$(document).ready(function() {
		//CSRF
		var request;
		if (window.XMLHttpRequest) {
			request = new XMLHttpRequest();
		} else {
			// code for IE6, IE5
			request = new ActiveXObject("Microsoft.XMLHTTP");
		}
		request.open('HEAD', 'actionHandler/ajax_at_a_glance.php', false);
		request.onload = function(){
			$.ajaxSetup({
				beforeSend: function (xhr)
				{
					xhr.setRequestHeader("X-Csrf-Token",request.getResponseHeader('X-Csrf-Token'));
				}
			});
		};
		request.send();
	});
</script>
<body>
    <!--Main Container - Centers Everything-->
	<div id="container">
		<!--Header-->
		<div id="header">
			<h2 id="logo" style="margin-top: 10px"><img src="./cmn/img/logo_xfinity.png" alt="Xfinity" title="Xfinity" /></h2>
		</div> <!-- end #header -->
		<div id='div-skip-to' style="display: none;">
			<a id="skip-link" name="skip-link" href="#content">Skip to content</a>
		</div>
		<!--Main Content-->
		<div id="main-content">
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
			//msg.p_status >> Good_PWD, Default_PWD, Invalid_PWD
			if ("Good_PWD" == msg.p_status) {
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
	var jsConfig = '{"newPassword": "' + newPwd + '", "instanceNum": "3", "oldPassword": "' + oldPwd + '", "ChangePassword": "true"}';
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
    <h1 style="margin-left: 107px;margin-top:86px">Change Password</h1>
    <div id="educational-tip"  style="margin-left: 107px;width: 684px">
        <p class="tip">Periodically change your Admin Tool password to protect your network.</p>
	</div>
<form method="post" id="pageForm">
	<div class="module forms" style="margin-left: 107px">
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
