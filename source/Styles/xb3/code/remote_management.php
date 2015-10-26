<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: device_discovery.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php
$RemoteAccess_param = array(
	"http_mode"	=> "Device.UserInterface.X_CISCO_COM_RemoteAccess.HttpEnable",
	"https_mode"	=> "Device.UserInterface.X_CISCO_COM_RemoteAccess.HttpsEnable",
	"allow_type"	=> "Device.UserInterface.X_CISCO_COM_RemoteAccess.FromAnyIP",
	"start_ip"	=> "Device.UserInterface.X_CISCO_COM_RemoteAccess.StartIp",
	"end_ip"	=> "Device.UserInterface.X_CISCO_COM_RemoteAccess.EndIp",
	"start_ipv6"	=> "Device.UserInterface.X_CISCO_COM_RemoteAccess.StartIpV6",
	"end_ipv6"	=> "Device.UserInterface.X_CISCO_COM_RemoteAccess.EndIpV6",
	);
$RemoteAccess_value = KeyExtGet("Device.UserInterface.X_CISCO_COM_RemoteAccess.", $RemoteAccess_param);

$DeviceControl_param = array(
	"http_port"	=> "Device.X_CISCO_COM_DeviceControl.HTTPPort",
	"https_port"	=> "Device.X_CISCO_COM_DeviceControl.HTTPSPort",
	"telnet_mode"	=> "Device.X_CISCO_COM_DeviceControl.TelnetEnable",
	"ssh_mode"	=> "Device.X_CISCO_COM_DeviceControl.SSHEnable",
	"ipv4_gw"	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress",
	"ipv4_smask"	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask",
	);
$DeviceControl_value = KeyExtGet("Device.X_CISCO_COM_DeviceControl.", $DeviceControl_param);

$http_mode	= $RemoteAccess_value['http_mode'];
$https_mode	= $RemoteAccess_value['https_mode'];
$allow_type	= $RemoteAccess_value['allow_type'];
$start_ip	= $RemoteAccess_value['start_ip'];
$end_ip		= $RemoteAccess_value['end_ip'];
$start_ipv6	= $RemoteAccess_value['start_ipv6'];
$end_ipv6	= $RemoteAccess_value['end_ipv6'];

$http_port	= $DeviceControl_value['http_port'];
$https_port	= $DeviceControl_value['https_port'];
$telnet_mode	= $DeviceControl_value['telnet_mode'];
$ssh_mode	= $DeviceControl_value['ssh_mode'];
$ipv4_gw	= $DeviceControl_value['ipv4_gw'];
$ipv4_smask	= $DeviceControl_value['ipv4_smask'];
?>

<style type="text/css">

.ipv6-input{
	width: 35px;
}

label{
	margin-right: 10px !important;
}

</style>

