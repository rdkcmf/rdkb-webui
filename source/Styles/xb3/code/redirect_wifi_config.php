<?php
$CONFIGUREWIFI	= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_ConfigureWiFi");
if(strstr($CONFIGUREWIFI, "false"))	header('Location:index.php');
?>

<?php include('includes/rediection_header.php'); ?>

<div id="sub-header">
</div><!-- end #sub-header -->

<?php
//WiFi Defaults are expected to be same for 2.4Ghz and 5Ghz
$network_name	= getStr("Device.WiFi.SSID.1.SSID");
$network_name 	= explode('-2.4', $network_name);
$network_name	= $network_name[0];
$network_pass	= getStr("Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_KeyPassphrase");

$ipv4_addr = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress");
?>

<script type="text/javascript" src="cmn/js/lib/jquery.alerts.progress.js"></script>
<script type="text/javascript">

$(document).ready(function() {
    comcast.page.init("Private Wi-Fi Configuration", "nav-wifi-config");

	$("#password_show").change(function() {
		if ($("#password_show").is(":checked")) {
			document.getElementById("password_field").innerHTML = 
			'<input type="text"     size="23" id="network_password" name="network_password" class="text" value="' + $("#network_password").val() + '" />'
		}
		else {
			document.getElementById("password_field").innerHTML = 
			'<input type="password" size="23" id="network_password" name="network_password" class="text" value="' + $("#network_password").val() + '" />'
		}
	});
	
	$.validator.addMethod("netw_pass", function(value, element, param) {
		return !param || /^[a-zA-Z0-9\-_.]{8,20}$/i.test(value);
	}, "8-20 characters. Alphanumeric only. No spaces. Case sensitive.");

	$.validator.addMethod("ssid_name", function(value, element, param) {
		return !param || /^[a-zA-Z0-9\-_.]{3,31}$/i.test(value);
	}, "3 to 31 characters combined with alphabet, digit, underscore, hyphen and dot");

    	$.validator.addMethod("not_hhs", function(value, element, param) {
		//prevent users to set XHSXXX or Xfinitywifixxx as ssid
		return value.toLowerCase().indexOf("xhs")==-1 && value.toLowerCase().indexOf("xfinitywifi")==-1;
	}, 'SSID containing "XHS" and "Xfinitywifi" are reserved !');

    	$.validator.addMethod("not_hhs2", function(value, element, param) {
		//prevent users to set optimumwifi or TWCWiFi  or CableWiFi as ssid
		return value.toLowerCase().indexOf("optimumwifi")==-1 && value.toLowerCase().indexOf("twcwifi")==-1 && value.toLowerCase().indexOf("cablewifi")==-1;
	}, 'SSID containing "optimumwifi", "TWCWiFi" and "CableWiFi" are reserved !');

/*
wep 64 ==> 5 Ascii characters or 10 Hex digits
wep 128 ==> 13 Ascii characters or 26 Hex digits
wpapsk ==> 8 to 63 Ascii characters or 64 Hex digits
wpa2psk ==> 8 to 63 Ascii characters
*/

    	$("#pageForm").validate({
    		debug: true,
    		rules: {
				network_name: {
					ssid_name: true,
					not_hhs: true,
					not_hhs2: true
				},
				network_password: {
					netw_pass: true
			   	}
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.closest("td") );
			},
			submitHandler:function(form){
				click_save();
			}
    	});	

});

function trigger_save(jsConfig){

	jProgress('We’re updating your WiFi network settings. Please give us a minute to finish up.', 600);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_wireless_network_configuration_redirection.php",
		data: { rediection_Info: jsConfig },
		success: function(msg) {
			jHide();
			location.href = "index.php";
		},
		error: function(){
			jHide();
			jProgress('Please connect to WiFi using new SSID and password.', 600);
			setTimeout(function(){ startPing(); }, 10000); //ping after 10s
		}
	});
}

function click_save()
{
	var network_name	= $("#network_name").val();
	var network_password	= $("#network_password").val();
	
	if("<?php echo $network_name;?>" == network_name){
		jAlert("PLease change the Network Name (SSID).");
		return;
	}
	
	if("<?php echo $network_pass;?>" == network_password){
		jAlert("PLease change the Network Password.");
		return;
	}

	var jsConfig = '{"network_name":"'+network_name
	+'", "network_password":"'+network_password
	+'"}';	
		
	jAlert('Please connect to WiFi using new SSID "'+network_name +'" and password "'+network_password+'".\n Press OK to continue.', 'Alert', function(){
		trigger_save(jsConfig);
	});
}

/*------	check if gateway is accesible [ http://customer.comcast.com ]	------*/
	function startPing(){
		$.ajax({
		    url: 'http://<?php echo $ipv4_addr; ?>/check.php',
		    async: true,
		    data: {pingTest:"test"},
		    success: function() {
				location.href = "http://customer.comcast.com";
				//console.log("success");
			},
		    error: function() {
			setTimeout(function(){
				//console.log("error");
				startPing();
			}, 2000);},
		    timeout: 10000
		});
	}

</script>

<style>
table, th, td {
	border: 0; 
}
td {
   padding:0; margin:0;
}
</style>

