<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>XFINITY Smart Internet</title>
		<link rel="stylesheet" href="cmn/css/styles.css">
	</head>
<!-- for Dual Band Network -->
<style>
.confirm-text{
	font-family: 'xfinSansLt';
	font-size: 14px;
	line-height: 24px;
	color: #fff;
	-webkit-font-smoothing: antialiased;
}
.left-settings	{
	padding: 10px 30px 0px 0px;
	text-align: right;
	font-family: 'xfinSansLt';
	font-size: 14px;
	line-height: 24px;
	color: #888;
	-webkit-font-smoothing: antialiased;
}
</style>
<?php include('includes/utility.php'); ?>
<?php
	// should we allow to Configure WiFi
	// redirection logic - uncomment the code below while checking in
	$DeviceInfo_param = array(
		"CONFIGUREWIFI" => "Device.DeviceInfo.X_RDKCENTRAL-COM_ConfigureWiFi",
		"CaptivePortalEnable"	=> "Device.DeviceInfo.X_RDKCENTRAL-COM_CaptivePortalEnable",
		"CloudPersonalizationURL"	=> "Device.DeviceInfo.X_RDKCENTRAL-COM_CloudPersonalizationURL",
		"CloudUIEnable"	=> "Device.DeviceInfo.X_RDKCENTRAL-COM_CloudUIEnable",
	);
	$DeviceInfo_value	= KeyExtGet("Device.DeviceInfo.", $DeviceInfo_param);
	$CONFIGUREWIFI = $DeviceInfo_value["CONFIGUREWIFI"];
	$CaptivePortalEnable	= $DeviceInfo_value["CaptivePortalEnable"];
	$CloudPersonalizationURL	= $DeviceInfo_value["CloudPersonalizationURL"];
	$CloudUIEnable	= $DeviceInfo_value["CloudUIEnable"];
	if(!strcmp($CaptivePortalEnable, "false") || !strcmp($CONFIGUREWIFI, "false")) {
		header('Location:index.php');
		exit;
	}
	//WiFi Defaults are same for 2.4Ghz and 5Ghz
	$wifi_param = array(
		"network_name"	 => "Device.WiFi.SSID.1.SSID",
		"network_name1"	 => "Device.WiFi.SSID.2.SSID",
		"KeyPassphrase"	 => "Device.WiFi.AccessPoint.1.Security.X_COMCAST-COM_KeyPassphrase",
		"KeyPassphrase1" => "Device.WiFi.AccessPoint.2.Security.X_COMCAST-COM_KeyPassphrase",
	);
	$wifi_value = KeyExtGet("Device.WiFi.", $wifi_param);
	$network_name	= $wifi_value['network_name'];
	$network_pass	= $wifi_value['KeyPassphrase'];
	$network_name1	= $wifi_value['network_name1'];
	$network_pass1	= $wifi_value['KeyPassphrase1'];
	$ipv4_addr 	= getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress");
	// logic to figure out LAN or WiFi from Connected Devices List
	// get clients IP
	// Known prefix
	$v4mapped_prefix_hex = '00000000000000000000ffff';
	$v4mapped_prefix_bin = pack("H*", $v4mapped_prefix_hex);
	// Parse
	$addr = $_SERVER['REMOTE_ADDR'];
	$addr_bin = inet_pton($addr);
	if( $addr_bin === FALSE ) {
	  // Unparsable? How did they connect?!?
	  die('Invalid IP address');
	}
	// Check prefix
	if( substr($addr_bin, 0, strlen($v4mapped_prefix_bin)) == $v4mapped_prefix_bin) {
	  // Strip prefix
	  $addr_bin = substr($addr_bin, strlen($v4mapped_prefix_bin));
	}
	// Convert back to printable address in canonical form
	$clientIP = inet_ntop($addr_bin);
	// cross check IP in Connected Devices List
	function ProcessLay1Interface($interface){
		if (stristr($interface, "WiFi")){
			if (stristr($interface, "WiFi.SSID.1")) {
				$host['networkType'] = "Private";
				$host['connectionType'] = "Wi-Fi 2.4G";
			}
			elseif (stristr($interface, "WiFi.SSID.2")) {
				$host['networkType'] = "Private";
				$host['connectionType'] = "Wi-Fi 5G";
			}
			else {
				$host['networkType'] = "Public";
				$host['connectionType'] = "Wi-Fi";
			}
		}
		elseif (stristr($interface, "MoCA")) {
			$host['connectionType'] = "MoCA";
			$host['networkType'] = "Private";
		}
		elseif (stristr($interface, "Ethernet")) {
			$host['connectionType'] = "Ethernet";
			$host['networkType'] = "Private";
		} 
		else{
			$host['connectionType'] = "Unknown";
			$host['networkType'] = "Private";
		}
    	return $host;
	}
	$connectionType = "none";
	$rootObjName    = "Device.Hosts.Host.";
	$paramNameArray = array("Device.Hosts.Host.");
	$mapping_array  = array("IPAddress", "Layer1Interface");
	$HostIndexArr = DmExtGetInstanceIds("Device.Hosts.Host.");
	if(0 == $HostIndexArr[0]){  
	    // status code 0 = success   
		$HostNum = count($HostIndexArr) - 1;
	}
	if(!empty($HostNum)){
		$Host = getParaValues($rootObjName, $paramNameArray, $mapping_array);
		if(!empty($Host)){
			foreach ($Host as $key => $value) {
				if(stristr($value["IPAddress"], $clientIP)){
					if(stristr($value["Layer1Interface"], "Ethernet")){ $connectionType = "Ethernet"; }
					else if(stristr($value["Layer1Interface"], "WiFi.SSID.1")){ $connectionType = "WiFi"; }//WiFi 2.4GHz
					else if(stristr($value["Layer1Interface"], "WiFi.SSID.2")){ $connectionType = "WiFi"; }//WiFi 5GHz
					else if(stristr($value["Layer1Interface"], "Public")){ $connectionType = "WiFi"; }//WiFi Public
					else { $connectionType = "Ethernet"; }
				}
			}
		}//end of if empty host
	}//end of if empty hostNums
	//allow redirect config only over Ethernet, Private WiFi 2.4G or 5G
	/*allow redirection for all
	if(!(stristr($connectionType, "Ethernet") || stristr($connectionType, "WiFi"))){
		echo '<h2><br>Access Denied!<br><br>Access is allowed only over Ethernet, Private WiFi 2.4GHz or 5GHz</h2>';
		exit(0);
	}
	*/