<script type="text/javascript">
$(document).ready(function() {
	comcast.page.init("Advanced > Remote Management", "nav-remote-management");

	var HTTP = <?php echo ($http_mode === 'true' ? "true" : "false"); ?>;
	var HTTPPORT = "<?php echo $http_port;?>";
	var HTTPS = <?php echo ($https_mode === 'true' ? "true" : "false"); ?>;
	var HTTPSPORT = "<?php echo $https_port;?>";
	var TELNET = <?php echo ($telnet_mode === 'true' ? "true" : "false"); ?>;
	var SSH = <?php echo ($ssh_mode === 'true' ? "true" : "false"); ?>;

	$("#http_switch").radioswitch({
		id: "http-switch",
		radio_name: "http",
		id_on: "http_enabled",
		id_off: "http_disabled",
		title_on: "Enable HTTP",
		title_off: "Disable HTTP",
		state: HTTP ? "on" : "off"
	}).change(function() {
		$("#http").val(HTTPPORT);
		validator.element("#http");
		var isUHTTPDisabled = $(this).radioswitch("getState").on === false;
		if(isUHTTPDisabled) {
			document.getElementById('http').disabled = true;
			remote_access_allowe();
		} else {
			// document.getElementById('http').disabled = false;
			jConfirm(
				"WARNING: Enabling Remote Management will expose your Gateway GUI to Internet. Your Gateway will only be protected by your logon password. Are you sure you want to continue?"
				,"Confirm:"
				,function(ret) {
					if(ret) {
						document.getElementById('http').disabled = false;
						remote_access_allowe();
					}
					else{
						$("#http_switch").radioswitch("doSwitch", "off");
					}
				}
			);
		}
	});
	$("#http").val(HTTPPORT).prop("disabled", !HTTP);

	$("#https_switch").radioswitch({
		id: "https-switch",
		radio_name: "https",
		id_on: "https_enabled",
		id_off: "https_disabled",
		title_on: "Enable HTTPS",
		title_off: "Disable HTTPS",
		state: HTTPS ? "on" : "off"
	}).change(function() {
		$("#https").val(HTTPSPORT);
		validator.element("#https");
		var isUHTTPsDisabled = $(this).radioswitch("getState").on === false;
		if(isUHTTPsDisabled) {
			document.getElementById('https').disabled = true;
			remote_access_allowe();
		} else {
			// document.getElementById('https').disabled = false;
			jConfirm(
				"WARNING: Enabling Remote Management will expose your Gateway GUI to Internet. Your Gateway will only be protected by your logon password. Are you sure you want to continue?"
				,"Confirm:"
				,function(ret) {
					if(ret) {
						document.getElementById('https').disabled = false;
						remote_access_allowe();
					}
					else{
						$("#https_switch").radioswitch("doSwitch", "off");
					}
				}
			);
		}
	});
	$("#https").val(HTTPSPORT).prop("disabled", !HTTPS);

	$("#telnet1_switch").radioswitch({
		id: "telnet1-switch",
		radio_name: "telnet1",
		id_on: "telnet1_enabled",
		id_off: "telnet1_disabled",
		title_on: "Enable Telnet",
		title_off: "Disable Telnet",
		state: TELNET ? "on" : "off"
	}).change(function() {
		var isUTELDisabled = $(this).radioswitch("getState").on === false;
		if(isUTELDisabled) {
			//document.getElementById('telnet').disabled = true;
		} else {
			//document.getElementById('telnet').disabled = false;
		}
	});

	$("#ssh1_switch").radioswitch({
		id: "ssh1-switch",
		radio_name: "ssh1",
		id_on: "ssh1_enabled",
		id_off: "ssh1_disabled",
		title_on: "Enable SSH",
		title_off: "Disable SSh",
		state: SSH ? "on" : "off"
	}).change(function() {
		var isUSSHDisabled = $(this).radioswitch("getState").on === false;
		if(isUSSHDisabled) {
			//document.getElementById('ssh').disabled = true;
		} else {
			//document.getElementById('ssh').disabled = false;
		}
	});

var ALLOWTYPE=$('input[name="single"]:radio:checked').val();
switch (ALLOWTYPE) {
	case "single":
		var STARTIP=$("#ip_address_1").val()+"."+$("#ip_address_2").val()+"."+$("#ip_address_3").val()+"."+$("#ip_address_4").val();
		var ENDIP=STARTIP;
	break;
	case "range":
		var STARTIP=$("#rangeip_address_1").val()+"."+$("#rangeip_address_2").val()+"."+$("#rangeip_address_3").val()+"."+$("#rangeip_address_4").val();
		var ENDIP=$("#endip_address_1").val()+"."+$("#endip_address_2").val()+"."+$("#endip_address_3").val()+"."+$("#endip_address_4").val();
	break;
	case "any":
		var START="notset";
		var ENDIP="notset";
	break;
}

/*********************
$("#snmp-switch").change(function() {
	var isUSNMPDisabled = $("#snmp_disabled").is(":checked");
	if(isUSNMPDisabled) {
		document.getElementById('snmp').disabled = true;
	} else {
		document.getElementById('snmp').disabled = false;
	}
});
**********************/
$("input[name='single']").click(function() {
    if($(this).val() == "range") {
	   	$("#ip_address_1").prop("disabled", true);
    	$("#ip_address_2").prop("disabled", true);
	    $("#ip_address_3").prop("disabled", true);
    	$("#ip_address_4").prop("disabled", true);
    	$("#rangeip_address_1").prop("disabled", false);
	   	$("#rangeip_address_2").prop("disabled", false);
	    $("#rangeip_address_3").prop("disabled", false);
	   	$("#rangeip_address_4").prop("disabled", false);
		$("#endip_address_1").prop("disabled", false);
	   	$("#endip_address_2").prop("disabled", false);
       	$("#endip_address_3").prop("disabled", false);
	   	$("#endip_address_4").prop("disabled", false);
		$("[id^='ipv6_']").prop("disabled", true);
		$("[id^='rangeipv6_']").prop("disabled", false);
		$("[id^='endipv6_']").prop("disabled", false);
 	} else if($(this).val() == "single") {
    	$("#ip_address_1").prop("disabled", false);
	   	$("#ip_address_2").prop("disabled", false);
       	$("#ip_address_3").prop("disabled", false);
	   	$("#ip_address_4").prop("disabled", false);
    	$("#rangeip_address_1").prop("disabled", true);	
	   	$("#rangeip_address_2").prop("disabled", true);
	    $("#rangeip_address_3").prop("disabled", true);
	   	$("#rangeip_address_4").prop("disabled", true);
		$("#endip_address_1").prop("disabled", true);
	   	$("#endip_address_2").prop("disabled", true);
       	$("#endip_address_3").prop("disabled", true);
	   	$("#endip_address_4").prop("disabled", true);
		$("[id^='ipv6_']").prop("disabled", false);
		$("[id^='rangeipv6_']").prop("disabled", true);
		$("[id^='endipv6_']").prop("disabled", true);
	} else {
		$("#ip_address_1").prop("disabled", true);
	   	$("#ip_address_2").prop("disabled", true);
       	$("#ip_address_3").prop("disabled", true);
	   	$("#ip_address_4").prop("disabled", true);
    	$("#rangeip_address_1").prop("disabled", true);
	   	$("#rangeip_address_2").prop("disabled", true);
	    $("#rangeip_address_3").prop("disabled", true);
	   	$("#rangeip_address_4").prop("disabled", true);
		$("#endip_address_1").prop("disabled", true);
	   	$("#endip_address_2").prop("disabled", true);
       	$("#endip_address_3").prop("disabled", true);
	   	$("#endip_address_4").prop("disabled", true);
		$("[id^='ipv6_']").prop("disabled", true);
		$("[id^='rangeipv6_']").prop("disabled", true);
		$("[id^='endipv6_']").prop("disabled", true);
	}
});

jQuery.validator.addMethod("ip",function(value,element){
	var id=$(element).attr("id").split("_")[2];
	return this.optional(element) || (value.match(/^\d+$/g) && (id==1?value>0:value >= 0) && (id==4?value<255:value <= 255));
}, "Please enter a valid IP address.");

jQuery.validator.addMethod("rangeip",function(value,element){
	var id=$(element).attr("id").split("_")[2];
	var s=0, e=0;
	for(var i=1; i<=id; i++){
		s=s*256+parseInt($("#rangeip_address_"+i).val());
		e=e*256+parseInt($("#endip_address_"+i).val());
	}
	return this.optional(element) || (id==4?s<e:s<=e) ;
}, "Please enter valid IP address (Note: Start IP must be less than end IP !)");

jQuery.validator.addMethod("port",function(value,element){
	return this.optional(element) || (value.match(/^\d+$/g) && value >= 1025 && value <= 65535);
}, "Please enter a port number 1025 ~ 65535.");

jQuery.validator.addMethod("notEqual",function(value,element){
	return this.optional(element) || (value != $('#http').val());
}, "Please enter a different port.");

var validator = $("#pageForm").validate({
	groups:{
		ip_address_x:		"ip_address_1 ip_address_2 ip_address_3 ip_address_4",
		rangeip_address_x:	"rangeip_address_1 rangeip_address_2 rangeip_address_3 rangeip_address_4",
		endip_address_x:	"endip_address_1 endip_address_2 endip_address_3 endip_address_4",
		ipv6_address_x:		"ipv6_address_1 ipv6_address_2 ipv6_address_3 ipv6_address_4 ipv6_address_5 ipv6_address_6 ipv6_address_7 ipv6_address_8",
		rangeipv6_address_x:"rangeipv6_address_1 rangeipv6_address_2 rangeipv6_address_3 rangeipv6_address_4 rangeipv6_address_5 rangeipv6_address_6 rangeipv6_address_7 rangeipv6_address_8",
		endipv6_address_x:	"endipv6_address_1 endipv6_address_2 endipv6_address_3 endipv6_address_4 endipv6_address_5 endipv6_address_6 endipv6_address_7 endipv6_address_8"
	},
	rules: {
		http:{
			required: function(element){return !$(element).prop("disabled")},
			port: true
		},
		https:{
			required: function(element){return !$(element).prop("disabled")},
			port: true,
			notEqual: true
		},
		ip_address_1:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true
		},
		ip_address_2:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true
		},
		ip_address_3:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true
		},
		ip_address_4:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true
		},
		rangeip_address_1:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true
		},
		rangeip_address_2:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true
		},
		rangeip_address_3:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true
		},
		rangeip_address_4:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true
		},
		endip_address_1:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true,
			rangeip: true
		},
		endip_address_2:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true,
			rangeip: true
		},
		endip_address_3:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true,
			rangeip: true
		},
		endip_address_4:{
			required: function(element){return !$(element).prop("disabled")},
			ip: true,
			rangeip: true
		},
