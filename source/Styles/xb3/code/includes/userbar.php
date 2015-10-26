<!--dynamic generate user bar icon and tips-->

<?php

	session_start();

	$sta_batt = $_SESSION['sta_batt'];
	$battery_class = $_SESSION['battery_class'];
	$sta_inet = $_SESSION['sta_inet'];
	$sta_wifi = $_SESSION['sta_wifi'];
	$sta_moca = $_SESSION['sta_moca'];
	$sta_fire = $_SESSION['sta_fire'];

?>

<script type="text/javascript">
$(document).ready(function() {

	/*
	* get status when hover or tab focused one by one
	* but for screen reader we have to load all status once
	* below code can easily rollback
	*/

	//update user bar
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_userbar.php",
		data: { configInfo: "noData" },
		dataType: "json",
		success: function(msg) {
			// theObj.find(".tooltip").html(msg.tips);
			for (var i=0; i<msg.tags.length; i++){
				$("#"+msg.tags[i]).find(".tooltip").html(msg.tips[i]);
				$("#"+msg.tags[i]).removeClass("off");
				if(msg.mainStatus[i]=="false")$("#"+msg.tags[i]).addClass("off");
				if(msg.tags[i] === "sta_fire")
				{
					if (!(("High"== msg.mainStatus[i]) || ("Medium"==msg.mainStatus[i]))) 
					{
						$("#"+msg.tags[i]).addClass("off");
					}
					$("#sta_fire a>span").text(msg.mainStatus[i] + " Security")
				}
			}
			//$sta_batt,$battery_class
			$("#sta_batt a").text(msg.mainStatus[4]+"%");
			$("#sta_batt > div > span").removeClass().addClass(msg.mainStatus[5]);
		},
		error: function(){
			// does something
		}
	});

	//when clicked on this page, restart timer
	var jsInactTimeout = parseInt("<?php echo $_SESSION["timeout"]; ?>") * 1000;
	// if ("<?php echo $_DEBUG; ?>") jsInactTimeout = 5000;	// 5 seconds debug
	
	// var h_timer = setTimeout('alert("You are being logged out due to inactivity."); location.href="home_loggedout.php";', jsInactTimeout);
	var h_timer = null;
	$(document).click(function() {
		// do not handle click if no-login for GA
		// if ("" == "<?php echo (isset($_SESSION["loginid"])?$_SESSION["loginid"]:""); ?>") {
			// return;
		// }			
	
		// do not handle click event when count-down show up
		if ($("#count_down").length > 0) {
			return;
		}
		// console.log(h_timer);
		
		clearTimeout(h_timer);
		h_timer = setTimeout(function(){
			var cnt		= 60;
			var h_cntd  = setInterval(function(){
				$("#count_down").text(--cnt);
				// (1)stop counter when less than 0, (2)hide warning when achieved 0, (3)add another alert to block user action if network unreachable
				if (cnt<=0) {
					clearInterval(h_cntd);	
					jAlert("You have been logged out due to inactivity!");
					location.href="home_loggedout.php";
				}
			}, 1000);
			// use jAlert instead of alert, or it will not auto log out untill OK pressed!
			jAlert('Press <b>OK</b> to continue session. Otherwise you will be logged out in <span id="count_down" style="font-size: 200%; color: red;">'+cnt+'</span> seconds!'
			, 'You are being logged out due to inactivity!'
			, function(){
				clearInterval(h_cntd);
			});
		}
		, jsInactTimeout);
	}).trigger("click");
	
	// show pop-up info when focus
	$("#status a").focus(function() {
		$(this).mouseenter();
	});
	
	// disappear previous pop-up
	$("#status a").blur(function() {
		$(".tooltip").hide();
	});

});

</script>

<style>
#status a:link, #status a:visited {
	text-decoration: none;
	color: #808080;
}
</style>

<ul id="userToolbar" class="on">
	<li class="first-child"> Hi <?php echo $_SESSION["loginuser"]; ?></li>
	<li style="list-style:none outside none; margin-left:0">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;<a href="home_loggedout.php" tabindex="0">Logout</a></li>
	<?php
		if($_SESSION["loginuser"] == "admin")
		echo '<li style="list-style:none outside none; margin-left:0">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;<a href="password_change.php" tabindex="0">Change Password</a></li>';
	?>
</ul>

<ul id="status">
	<?php
	echo '<li id="sta_batt" class="battery first-child"><div class="sprite_cont"><span class="'.$battery_class.'" ><img src="./cmn/img/icn_battery.png"  alt="Battery icon" title="Battery icon" /></span></div><a role="toolbar" href="javascript: void(0);" tabindex="0">'.$sta_batt.'%</a>
		<!-- NOTE: When this value changes JS will set the battery icon -->
	</li>';

	if ("true"==$sta_inet) {
		echo '<li id="sta_inet" class="internet"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Internet Online" /></span><a href="javascript: void(0);" tabindex="0">Internet<div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_inet" class="internet off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Internet Offline" /></span><a href="javascript: void(0);" tabindex="0">Internet<div class="tooltip">Loading...</div></a></li>';
	}
	
	if ("true"==$sta_wifi) {
		echo '<li id="sta_wifi" class="wifi"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="WiFi Online" /></span><a href="javascript: void(0);" tabindex="0">Wi-Fi<div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_wifi" class="wifi off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="WiFi Offline" /></span><a href="javascript: void(0);" tabindex="0">Wi-Fi<div class="tooltip">Loading...</div></a></li>';
	}
	
	if ("true"==$sta_moca) {
		echo '<li id="sta_moca" class="MoCA"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="MoCA Online" /></span><a href="javascript: void(0);" tabindex="0">MoCA<div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_moca" class="MoCA off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="MoCA Offline" /></span><a href="javascript: void(0);" tabindex="0">MoCA<div class="tooltip">Loading...</div></a></li>';
	}
	
	/*if ("true"==$sta_dect) {
		echo '<li id="sta_dect" class="DECT"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="DECT Online" /></span><a href="javascript: void(0);" tabindex="0">DECT<div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_dect" class="DECT off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="DECT Offline" /></span><a href="javascript: void(0);" tabindex="0">DECT<div class="tooltip">Loading...</div></a></li>';
	}*/
	
	if (("High"==$sta_fire) || ("Medium"==$sta_fire)) {
		echo '<li id="sta_fire" class="security last"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Security On" /></span><a href="javascript: void(0);" tabindex="0"><span>'.$sta_fire.' Security</span><div class="tooltip">Loading...</div></a></li>';
	} else {
		echo '<li id="sta_fire" class="security last off"><span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Security Off" /></span><a href="javascript: void(0);" tabindex="0"><span>'.$sta_fire.' Security</span><div class="tooltip">Loading...</div></a></li>';
	}
	?>
</ul>
