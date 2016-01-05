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
	color: #fff;
	-webkit-font-smoothing: antialiased;
}
</style>
<?php include('includes/utility.php'); ?>
<?php
	// should we allow to Configure WiFi
	// redirection logic - uncomment the code below while checking in
	$DeviceInfo_param = array(
		"CONFIGUREWIFI" => "Device.DeviceInfo.X_RDKCENTRAL-COM_ConfigureWiFi",
		"EMS_ServerURL" => "Device.DeviceInfo.X_COMCAST-COM_EMS_ServerURL",
	);
	$DeviceInfo_value	= KeyExtGet("Device.DeviceInfo.", $DeviceInfo_param);
	$CONFIGUREWIFI = $DeviceInfo_value["CONFIGUREWIFI"];
	if(strstr($CONFIGUREWIFI, "false"))	header('Location:index.php');
	//WiFi Defaults are same for 2.4Ghz and 5Ghz
	$wifi_param = array(
		"network_name"	 => "Device.WiFi.SSID.1.SSID",
		"network_name1"	 => "Device.WiFi.SSID.2.SSID",
		"KeyPassphrase"	 => "Device.WiFi.AccessPoint.1.Security.X_CISCO_COM_KeyPassphrase",
		"KeyPassphrase1" => "Device.WiFi.AccessPoint.2.Security.X_CISCO_COM_KeyPassphrase",
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
	// logic t0 figure out LAN or WiFi from Connected Devices List
	var connectionType	= "<?php echo $connectionType;?>"; //"Ethernet", "WiFi", "none"
	var goNextName		= false;
	var goNextPassword	= false;
	var goNextName5		= false;
	var goNextPassword5	= false;
	var goNextphoneNumber	= true;
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
			setTimeout(function(){ GWReachable(); }, 2000);
		} else {
			$("#ready").show();
			$("#complete").hide();
			setTimeout(function(){ location.href = "http://xfinity.com"; }, 5000);
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
	function saveConfig(){
		var network_name 	= $("#WiFi_Name").val();
		var network_password 	= $("#WiFi_Password").val();
		var network5_name 	= $("#WiFi5_Name").val();
		var network5_password 	= $("#WiFi5_Password").val();
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
			}
		});
		setTimeout(function(){ goToReady(); }, 25000);
	}
	var NameTimeout, PasswordTimeout, Name5Timeout, Password5Timeout, phoneNumberTimeout;
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
			clearTimeout(phoneNumberTimeout);
			phoneNumberTimeout = setTimeout(function(){ $("#phoneNumberContainer").fadeOut("slow"); }, 5000);
			$("#phoneNumberMessageTop").text(topMessage);
			$("#phoneNumberMessageBottom").text(bottomMessage);
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
		var specials 	= val.search(/[\-_.]/) === -1 ? 0 : 1 ;	//special characters
		var strength = nums+lowers+uppers+specials;
		strength = val.length > 7 ? strength : 0 ;
		strength = val.length < 64 ? strength : 5 ;
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
		/*return !param || /^[a-zA-Z0-9\-_.]{3,32}$/i.test(value);
		"3 to 32 characters combined with alphabet, digit, underscore, hyphen and dot");
		return value.toLowerCase().indexOf("xhs")==-1 && value.toLowerCase().indexOf("xfinitywifi")==-1;
		'SSID containing "XHS" and "Xfinitywifi" are reserved !'
		return value.toLowerCase().indexOf("optimumwifi")==-1 && value.toLowerCase().indexOf("twcwifi")==-1 && value.toLowerCase().indexOf("cablewifi")==-1;
		'SSID containing "optimumwifi", "TWCWiFi" and "CableWiFi" are reserved !');*/
		var val	= $(this).val();
		isValid		= /^[a-zA-Z0-9\-_.]{3,32}$/i.test(val);
		valLowerCase	= val.toLowerCase();
		isXHS		= valLowerCase.indexOf("xhs")==-1;
		isXFSETUP 	= valLowerCase.indexOf("xfsetup") != 0;
		isHOME 		= valLowerCase.indexOf("home") != 0;
		isXFINITY 	= valLowerCase.indexOf("xfinity")==-1;
		//isOther checks for "wifi" || "cable" && "twc" && "optimum" && "Cox" && "BHN"
		var str = val.replace(/[\.,-\/#@!$%\^&\*;:{}=\-_`~()\s]/g,'').toLowerCase();
		isOther	= str.indexOf("wifi") == -1 || str.indexOf("cable") == -1 && str.indexOf("twc") == -1 && str.indexOf("optimum") == -1 && str.indexOf("cox") == -1 && str.indexOf("bhn") == -1;
		if(val == ""){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Wi-Fi Name", "Please enter Wi-Fi Name.");
		}
		else if("<?php echo $network_name;?>".toLowerCase() == val.toLowerCase()){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", "Choose a different name than the one provided on your gateway.");
		}
		else if(!isXFSETUP){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", 'SSID starting with "XFSETUP" is reserved !');
		}
		else if(!isHOME){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", 'SSID starting with "HOME" is reserved !');
		}
		else if(!isXFINITY){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", 'SSID is invalid/reserved.');
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
			messageHandler("name", "Let's try that again", "3 to 32 characters combined with alphabet, digit, underscore, hyphen and dot");
		}
		else if($("#dualSettings").css('display') == "block" && !$("#selectSettings").is(":checked") && val == $("#WiFi5_Name").val()){
			goNextName = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name", "Let's try that again", "Network Name for both bands cannot be the same.");
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
			return !param || /^[a-zA-Z0-9\-_.]{8,63}$/i.test(value); "8-20 characters. Alphanumeric only. No spaces. Case sensitive."
		*/
		//VALIDATION for WiFi_Password
		$WiFiPass = $("#WiFi_Password");
		var val = $WiFiPass.val();
		isValid	= /^[a-zA-Z0-9\-_.]{8,63}$/i.test(val);
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
			messageHandler("password", "Let's try that again", "Passwords are case sensitive and should include 8-63 alphanumeric characters with no spaces.");
		}
		/*else if($("#dualSettings").css('display') == "block" && !$("#selectSettings").is(":checked") && val == $("#WiFi5_Password").val()){
			goNextPassword = false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password", "Let's try that again", "Network Password for both bands cannot be the same.");
		}*/
		else {
			goNextPassword	= true;
			$WiFiPass.addClass("success").removeClass("error");
			messageHandler("password", "Wi-Fi Password", "Passwords are case sensitive and should include 8-63 alphanumeric characters with no spaces.");
		}
		toShowNext();
		showPasswordStrength("", goNextPassword);
	}));
	//for Dual Band Network
	$("#WiFi5_Name").bind("focusin keyup change input",(function() {
		//VALIDATION for wifi_name
		/*return !param || /^[a-zA-Z0-9\-_.]{3,32}$/i.test(value);
		"3 to 32 characters combined with alphabet, digit, underscore, hyphen and dot");
		return value.toLowerCase().indexOf("xhs")==-1 && value.toLowerCase().indexOf("xfinitywifi")==-1;
		'SSID containing "XHS" and "Xfinitywifi" are reserved !'
		return value.toLowerCase().indexOf("optimumwifi")==-1 && value.toLowerCase().indexOf("twcwifi")==-1 && value.toLowerCase().indexOf("cablewifi")==-1;
		'SSID containing "optimumwifi", "TWCWiFi" and "CableWiFi" are reserved !');*/
		var val	= $(this).val();
		isValid		= /^[a-zA-Z0-9\-_.]{3,32}$/i.test(val);
		valLowerCase	= val.toLowerCase();
		isXHS		= valLowerCase.indexOf("xhs")==-1;
		isXFSETUP 	= valLowerCase.indexOf("xfsetup") != 0;
		isHOME 		= valLowerCase.indexOf("home") != 0;
		isXFINITY 	= valLowerCase.indexOf("xfinity")==-1;
		//isOther checks for "wifi" || "cable" && "twc" && "optimum" && "Cox" && "BHN"
		var str = val.replace(/[\.,-\/#@!$%\^&\*;:{}=\-_`~()\s]/g,'').toLowerCase();
		isOther	= str.indexOf("wifi") == -1 || str.indexOf("cable") == -1 && str.indexOf("twc") == -1 && str.indexOf("optimum") == -1 && str.indexOf("cox") == -1 && str.indexOf("bhn") == -1;
		if(val == ""){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Wi-Fi Name", "Please enter Wi-Fi Name.");
		}
		else if("<?php echo $network_name1;?>".toLowerCase() == val.toLowerCase()){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", "Choose a different name than the one provided on your gateway.");
		}
		else if(!isXFSETUP){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", 'SSID starting with "XFSETUP" is reserved !');
		}
		else if(!isHOME){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", 'SSID starting with "HOME" is reserved !');
		}
		else if(!isXFINITY){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", 'SSID is invalid/reserved.');
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
			messageHandler("name5", "Let's try that again", "3 to 32 characters combined with alphabet, digit, underscore, hyphen and dot");
		}
		else if($("#dualSettings").css('display') == "block" && !$("#selectSettings").is(":checked") && val == $("#WiFi_Name").val()){
			goNextName5 = false;
			$(this).addClass("error").removeClass("success");
			messageHandler("name5", "Let's try that again", "Network Name for both bands cannot be the same.");
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
			return !param || /^[a-zA-Z0-9\-_.]{8,63}$/i.test(value); "8-20 characters. Alphanumeric only. No spaces. Case sensitive."
		*/
		//VALIDATION for WiFi_Password
		$WiFiPass = $("#WiFi5_Password");
		var val = $WiFiPass.val();
		isValid	= /^[a-zA-Z0-9\-_.]{8,63}$/i.test(val);
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
			messageHandler("password5", "Let's try that again", "Passwords are case sensitive and should include 8-63 alphanumeric characters with no spaces.");
		}
		/*else if($("#dualSettings").css('display') == "block" && !$("#selectSettings").is(":checked") && val == $("#WiFi_Password").val()){
			goNextPassword5 = false;
			$WiFiPass.addClass("error").removeClass("success");
			messageHandler("password5", "Let's try that again", "Network Password for both bands cannot be the same.");
		}*/
		else {
			goNextPassword5	= true;
			$WiFiPass.addClass("success").removeClass("error");
			messageHandler("password5", "Wi-Fi Password", "Passwords are case sensitive and should include 8-63 alphanumeric characters with no spaces.");
		}
		toShowNext();
		showPasswordStrength("5", goNextPassword5);
	}));
	$("#phoneNumber").bind("keyup",(function() {
		if($("#text_sms").css('display') == "block"){
			$phoneNumber = $("#phoneNumber");
			var val = $phoneNumber.val();
			isValid	= /^(\+?0?1?\s?)?(\(\d{3}\)|\d{3})[\s-]?\d{3}[\s-]?\d{4}$/.test(val);
			if(val == ""){
				goNextphoneNumber = true;
				$phoneNumber.removeClass("success").removeClass("error");
				//messageHandler("phoneNumber", "Text (SMS)", "Passwords are case sensitive and should include 8-63 alphanumeric characters with no spaces.");
				$("#phoneNumberContainer").fadeOut("slow");
			}
			else if(!isValid){
				goNextphoneNumber = false;
				$phoneNumber.addClass("error").removeClass("success");
				messageHandler("phoneNumber", "Let's try that again", "Please enter the 10 digit Phone Number.");
			}
			else {
				goNextphoneNumber = true;
				$phoneNumber.addClass("success").removeClass("error");
				messageHandler("phoneNumber", "Text (SMS)", "Textes are not encrypted. You can always view Wi-Fi name/password under My Account instead.");
			}
		}
	}));
	//to show password on click
	$("#showPass").change(function() {
		passwordVal = $("#WiFi_Password").val();
		classVal = $("#WiFi_Password").attr('class');
		if ($("#check").is(":checked")) {
			$("[id^='showPass']").each(function() {
				$(this).find('label').addClass('checkLabel');
			});
			document.getElementById("password_field").innerHTML = '<input id="WiFi_Password" type="text" placeholder="Minimum Eight Characters" maxlength="63" class="">';
			$("[id^='WiFi_Password_0']").show();
			$("[id^='WiFi_Password_pass_0']").hide();
			$("[id^='check']").prop('checked', true);
		}
		else {
			$("[id^='showPass']").each(function() {
				$(this).find('label').removeClass('checkLabel');
			});
			document.getElementById("password_field").innerHTML = '<input id="WiFi_Password" type="password" placeholder="Minimum Eight Characters" maxlength="63" class="">';
			$("[id^='WiFi_Password_0']").hide();
			$("[id^='WiFi_Password_pass_0']").show();
			$("[id^='check']").prop('checked', false);
		}
		$("#WiFi_Password").val(passwordVal).addClass(classVal);
		//for Dual Band Network
			password5Val = $("#WiFi5_Password").val();
			class5Val = $("#WiFi5_Password").attr('class');
			if ($("#check").is(":checked")) {
				document.getElementById("password5_field").innerHTML = '<input id="WiFi5_Password" type="text" placeholder="Minimum Eight Characters" maxlength="63" class="">';
				$("[id^='WiFi5_Password_0']").show();
				$("[id^='WiFi5_Password_pass_0']").hide();
			}
			else {
				document.getElementById("password5_field").innerHTML = '<input id="WiFi5_Password" type="password" placeholder="Minimum Eight Characters" maxlength="63" class="">';
				$("[id^='WiFi5_Password_0']").hide();
				$("[id^='WiFi5_Password_pass_0']").show();
			}
			$("#WiFi5_Password").val(password5Val).addClass(class5Val);
	});
	$("[id^='showPass0']").change(function() {
		varid = this.id.split("0");
		if ($("#check_0"+varid[1]).is(":checked")) {
			$("[id^='showPass']").each(function() {
				$(this).find('label').addClass('checkLabel');
			});
			$("[id^='WiFi_Password_0']").show();
			$("[id^='WiFi_Password_pass_0']").hide();
			$("[id^='check']").prop('checked', true);
			//for Dual Band Network
				$("[id^='WiFi5_Password_0']").show();
				$("[id^='WiFi5_Password_pass_0']").hide();
		}
		else {
			$("[id^='showPass']").each(function() {
				$(this).find('label').removeClass('checkLabel');
			});
			$("[id^='WiFi_Password_0']").hide();
			$("[id^='WiFi_Password_pass_0']").show();
			$("[id^='check']").prop('checked', false);
			//for Dual Band Network
				$("[id^='WiFi5_Password_0']").hide();
				$("[id^='WiFi5_Password_pass_0']").show();
		}
		$("#showPass").trigger("change");
	});
	//check all the check boxes by default
	$("[id^='check']").prop('checked', true);
	$("#selectSettings").prop('checked', true);
	$("[id^='showPass0']").trigger("change");
	$("#showPass").trigger("change");
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
	$("#share_link, #concent").click(function(){
		$("#text_sms, #concent_check").fadeToggle("slow", "linear");
		$("#concent").prop("checked", true);
		$("#phoneNumber").val("").keyup().removeClass();
		$("#phoneNumberContainer").hide();
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
		<div id="personalize" class="portal">
			<h1>Set Up Your Wi-Fi</h1>
			<p style="width: 500px;">
				Create a Wi-Fi name and password to access the Internet.<br>
				This step is required so choose something you'll remember.
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
				<p style="display:inline; margin: 1px 40px 0 0; text-align: right;">Wi-Fi Password</p>
				<span style="display:inline; margin: 4px 0 0 -30px;" id="password_field"><input id="WiFi_Password" type="text" placeholder="Minimum Eight Characters" maxlength="63" class="" ></span>
				<div id="passwordIndicator" style="display: none;">
					<div class="progress-bg"><div id="passwordStrength"></div></div>
					<p id="passwordInfo" class="password-text"></p>
				</div>
				<br>
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
					<p style="display:inline; margin: 1px 40px 0 0; text-align: right;">Wi-Fi Password</p>
					<span style="display:inline; margin: 4px 0 0 -30px;" id="password5_field"><input id="WiFi5_Password" type="text" placeholder="Minimum Eight Characters" maxlength="63" class="" ></span>
					<div id="passwordIndicator5" style="display: none;">
						<div class="progress-bg"><div id="passwordStrength5"></div></div>
						<p id="passwordInfo5" class="password-text"></p>
					</div>
					<br><br>
				</div>
			<hr>
			<div id="showPass" class="checkbox" style="display:inline; margin:0 50px;" >
				<input id="check" type="checkbox" name="check">  
			    	<label for="check" class="insertBox checkLabel"></label> 
			    	<div class="check-copy">Display Password</div>
		    	</div>
			<div id="showDual" style="display:inline; margin:0 50px;" >
				<a id="showDualText" href="javascript:void(0)">Show More Settings</a>
			</div>
			<br>
			<div id="dualSettings" class="checkbox" style="margin:0 50px; display: none;" >
				<br><br>
				<input id="selectSettings" type="checkbox" name="selectSettings">
			    	<label for="selectSettings" class="insertBox checkLabel"></label> 
			    	<div class="check-copy">Use same settings for 2.4GHz and 5GHz Wi-Fi networks.</div>
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
				</tr>
				<tr>
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi_Password_01" ></td>
					<td class="final-settings" id="WiFi_Password_pass_01" ></td>
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
				</tr>
				<tr name="dualBand">
					<td class="left-settings" >Wi-Fi Password</td>
					<td class="final-settings" id="WiFi5_Password_01" ></td>
					<td class="final-settings" id="WiFi5_Password_pass_01" ></td>
				</tr>
			</table>
			<div id="showPass01" class="checkbox">
				<input id="check_01" type="checkbox" name="check_01">  
			    	<label for="check_01" class="insertBox checkLabel"></label>
			    	<div class="check-copy">Display Password</div>
		    	</div>
			<hr>
			<div <?php echo strlen($DeviceInfo_value["EMS_ServerURL"]) > 0 ? '' : 'style="display: none;"' ; ?>id="share_link">
				<a href="javascript:void(0)">Share your new Wi-Fi name and password with your family and friends. (optional)</a>
			</div>
			<div id="phoneNumberContainer" class="container" style="display: none;">
				<div class="requirements" style="top: 20px; left: 495px;">
					<div id="phoneNumberMessageTop" class="top">Text (SMS)</div>
					<div id="phoneNumberMessageBottom" class="bottom">Textes are not encrypted. You can always view Wi-Fi name/password under My Account instead.</div>
					<div class="arrow"></div>
				</div>
			</div>
			<div id="text_sms" style="display: none;">
				<h2>Phone Number</h2>
				<input id="phoneNumber" type="text" placeholder="1(  )  -  " class=""><br/><br/>
			</div>
			<div id="concent_check" class="checkbox" style="display: none;">
				<input id="concent" type="checkbox" name="concent" checked>
			    	<label for="concent" class="insertBox checkLabel"></label>
			    	<div class="check-copy">
					I agree to receive a text message from Comcast at<br/>
					the number above, containing my Wi-Fi network<br/>
					name and password. Message and data rates apply.<br/>
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
			<h1>Almost there...</h1>
			<img src="cmn/img/progress.gif" height="75" width="75"/>
			<p>
				Your WiFi will begin broadcasting in about a minute<br>
				and then you'll be able to connect using the info below.
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
				</tr>
			</table>
			<div id="showPass02" class="checkbox">
				<input id="check_02" type="checkbox" name="check_02">
			    	<label for="check_02" class="insertBox checkLabel"></label> 
			    	<div class="check-copy">Display Password</div>
		    	</div>
			<hr>
			<br><br>
		</div>
		<div id="complete" style="display: none;" class="portal">
			<h1>Your Wi-Fi is Nearly Complete</h1>
			<img src="cmn/img/progress.gif" height="75" width="75"/>
			<div id="link_example">
				<p>We'll have this finished up shortly.<br>
				Once complete, you can start connecting devices.</p>
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
				</tr>
			</table>
			<div id="showPass04" class="checkbox">
				<input id="check_04" type="checkbox" name="check_04">
			    	<label for="check_04" class="insertBox checkLabel"></label>
			    	<div class="check-copy">Display Password</div>
		    	</div>
			<hr>
		</div>
		<div id="ready" style="display: none;" class="portal">
			<h1>Your Wi-Fi is Ready</h1>
			<img src="cmn/img/success_lg.png"/>
			<div id="link_example">
				<p>Start using your new Wi-Fi whenever you'd like.</p>
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
				</tr>
			</table>
			<div id="showPass05" class="checkbox">
				<input id="check_05" type="checkbox" name="check_05">
			    	<label for="check_05" class="insertBox checkLabel"></label>
			    	<div class="check-copy">Display Password</div>
		    	</div>
			<hr>
			<div>
				<button id="visit_xfinity" style="text-align: center; width: 215px;">Visit XFINITY</button>
			</div>
			<br><br>
			<a href="http://customer.xfinity.com/help-and-support/Wireless-Gateway/">Learn how to join your WiFi</a>
			<br><br><br><br>
		</div>
	</body>
</html>