<div id="content">
	<h1>Private WiFi Configuration</h1>
	<div id="educational-tip">
		<p class="tip">Manage your WiFi network settings.</p>
		<p class="hidden"><strong>Network Name (SSID):</strong> Identifies your home network from other nearby networks. Your default name can be found on the bottom label of the Gateway, but can be changed for easier identification.</p>
		<p class="hidden"><strong>Network Password(Key):</strong> Required by WiFi products to connect to your secure network. The default setting can be found on the bottom label of the Gateway. </p>
	</div>

	<div class="module forms">
		<form action="#TBD" method="post" id="pageForm">
		<h2>Personalize Your WiFi Network</h2>
		
		<table>
		<tbody>
		 <tr><td><span><br/></span></td></tr>
		 <tr>
      		  <td style="border-right: groove #ffffff;">
			<label for="network_name" style="margin: 4px 10px 0 0;">Network Name (SSID):</label>
			<input type="text" size="23" value="" id="network_name" name="network_name" class="text" />
		  </td>
		  <td>
			<span>We recomend you to change the network name to a name you can remember.<br/>e.g. SmithFamilyWiFi</span>
		  </td>
		 </tr>
		 <tr><td><span><br/></span></td></tr>
		 <tr>
		  <td style="border-right: groove #ffffff;" class="odd">
			<label for="network_password" style="margin: 4px 10px 0 0;">Network Password:</label>
			<span id="password_field"><input type="password" size="23" id="network_password" name="network_password" class="text" value="" />
		  </td>
		  <td class="odd">
			<span>Hint: Create unique phrase you will remember.<br/><br/>8-20 characters. Alphanumeric only. No spaces. Case sensitive.</span>
		  </tr>
		  <tr>
		   <td width=65%">
			<div id="div_password_show">
				<label for="password_show">Show Network Password:</label>
				<span class="checkbox"><input type="checkbox" id="password_show" name="password_show" /></span>
			</div>
		   </td>
		  </tr>
		</tbody>
		</table>
		<div class="form-row form-btn">
			<input type="submit" class="btn confirm" id="save_settings" name="save_settings" value="NEXT" />
		</div>
		</form>
	</div> <!-- end .module -->
</div><!-- end #content -->

<!-- $Id: footer.php 2976 2009-09-02 21:42:51Z cporto $ -->
		</div> <!-- end #main-content-->
		
		<!--Footer-->
		<div id="footer">
			<ul id="footer-links">
				<br/><br/>
			</ul>
		</div> <!-- end #footer -->
	</div> <!-- end #container -->
<script type="text/javascript">
$(document).ready(function() {
	// focus current page link, must after page.init()
	//$('#nav [href="'+location.href.replace(/^.*\//g, '')+'"]').focus();		// need a "skip nav" function
	$("#skip-link").click(function () {
        $('#content').attr('tabIndex', -1).focus();  //this is to fix skip-link doesn't work on webkit-based Chrome
    });

	// change radio-btn status and do ajax when press "enter"
	//$(".radio-btns a").keydown(function(event){
	$(".radio-btns a").keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(13 == keycode){
			if (!$(this).parent(".radio-btns").find("li").hasClass("selected")){
				return;		// do nothing if has disabled class, don't detect disabled attr for radio-btn
			}
			// console.log($(this).find(":radio").hasClass("disabled"));
			$(this).find(":radio").trigger('click');
			$(this).find(":radio").trigger('change');
			$(this).parent(".radio-btns").radioToButton();
		}
	});
	
	// press Esc to skip menu and goto first control of content
	// Esc:keypress:which is zero in FF, Esc:keypress is not work in Chrome
	$("#nav").keydown(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(27 == keycode){
			$("#content textarea:eq(0)").focus();
			$("#content input:eq(0)").focus();
			$("#content a:eq(0)").focus();			// high priority element to focus			
		}
		// alert(event.keyCode+"---"+event.which+"---"+event.charCode);		
	});
	
	/* changes for high contrast mode */
	$.highContrastDetect({useExtraCss: true, debugInNormalMode: false});
	if ($.__isHighContrast) {
		/* change plus/minus tree indicator of nav menu */
		$("#nav a.top-level").prepend('<span class="hi_nav_top_indi">[+]</span>');
		$("#nav a.folder").prepend('<span class="hi_nav_folder_indi">[+]</span>');
		$("#nav a.top-level-active span.hi_nav_top_indi").text("[-]");
		$("#nav a.folder").click(function() {
			/* this should be called after nav state changed */
			var $link = $(this);
			if ($link.hasClass("folder-open")) {
				$link.children("span.hi_nav_folder_indi").text("[-]");
			}
			else {
				$link.children("span.hi_nav_folder_indi").text("[+]");
			}
		});
	}

	/*
	*	these 3 sections for radio-btn accessibility, as a workaround, maybe should put at the front of .ready().
	*/
	// add "role" and "title" for ARIA, attr may need to be embedded into html
	$(".radio-btns a").each(function(){
		$(this).attr("role", "radio").attr("title", $(this).closest("ul").prev().text() + $(this).find("label").text());
	});
	
	// monitor "aria-checked" status for JAWS, NOTE: better depends on input element
	$(".radio-btns").change(function(){
		$(this).find("a").each(function(){
			$(this).attr("aria-checked", $(this).find("input").attr("checked") ? "true" : "false");
		});
	});
	
	//give the initial status, do not trigger change above
	$(".radio-btns").find("a").each(function(){
		$(this).attr("aria-checked", $(this).find("input").attr("checked") ? "true" : "false");
	});

});
</script>	
</body>
</html>