//for IPv6 validation
		ipv6_address_1:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		ipv6_address_2:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		ipv6_address_3:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		ipv6_address_4:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		ipv6_address_5:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		ipv6_address_6:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		ipv6_address_7:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		ipv6_address_8:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		rangeipv6_address_1:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		rangeipv6_address_2:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		rangeipv6_address_3:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		rangeipv6_address_4:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		rangeipv6_address_5:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		rangeipv6_address_6:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		rangeipv6_address_7:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		rangeipv6_address_8:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		endipv6_address_1:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		endipv6_address_2:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		endipv6_address_3:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		endipv6_address_4:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		endipv6_address_5:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		endipv6_address_6:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		endipv6_address_7:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		},
		endipv6_address_8:{
			required: function(element){return !$(element).prop("disabled")},
			hexadecimal: true,
		}
	}
	,highlight: function( element, errorClass, validClass ) {
		$(element).closest(".form-row").find("input").addClass(errorClass).removeClass(validClass);
	}
	,unhighlight: function( element, errorClass, validClass ) {
		$(element).closest(".form-row").find("input").removeClass(errorClass).addClass(validClass);
	}
});

$("#https").keydown(function(){	validator.element($(this)); });
$("#http").keydown(function() {	validator.element($(this)); });

// $(":text").val("");
var snetCal = {

	getsnetRange : function( ip, snetMask )
	{
		var ipNum = snetCal.toDeci( ip );
		var snetMaskNum = snetCal.toDeci( snetMask );
	
		var prefix = 0;
		var newPrefix = 0;
		var prefixSize;
	
		for( prefixSize = 0; prefixSize < 32; prefixSize++ )
		{
			newPrefix = ( prefix + ( 1 << ( 32 - ( prefixSize + 1 ) ) ) ) >>> 0;
		
			if( ( ( snetMaskNum & newPrefix ) >>> 0 ) !== newPrefix )
			{
				break;
			}
		
			prefix = newPrefix;		
		} 
		
		return snetCal.getMaskRange( ipNum, prefixSize );		 
	},

	getMaskRange : function( ipNum, prefixSize )
	{
		var prefixMask = snetCal.getPrefixMask( prefixSize );
		var lowMask = snetCal.getMask( 32 - prefixSize );
		
		var ipLow = ( ipNum & prefixMask ) >>> 0;
		var ipHigh = ( ( ( ipNum & prefixMask ) >>> 0 ) + lowMask ) >>> 0;
		
		return {
			'ipLow'		: snetCal.toStr( ipLow ),
			'ipHigh'	: snetCal.toStr( ipHigh )
		};
	},

	getPrefixMask : function( prefixSize )
	{
		var mask = 0;
		var i;
		
		for( i = 0; i < prefixSize; i++ )
		{
			mask += ( 1 << ( 32 - ( i + 1 ) ) ) >>> 0;
		}
		
		return mask;
	},
	
	getMask : function( maskSize )
	{
		var mask = 0;
		var i;
		
		for( i = 0; i < maskSize; i++ )
		{
			mask += ( 1 << i ) >>> 0;
		}
		
		return mask;
	},
	
	toDeci : function( ipString )
	{
		var d = ipString.split( '.' );
		return ( ( ( ( ( ( +d[ 0 ] ) * 256 ) + ( +d [ 1 ] ) ) * 256 ) + ( +d[ 2 ] ) ) * 256 ) + ( +d[ 3 ] );
	},
	
	toStr : function( ipNum )
	{	
		var d = ipNum % 256;
		
		for( var i = 3; i > 0; i-- )
		{ 
			ipNum   = Math.floor( ipNum / 256 );
			d       = ipNum % 256 + '.' + d;
		}
		
		return d;
	}
};
$(".btn").click(function(){
	var isValid = true;
	$("p.error").remove();

	var http_enabled  = $("#http_switch").radioswitch("getState").on;
	var https_enabled = $("#https_switch").radioswitch("getState").on;

	if(http_enabled || https_enabled){
		if ($(":radio[value='single']").prop("checked")){
			if (IsBlank("ip_address_") && IsBlank("ipv6_address_")){
				jAlert("Please input at least one single address of IPv4 or IPv6!");
				isValid = false;
			}
			else if (IsBlank("ip_address_")){		
				$("[id^='ipv6_address_']").each(function(){
					if (!validator.element($(this))){
						isValid = false;	//any invalid will make this false
					}
				});	
			}
			else if (IsBlank("ipv6_address_")){
				$("[id^='ip_address_']").each(function(){
					if (!validator.element($(this))){
						isValid = false;	//any invalid will make this false
					}
				});			
			}
			else {
				if(!$("#pageForm").valid()){
					isValid = false;
				}
			}
		}
		else if ($(":radio[value='range']").prop("checked")){
			if (IsBlank("rangeip_address_") && IsBlank("endip_address_") && IsBlank("rangeipv6_address_") && IsBlank("endipv6_address_")){
				jAlert("Please input at least one range address of IPv4 or IPv6!");
				isValid = false;
			}
			else if (IsBlank("rangeip_address_") && IsBlank("endip_address_")){		
				$("[id^='rangeipv6_address_'],[id^='endipv6_address_']").each(function(){
					if (!validator.element($(this))){
						isValid = false;	//any invalid will make this false
					}
				});	
			}
			else if (IsBlank("rangeipv6_address_") && IsBlank("endipv6_address_")){		
				$("[id^='rangeip_address_'],[id^='endip_address_']").each(function(){
					if (!validator.element($(this))){
						isValid = false;	//any invalid will make this false
					}
				});	
			}
			else {
				if(!$("#pageForm").valid()){
					isValid = false;
				}
			}	
		}
		else {
			if(!$("#pageForm").valid()){
				isValid = false;
			}
		}
	
		if (!isValid) return;
	}

	if ($("#telnet1_switch").radioswitch("getState").on && $("#ssh1_switch").radioswitch("getState").on)
	{
		jAlert("Telnet and SSH can not be enabled at the same time.\r\nPlease disable at least one of them.");
		return;
	}
	
	var telnet = $("#telnet1_switch").radioswitch("getState").on;
	if (TELNET==telnet) telnet="notset";
	var ssh = $("#ssh1_switch").radioswitch("getState").on;
	if (SSH==ssh) ssh="notset";

 	var http = $("#http_switch").radioswitch("getState").on;
	if (HTTP==http) http="notset";
	var httpport=$('#http').val();
	// if (HTTPPORT==httpport || HTTP=="false") httpport="notset";
	if (HTTPPORT==httpport) httpport="notset";
	
	var https = $("#https_switch").radioswitch("getState").on;
	if (HTTPS==https) https="notset";
	var httpsport=$('#https').val();
	// if (HTTPSPORT==httpsport || HTTPS=="false") httpsport="notset";
	if (HTTPSPORT==httpsport) httpsport="notset";

	if(!http && !https){
		allowtype	="notset";
		startIP		="notset";
		endIP		="notset";
		startIPv6	="notset";
		endIPv6		="notset";
	} else {
		var allowtype=$('input[name="single"]:radio:checked').val();
		switch (allowtype) {
			case "single":
				var startIP=$("#ip_address_1").val()+"."+$("#ip_address_2").val()+"."+$("#ip_address_3").val()+"."+$("#ip_address_4").val();
				var endIP=startIP;
				var startIPv6	= GetAddress(":", "ipv6_address_");
				var endIPv6		= startIPv6;
				if (IsBlank("ip_address_")){
					startIP = endIP = "255.255.255.255";
				}
				if (IsBlank("ipv6_address_")){
					startIPv6 = endIPv6 = "x";
				}
			break;
			case "range":
				var startIP=$("#rangeip_address_1").val()+"."+$("#rangeip_address_2").val()+"."+$("#rangeip_address_3").val()+"."+$("#rangeip_address_4").val();
				var endIP=$("#endip_address_1").val()+"."+$("#endip_address_2").val()+"."+$("#endip_address_3").val()+"."+$("#endip_address_4").val();
				var startIPv6	= GetAddress(":", "rangeipv6_address_");
				var endIPv6		= GetAddress(":", "endipv6_address_");
				if (IsBlank("rangeip_address_")){
					startIP = endIP = "255.255.255.255";
				}
				if (IsBlank("rangeipv6_address_")){
					startIPv6 = endIPv6 = "x";
				}
			break;
			case "any":
				var startIP="notset";
				var endIP="notset";
				var startIPv6	= "notset";
				var endIPv6		= "notset";
			break;
		}
		if (ALLOWTYPE==allowtype) {
			allowtype="notset";
			if (STARTIP==startIP) startIP="notset";
			if (ENDIP==endIP) endIP="notset";
		} else {
			if (allowtype=="any") allowtype=true;
			else allowtype=false;
		}

		var ipv4_gw 	= "<?php echo $ipv4_gw;?>";
		var ipv4_smask 	= "<?php echo $ipv4_smask;?>";
		var lanRange 	= snetCal.getsnetRange( ipv4_gw, ipv4_smask );
		var ipv4_rhigh 	= lanRange["ipHigh"];
		var ipv4_rlow 	= lanRange["ipLow"];

		var allowtype_Compare=$('input[name="single"]:radio:checked').val();
		var startIP_single=$("#ip_address_1").val()+"."+$("#ip_address_2").val()+"."+$("#ip_address_3").val()+"."+$("#ip_address_4").val();
		var startIP_Compare=$("#rangeip_address_1").val()+"."+$("#rangeip_address_2").val()+"."+$("#rangeip_address_3").val()+"."+$("#rangeip_address_4").val();
		var endIP_Compare=$("#endip_address_1").val()+"."+$("#endip_address_2").val()+"."+$("#endip_address_3").val()+"."+$("#endip_address_4").val();

		if(allowtype_Compare == "single"){
			if( (snetCal.toDeci(startIP_single) > snetCal.toDeci(ipv4_rlow)) && (snetCal.toDeci(startIP_single) < snetCal.toDeci(ipv4_rhigh)) ){
				jAlert("Invalid IPv4 Address.");
				return;
			}
		}else if(allowtype_Compare == "range"){
			if( (snetCal.toDeci(startIP_Compare) > snetCal.toDeci(ipv4_rlow)) && (snetCal.toDeci(startIP_Compare) < snetCal.toDeci(ipv4_rhigh)) ){
				jAlert("Invalid IPv4 Start Address.");
				return;
			}
			if( (snetCal.toDeci(endIP_Compare) > snetCal.toDeci(ipv4_rlow)) && (snetCal.toDeci(endIP_Compare) < snetCal.toDeci(ipv4_rhigh)) ){
				jAlert("Invalid IPv4 End Address.");
				return;
			}
				
		}
	}

	// if($("#pageForm").valid()) {	
		jProgress('This will take several seconds!', 60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_remote_management.php",
			data:{http:http, httpport:httpport, https:https, httpsport:httpsport,
					allowtype:allowtype, startIP:startIP, endIP:endIP,
					telnet:telnet, ssh:ssh, startIPv6:startIPv6, endIPv6:endIPv6
					/*
					mso_mgmt:$("#mso_mgmt").prop("checked"),
					cus_mgmt:$("#cus_mgmt").prop("checked")
					*/
					},
			success:function(){
				setTimeout(function(){
					jHide();
				    window.location.href="remote_management.php";
				}, 15000);			
			},
			error:function(){
				setTimeout(function(){
					jHide();
					/*if (http!="notset" && httpport!="notset") {
						jAlert("Something wrong, please try later!");
					} else {
						window.location.href="remote_management.php";
					}*/
				}, 15000);
			}
		});
	// }
});

