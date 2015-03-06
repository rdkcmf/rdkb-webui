<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<!-- $Id: header.php 3167 2010-03-03 18:11:27Z slemoine $ -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>XFINITY</title>
	<!--CSS-->
	<link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/common-min.css" />
	<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="./cmn/css/ie6-min.css" />
	<![endif]-->
	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="./cmn/css/ie7-min.css" />
	<![endif]-->

	<!--Character Encoding-->
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

    <script type="text/javascript" src="./cmn/js/lib/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery-migrate-1.2.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.validate.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.alerts.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.alerts.progress.js"></script>

	<script type="text/javascript" src="./cmn/js/utilityFunctions.js"></script>
    <script type="text/javascript" src="./cmn/js/comcast.js"></script>
</head>

<body>
<!-- $Id: home_loggedout.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Login", "nav-login");

    $("#pageForm").validate({
        errorElement : "p"
        ,errorContainer : "#error-msg-box"
        ,invalidHandler: function(form, validator) {
            var errors = validator.numberOfInvalids();
            if (errors) {
                var message = errors == 1 ? 'You missed 1 field. It has been highlighted' : 'You missed ' + errors + ' fields. They have been highlighted';
                $("div.error").html(message);
                $("div.error").show();
            } else {
                $("div.error").hide();
            }
        }
        ,rules : {
            username: {
                required: true
                ,minlength: 3
            }
            ,password: {
                required: true
                ,minlength: 3
            }
        }
        ,messages: {
            username: {
                required: "Username cannot be blank. Please enter a valid username."
            }
            ,password: {
                required: "Password cannot be blank. Please enter a valid password."
                ,minlength: "Password must be at least 3 characters."
            }
        }
    });
	
	$("#username").focus();
	$("#username").val("");
	$("#password").val("");
});

function f()
{
     return true;
}
</script>

<!--Main Container - Centers Everything-->
<div id="container">
	<!--Header-->
	<div id="header">
		<h2 id="logo"><a><img src="./cmn/img/logo_xfinity.png" alt="Xfinity" title="Xfinity" /></a></h2>
	</div> <!-- end #header -->
	
	<!--Main Content-->
	<div id="main-content">
		<div id="sub-header"></div><!-- end #sub-header -->
		<div id="content">
			<h1>Admin Tool Login</h1>
			<div id="login" class="module forms">
				<form action="check.php" method="post" id="pageForm"  onsubmit="return f();">
				<div class="form-row">
					<p>Please login to manage your router.</p>
				</div>
				<table>
					<tr>
						<td><label for="username">Username:</label></td>
						<td><input type="text"     id="username" name="username" size="20" class="text" autocomplete="off" /></td>
					</tr>
					<tr>
						<td><label for="password">Password:</label></td>
						<td><input type="password" id="password" name="password" size="20" class="text" autocomplete="off" /></td>
					</tr>
				</table>
				<div class="form-btn">
					<input type="submit" class="btn" value="Login" />
				</div>
				</form>
			</div>
		</div><!-- end #content -->
	</div> <!-- end #main-content-->
	
		<!--Footer-->
		<div id="footer">
			<ul id="footer-links">
				<li class="first-child"><a href="http://www.xfinity.com" target="_blank">Xfinity.com</a></li>
				<li style="list-style:none outside none; margin-left:10px">&#8226;&nbsp;&nbsp;<a href="https://customer.comcast.com/" target="_blank">customerCentral</a></li>
				<li style="list-style:none outside none; margin-left:10px">&#8226;&nbsp;&nbsp;<a href="http://customer.comcast.com/userguides" target="_blank">User Guide</a></li>
			</ul>
		</div> <!-- end #footer -->
</div> <!-- end #container -->
</body>
</html>
