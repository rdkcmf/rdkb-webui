<?php include('includes/header.php'); ?>

<!-- $Id: restore_reboot.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>


<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Troubleshooting > Reset / Restore Gateway", "nav-restore-reboot");

	//Having only Reset and Restore Factory Defaults under troubleshooting page in bridge mode is good enough.
	//hide 2 3 4 6
	if ("router" != "<?php echo $_SESSION["lanMode"]; ?>") {
		$("#div2, #div3, #div4, #div6").hide();
	}
	else if ("admin" == "<?php echo $_SESSION["loginuser"]; ?>") {
		$("#div6").hide();
		$("#div5").addClass("odd");
	}

//start by licha
$('#btn1').click(function(e) {
	e.preventDefault();
	
	var href = $(this).attr("href");
	var message = ($(this).attr("title").length > 0) ? "Are you sure you want to " + $(this).attr("title") + "?" : "Are you sure?";
	var info = new Array("btn1", "Router,Wifi,VoIP,Dect,MoCA");

	jConfirm(
	message+"<br/><br/><strong>WARNING:</strong> Gateway will be rebooted!<br/>Incoming/outgoing call and internet connection will be interrupted!"
	, "Are You Sure?"
	,function(ret) {
	if(ret) {
		jProgress('Check telephony line status, please wait...', 60);
		$.post(
			"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
			{"get_statusx":"true"},
			function(msg)
			{
				jHide();
				if ("Off-Hook" == msg.linexhook){
					jConfirm(
					'Phone is Off-Hook, do you want to proceed anyway?'
					, 'Are You Sure?'
					, function(ret){
						if(ret){
							setResetInfo(info);		//async
						}
					});
				}
				else{
					setResetInfo(info);		//async
					setTimeout(function(){
						location.href = "home_loggedout.php";
					}, 10000);
				}
			},
			"json"     
		);
	}
	});	
});

$('#btn2').click(function(e) {
	e.preventDefault();
	
	var href = $(this).attr("href");
	var message = ($(this).attr("title").length > 0) ? "Are you sure you want to " + $(this).attr("title") + "?" : "Are you sure?";
	var info = new Array("btn2", "Wifi");

	jConfirm(
	message+"<br/><br/><strong>WARNING:</strong> Wi-Fi will be unavailable for at least 30 seconds!"
	, "Are You Sure?"
	,function(ret) {
	if(ret) {
		setResetInfo(info);
	}
	});	
});

$('#btn3').click(function(e) {
	e.preventDefault();
	
	var href = $(this).attr("href");
	var message = ($(this).attr("title").length > 0) ? "Are you sure you want to " + $(this).attr("title") + "?" : "Are you sure?";
	var info = new Array("btn3", "Wifi,Router");

	jConfirm(
	message+"<br/><br/><strong>WARNING:</strong> Wi-Fi will be unavailable for at least 30 seconds!"
	, "Are You Sure?"
	,function(ret) {
	if(ret) {
		setResetInfo(info);
	}
	});	
});

$('#btn4').click(function(e) {
	e.preventDefault();
	
	var href = $(this).attr("href");
	var message = ($(this).attr("title").length > 0) ? "Are you sure you want to " + $(this).attr("title") + "?" : "Are you sure?";
	var info = new Array("btn4", "Wifi");

	jConfirm(
	message+"<br/><br/><strong>WARNING:</strong> Wi-Fi will be unavailable for at least 30 seconds!"
	, "Are You Sure?"
	,function(ret) {
	if(ret) {
		setResetInfo(info);
	}
	});	
});

$('#btn5').click(function(e) {
	e.preventDefault();
	
	var href = $(this).attr("href");
	var message = ($(this).attr("title").length > 0) ? "Are you sure you want to " + $(this).attr("title") + "?" : "Are you sure?";
	var info = new Array("btn5", "Router,Wifi,VoIP,Dect,MoCA");

	jConfirm(
	message+"<br/><br/><strong>WARNING:</strong> Gateway will be rebooted!<br/>Incoming/outgoing call and internet connection will be interrupted!"
	, "Are You Sure?"
	,function(ret) {
	if(ret) {
		jProgress('Check telephony line status, please wait...', 60);
		$.post(
			"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
			{"get_statusx":"true"},
			function(msg)
			{
				jHide();
				if ("Off-Hook" == msg.linexhook){
					jConfirm(
					'Phone is Off-Hook, do you want to proceed anyway?'
					, 'Are You Sure?'
					, function(ret){
						if(ret){
							setResetInfo(info);		//async
						}
					});
				}
				else{
					setResetInfo(info);		//async
					setTimeout(function(){
						location.href = "home_loggedout.php";
					}, 10000);
				}
			},
			"json"     
		);
	}
	});	
});