function IsBlank(id_prefix){
	var ret = true;
	$('[id^="'+id_prefix+'"]').each(function(){
		if ($(this).val().replace(/\s/g, '') != ""){
			ret = false;
		}
	});
	return ret;
}

function GetAddress(separator, id_prefix){
	var ret = "";
	$('[id^="'+id_prefix+'"]').each(function(){
		ret = ret + $(this).val() + separator;
	});
	return ret.replace(eval('/'+separator+'$/'), '');
}

	if ("cusadmin" == "<?php echo $_SESSION["loginuser"]; ?>"){
		$(".div_global").hide();
	}
	else if ("admin" == "<?php echo $_SESSION["loginuser"]; ?>"){
		$(".div_global").hide();
	}

function remote_access_block(){
	$("#single, #range, #any").attr('disabled', true);

	$("#ip_address_1").prop("disabled", true);
   	$("#ip_address_2").prop("disabled", true);
	$("#ip_address_3").prop("disabled", true);
   	$("#ip_address_4").prop("disabled", true);
	$("#rangeip_address_1").prop("disabled", true);
   	$("#rangeip_address_2").prop("disabled", true);
    	$("#rangeip_address_3").prop("disabled", true);
   	$("#rangeip_address_4").prop("disabled", true);
	$("#endip_address_1").prop("disabled", true);
   	$("#endip_address_2").prop("disabled", true);
	$("#endip_address_3").prop("disabled", true);
   	$("#endip_address_4").prop("disabled", true);
	$("[id^='ipv6_']").prop("disabled", true);
	$("[id^='rangeipv6_']").prop("disabled", true);
	$("[id^='endipv6_']").prop("disabled", true);
	
	$("#message_note").show();
}

	$("#message_note").hide();

	if(!HTTP && !HTTPS){remote_access_block();}
	
	function remote_access_allowe(){
		var http_enabled  = $("#http_switch").radioswitch("getState").on;
		var https_enabled = $("#https_switch").radioswitch("getState").on;
		var allowtype	  =$('input[name="single"]:radio:checked').val();

		if(http_enabled || https_enabled){
			$("#message_note").hide();
			$("#"+allowtype).trigger("click");
			$("#single, #range, #any").attr('disabled', false);
		} else {
		remote_access_block();
		}
	}

});
</script>
<div id="content">
	<h1>Advanced >Remote Management</h1>
	<div id="educational-tip">
        <p class="tip">Remote Management allows the gateway to be remotely accessed by a customer account representative to perform troubleshooting or maintenance.</p>
	    <p class="hidden">Remote Management can be used via HTTP and HTTPS.</p>
		<p class="hidden">Enable the HTTP option and enter the value for HTTP Port, then you can access your device from HTTP. For example,if the WAN IP address is 11.22.11.22 and the HTTP port number is 8080, then you would use http://11.22.11.22:8080</p>
		<p class="hidden">It's the same way to configure HTTPS.</p> 
		<p class="hidden">Select whether you would like to have Remote Management open to all Internet IP Addresses, an Internet IP Address range, or a single Internet IP Address.</p>
	</div>
	<form method="" id="pageForm" action="">
	<div class="module forms">
		<h2>Remote Management</h2>

		<div class="form-row">
			<label for="http">HTTP: <input type="text" value="" name="http"  maxlength="5" size=7 id="http" /></label>
			<span id="http_switch"></span>
		</div>
		<div class="form-row ">
			<label for="https">HTTPS: <input type="text" value="" name="https" maxlength="5" size=7 id="https" /></label>
			<span id="https_switch"></span>
        </div>
		<div class="form-row">
			<p style="position:relative; left:60px"><strong>
			<?php
			/*	$client_ip = $_SERVER["REMOTE_ADDR"];			// $client_ip="::ffff:10.0.0.101";			
				if (strpos($client_ip, ".")){
					echo 'Remote Management Address (IPv4): ';
					echo str_replace("::ffff:", "", $_SERVER["REMOTE_ADDR"]);
					echo '<br/>Remote Management Address (IPv6): ';
				}
				else{
					echo 'Remote Management Address (IPv4): ';
					echo '<br/>Remote Management Address (IPv6): ';
					echo $_SERVER["REMOTE_ADDR"];
				}*/
			?>
			Remote Management Address (IPv4): 
			<?php 
				$fistUSif = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstUpstreamIpInterface");
				echo getStr($fistUSif."IPv4Address.1.IPAddress");
			?>
			<br/>
			Remote Management Address (IPv6): 		
			<?php
				$ids = explode(",", getInstanceIds($fistUSif."IPv6Address."));
				foreach ($ids as $i){
					$val = getStr($fistUSif."IPv6Address.$i.IPAddress");
					if (!strstr($val, "fe80::")){
						echo $val;
						break;
					}
				}
			?>		
			</strong></p>
		</div>
		<p  id="message_note" style="position:relative; left:40px" class="error">Please enable HTTP or HTTPS to configure Remote Access Allowed From.</p>
	</div> <!-- end .module -->
	<div class="module forms">
	    <h2>Remote Access Allowed From</h2><br/>
		<?php
		$allow_type=$RemoteAccess_value['allow_type'];
		// $allow_type="true";
		if ($allow_type=="true") { ?>
		<h3><input type="radio"  name="single" value="single" id="single" /><label class="acs-hide" for="single"></label><b>Single Computer</b><br/></h3>
		<div class="form-row">
			<label for="ip_address_1">IPv4 Address:</label>
			 <input type="text" size="5" maxlength="3"  id="ip_address_1" name="ip_address_1"  disabled="disabled"/><label for="ip_address_2" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="ip_address_2" name="ip_address_2"  disabled="disabled"/><label for="ip_address_3" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="ip_address_3" name="ip_address_3"  disabled="disabled"/><label for="ip_address_4" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="ip_address_4" name="ip_address_4"  disabled="disabled" />
		</div>
		<!--IPv6 disabled-->
		<div class="form-row">
			<label for="ipv6_address_1">IPv6 Address:</label>
			 <input type="text" name="ipv6_address_1" id="ipv6_address_1" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_2" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_2" id="ipv6_address_2" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_3" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_3" id="ipv6_address_3" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_4" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_4" id="ipv6_address_4" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_5" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_5" id="ipv6_address_5" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_6" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_6" id="ipv6_address_6" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_7" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_7" id="ipv6_address_7" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_8" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_8" id="ipv6_address_8" maxlength="4" class="ipv6-input" disabled="disabled" />						
		</div>
	    <h3> <input type="radio"  name="single" value="range" id="range" /><label for="range" class="acs-hide"></label><b>Range Of IPs</b></h3>
		<div class="form-row">
		    <label for="rangeip_address_1">Start IPv4 Address:</label>
		     <input type="text" size="5" maxlength="3"  id="rangeip_address_1" name="rangeip_address_1" disabled="disabled" /><label for="rangeip_address_2" class="acs-hide"></label>
		    .<input type="text" size="5" maxlength="3"  id="rangeip_address_2" name="rangeip_address_2" disabled="disabled" /><label for="rangeip_address_3" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="rangeip_address_3" name="rangeip_address_3" disabled="disabled" /><label for="rangeip_address_4" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="rangeip_address_4" name="rangeip_address_4" disabled="disabled" />
		</div>
		<div class="form-row">
		    <label for="endip_address_1">End IPv4 Address:</label>
			 <input type="text" size="5" maxlength="3"  id="endip_address_1" name="endip_address_1"  disabled="disabled"/><label for="endip_address_2" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="endip_address_2" name="endip_address_2"  disabled="disabled"/><label for="endip_address_3" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="endip_address_3" name="endip_address_3"  disabled="disabled"/><label for="endip_address_4" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="endip_address_4" name="endip_address_4"  disabled="disabled"/>
		</div>
		<!--IPv6 Start-End disabled-->
		<div class="form-row">
			<label for="rangeipv6_address_1">Start IPv6 Address:</label>
			 <input type="text" disabled="disabled" name="rangeipv6_address_1" id="rangeipv6_address_1" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_2" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="rangeipv6_address_2" id="rangeipv6_address_2" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_3" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="rangeipv6_address_3" id="rangeipv6_address_3" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_4" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="rangeipv6_address_4" id="rangeipv6_address_4" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_5" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="rangeipv6_address_5" id="rangeipv6_address_5" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_6" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="rangeipv6_address_6" id="rangeipv6_address_6" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_7" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="rangeipv6_address_7" id="rangeipv6_address_7" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_8" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="rangeipv6_address_8" id="rangeipv6_address_8" maxlength="4" class="ipv6-input">							
		</div>
		<div class="form-row">
			<label for="endipv6_address_1">End IPv6 Address:</label>
			 <input type="text" disabled="disabled" name="endipv6_address_1" id="endipv6_address_1" maxlength="4" class="ipv6-input"><label for="endipv6_address_2" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="endipv6_address_2" id="endipv6_address_2" maxlength="4" class="ipv6-input"><label for="endipv6_address_3" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="endipv6_address_3" id="endipv6_address_3" maxlength="4" class="ipv6-input"><label for="endipv6_address_4" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="endipv6_address_4" id="endipv6_address_4" maxlength="4" class="ipv6-input"><label for="endipv6_address_5" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="endipv6_address_5" id="endipv6_address_5" maxlength="4" class="ipv6-input"><label for="endipv6_address_6" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="endipv6_address_6" id="endipv6_address_6" maxlength="4" class="ipv6-input"><label for="endipv6_address_7" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="endipv6_address_7" id="endipv6_address_7" maxlength="4" class="ipv6-input"><label for="endipv6_address_8" class="acs-hide"></label>
			:<input type="text" disabled="disabled" name="endipv6_address_8" id="endipv6_address_8" maxlength="4" class="ipv6-input">							
		</div>
		<h3><input type="radio"  name="single" value="any" id="any" checked="checked" /><label for="any" class="acs-hide"></label><b>Any Computer</b></h3>
		<?php } else {

			$start_ip	= $RemoteAccess_value['start_ip'];
			$end_ip		= $RemoteAccess_value['end_ip'];

			$start_ipv6	= $RemoteAccess_value['start_ipv6'];
			$end_ipv6	= $RemoteAccess_value['end_ipv6'];

			// $start_ip="10.0.0.111";
			// $end_ip="10.0.0.222";
			// $end_ip="255.255.255.255";
			// $start_ipv6="0:0:0:0:0:0:0:1";
			// $end_ipv6="0:0:0:0:0:0:0:2";
			// $end_ipv6="x";
			
			if ("255.255.255.255"==$start_ip || "255.255.255.255"==$end_ip){
				$start_ip = $end_ip = "...";
				// $start_ip	= getStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.iprange.1.StartIP");
				// $end_ip		= getStr("Device.UserInterface.X_CISCO_COM_RemoteAccess.iprange.1.EndIP");
			}
			if ("x"==$start_ipv6 || "x"==$end_ipv6){
				$start_ipv6 = $end_ipv6 = ":::::::";
			}
			
			if ($start_ip==$end_ip && $start_ipv6==$end_ipv6) {
				$single_ip=explode(".",$start_ip); 
				$single_ipv6=explode(":",$start_ipv6); 
			?>
			<h3><input type="radio"  name="single" value="single" id="single" checked="checked" /><label class="acs-hide" for="single"></label><b>Single Computer</b><br/></h3>
			<div class="form-row">
				<label for="ip_address_1">IPv4 Address:</label>
				 <input type="text" size="5" maxlength="3"  id="ip_address_1" name="ip_address_1" value="<?php echo $single_ip[0];?>" /><label for="ip_address_2" class="acs-hide"></label>
				.<input type="text" size="5" maxlength="3"  id="ip_address_2" name="ip_address_2" value="<?php echo $single_ip[1];?>" /><label for="ip_address_3" class="acs-hide"></label>
				.<input type="text" size="5" maxlength="3"  id="ip_address_3" name="ip_address_3" value="<?php echo $single_ip[2];?>" /><label for="ip_address_4" class="acs-hide"></label>
				.<input type="text" size="5" maxlength="3"  id="ip_address_4" name="ip_address_4" value="<?php echo $single_ip[3];?>" />
			</div>
			<!--IPv6 enabled-->
			<div class="form-row">
				<label for="ipv6_address_1">IPv6 Address:</label>
				 <input type="text" name="ipv6_address_1" id="ipv6_address_1" maxlength="4" class="ipv6-input" value="<?php echo $single_ipv6[0];?>" /><label for="ipv6_address_2" class="acs-hide"></label>
				:<input type="text" name="ipv6_address_2" id="ipv6_address_2" maxlength="4" class="ipv6-input" value="<?php echo $single_ipv6[1];?>" /><label for="ipv6_address_3" class="acs-hide"></label>
				:<input type="text" name="ipv6_address_3" id="ipv6_address_3" maxlength="4" class="ipv6-input" value="<?php echo $single_ipv6[2];?>" /><label for="ipv6_address_4" class="acs-hide"></label>
				:<input type="text" name="ipv6_address_4" id="ipv6_address_4" maxlength="4" class="ipv6-input" value="<?php echo $single_ipv6[3];?>" /><label for="ipv6_address_5" class="acs-hide"></label>
				:<input type="text" name="ipv6_address_5" id="ipv6_address_5" maxlength="4" class="ipv6-input" value="<?php echo $single_ipv6[4];?>" /><label for="ipv6_address_6" class="acs-hide"></label>
				:<input type="text" name="ipv6_address_6" id="ipv6_address_6" maxlength="4" class="ipv6-input" value="<?php echo $single_ipv6[5];?>" /><label for="ipv6_address_7" class="acs-hide"></label>
				:<input type="text" name="ipv6_address_7" id="ipv6_address_7" maxlength="4" class="ipv6-input" value="<?php echo $single_ipv6[6];?>" /><label for="ipv6_address_8" class="acs-hide"></label>
				:<input type="text" name="ipv6_address_8" id="ipv6_address_8" maxlength="4" class="ipv6-input" value="<?php echo $single_ipv6[7];?>" />						
			</div>
			<h3> <input type="radio"  name="single" value="range" id="range" /><label for="range" class="acs-hide"></label><b>Range Of IPs</b></h3>
			<div class="form-row">
				<label for="rangeip_address_1">Start IPv4 Address:</label>
				 <input type="text" size="5" maxlength="3"  id="rangeip_address_1" name="rangeip_address_1" disabled="disabled" /><label for="rangeip_address_2" class="acs-hide"></label>
				.<input type="text" size="5" maxlength="3"  id="rangeip_address_2" name="rangeip_address_2" disabled="disabled" /><label for="rangeip_address_3" class="acs-hide"></label>
				.<input type="text" size="5" maxlength="3"  id="rangeip_address_3" name="rangeip_address_3" disabled="disabled" /><label for="rangeip_address_4" class="acs-hide"></label>
				.<input type="text" size="5" maxlength="3"  id="rangeip_address_4" name="rangeip_address_4" disabled="disabled" />
			</div>
			<div class="form-row">
				<label for="endip_address_1">End IPv4 Address:</label>
				 <input type="text" size="5" maxlength="3"  id="endip_address_1" name="endip_address_1"  disabled="disabled"/><label for="endip_address_2" class="acs-hide"></label>
				.<input type="text" size="5" maxlength="3"  id="endip_address_2" name="endip_address_2"  disabled="disabled"/><label for="endip_address_3" class="acs-hide"></label>
				.<input type="text" size="5" maxlength="3"  id="endip_address_3" name="endip_address_3"  disabled="disabled"/><label for="endip_address_4" class="acs-hide"></label>
				.<input type="text" size="5" maxlength="3"  id="endip_address_4" name="endip_address_4"  disabled="disabled"/>
			</div>
			<!--IPv6 Start-End disabled-->
			<div class="form-row">
				<label for="rangeipv6_address_1">Start IPv6 Address:</label>
				 <input type="text" disabled="disabled" name="rangeipv6_address_1" id="rangeipv6_address_1" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_2" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="rangeipv6_address_2" id="rangeipv6_address_2" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_3" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="rangeipv6_address_3" id="rangeipv6_address_3" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_4" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="rangeipv6_address_4" id="rangeipv6_address_4" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_5" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="rangeipv6_address_5" id="rangeipv6_address_5" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_6" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="rangeipv6_address_6" id="rangeipv6_address_6" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_7" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="rangeipv6_address_7" id="rangeipv6_address_7" maxlength="4" class="ipv6-input"><label for="rangeipv6_address_8" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="rangeipv6_address_8" id="rangeipv6_address_8" maxlength="4" class="ipv6-input">							
			</div>
			<div class="form-row">
				<label for="endipv6_address_1">End IPv6 Address:</label>
				 <input type="text" disabled="disabled" name="endipv6_address_1" id="endipv6_address_1" maxlength="4" class="ipv6-input"><label for="endipv6_address_2" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="endipv6_address_2" id="endipv6_address_2" maxlength="4" class="ipv6-input"><label for="endipv6_address_3" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="endipv6_address_3" id="endipv6_address_3" maxlength="4" class="ipv6-input"><label for="endipv6_address_4" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="endipv6_address_4" id="endipv6_address_4" maxlength="4" class="ipv6-input"><label for="endipv6_address_5" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="endipv6_address_5" id="endipv6_address_5" maxlength="4" class="ipv6-input"><label for="endipv6_address_6" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="endipv6_address_6" id="endipv6_address_6" maxlength="4" class="ipv6-input"><label for="endipv6_address_7" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="endipv6_address_7" id="endipv6_address_7" maxlength="4" class="ipv6-input"><label for="endipv6_address_8" class="acs-hide"></label>
				:<input type="text" disabled="disabled" name="endipv6_address_8" id="endipv6_address_8" maxlength="4" class="ipv6-input">							
			</div>
			<h3><input type="radio"  name="single" value="any" id="any" /><label for="any" class="acs-hide"></label><b>Any Computer</b></h3>
			<?php } else { 
				$range_start_ip		= explode(".", $start_ip);
				$range_end_ip		= explode(".", $end_ip);
				$range_start_ipv6	= explode(":", $start_ipv6);
				$range_end_ipv6		= explode(":", $end_ipv6);
			?>
		<h3><input type="radio"  name="single" value="single" id="single" /><label class="acs-hide" for="single"></label><b>Single Computer</b><br/></h3>
		<div class="form-row">
			<label for="ip_address_1">IPv4 Address:</label>
			 <input type="text" size="5" maxlength="3"  id="ip_address_1" name="ip_address_1"  disabled="disabled"/><label for="ip_address_2" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="ip_address_2" name="ip_address_2"  disabled="disabled"/><label for="ip_address_3" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="ip_address_3" name="ip_address_3"  disabled="disabled"/><label for="ip_address_4" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="ip_address_4" name="ip_address_4"  disabled="disabled"/>
		</div>
		<!--IPv6 disabled-->
		<div class="form-row">
			<label for="ipv6_address_1">IPv6 Address:</label>
			 <input type="text" name="ipv6_address_1" id="ipv6_address_1" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_2" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_2" id="ipv6_address_2" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_3" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_3" id="ipv6_address_3" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_4" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_4" id="ipv6_address_4" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_5" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_5" id="ipv6_address_5" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_6" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_6" id="ipv6_address_6" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_7" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_7" id="ipv6_address_7" maxlength="4" class="ipv6-input" disabled="disabled" /><label for="ipv6_address_8" class="acs-hide"></label>
			:<input type="text" name="ipv6_address_8" id="ipv6_address_8" maxlength="4" class="ipv6-input" disabled="disabled" />						
		</div>
	    <h3> <input type="radio"  name="single" value="range" id="range" checked="checked" /><label for="range" class="acs-hide"></label><b>Range Of IPs</b></h3>
		<div class="form-row">
		    <label for="rangeip_address_1">Start IPv4 Address:</label>
		     <input type="text" size="5" maxlength="3"  id="rangeip_address_1" name="rangeip_address_1"  value="<?php echo $range_start_ip[0];?>" /><label for="rangeip_address_2" class="acs-hide"></label>
		    .<input type="text" size="5" maxlength="3"  id="rangeip_address_2" name="rangeip_address_2"  value="<?php echo $range_start_ip[1];?>" /><label for="rangeip_address_3" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="rangeip_address_3" name="rangeip_address_3"  value="<?php echo $range_start_ip[2];?>" /><label for="rangeip_address_4" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="rangeip_address_4" name="rangeip_address_4"  value="<?php echo $range_start_ip[3];?>" />
		</div>
		<div class="form-row">
		    <label for="endip_address_1">End IPv4 Address:</label>
			 <input type="text" size="5" maxlength="3"  id="endip_address_1" name="endip_address_1"  value="<?php echo $range_end_ip[0];?>" /><label for="endip_address_2" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="endip_address_2" name="endip_address_2" value="<?php echo $range_end_ip[1];?>" /><label for="endip_address_3" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="endip_address_3" name="endip_address_3" value="<?php echo $range_end_ip[2];?>" /><label for="endip_address_4" class="acs-hide"></label>
			.<input type="text" size="5" maxlength="3"  id="endip_address_4" name="endip_address_4" value="<?php echo $range_end_ip[3];?>" />
		</div>
		<!--IPv6 Start-End enabled-->
		<div class="form-row">
			<label for="rangeipv6_address_1">Start IPv6 Address:</label>
			 <input type="text"  name="rangeipv6_address_1" id="rangeipv6_address_1" maxlength="4" class="ipv6-input" value="<?php echo $range_start_ipv6[0];?>" /><label for="rangeipv6_address_2" class="acs-hide"></label>
			:<input type="text"  name="rangeipv6_address_2" id="rangeipv6_address_2" maxlength="4" class="ipv6-input" value="<?php echo $range_start_ipv6[1];?>" /><label for="rangeipv6_address_3" class="acs-hide"></label>
			:<input type="text"  name="rangeipv6_address_3" id="rangeipv6_address_3" maxlength="4" class="ipv6-input" value="<?php echo $range_start_ipv6[2];?>" /><label for="rangeipv6_address_4" class="acs-hide"></label>
			:<input type="text"  name="rangeipv6_address_4" id="rangeipv6_address_4" maxlength="4" class="ipv6-input" value="<?php echo $range_start_ipv6[3];?>" /><label for="rangeipv6_address_5" class="acs-hide"></label>
			:<input type="text"  name="rangeipv6_address_5" id="rangeipv6_address_5" maxlength="4" class="ipv6-input" value="<?php echo $range_start_ipv6[4];?>" /><label for="rangeipv6_address_6" class="acs-hide"></label>
			:<input type="text"  name="rangeipv6_address_6" id="rangeipv6_address_6" maxlength="4" class="ipv6-input" value="<?php echo $range_start_ipv6[5];?>" /><label for="rangeipv6_address_7" class="acs-hide"></label>
			:<input type="text"  name="rangeipv6_address_7" id="rangeipv6_address_7" maxlength="4" class="ipv6-input" value="<?php echo $range_start_ipv6[6];?>" /><label for="rangeipv6_address_8" class="acs-hide"></label>
			:<input type="text"  name="rangeipv6_address_8" id="rangeipv6_address_8" maxlength="4" class="ipv6-input" value="<?php echo $range_start_ipv6[7];?>" />							
		</div>
		<div class="form-row">
			<label for="endipv6_address_1">End IPv6 Address:</label>
			 <input type="text"  name="endipv6_address_1" id="endipv6_address_1" maxlength="4" class="ipv6-input" value="<?php echo $range_end_ipv6[0];?>" /><label for="endipv6_address_2" class="acs-hide"></label>
			:<input type="text"  name="endipv6_address_2" id="endipv6_address_2" maxlength="4" class="ipv6-input" value="<?php echo $range_end_ipv6[1];?>" /><label for="endipv6_address_3" class="acs-hide"></label>
			:<input type="text"  name="endipv6_address_3" id="endipv6_address_3" maxlength="4" class="ipv6-input" value="<?php echo $range_end_ipv6[2];?>" /><label for="endipv6_address_4" class="acs-hide"></label>
			:<input type="text"  name="endipv6_address_4" id="endipv6_address_4" maxlength="4" class="ipv6-input" value="<?php echo $range_end_ipv6[3];?>" /><label for="endipv6_address_5" class="acs-hide"></label>
			:<input type="text"  name="endipv6_address_5" id="endipv6_address_5" maxlength="4" class="ipv6-input" value="<?php echo $range_end_ipv6[4];?>" /><label for="endipv6_address_6" class="acs-hide"></label>
			:<input type="text"  name="endipv6_address_6" id="endipv6_address_6" maxlength="4" class="ipv6-input" value="<?php echo $range_end_ipv6[5];?>" /><label for="endipv6_address_7" class="acs-hide"></label>
			:<input type="text"  name="endipv6_address_7" id="endipv6_address_7" maxlength="4" class="ipv6-input" value="<?php echo $range_end_ipv6[6];?>" /><label for="endipv6_address_8" class="acs-hide"></label>
			:<input type="text"  name="endipv6_address_8" id="endipv6_address_8" maxlength="4" class="ipv6-input" value="<?php echo $range_end_ipv6[7];?>" />							
		</div>
		<h3><input type="radio"  name="single" value="any" id="any" /><label for="any" class="acs-hide"></label><b>Any Computer</b></h3>
		<?php }
		} ?>
		<div>
			<p> Note:This option will allow any computer on the Internet to access your network and may cause a security risk.</p>
		</div>
	</div>
	<div class="module forms div_global">
		<h2>Global Management</h2>
			<div class="form-row">
				<label for="telnet1-switch">Telnet:</label>
				<span id="telnet1_switch"></span>
			</div>
			<div class="form-row ">
			  	<label for="ssh1-switch">SSH:</label>
				<span id="ssh1_switch"></span>
			</div>
	</div> <!-- end .module -->
	<div class="form-btn">
		<input type="button" value="Save" class="btn" />
	</div>
    
	</form>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