?>
<script type="text/javascript" src="./cmn/js/lib/jquery-1.9.1.js"></script>
<script>
$(document).ready(function(){
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
	$CloudPersonalizationURL = "<?php echo $CloudPersonalizationURL;?>";
	$CloudUIEnable = <?php echo $CloudUIEnable;?>;
	function cloudRedirection(cloudReachable){
		if(cloudReachable){
			location.href = $CloudPersonalizationURL;
		}
		else{
			$("#redirect_process").hide();
			$("#set_up").show();
		}
	}
	if($CloudUIEnable){
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_wireless_network_configuration_redirection.php",
			data: { CloudUIEnable: true },
			success: function (msg, status, jqXHR) {
				//msg is the response
				msg = JSON.parse(msg);
				if(msg[0] == "true") {
					cloudRedirection(true);
				}
				else{
					cloudRedirection(false);
				}
			}
		});
	}
	else {
		cloudRedirection(false);
	}
	// logic t0 figure out LAN or WiFi from Connected Devices List
	var connectionType	= "<?php echo $connectionType;?>"; //"Ethernet", "WiFi", "none"
	var goNextName		= false;
	var goNextPassword	= false;
	var goNextName5		= false;
	var goNextPassword5	= false;
	function GWReachable(){
		//location.href = "http://xfinity.com";
		// Handle IE and more capable browsers
		var xhr = new ( window.ActiveXObject || XMLHttpRequest )( "Microsoft.XMLHTTP" );
		var status;
		var pingTest;
		var isGWReachable = false;
		function pingGW(){
			/* 
				https://xhr.spec.whatwg.org/
				Synchronous XMLHttpRequest outside of workers is in the process of being removed from
				the web platform as it has detrimental effects to the end user's experience.
			*/
			// Open new request as a HEAD to the root hostname with a random param to bust the cache
			xhr.open( "HEAD", "http://<?php echo $ipv4_addr; ?>/check.php" );// + (new Date).getTime()
			// Issue request and handle response
			try {
				xhr.send();
				xhr.onreadystatechange=function(){
					if( xhr.status >= 200 && xhr.status < 304 ){
						isGWReachable = true;
					} else {
						isGWReachable = false;
					}
				}
			} catch (error) {
				isGWReachable = false;
			}
		}
		pingTest = pingGW();
		setInterval(function () {
			if(isGWReachable){
				$("#ready").show();
				$("#setup").hide();
				setTimeout(function(){ location.href = "http://xfinity.com"; }, 5000);
			}
			else{
				pingTest = pingGW();
			}
		}, 5000);
	}
	function goToReady(){
		if(connectionType == "WiFi"){ //"Ethernet", "WiFi", "none"
			$("#setup_started").hide();
			$("#setup_completed").show();
			setTimeout(function(){ GWReachable(); }, 2000);
		} else {
			$("#ready").show();
			$("#complete").hide();
		}
	}
	function EMS_mobileNumber(){
		//call EMS Service
		if($("#text_sms").css('display') == "block"){
			if(!$("#concent").is(':checked')){
				// Notify if concent_check is not checked
					return '0000000000';
			}
			//+01(111)-111-1111 or +01 111 111 1111 or others, so keep only 10 last numbers
			var phoneNumber = $("#phoneNumber").val().replace(/\D+/g, '').slice(-10);
			return phoneNumber;
		}
		else {
			return '0000000000';
		}
	}
	function addslashes( str ) {
		return (str + '').replace(/[\\]/g, '\\$&').replace(/["]/g, '\\\$&').replace(/\u0000/g, '\\0');
	}
	function saveConfig(){
		var network_name 	= addslashes($("#WiFi_Name").val());
		var network_password 	= addslashes($("#WiFi_Password").val());
		var network5_name 	= addslashes($("#WiFi5_Name").val());
		var network5_password 	= addslashes($("#WiFi5_Password").val());
		var jsConfig;
		if($("#dualSettings").css('display') == "block" && !$("#selectSettings" ).is(":checked")){
			jsConfig = '{"dualband":"true", "network_name":"'+network_name+'", "network_password":"'+network_password+'", "network5_name":"'+network5_name+'", "network5_password":"'+network5_password+'", "phoneNumber":"'+EMS_mobileNumber()+'"}';
		}
		else {
			jsConfig = '{"dualband":"false", "network_name":"'+network_name+'", "network_password":"'+network_password+'", "phoneNumber":"'+EMS_mobileNumber()+'"}';
		}
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_wireless_network_configuration_redirection.php",
			data: { rediection_Info: jsConfig },
			success: function (msg, status, jqXHR) {
				//msg is the response
				msg = JSON.parse(msg);
				if(msg[0] == "outOfCaptivePortal")
				{
					setTimeout(function(){ 
						location.href="index.php"; 
					}, 10000);
				}
			}
		});
		if(connectionType != "WiFi"){
			setTimeout(function(){ goToReady(); }, 25000);
		}
	}
	var NameTimeout, PasswordTimeout, Name5Timeout, Password5Timeout, phoneNumberTimeout, agreementTimeout;
	function messageHandler(target, topMessage, bottomMessage){
		//target	- "name", "password", "name5", "password5", "phoneNumber"
		//topMessage	- top message to show
		//bottomMessage	- bottom message to show
		if(target == "name"){
			$("#NameContainer").fadeIn("slow");
			clearTimeout(NameTimeout);
			NameTimeout = setTimeout(function(){ $("#NameContainer").fadeOut("slow"); }, 5000);
			$("#NameMessageTop").text(topMessage);
			$("#NameMessageBottom").text(bottomMessage);
		}
		else if(target == "password"){
			$("#PasswordContainer").fadeIn("slow");
			clearTimeout(PasswordTimeout);
			PasswordTimeout = setTimeout(function(){ $("#PasswordContainer").fadeOut("slow"); }, 5000);
			$("#PasswordMessageTop").text(topMessage);
			$("#PasswordMessageBottom").text(bottomMessage);
		}
		else if(target == "name5"){
			$("#NameContainer5").fadeIn("slow");
			clearTimeout(Name5Timeout);
			Name5Timeout = setTimeout(function(){ $("#NameContainer5").fadeOut("slow"); }, 5000);
			$("#NameMessageTop5").text(topMessage);
			$("#NameMessageBottom5").text(bottomMessage);
		}
		else if(target == "password5"){
			$("#PasswordContainer5").fadeIn("slow");
			clearTimeout(Password5Timeout);
			Password5Timeout = setTimeout(function(){ $("#PasswordContainer5").fadeOut("slow"); }, 5000);
			$("#PasswordMessageTop5").text(topMessage);
			$("#PasswordMessageBottom5").text(bottomMessage);
		}
		else if(target == "phoneNumber"){
			$("#phoneNumberContainer").fadeIn("slow");
			$("#agreementContainer").hide();
			clearTimeout(phoneNumberTimeout);
			phoneNumberTimeout = setTimeout(function(){ $("#phoneNumberContainer").fadeOut("slow"); }, 5000);
			$("#phoneNumberMessageTop").text(topMessage);
			$("#phoneNumberMessageBottom").text(bottomMessage);
		}
		else if(target == "concent_check"){
			$("#agreementContainer").fadeIn("slow");
			$("#phoneNumberContainer").hide();
			clearTimeout(agreementTimeout);
			agreementTimeout = setTimeout(function(){ $("#agreementContainer").fadeOut("slow"); }, 5000);
			$("#agreementMessageTop").text(topMessage);
			$("#agreementMessageBottom").text(bottomMessage);
		}
	}
	function passStars(val){
		var textVal="";
		for (i = 0; i < val.length; i++) {
			textVal += "*";
		}
		return textVal;
	}
	function toShowNext(){
		//is NOT Dual Band Network
		var selectSettings	= $("#selectSettings").is(":checked");
		var notDualSettings	= $("#dualSettings").css('display') == "block" ? selectSettings : true ;
		if(goNextName && goNextPassword && notDualSettings){
			setTimeout(function(){
				$("#NameContainer").hide();
				$("#PasswordContainer").hide();
			}, 2000);
			$("#button_next").show();
			$("#WiFi_Name_01").text($("#WiFi_Name").val());
			$("#WiFi_Password_01").text($("#WiFi_Password").val());
			$("#WiFi_Password_pass_01").text(passStars($("#WiFi_Password").val()));
		}
		else if(goNextName && goNextPassword && !notDualSettings && goNextName5 && goNextPassword5){
			setTimeout(function(){
				$("#NameContainer").hide();
				$("#PasswordContainer").hide();
				$("#NameContainer5").hide();
				$("#PasswordContainer5").hide();
			}, 2000);
			$("#button_next").show();
			$("#WiFi_Name_01").text($("#WiFi_Name").val());
			$("#WiFi_Password_01").text($("#WiFi_Password").val());
			$("#WiFi_Password_pass_01").text(passStars($("#WiFi_Password").val()));
			//for Dual Band Network
			$("#WiFi5_Name_01").text($("#WiFi5_Name").val());
			$("#WiFi5_Password_01").text($("#WiFi5_Password").val());
			$("#WiFi5_Password_pass_01").text(passStars($("#WiFi5_Password").val()));
		}
		else {
			$("#button_next").hide();
		}
	}
	function showPasswordStrength(element, isValidPassword){
		//passwordStrength >> 0-progress-bg, 1&2-weak-red 3-average-yellow 4-strong-green 5-too-long
		$passVal 	= $("#WiFi"+element+"_Password");
		$passStrength 	= $("#passwordStrength"+element);
		$passInfo 	= $("#passwordInfo"+element);
		var val  = $passVal.val();
		var nums 	= val.search(/\d/) === -1 ? 0 : 1 ;	//numbers
		var lowers 	= val.search(/[a-z]/) === -1 ? 0 : 1 ;	//lower case
		var uppers 	= val.search(/[A-Z]/) === -1 ? 0 : 1 ;	//upper case
		var specials 	= val.search(/(?![a-zA-Z0-9])[ -~]/) === -1 ? 0 : 1 ;	//All "Special Characters" in the ASCII Table
		var strength = nums+lowers+uppers+specials;
		strength = val.length > 7 ? strength : 0 ;
		strength = val.length < 65 ? strength : 5 ;
		if(isValidPassword){
			switch (strength) {
			    case 0:
				$passStrength.removeClass();
				$passInfo.text("Your password does not meet the requirements yet.");
				break;
			    case 1:
				$passStrength.removeClass().addClass("weak-red");
				$passInfo.text("Your password is currently: Weak");
				break;
			    case 2:
				$passStrength.removeClass().addClass("weak-red");
				$passInfo.text("Your password is currently: Weak");
				break;
			    case 3:
				$passStrength.removeClass().addClass("average-yellow");
				$passInfo.text("Your password is currently: Average");
				break;
			    case 4:
				$passStrength.removeClass().addClass("strong-green");
				$passInfo.text("Your password is currently: Strong");
				break;
			    case 5:
				$passStrength.removeClass().addClass("too-long");
				$passInfo.text("Your password is too long!");
				break;
			}
		}
		else {
			$passStrength.removeClass();
			passTeext = $passVal.val().length > 7 ? "" : " yet" ;
			$passInfo.text("Your password does not meet the requirements"+passTeext+".");
		}
	if($passVal.val().length > 7){
		$("#passwordIndicator"+element).show();
	}
	else{
		$("#passwordIndicator"+element).hide();
	}
	}
	$("#get_set_up").click(function(){
		//button >> get_set_up
		$("#set_up").hide();
		$("#personalize").show();
	});
	$("#button_next").click(function(){
		//button >> personalize
		$("#personalize").hide();
		$("#confirm").show();
	});
	$("#button_previous_01").click(function(){
		//button >> confirm - Previous
		$("#personalize").show();
		$("#confirm").hide();
	});
	$("#button_next_01").click(function(){
		$("[id^='WiFi_Name_0']").text($("#WiFi_Name").val());
		$("[id^='WiFi_Password_0']").text($("#WiFi_Password").val());
		$("[id^='WiFi_Password_pass_0']").text(passStars($("#WiFi_Password").val()));
		$("[id^='WiFi5_Name_0']").text($("#WiFi5_Name").val());
		$("[id^='WiFi5_Password_0']").text($("#WiFi5_Password").val());
		$("[id^='WiFi5_Password_pass_0']").text(passStars($("#WiFi5_Password").val()));
		if(connectionType == "WiFi"){ //"Ethernet", "WiFi", "none"
			$("#setup").show();
			$("#confirm").hide();
			saveConfig();
		} else {
			$("#complete").show();
			$("#confirm").hide();
			setTimeout(function(){ saveConfig(); }, 2000);
		}
	});
	$("#visit_xfinity").click(function(){
		location.href = "http://XFINITY.net";
	});
	$("#WiFi_Name").bind("focusin keyup change input",(function() {
		//VALIDATION for wifi_name
		/*return !param || /^[ -~]{3,32}$/i.test(value);
		"3-32 ASCII Printable Characters");
		return value.toLowerCase().indexOf("xhs")==-1 && value.toLowerCase().indexOf("xfinitywifi")==-1;
		'SSID containing "XHS" and "Xfinitywifi" are reserved !'
		return value.toLowerCase().indexOf("optimumwifi")==-1 && value.toLowerCase().indexOf("twcwifi")==-1 && value.toLowerCase().indexOf("cablewifi")==-1;
		'SSID containing "optimumwifi", "TWCWiFi" and "CableWiFi" are reserved !');*/
		var val	= $(this).val();
		isValid		= /^[ -~]{1,32}$/i.test(val);
		valLowerCase	= val.toLowerCase();
		isXHS		= valLowerCase.indexOf("xhs-") !=0 && valLowerCase.indexOf("xh-") !=0;
		//isXFSETUP 	= valLowerCase.indexOf("xfsetup") != 0;
		//isHOME 		= valLowerCase.indexOf("home") != 0;
		//isXFINITY 	= valLowerCase.indexOf("xfinity")==-1;
		isOnlySpaces = /^\s+$/.test(valLowerCase);
		//isOther checks for "wifi" || "cable" && "twc" && "optimum" && "Cox" && "BHN"
		var str = val.replace(/[\.,-\/#@!$%\^&\*;:{}=+?\-_`~()"'\\|<>\[\]\s]/g,'').toLowerCase();
		isOther	= str.indexOf("cablewifi") == -1 && str.indexOf("twcwifi") == -1 && str.indexOf("optimumwifi") == -1 && str.indexOf("xfinitywifi") == -1;
		if(val == ""){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Wi-Fi Name", "Please enter Wi-Fi Name.");
		}
		else if(isOnlySpaces)
		{
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", "Wifi Name cannot contain only spaces.");
		}
		else if("<?php echo $network_name;?>".toLowerCase() == val.toLowerCase()){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", "Choose a different name than the one provided on your gateway.");
		}
		else if(!isXHS){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", 'SSID is invalid/reserved.');
		}
		else if(!isOther){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", 'SSID is invalid/reserved.');
		}
		else if(!isValid){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", "1 to 32 ASCII characters.");
		}
		else if($("#dualSettings").css('display') == "block" && !$("#selectSettings").is(":checked") && val == $("#WiFi5_Name").val()){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", "This name is already in use. Please choose a different name.");
		}
		else {
			goNextName = true;
			$(this).addClass("success").removeClass("error");
			messageHandler("name", "Wi-Fi Name", "This identifies your Wi-Fi network from other nearby networks.");
		}
		toShowNext();
	}));
	$("#password_field").bind("focusin keyup change input",(function() {
		/*
			return !param || /^[ -~]{8,63}$/i.test(value); "8-63 ASCII characters or a 64 hex character password"
		*/
		//VALIDATION for WiFi_Password
		$WiFiPass = $("#WiFi_Password");
		var val = $WiFiPass.val();
		isValid	= /^[ -~]{8,63}$|^[a-fA-F0-9]{64}$/i.test(val);
		if(val == ""){
			goNextPassword	= false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password", "Wi-Fi Password", "Please enter Wi-Fi Password.");
		}
		else if("<?php echo $network_pass;?>" == val){
			goNextPassword	= false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password", "Let's try that again", "Choose a different password than the one provided on your gateway.");
		}
		else if(!isValid){
			goNextPassword	= false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password", "Let's try that again", "Passwords are case sensitive and should include 8-63 ASCII characters or a 64 hex character password. Hex means only the following characters can be used: ABCDEF0123456789.");
		}
		/*else if($("#dualSettings").css('display') == "block" && !$("#selectSettings").is(":checked") && val == $("#WiFi5_Password").val()){
			goNextPassword = false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password", "Let's try that again", "Network Password for both bands cannot be the same.");
		}*/
		else {
			goNextPassword	= true;
			$WiFiPass.addClass("success").removeClass("error");
			messageHandler("password", "Wi-Fi Password", "Passwords are case sensitive and should include 8-63 ASCII characters or a 64 hex character password. Hex means only the following characters can be used: ABCDEF0123456789.");
		}
		toShowNext();
		showPasswordStrength("", goNextPassword);
	}));
	//for Dual Band Network
	$("#WiFi5_Name").bind("focusin keyup change input",(function() {
		//VALIDATION for wifi_name
		/*return !param || /^[ -~]{3,32}$/i.test(value);
		"3-32 ASCII Printable Characters");
		return value.toLowerCase().indexOf("xhs")==-1 && value.toLowerCase().indexOf("xfinitywifi")==-1;
		'SSID containing "XHS" and "Xfinitywifi" are reserved !'
		return value.toLowerCase().indexOf("optimumwifi")==-1 && value.toLowerCase().indexOf("twcwifi")==-1 && value.toLowerCase().indexOf("cablewifi")==-1;
		'SSID containing "optimumwifi", "TWCWiFi" and "CableWiFi" are reserved !');*/
		var val	= $(this).val();
		isValid		= /^[ -~]{1,32}$/i.test(val);
		valLowerCase	= val.toLowerCase();
		isXHS		= valLowerCase.indexOf("xhs-") !=0 && valLowerCase.indexOf("xh-") != 0;
		//isXFSETUP 	= valLowerCase.indexOf("xfsetup") != 0;
		//isHOME 		= valLowerCase.indexOf("home") != 0;
		//isXFINITY 	= valLowerCase.indexOf("xfinity")==-1;
		isOnlySpaces = /^\s+$/.test(valLowerCase);
		//isOther checks for "wifi" || "cable" && "twc" && "optimum" && "Cox" && "BHN"
		var str = val.replace(/[\.,-\/#@!$%\^&\*;:{}=+?\-_`~()"'\\|<>\[\]\s]/g,'').toLowerCase();
		isOther	= str.indexOf("cablewifi") == -1 && str.indexOf("twcwifi") == -1 && str.indexOf("optimumwifi") == -1 && str.indexOf("xfinitywifi") == -1;
		if(val == ""){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Wi-Fi Name", "Please enter Wi-Fi Name.");
		}
		else if(isOnlySpaces)
		{
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", "Wifi Name cannot contain only spaces.");
		}
		else if("<?php echo $network_name1;?>".toLowerCase() == val.toLowerCase()){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", "Choose a different name than the one provided on your gateway.");
		}
		else if(!isXHS){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", 'SSID is invalid/reserved.');
		}
		else if(!isOther){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", 'SSID is invalid/reserved.');
		}
		else if(!isValid){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", "1 to 32 ASCII characters.");
		}
		else if($("#dualSettings").css('display') == "block" && !$("#selectSettings").is(":checked") && val == $("#WiFi_Name").val()){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", "This name is already in use. Please choose a different name.");
		}
		else {
			goNextName5 = true;
			$(this).addClass("success").removeClass("error");
			messageHandler("name5", "Wi-Fi Name", "This identifies your Wi-Fi network from other nearby networks.");
		}
		toShowNext();
	}));
	$("#password5_field").bind("focusin keyup change input",(function() {
		/*
			return !param || /^[ -~]{8,63}$/i.test(value); "8-63 ASCII characters or a 64 hex character password"
		*/
		//VALIDATION for WiFi_Password
		$WiFiPass = $("#WiFi5_Password");
		var val = $WiFiPass.val();
		isValid	= /^[ -~]{8,63}$|^[a-fA-F0-9]{64}$/i.test(val);
		if(val == ""){
			goNextPassword5	= false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password5", "Wi-Fi Password", "Please enter Wi-Fi Password.");
		}
		else if("<?php echo $network_pass1;?>" == val){
			goNextPassword5	= false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password5", "Let's try that again", "Choose a different password than the one provided on your gateway.");
		}
		else if(!isValid){
			goNextPassword5	= false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password5", "Let's try that again", "Passwords are case sensitive and should include 8-63 ASCII characters or a 64 hex character password. Hex means only the following characters can be used: ABCDEF0123456789.");
		}
		/*else if($("#dualSettings").css('display') == "block" && !$("#selectSettings").is(":checked") && val == $("#WiFi_Password").val()){
			goNextPassword5 = false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password5", "Let's try that again", "Network Password for both bands cannot be the same.");
		}*/
		else {
			goNextPassword5	= true;
			$WiFiPass.addClass("success").removeClass("error");
			messageHandler("password5", "Wi-Fi Password", "Passwords are case sensitive and should include 8-63 ASCII characters or a 64 hex character password. Hex means only the following characters can be used: ABCDEF0123456789.");
		}
		toShowNext();
		showPasswordStrength("5", goNextPassword5);
	}));
	function goNextphoneNumber(value){
		if(value){
			$("#button_next_01").show();
		}
		else{
			$("#button_next_01").hide();
		}
	}
	function checkValidPhoneNumber(phNo)
	{
		isValid	= /^(\+?0?1?\s?)?(\(\d{3}\)|\d{3})[\s-]?\d{3}[\s-]?\d{4}$/.test(phNo);
		return isValid;
	}
	$("#phoneNumber").bind("keyup",(function() {
		if($("#text_sms").css('display') == "block"){
			$phoneNumber = $("#phoneNumber");
			var val = $phoneNumber.val();
			isValid	= checkValidPhoneNumber(val);
			if(val == ""){
				goNextphoneNumber(true);
				$phoneNumber.removeClass("success").removeClass("error");
				//messageHandler("phoneNumber", "Text (SMS)", "Passwords are case sensitive and should include 8-63 alphanumeric characters with no spaces.");
				$("#phoneNumberContainer").fadeOut("slow");
			}
			else if(!isValid){
				goNextphoneNumber(false);
				$phoneNumber.addClass("error").removeClass("success");
				messageHandler("phoneNumber", "Let's try that again", "Please enter the 10 digit Phone Number.");
			}
			else {
				//goNextphoneNumber(true);
				$phoneNumber.addClass("success").removeClass("error");
				if ($("#concent").is(":checked"))
				{
					goNextphoneNumber(true);
				}
				else
					messageHandler("concent_check", "Confirmation", "Please confirm your agreement to receive a text message.");
			}
		}
	}));
	//to show password on click
	$("#showPass").click(function() {
		passwordVal = $("#WiFi_Password").val();
		classVal = $("#WiFi_Password").attr('class');
		if ($("#showPass").children().text() == "Hide ") {
			$("[id^='showPass']").children().text("Show");
			document.getElementById("password_field").innerHTML = '<input id="WiFi_Password" type="password" placeholder="Minimum Eight Characters" maxlength="64" class="">';
			$("[id^='WiFi_Password_0']").hide();
			$("[id^='WiFi_Password_pass_0']").show();
		}
		else {
			$("[id^='showPass']").children().text("Hide ");
			document.getElementById("password_field").innerHTML = '<input id="WiFi_Password" type="text" placeholder="Minimum Eight Characters" maxlength="64" class="">';
			$("[id^='WiFi_Password_0']").show();
			$("[id^='WiFi_Password_pass_0']").hide();
		}
		$("#WiFi_Password").val(passwordVal).addClass(classVal);
	});
	//for Dual Band Network
	$("#show5Pass").click(function() {
		password5Val = $("#WiFi5_Password").val();
		class5Val = $("#WiFi5_Password").attr('class');
		if ($("#show5Pass").children().text() == "Hide ") {
			$("[id^='show5Pass']").children().text("Show");
			document.getElementById("password5_field").innerHTML = '<input id="WiFi5_Password" type="password" placeholder="Minimum Eight Characters" maxlength="64" class="">';
			$("[id^='WiFi5_Password_0']").hide();
			$("[id^='WiFi5_Password_pass_0']").show();
		}
		else {
			$("[id^='show5Pass']").children().text("Hide ");
			document.getElementById("password5_field").innerHTML = '<input id="WiFi5_Password" type="text" placeholder="Minimum Eight Characters" maxlength="64" class="">';
			$("[id^='WiFi5_Password_0']").show();
			$("[id^='WiFi5_Password_pass_0']").hide();
		}
		$("#WiFi5_Password").val(password5Val).addClass(class5Val);
	});
	$("[id^='showPass0']").click(function() {
		$("#showPass").trigger("click");
	});
	$("[id^='show5Pass0']").click(function() {
		$("#show5Pass").trigger("click");
	});
	//check all the check boxes by default
	$("#selectSettings").prop('checked', true);
	$("#showDual").click(function(){
		$("#dualSettings").toggle();
		if($("#dualSettings").css('display') == "block"){
			$("#selectSettings").prop('checked', true);
			$("#selectSettings").siblings('label').addClass('checkLabel');
		}
		else {
			$("#selectSettings").prop('checked', false);
			$("[name=dualBand]").hide();
			$("#WiFi5_Name, #WiFi5_Password").val("").keyup().removeClass();
			$("#NameContainer5, #PasswordContainer5").hide();
			$("#passwordIndicator5").hide();
			$("#WiFi_Name, #password_field").change();
			$("#selectSettings").siblings('label').removeClass('checkLabel');
		}
		$("#showDualText").text(($("#dualSettings").css('display') != "block")?"Show More Settings":"Show Less Settings");
		toShowNext();
	});
	$("#selectSettings").change(function() {
		if ($(this).is(":checked")) {
			$(this).siblings('label').addClass('checkLabel');
			$("[name=dualBand]").hide();
			$("#WiFi5_Name, #WiFi5_Password").val("").keyup().removeClass();
			$("#NameContainer5, #PasswordContainer5").hide();
			$("#passwordIndicator5").hide();
			$("#WiFi_Name, #password_field").change();
		}
		else {
			$("[name=dualBand]").show();
			$(this).siblings('label').removeClass('checkLabel');
		}
		toShowNext();
	});
	$("#concent_check").change(function(){
		if ($("#concent").is(":checked")) {
			$(this).find('label').addClass('checkLabel');
			$("#phoneNumber").keyup();
			var val = $("#phoneNumber").val();
			if(val !== "")
			{
				isValid	= checkValidPhoneNumber(val);
				goNextphoneNumber(isValid);
			}
			else{
				goNextphoneNumber(false);
				$phoneNumber.addClass("error").removeClass("success");
				messageHandler("phoneNumber", "Let's try that again", "Please enter the 10 digit Phone Number.");
			}
		}
		else {
			$(this).find('label').removeClass('checkLabel');
			$("#phoneNumber").val("").keyup().removeClass();
			$("#phoneNumberContainer").hide();
		}
	});
/*
	$( window ).resize(function() {
		$("#append").append( '<div class="container" >'+$(document).width()+'</div>');
		$("#topbar").width($(document).width());
	});
*/
	//for Dual Band Network
	$("[name=dualBand]").hide();
	$("[id^='WiFi_Password_pass_0']").hide();
	$("[id^='WiFi5_Password_pass_0']").hide();
});
</script>
	<body>
		<div id="topbar">
			<img src="cmn/img/logo.png"/>
		</div>
		<div id="redirect_process">
			<br><br><br>
			<img src="cmn/img/progress.gif" height="75" width="75"/>
		</div>
		<div id="set_up" style="display: none;" class="portal">
			<h1>Welcome to XFINITY Internet</h1>
			<hr>
			<p>
			<b>This step is required to get your devices online</b><br><br>
				Your connection has been activated, but now we need to create your<br>
				personal <b>Wi-Fi Name and Password</b>.
			</p>
			<hr>
			<div>
				<button id="get_set_up">Let's Get Set Up</button>
			</div>
			<br><br>
		</div>
		<div id="personalize" style="display: none;" class="portal">
			<br>
			<h1 style="margin: 20px auto 0 auto;">
				Create Your Wi-Fi Name & Password
			</h1>
			<p style="width: 500px;">
				This step is <b style="color: #DC4343;">required</b>, so choose something that you will easily remember.<br>
				You'll have to reconnect your devices using the new credentials.
			</p>
			<hr>
				<p name="dualBand" style="margin: 1px 40px 0 0;">2.4 GHz Network</p>
				<div id="NameContainer" class="container" style="display: none;">
					<div class="requirements">
						<div id="NameMessageTop" class="top">Let's try that again.</div>
						<div id="NameMessageBottom" class="bottom">Choose a different name than the one printed on your gateway.</div>
						<div class="arrow"></div>
					</div>
				</div>
				<p style="display:inline; margin: 1px 40px 0 0; text-align: right;">Wi-Fi Name</p>
				<input style="display:inline; margin: 4px 0 0 -8px;" id="WiFi_Name" type="text" placeholder="Example: [account name] Wi-Fi" maxlength="32" class="">
				<br>
				<div id="PasswordContainer" class="container" style="display: none;">
					<div class="requirements">
						<div id="PasswordMessageTop" class="top">Let's try that again.</div>
						<div id="PasswordMessageBottom" class="bottom">Choose a different name than the one printed on your gateway.</div>
						<div class="arrow"></div>
					</div>
				</div>
				<p style="display:inline; margin: 1px 40px 0 -60px; text-align: right;">Wi-Fi Password</p>
				<span style="display:inline; margin: 4px 0 0 -26px;" id="password_field"><input id="WiFi_Password" type="text" placeholder="Minimum Eight Characters" maxlength="64" class="" ></span>
				<div id="showPass" style="display:inline-table; margin: 4px 0 0 -90px;">
					<a href="javascript:void(0)" style="white-space: pre;">Hide </a>
			    </div>
				<div id="passwordIndicator" style="display: none;">
					<div class="progress-bg"><div id="passwordStrength"></div></div>
					<p id="passwordInfo" class="password-text"></p>
				</div>
				<div name="dualBand" id="showDualConfig">
				<br>
					<p style="margin: 10px 40px 0 -10px;">5 GHz Network</p>
					<div id="NameContainer5" class="container" style="display: none;">
						<div class="requirements">
							<div id="NameMessageTop5" class="top">Let's try that again.</div>
							<div id="NameMessageBottom5" class="bottom">Choose a different name than the one printed on your gateway.</div>
							<div class="arrow"></div>
						</div>
					</div>
					<p style="display:inline; margin: 1px 40px 0 0; text-align: right;">Wi-Fi Name</p>
					<input style="display:inline; margin: 4px 0 0 -8px;" id="WiFi5_Name" type="text" placeholder="Example: [account name] Wi-Fi" maxlength="32" class="">
					<br>
					<div id="PasswordContainer5" class="container" style="display: none;">
						<div class="requirements">
							<div id="PasswordMessageTop5" class="top">Let's try that again.</div>
							<div id="PasswordMessageBottom5" class="bottom">Choose a different name than the one printed on your gateway.</div>
							<div class="arrow"></div>
						</div>
					</div>
					<p style="display:inline; margin: 1px 40px 0 -60px; text-align: right;">Wi-Fi Password</p>
					<span style="display:inline; margin: 4px 0 0 -26px;" id="password5_field"><input id="WiFi5_Password" type="text" placeholder="Minimum Eight Characters" maxlength="64" class="" ></span>
					<div id="show5Pass" style="display:inline-table; margin: 4px 0 0 -90px;">
						<a href="javascript:void(0)" style="white-space: pre;">Hide </a>
				    </div>
					<div id="passwordIndicator5" style="display: none;">
						<div class="progress-bg"><div id="passwordStrength5"></div></div>
						<p id="passwordInfo5" class="password-text"></p>
					</div>
				</div>
			<hr>
			<div id="showDual" style="display:inline; margin:0 260px 0 0;">
				<a id="showDualText" href="javascript:void(0)">Show More Settings</a>
			</div>
			<br>
			<div id="dualSettings" class="checkbox" style="margin:0 50px; display: none;">
				<br><br>
				<input id="selectSettings" type="checkbox" name="selectSettings">
			    	<label for="selectSettings" class="insertBox checkLabel"></label> 
			    	<div class="check-copy" style="color: #888;">Use same settings for 2.4GHz and 5GHz Wi-Fi networks.</div>
		    	</div>
			<br><br>
			<div>
				<button id="button_next" style="text-align: center; width: 215px; display: none;">Next</button>
			</div>
			<br><br>
		</div>
		<div id="confirm" style="display: none;" class="portal">
			<h1>Confirm Wi-Fi Settings</h1>
			<hr>
			<table align="center" border="0">
				<tr>
					<td name="dualBand" class="left-settings" ></td>
					<td class="confirm-text" name="dualBand" >2.4 GHz Network</td>
				</tr>
				<tr>
					<td class="left-settings" >Wi-Fi Name</td>
					<td class="final-settings" id="WiFi_Name_01" ></td>
					<td></td>
				</tr>
				<tr>
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi_Password_01" ></td>
					<td class="final-settings" id="WiFi_Password_pass_01" ></td>
					<td id="showPass01">
						<a href="javascript:void(0)" style="white-space: pre; display: none;">Hide </a>
				    </td>
				</tr>
				<tr>
					<td><br></td>
				</tr>
				<tr name="dualBand">
					<td name="dualBand" class="left-settings" ></td>
					<td class="confirm-text" >5 GHz Network</td>
				</tr>
				<tr name="dualBand">
					<td class="left-settings" >Wi-Fi Name</td>
					<td class="final-settings" id="WiFi5_Name_01" ></td>
					<td></td>
				</tr>
				<tr name="dualBand">
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi5_Password_01" ></td>
					<td class="final-settings" id="WiFi5_Password_pass_01" ></td>
					<td id="show5Pass01">
						<a href="javascript:void(0)" style="white-space: pre; display: none;">Hide </a>
				    </td>
				</tr>
			</table>
			<hr>
			<p style="text-align: left; margin: 13px 0 0 115px;">
				Send yourself a text with your Wi-Fi name and password.<br>
				This is an optional one-time-only text.
			</p>
			<div id="phoneNumberContainer" class="container" style="display: none;">
				<div class="requirements" style="top: 20px; left: 495px;">
					<div id="phoneNumberMessageTop" class="top">Text (SMS)</div>
					<div id="phoneNumberMessageBottom" class="bottom">Texts are not encrypted. You can always view Wi-Fi name/password under My Account instead.</div>
					<div class="arrow"></div>
				</div>
			</div>
			<div id="text_sms">
				<p style="text-align: left; margin: 27px 0 0 115px;">Your Mobile Number (<b>Optional</b>)</p>
				<input id="phoneNumber" type="text" placeholder="1(  )  -  " class=""><br/><br/>
			</div>
			<div id="agreementContainer" class="container" style="display: none;">
				<div class="requirements" style="top: -6px; left: 509px;">
					<div id="agreementMessageTop" class="top">Confirmation</div>
					<div id="agreementMessageBottom" class="bottom">Please confirm your agreement to receive a text message.</div>
					<div class="arrow"></div>
				</div>
				</div>
			<div id="concent_check" class="checkbox">
				<input id="concent" type="checkbox" name="concent">
			    	<label for="concent" class="insertBox" style="margin: -40px 10px 0 15px;"></label>
			    	<div class="check-copy" style="text-align: left; color: #888;">
					I agree to receive a text message from Comcast via<br/>
					automated technology to my mobile number provided<br/>
					regarding my Wi-Fi name and password.<br/>
				</div>
		    	</div>
			<br/><br/>
			<div>
				<button id="button_previous_01" class="transparent">Previous Step</button>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
				<button id="button_next_01">Next</button>
			</div>
			<br><br>
		</div>
		<div id="setup" style="display: none;" class="portal">
			<h1>Join your new Wi-Fi Network</h1>
			<p>Your Wi-Fi will begin broadcasting in about a minute.<br>
				<b>You'll have to reconnect your device using the new credentials.</b>
			</p>
			<hr>
			<table align="center" border="0">
				<tr>
					<td name="dualBand" class="left-settings" ></td>
					<td class="confirm-text" name="dualBand" >2.4 GHz Network</td>
				</tr>
				<tr>
					<td class="left-settings" >Wi-Fi Name</td>
					<td class="final-settings" id="WiFi_Name_02" ></td>
				</tr>
				<tr>
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi_Password_02" ></td>
					<td class="final-settings" id="WiFi_Password_pass_02" ></td>
					<td id="showPass02">
						<a href="javascript:void(0)" style="white-space: pre; display: none;">Hide </a>
				    </td>
				</tr>
				<tr>
					<td><br></td>
				</tr>
				<tr name="dualBand">
					<td name="dualBand" class="left-settings" ></td>
					<td class="confirm-text" >5 GHz Network</td>
				</tr>
				<tr name="dualBand">
					<td class="left-settings" >Wi-Fi Name</td>
					<td class="final-settings" id="WiFi5_Name_02" ></td>
				</tr>
				<tr name="dualBand">
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi5_Password_02" ></td>
					<td class="final-settings" id="WiFi5_Password_pass_02" ></td>
					<td id="show5Pass02">
						<a href="javascript:void(0)" style="white-space: pre; display: none;">Hide </a>
				    </td>
				</tr>
			</table>
			<hr>
			<div class="access-box">
				<div style="float: left; padding-bottom: 50px;">
					<a href="http://xfinity.com">
						<img class="img-hover" src="cmn/img/xfinity_My_Account.png" style="margin: 10px 20px 0 20px;" height="100px"/>
					</a>
				</div>
				<div>
					<p style="margin: 10px 0 0 0; text-align: left; width: 380px; font-size: large;">
						Want to change your settings at any time?
					</p>
					<p style="margin: 10px 0 0 0; text-align: left; width: 400px;">
						Download the XFINITY My Account app to access these settings and other features of your service.
					</p>
				</div>
			</div>
			<br><br>
		</div>
		<div id="complete" style="display: none;" class="portal">
			<h1>Your Wi-Fi is Nearly Complete</h1>
			<img src="cmn/img/progress.gif" height="75" width="75"/>
			<div class="link_example">
				<p>We'll have this finished up shortly.<br>
					Once complete, you can start connecting devices.
				</p>
			</div>
			<hr>
			<table align="center" border="0">
				<tr>
					<td name="dualBand" class="left-settings" ></td>
					<td class="confirm-text" name="dualBand" >2.4 GHz Network</td>
				</tr>
				<tr>
					<td class="left-settings" >Wi-Fi Name</td>
					<td class="final-settings" id="WiFi_Name_04" ></td>
				</tr>
				<tr>
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi_Password_04" ></td>
					<td class="final-settings" id="WiFi_Password_pass_04" ></td>
					<td id="showPass03">
						<a href="javascript:void(0)" style="white-space: pre; display: none;">Hide </a>
				    </td>
				</tr>
				<tr>
					<td><br></td>
				</tr>
				<tr name="dualBand">
					<td name="dualBand" class="left-settings" ></td>
					<td class="confirm-text" >5 GHz Network</td>
				</tr>
				<tr name="dualBand">
					<td class="left-settings" >Wi-Fi Name</td>
					<td class="final-settings" id="WiFi5_Name_04" ></td>
				</tr>
				<tr name="dualBand">
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi5_Password_04" ></td>
					<td class="final-settings" id="WiFi5_Password_pass_04" ></td>
					<td id="show5Pass03">
						<a href="javascript:void(0)" style="white-space: pre; display: none;">Hide </a>
				    </td>
				</tr>
			</table>
			<hr>
			<div class="access-box">
				<div style="float: left; padding-bottom: 50px;">
					<a href="http://xfinity.com">
						<img class="img-hover" src="cmn/img/xfinity_My_Account.png" style="margin: 10px 20px 0 20px;" height="100px"/>
					</a>
				</div>
				<div>
					<p style="margin: 10px 0 0 0; text-align: left; width: 380px; font-size: large;">
						Want to change your settings at any time?
					</p>
					<p style="margin: 10px 0 0 0; text-align: left; width: 400px;">
						Download the XFINITY My Account app to access these settings and other features of your service.
					</p>
				</div>
			</div>
			<br><br>
		</div>
		<div id="ready" style="display: none;" class="portal">
			<h1>Your Wi-Fi is Ready</h1>
			<img src="cmn/img/success_lg.png"/>
			<div class="link_example">
				<p>You may begin using your Wi-Fi.<br>
				<b>You'll have to reconnect your device using the new credentials.</b>
				</p>
			</div>
			<hr>
			<table align="center" border="0">
				<tr>
					<td name="dualBand" class="left-settings" ></td>
					<td class="confirm-text" name="dualBand" >2.4 GHz Network</td>
				</tr>
				<tr>
					<td class="left-settings" >Wi-Fi Name</td>
					<td class="final-settings" id="WiFi_Name_05" ></td>
				</tr>
				<tr>
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi_Password_05" ></td>
					<td class="final-settings" id="WiFi_Password_pass_05" ></td>
					<td id="showPass04">
						<a href="javascript:void(0)" style="white-space: pre; display: none;">Hide </a>
				    </td>
				</tr>
				<tr>
					<td><br></td>
				</tr>
				<tr name="dualBand">
					<td name="dualBand" class="left-settings" ></td>
					<td class="confirm-text" >5 GHz Network</td>
				</tr>
				<tr name="dualBand">
					<td class="left-settings" >Wi-Fi Name</td>
					<td class="final-settings" id="WiFi5_Name_05" ></td>
				</tr>
				<tr name="dualBand">
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi5_Password_05" ></td>
					<td class="final-settings" id="WiFi5_Password_pass_05" ></td>
					<td id="show5Pass04">
						<a href="javascript:void(0)" style="white-space: pre; display: none;">Hide </a>
				    </td>
				</tr>
			</table>
			<hr>
			<div class="access-box">
				<div style="float: left; padding-bottom: 50px;">
					<a href="http://xfinity.com">
						<img class="img-hover" src="cmn/img/xfinity_My_Account.png" style="margin: 10px 20px 0 20px;" height="100px"/>
					</a>
				</div>
				<div>
					<p style="margin: 10px 0 0 0; text-align: left; width: 380px; font-size: large;">
						Want to change your settings at any time?
					</p>
					<p style="margin: 10px 0 0 0; text-align: left; width: 400px;">
						Download the XFINITY My Account app to access these settings and other features of your service.
					</p>
				</div>
			</div>
			<br><br>
		</div>
	</body>
</html>
