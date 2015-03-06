<?php include('includes/header.php'); ?>

<!-- $Id: wizard_step1.php 2943 2009-08-25 20:58:43Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">

$(document).ready(function() {
    comcast.page.init("Gateway > Home Network Wizard - Step 1", "nav-wizard");

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
		var pwd_t = $(this).attr("checked") ? 'type="text"' : 'type="password"';
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
				window.location = "wizard_step2.php";
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
	var jsConfig = '{"newPassword": "' + newPwd + '", "instanceNum": "' + intNum + '", "oldPassword": "' + oldPwd + '"}';
	
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
	<h1>Gateway > Home Network Wizard - Step 1</h1>

	<div id="educational-tip">
		<p class="tip">The Home Network Wizard walks you through settings you may want to change for better network security.</p>
		<p class="hidden">If you have never changed the default information, the <strong>Current Password </strong>is <i>password</i>. Step 1 changes the Admin Tool password (the password to log into this site in the future) .</p>
	</div>
	
	<div class="module forms">
		<!--form action="wizard_step2.php" method="post" id="pageForm"-->
		<form method="post" id="pageForm">
			<h2>Step 1 of 2</h2>
			<p class="summary">To configure your home network, we need some basic information</p>
			
			<div class="form-row password">
				<label for="oldPassword">Current Password:</label>
				<input type="password" value="" name="oldPassword" id="oldPassword" autocomplete="off" />
   			</div>
			
			<div class="form-row odd password">
				<label for="userPassword">New Password:</label>
				<input type="password" value="" name="userPassword" id="userPassword" autocomplete="off" />
			</div>
			
			<div class="form-row password">
				<label for="verifyPassword">Re-enter New Password:</label>
				<input type="password" value="" name="verifyPassword" id="verifyPassword" autocomplete="off" />
			</div>

			<div class="form-row odd">
				<label for="password_show">Show Typed Password:</label>
				<span class="checkbox"><input type="checkbox" id="password_show" name="password_show" /></span>
			</div> 

			<p class="footnote">8-20 characters. Alphanumeric only. No spaces. Case sensitive.</p>
			<div class="form-row form-btn">
				<input id="submit_pwd" type="submit" value="Next Step" class="btn" />
			</div>
		</form>
	</div> <!-- end .module -->	
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
