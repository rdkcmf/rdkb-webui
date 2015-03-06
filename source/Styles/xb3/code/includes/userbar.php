<!--dynamic generate user bar icon and tips-->

<script type="text/javascript">
$(document).ready(function() {
	var a = parseInt("<?php echo getStr("Device.X_CISCO_COM_CableModem.Battery.RemainingCharge");?>");
	var b = parseInt("<?php echo getStr("Device.X_CISCO_COM_CableModem.Battery.ActualCapacity");?>");
	var sta_batt = (a<=b && a && b) ? Math.round(100*a/b) : 0;
	var sta_inet = "<?php echo ("OPERATIONAL"==getStr("Device.X_CISCO_COM_CableModem.CMStatus")) ? "true" : "false";?>";
	<?php
		$sta_wifi = "false";
		if("Disabled"==$_SESSION["psmMode"]){
			$ssids = explode(",", getInstanceIds("Device.WiFi.SSID."));
			foreach ($ssids as $i){
				$r = (2 - intval($i)%2);	//1,3,5,7==1(2.4G); 2,4,6,8==2(5G)
				if ("true" == getStr("Device.WiFi.Radio.$r.Enable") && "true" == getStr("Device.WiFi.SSID.$i.Enable")){	//bwg has radio.enable, active status is “at least one SSID and its Radio is enabled”
					$sta_wifi = "true";
					break;
				}
			}	
		}
	?>
	var sta_wifi = "<?php echo $sta_wifi;?>";
	var sta_moca = "<?php if("Disabled"==$_SESSION["psmMode"]) echo getStr("Device.MoCA.Interface.1.Enable");?>";
	var sta_dect = "<?php echo getStr("Device.X_CISCO_COM_MTA.Dect.Enable");?>";
	var sta_fire = "<?php echo getStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevel");?>";
	
	// sta_batt = "61";
	// sta_inet = "true";
	// sta_wifi = "false";
	// sta_moca = "true";
	// sta_dect = "false";
	// sta_fire = "Medium";

	// $("#sta_batt").append(sta_batt+'%');
	$("#sta_batt a").text(sta_batt+'%');
	if ("true"==sta_inet) {
		$("#sta_inet").removeClass("off").find(".sprite_cont > img").attr("alt", "Internet Online");
	}
	if ("true"==sta_wifi) {
		$("#sta_wifi").removeClass("off").find(".sprite_cont > img").attr("alt", "WiFi Online");
	}
	if ("true"==sta_moca) {
		$("#sta_moca").removeClass("off").find(".sprite_cont > img").attr("alt", "MoCA Online");
	}
	if ("true"==sta_dect) {
		$("#sta_dect").removeClass("off").find(".sprite_cont > img").attr("alt", "DECT Online");
	}
	if (("High"==sta_fire) || ("Medium"==sta_fire)) {
		$("#sta_fire").removeClass("off").find(".sprite_cont > img").attr("alt", "Security On");
	}
	// $("#sta_fire>span:eq(1)").html(sta_fire+' Security');
	$("#sta_fire a span").text(sta_fire+' Security');

	/*
	* get status when hover or tab focused one by one
	* but for screen reader we have to load all status once
	* below code can easily rollback
	*/
	
	// $("[id^='sta_']:not(#sta_batt)").one("mouseenter",function(){
		// var theObj = $(this);
		// var target = theObj.attr("id");
		// var status = ("sta_fire"==target)? sta_fire : !(theObj.hasClass("off"));
		// var jsConfig = '{"status":"'+status+'", "target":"'+target+'"}';
		var jsConfig = '{"target":"'+"sta_inet,sta_wifi,sta_moca,sta_dect,sta_fire"
		+'", "status":"'+sta_inet+','+sta_wifi+','+sta_moca+','+sta_dect+','+sta_fire+'"}';

		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_userbar.php",
			data: { configInfo: jsConfig },
			dataType: "json",
			success: function(msg) {
				// theObj.find(".tooltip").html(msg.tips);
				for (var i=0; i<msg.tags.length; i++){
					$("#"+msg.tags[i]).find(".tooltip").html(msg.tips[i]);
				}
			},
			error: function(){
				// does something
			}
		});
	// });
	
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
	<li style="list-style:none outside none; margin-left:0">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;<a href="password_change.php" tabindex="0">Change Password</a></li>
</ul>

<ul id="status">
	<li id="sta_batt" class="battery first-child">
		<div class="sprite_cont"><span><img src="./cmn/img/icn_battery.png"  alt="Battery icon" title="Battery icon" /></span></div><a role="toolbar" href="javascript: void(0);" tabindex="0">60%</a>
		<!-- NOTE: When this value changes JS will set the battery icon -->
	</li>

	<li id="sta_inet" class="internet off">
		<span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Internet Offline" /></span><a href="javascript: void(0);" tabindex="0">Internet
		<div class="tooltip">Loading...</div></a>
	</li>
	
	<li id="sta_wifi" class="wifi off">
		<span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="WiFi Offline" /></span><a href="javascript: void(0);" tabindex="0">Wi-Fi
		<div class="tooltip">Loading...</div></a>
	</li>
	
	<li id="sta_moca" class="MoCA off">
		<span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="MoCA Offline" /></span><a href="javascript: void(0);" tabindex="0">MoCA
		<div class="tooltip">Loading...</div></a>
	</li>
	
	<li id="sta_dect" class="DECT off">
		<span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="DECT Offline" /></span><a href="javascript: void(0);" tabindex="0">DECT
		<div class="tooltip">Loading...</div></a>
	</li>
	
	<li id="sta_fire" class="security last off">
		<span class="value on-off sprite_cont"><img src="./cmn/img/icn_on_off.png" alt="Security Off" /></span><a href="javascript: void(0);" tabindex="0"><span>Custom Security</span>
		<div class="tooltip">Loading...</div></a>
	</li>
</ul>