$('#btn6').click(function(e) {
	e.preventDefault();
	
	var href = $(this).attr("href");
	var message = ($(this).attr("title").length > 0) ? "Are you sure you want to " + $(this).attr("title") + "?" : "Are you sure?";
	var info = new Array("btn6", "password");

	jConfirm(
	message
	, "Are You Sure?"
	,function(ret) {
	if(ret) {
		setResetInfo(info);
	}
	});	
});

	if("Enabled"=="<?php echo $_SESSION["psmMode"]; ?>") {
		$('#btn2,#btn3,#btn4').unbind("click").click(function(){
			jAlert("Your device is in battery mode that can't reset or restore Wi-Fi.");
		});
	}

function setResetInfo(info) {
//	alert(info);
	var jsonInfo = '["' + info[0] + '","' + info[1]+ '","' + "<?php echo $_SESSION["loginuser"]; ?>" + '"]';
	
	jProgress('This may take several seconds...', 60);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_Reset_Restore.php",
		data: { resetInfo: jsonInfo },
		success: function(data){
			jHide();
			if (data.reboot) {
				jProgress("Please wait for rebooting ...", 999999);
				setTimeout(checkForRebooting, 4 * 60 * 1000);
			}
			else if(data.wifi) {
				jProgress("Restarting Wi-Fi radios in upto 90 seconds...", 120);
				//we don't know if Wi-Fi is ready (eth client can restart wifi also), so just delay 90 seconds
				setTimeout(function(){jHide();}, 90 * 1000);
			}
		},
		error: function(){            
			jHide();
			//jAlert("Failure, please try again.");
        }
	});
}

//end by licha    
});

function checkForRebooting() {
	$.ajax({
		type: "GET",
		url: "index.php",
		timeout: 10000,
		success: function() {
			/* goto login page */
			window.location.href = "index.php";
		},
		error: function() {
			/* retry after 2 minutes */
			setTimeout(checkForRebooting, 2 * 60 * 1000);
		}
	});
}
</script>

<div id="content">
  	<h1>Troubleshooting > Reset / Restore Gateway</h1>
	<div id="educational-tip">
		<p class="tip">Reset or restore the Gateway.</p>
		<p class="hidden">If you're having problems with the Gateway, click <strong>RESET</strong> to restart or <strong>RESTORE</strong> to the default factory settings.</p>
		<p class="hidden">CAUTION:<strong> RESTORE </strong>will erase all your settings (passwords, parental controls, firewall).</p>

	</div>
	<form>
	<div class="module forms" id="restore">
		<h2>Reset / Restore Gateway</h2>
		<div id="div1" class="form-row odd">
			<span class="readonlyLabel"><a href="#" class="btn" id="btn1" title="Reset the Gateway" style="text-transform : none;">RESET</a></span> 
			<span class="value">Press "Reset" button to restart the gateway.</span>
		</div>
		<div id="div2" class="form-row">
			<span class="readonlyLabel"><a href="#" class="btn" id="btn2" title="Reset Wi-Fi Module" style="text-transform : none;">RESET Wi-Fi MODULE</a></span> 
			<span class="value">Press "Reset Wi-Fi Module" to restart just the Wi-Fi Module only.</span>
		</div>
		<div id="div3" class="form-row odd">
			<span class="readonlyLabel"><a href="#" class="btn" id="btn3" title="Reset the Wi-Fi Gateway" style="text-transform : none;">RESET Wi-Fi ROUTER</a></span> 
			<span class="value">Press "Reset Wi-Fi Router" to restart Wi-Fi and Router modules.</span>
		</div>
		<div id="div4" class="form-row">
			<span class="readonlyLabel"><a href="#" class="btn" id="btn4" title="Restore manufacturer defaults for Wi-Fi Only" style="text-transform : none;">RESTORE Wi-Fi SETTINGS</a></span> 
			<span class="value">Press "Restore Wi-Fi Settings" to activate your Gateway <span style="padding-left:231px">Default Settings for Wi-Fi only. Only your Wi-Fi settings will be lost.</span></span>
		</div>
		<div id="div6" class="form-row odd">
			<span class="readonlyLabel"><a href="#" class="btn" id="btn6" title="Reset Password" style="text-transform : none;">RESET PASSWORD</a></span> 
			<span class="value">Press "Reset Password" to reset User Admin tool password to factory <span style="padding-left:231px">default</span></span>
		</div>
		<div id="div5" class="form-row">
			<span class="readonlyLabel"><a href="#" class="btn" id="btn5" title="Restore Factory settings" style="text-transform : none;">RESTORE FACTORY SETTINGS</a></span> 
			<span class="value">Press "Restore Factory Settings" to activate your Gateway <span style="padding-left:231px">Default Settings. All your previous settings will be lost.</span></span>
		</div>
	</div> <!-- end .module -->
	</form>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
