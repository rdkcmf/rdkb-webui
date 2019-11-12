<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2018 RDK Management

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/
?>
<?php
$CloudUIEnable = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CloudUIEnable");
?>
<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: port_forwarding_add.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php 
$devices_param = array(
	"LanGwIP"   	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress",
	"LanSubnetMask"	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask",
	"DeviceMode"	=> "Device.X_CISCO_COM_DeviceControl.DeviceMode",
	);
    $devices_value = KeyExtGet("Device.X_CISCO_COM_DeviceControl.", $devices_param);
//add by yaosheng
$LanGwIP 	= $devices_value["LanGwIP"]; //getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress");
$LanSubnetMask 	= $devices_value["LanSubnetMask"]; //getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask");
$beginAddr 	= getStr("Device.DHCPv4.Server.Pool.1.MinAddress");
$endAddr 	= getStr("Device.DHCPv4.Server.Pool.1.MaxAddress");
if (!preg_match('/^\d{1,3}$/', $_GET['id'])) die();
$i = $_GET['id'];
$portmapping_param = array(
    "service_name"  => "Device.NAT.PortMapping.$i.Description",
	"v6ServerIP"	=> "Device.NAT.PortMapping.$i.X_CISCO_COM_InternalClientV6",
	"startport"	=> "Device.NAT.PortMapping.".$i.".ExternalPort",
	"endport"	=> "Device.NAT.PortMapping.".$i.".ExternalPortEndRange",
	"type"		=> "Device.NAT.PortMapping.".$i.".Protocol",
	"internClient"	=> "Device.NAT.PortMapping.".$i.".InternalClient",
	"internalPort"	=> "Device.NAT.PortMapping.".$i.".InternalPort",
	);
    $portmapping_value = KeyExtGet("Device.NAT.PortMapping.", $portmapping_param);
$service_name = $portmapping_value["service_name"]; //getStr("Device.NAT.PortMapping.$i.Description");
$service_name = htmlspecialchars($service_name, ENT_NOQUOTES, 'UTF-8');
$v6ServerIP = $portmapping_value["v6ServerIP"]; //getStr("Device.NAT.PortMapping.$i.X_CISCO_COM_InternalClientV6");
$startport = $portmapping_value["startport"]; //getStr("Device.NAT.PortMapping.".$i.".ExternalPort"); 
$endport   = $portmapping_value["endport"]; //getStr("Device.NAT.PortMapping.".$i.".ExternalPortEndRange");
$DeviceMode = $devices_value["DeviceMode"]; //getStr("Device.X_CISCO_COM_DeviceControl.DeviceMode");
$internalPort	= $portmapping_value["internalPort"];
if($internalPort != '0') {
	echo '<script type="text/javascript">location.href="port_forwarding.php";</script>';
	exit;
}
//$DeviceMode = "IPv6";
//2040::/64, 2040:1::/64, 2040:1:2::/64 and 2040:1:2:3::/64
$prefix_arr = explode('::/', getStr("Device.IP.Interface.1.IPv6Prefix.1.Prefix"));
$v6_begin_addr = "0000:0000:0000:0001";
$v6_end_addr = "ffff:ffff:ffff:fffe";
$productLink = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.CloudUI.link");
?>
<style type="text/css">
label{
	margin-right: 10px !important;
}
.form-row input.ipv6-input {
	width: 35px;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Advanced > Port Forwarding > Add Service", "nav-port-forwarding");
    $('#service_name').focus();
    var jsNetMask = "<?php echo $LanSubnetMask; ?>";
    var beginAddr	= "<?php echo $beginAddr; ?>";
    var endAddr		= "<?php echo $endAddr; ?>";
    var beginArr	= beginAddr.split(".");
    var endArr		= endAddr.split(".");
    var jsGwIP = "<?php echo $LanGwIP; ?>".split(".");
    var jsGatewayIP = "<?php echo $LanGwIP; ?>";
    var jsV6ServerIP = "<?php echo $v6ServerIP; ?>";
    var DeviceMode = "<?php echo $DeviceMode; ?>";
function populateIPv6Addr(v6addr){
    var v6_arr = new Array();
	var arr = v6addr.split("::");
	if (arr[1] != undefined) { //:: exist
		var arr_first = arr[0].split(':');
		var arr_second = arr[1].split(':');
		var arr1_num = arr_first.length;
		var arr2_num = arr_second.length;
		var zero_num = 8 - arr1_num - arr2_num;
		if (arr1_num == 0) v6_arr[0] = 0;
	    for (var i = 0; i < arr1_num ; i++) {
	    	v6_arr[i] = arr_first[i];
	    }
	    for (var i = arr1_num, j = 0; j<zero_num; i++, j++) {
	    	v6_arr[i] = 0;
	    }
	    for (var i = arr1_num + zero_num, j = 0; j < arr2_num; i++, j++) {
	    	v6_arr[i] = arr_second[j];
	    }
	} //end of if undefined
	else{
	    v6_arr = v6addr.split(':');
	}
    //console.log(v6_arr);
    return v6_arr;
}
function IsBlank(id_prefix){
	//Don't check for - ip6_address_r[1-4]
	var ret = true;
	$('[id^="'+id_prefix+'"]').each(function(){
		if($(this).attr('id').search(/^ip6_address_r[1-4]$/) == "-1"){
			if ($(this).val().replace(/\s/g, '') != ""){
				ret = false;
				return false;
			}
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
function isIp6AddrRequired()
{
	return !IsBlank('ip6_address_r');
}
function ipv6_in_int(ipv6_address){
	//Residential – /64, Only "interface id" is converted to int for ipv6
	ipv6_int = new Array();
	ipv6_int[0] = parseInt(ipv6_address[0].toUpperCase(), 16);
	ipv6_int[1] = parseInt(ipv6_address[1].toUpperCase(), 16);
	ipv6_int[2] = parseInt(ipv6_address[2].toUpperCase(), 16);
	ipv6_int[3] = parseInt(ipv6_address[3].toUpperCase(), 16);
	return ipv6_int;
}
function isHex(hexa_number){
	var int_number = parseInt(hexa_number,16);
	return (int_number.toString(16) === hexa_number);
}
/* This function checks ending address should be larger than begin address */
function validate_v6addr_pool (DBArr, DEArr) {
	var flag = true;
	if (DEArr[0] < DBArr[0]) {
		flag = false;
	}
	else if (DEArr[0] == DBArr[0]) {
		if (DEArr[1] < DBArr[1]) {
			flag = false;
		}
		else if (DEArr[1] == DBArr[1]) {
			if (DEArr[2] < DBArr[2]) {
				flag = false;
			}
			else if (DEArr[2] == DBArr[2]) {
				if (DEArr[3] < DBArr[3]) {
					flag = false;
				}
			}
		}
	}
	return flag;
}
function isIp4AddrRequired()
{
	return !IsBlank('server_ip_address_');
}
	jQuery.validator.addMethod("ip",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 0 && value <= 255);
	}, "<?php echo _("Please enter a valid IP address.")?>");
	jQuery.validator.addMethod("ip4",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 1 && value <= 254);
	}, "<?php echo _("Please enter a valid IP address.")?>");
	jQuery.validator.addMethod("ip4_end",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 1 && value <= 253);
	}, "<?php echo _("Please enter a valid IP address.")?>");
	jQuery.validator.addMethod("port",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 0 && value <= 65535);
	}, "<?php echo _("Please enter a port number less than 65536.")?>");
	jQuery.validator.addMethod("ltstart",function(value,element){
		return this.optional(element) || value>=parseInt($("#start_port").val());
	}, "<?php echo _("Please enter a value more than or equal to Start Port.")?>");
	jQuery.validator.addMethod("serviceNameRequired",function(value,element){
		var options = ['ssh', 'ftp','aim','http','pptp','https','telnet'];
		if(options.indexOf(value.toLowerCase()) !== -1) {
		  return false;
		}else{
			return true;
		}
	}, "<?php echo _('Please provide a service name other than the ones mentioned in Common Service.')?>");
	var validator = $("#pageForm").validate({
		onfocusout: false,
		groups:{
			server_ipv4: "server_ip_address_1 server_ip_address_2 server_ip_address_3 server_ip_address_4",
			server_ipv6: "ip6_address_r1 ip6_address_r2 ip6_address_r3 ip6_address_r4 ip6_address_r5 ip6_address_r6 ip6_address_r7 ip6_address_r8"
		},
        rules: {      
        	service_name: {
        		serviceNameRequired: true
        	},        
			start_port: {
                required: true,
				port: true,
				digits: true,
				min: 1
            }
            ,end_port: {
                required: true,
				port: true,
				digits: true,
				min: 1,
				ltstart: true
            }
            ,server_ip_address_1: {
                required: isIp4AddrRequired,
				ip4: true
            }
			,server_ip_address_2: {
                required: isIp4AddrRequired,
				ip: true
            }
			,server_ip_address_3: {
                required: isIp4AddrRequired,
				ip: true
            }
			,server_ip_address_4: {
                required: isIp4AddrRequired,
				ip4_end: true
            }
            ,ip6_address_r1:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            } 
            ,ip6_address_r2:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip6_address_r3:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip6_address_r4:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip6_address_r5:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip6_address_r6:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }   
            ,ip6_address_r7:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip6_address_r8:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }            
        }
        ,highlight: function( element, errorClass, validClass ) {
			$(element).closest(".form-row").find("input").addClass(errorClass).removeClass(validClass);
		}
		,unhighlight: function( element, errorClass, validClass ) {
			$(element).closest(".form-row").find("input").removeClass(errorClass).addClass(validClass);
		}
    });
    $("#btn-cancel").click(function() {
    	window.location = "port_forwarding.php";
    });
	$("#btn-save").click(function(){
		$("p.error").remove();
        if($("#common_services").find("option:selected").val() == "other") {
        	var name = $('#service_name').val().replace(/^\s+|\s+$/g, '');
        	if (name.length == 0){
        		jAlert("<?php echo _("Please input a service name !")?>");
        		return;
        	}
		else if(name.match(/[<>&"'|]/)!=null){
			jAlert('<?php echo _("Please input valid Service Name ! \\n Less than (<), Greater than (>), Ampersand (&), Double quote (\"), \\n Single quote (\') and Pipe (|) characters are not allowed.")?>');
				return;
        	}
        }
        else {
        	var name = $("#common_services").find("option:selected").text();
        }
		var type=$('#service_type').find("option:selected").text();
		var ip=$('#server_ip_address_1').val()+'.'+$('#server_ip_address_2').val()+'.'+$('#server_ip_address_3').val()+'.'+$('#server_ip_address_4').val();
		var startport=$('#start_port').val();
		var endport=$('#end_port').val();
		var ipv6addr = GetAddress(":", "ip6_address_r");
		var host0 = parseInt($("#server_ip_address_1").val());
	    	var host1 = parseInt($("#server_ip_address_2").val());
	    	var host2 = parseInt($("#server_ip_address_3").val());
	    	var host3 = parseInt($("#server_ip_address_4").val());
 		if (IsBlank("server_ip_address_") && IsBlank("ip6_address_r")) {
	   	  	jAlert("<?php echo _("Please input valid server address !")?>");
	   	  	return;
		}
		if (!IsBlank("server_ip_address_")) {
			//to check if "Server IPv4 Address" is in "DHCP Mask range"
			var IPv4_valid = ValidIp4Addr(ip, jsGatewayIP, jsNetMask);
			
			//to check if "Server IPv4 Address" is in "DHCP Pool range"
			IPv4_valid = ValidIp4AddrInDhcpPool(ip, beginAddr, endAddr);

			//IPv4 validation
			if (ip == jsGatewayIP){
				jAlert("<?php echo _("Server IP can't be equal to the Gateway IP address !")?>");
				return;
			} else if(!IPv4_valid){
				jAlert("<?php echo _("Server IP addr is not in valid range !")?>");
				return;
			}
		}
		if (!IsBlank("ip6_address_r")) {
			//IPv6 validation
			//if Stateful(Use Dhcp Server) then accept inrange values
			var start	= "<?php echo $v6_begin_addr; ?>";
			var start1	= start.split(":");
			var end		= "<?php echo $v6_end_addr; ?>";
			var end1	= end.split(":");
			var ipv6res	= ipv6addr.split(":");
			var ipv6res1	= ipv6res.splice(4, 4);
			ipv6res_int = ipv6_in_int(ipv6res1);
			start_int 	= ipv6_in_int(start1);
			end_int 	= ipv6_in_int(end1);
			//is valid "interface id" is converted to int for ipv6
			if(!(isHex(ipv6res1[0]) && isHex(ipv6res1[1]) && isHex(ipv6res1[2]) && isHex(ipv6res1[3]))){
				jAlert("<?php echo _("Server IPv6 addr is not valid!")?>");
				return;
			}
			if(!validate_v6addr_pool(start_int, ipv6res_int) || !validate_v6addr_pool(ipv6res_int, end_int)){
				jAlert("<?php echo _("Server IPv6 addr is not in valid range:").'\n'.$prefix_arr[0].':'.$v6_begin_addr.' ~ '.$prefix_arr[0].':'.$v6_end_addr; ?>");
				return;
			}
		}
		$('.port').each(function(){
			if (!validator.element($(this))){
				isValid = false;	//any invalid will make this false
				return;
			}
		});
		if (IsBlank("server_ip_address_")) {
	   	    	ip = "255.255.255.255";
		}
		if (IsBlank("ip6_address_r")) {
		    	ipv6addr = "x"; 
		}		
	    var ID = "<?php echo $i ?>";
		if($("#pageForm").valid()) {
			jProgress('<?php echo _("This may take several seconds.")?>',60);
			$.ajax({
				type:"POST",
				url:"actionHandler/ajax_port_forwarding.php",
				data:{edit:"true",ID:ID,name:name,type:type,ip:ip,ipv6addr:ipv6addr,startport:startport,endport:endport},
				dataType: "json",
				success:function(results){
					jHide();
					if (results=="<?php echo _("Success!")?>") { window.location.href="port_forwarding.php";}
					else if (results=="") {jAlert('<?php echo _("Failure! Please check your inputs.")?>');}
					else jAlert(results);
				},
				error:function(){
					jHide();
					jAlert("<?php echo _("Someting went wrong, please try later!")?>");
				}
			}); //end of ajax
		}//end of valid 
	}); //end of save btn click
//=================================================
service_names = ['FTP', 'AIM', 'HTTP', 'PPTP', 'HTTPs', 'Telnet', 'SSH'];
if(service_names.indexOf("<?php echo $service_name; ?>") < 0){
	var service_name='<?php echo $service_name; ?>';
	var startport	='<?php echo $startport; ?>';
	var endport	='<?php echo $endport; ?>';
} else {
	var service_name='';
	var startport	='';
	var endport	='';
}
$("#service_name, #start_port, #end_port").change(function() {
	if(service_names.indexOf($("#service_name").val()) < 0){
		service_name=$("#service_name").val();
		startport	=$("#start_port").val();
		endport	=$("#end_port").val();
	}
});
function update_service_field() {
	var $common_select = $("#common_services");
    var $other = $("#service_name");
    if($common_select.find("option:selected").val() == "other") {
        $other.prop("disabled", false).removeClass("disabled").closest(".form-row").show();
        $("#start_port, #end_port").prop("disabled", false);
	service_name = service_name.replace(/&lt;/g,'<').replace(/&gt;/g,'>');
	$other.val(service_name);
	$("#start_port").val(startport);
	$("#end_port").val(endport);
    }
    else {
        $other.prop("disabled", true).removeClass("disabled").val("").closest(".form-row").hide();
		// value in select must be start port + | + end port
		var ports = $common_select.find("option:selected").val();
		var start_port = ports.split("|")[0];
		var end_port = ports.split("|")[1];
		$("#start_port").val(start_port).prop("disabled", true);
		$("#end_port").val(end_port).prop("disabled", true);
    }
}
update_service_field();
 // Monitor Common Services because it informs value and visibility of other field
    $("#common_services").change(function() {
        update_service_field();
    });
	var ipv6_arr = populateIPv6Addr(jsV6ServerIP === 'x' ? '<?php echo $prefix_arr[0];?>' : jsV6ServerIP);
    $("#ip6_address_r1").val(ipv6_arr[0]);
    $("#ip6_address_r2").val(ipv6_arr[1]);
    $("#ip6_address_r3").val(ipv6_arr[2]);
    $("#ip6_address_r4").val(ipv6_arr[3]);
    $("#ip6_address_r5").val(ipv6_arr[4]);
    $("#ip6_address_r6").val(ipv6_arr[5]);
    $("#ip6_address_r7").val(ipv6_arr[6]);
    $("#ip6_address_r8").val(ipv6_arr[7]);
	if(DeviceMode == "Ipv4"){
		$("#ip6_address_r5, #ip6_address_r6, #ip6_address_r7, #ip6_address_r8").prop("disabled", true);
    	} else {
		$("#ip6_address_r5, #ip6_address_r6, #ip6_address_r7, #ip6_address_r8").prop("disabled", false);
	}
});
</script>
<?php if($CloudUIEnable == "true"){ ?>
<div  id="content">
	<h1><?php echo _("Advanced > Port Forwarding > Edit Service")?></h1>
	<div  class="module forms">
		<div id="content" style="text-align: center;">
			<br>
			<h3><?php echo sprintf(_("Managing your home network settings is now easier than ever.<br>Visit <a href='http://%s'>%s</a> to set up port forwards, among many other features and settings."),$productLink, $productLink)?></h3>
			<br>
		</div>
	</div> <!-- end .module -->
</div><!-- end #content -->
<?php } else { ?>
<div  id="content">
	<h1><?php echo _("Advanced > Port Forwarding > Edit Service")?></h1>
    <div  id="educational-tip">
        <p class="tip"> <?php echo _("Edit a rule for port forwarding services by user.")?></p>
        <p class="hidden"><?php echo _("Port forwarding permits communications from external hosts by forwarding them to a particular port.")?></p>
		<p class="hidden"><?php echo _("Port forwarding settings can affect the Gateway's performance.")?></p>
    </div>
	<form method="post" id="pageForm" action="">
	<div  class="module forms">
		<h2><?php echo _("Edit Port Forward")?></h2>
		<?php $serviceArr = array('FTP', 'AIM', 'HTTP', 'PPTP', 'HTTPs', 'Telnet', 'SSH'); ?>
		<div  class="form-row odd">
					<label for="common_services"><?php echo _("Common Service:")?></label>
					<select  id="common_services" name="common_services">
					<option <?php if(!strcasecmp($service_name, 'FTP')) echo 'selected'; ?> value="21|21" >FTP</option>
					<option <?php if(!strcasecmp($service_name, 'AIM')) echo 'selected'; ?> value="5190|5190">AIM</option>
					<option <?php if(!strcasecmp($service_name, 'HTTP')) echo 'selected'; ?> value="80|80" >HTTP</option>
					<option <?php if(!strcasecmp($service_name, 'PPTP')) echo 'selected'; ?> value="1723|1723">PPTP</option>
					<option <?php if(!strcasecmp($service_name, 'HTTPs')) echo 'selected'; ?> value="443|443">HTTPs</option>
					<option <?php if(!strcasecmp($service_name, 'Telnet')) echo 'selected'; ?> value="23|23">Telnet</option>
					<option <?php if(!strcasecmp($service_name, 'SSH')) echo 'selected'; ?> value="22|22">SSH</option>
					<option <?php if(!in_array($service_name, $serviceArr)) echo 'selected'; ?> value="other" class="other"><?php echo _("Other")?></option>
					</select>
				</div>
				<div class="form-row ">
			<label for="service_name"><?php echo _("Service Name:")?></label> 
			<input type="text" class="text" value="<?php echo $service_name; ?>" id="service_name" name="service_name" />
		</div>
		<?php $type = $portmapping_value["type"]; ?>
		<div  class="form-row odd">
			<label for="service_type"><?php echo _("Service Type:")?></label>
			<select id="service_type">
				<option value="tcp_udp" <?php if($type === 'BOTH') echo 'selected'; ?>>TCP/UDP</option>
				<option value="tcp" <?php if($type === 'TCP') echo 'selected'; ?>>TCP</option>
				<option value="udp" <?php if($type === 'UDP') echo 'selected'; ?>>UDP</option>
			</select>
		</div>
		<div  class="form-row">
		<?php
		$ip = explode(".",$portmapping_value["internClient"]);
		if (implode('.', $ip) === '255.255.255.255') {
			$ip = array('', '', '', '');
		}
		?>
			<label for="server_ip_address_1"><?php echo _("Server IPv4 Address:")?></label>
		    <input type="text" size="2" maxlength="3" id="server_ip_address_1" value="<?php echo $ip[0];?>" name="server_ip_address_1" class="ipv4-addr smallInput" />
	        <label for="server_ip_address_2" class="acs-hide"></label>
		   .<input type="text" size="2" maxlength="3" id="server_ip_address_2" value="<?php echo $ip[1];?>" name="server_ip_address_2" class="ipv4-addr smallInput" />
	        <label for="server_ip_address_3" class="acs-hide"></label>
		   .<input type="text" size="2" maxlength="3" id="server_ip_address_3" value="<?php echo $ip[2];?>" name="server_ip_address_3" class="ipv4-addr smallInput" />
	        <label for="server_ip_address_4" class="acs-hide"></label>
		   .<input type="text" size="2" maxlength="3" id="server_ip_address_4" value="<?php echo $ip[3];?>" name="server_ip_address_4" class="ipv4-addr smallInput" />
		</div>
		<div class="form-row odd">		
			<label for="ip6_address_r1"><?php echo _("Server IPv6 Address:")?></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r1" name="ip_address_1" disabled="disabled" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r2" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r2" name="ip_address_2" disabled="disabled" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r3" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r3" name="ip_address_3" disabled="disabled" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r4" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r4" name="ip_address_4" disabled="disabled" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r5" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r5" name="ip_address_5" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r6" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r6" name="ip_address_6" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r7" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r7" name="ip_address_7" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r8" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r8" name="ip_address_8" class="ipv6-addr ipv6-input"/>
    	</div>
		<div  class="form-row ">
			<label for="start_port"><?php echo _("Start Port:")?></label>  
			<input type="text" class="port" value="<?php echo $startport; ?>" id="start_port" name="start_port" />
		</div>
		<div  class="form-row odd">
			<label for="end_port"><?php echo _("End Port:")?></label>  
			<input type="text" class="port" value="<?php echo $endport;?>" id="end_port" name="end_port" />
		</div>
		<div  class="form-btn">
			<input type="button" id="btn-save" value="<?php echo _("save")?>" class="btn submit"/>
			<input type="button" id="btn-cancel" value="<?php echo _("Cancel")?>" class="btn alt reset"/>
		</div>
	</div> <!-- end .module -->
	</form>
</div><!-- end #content -->
<?php } ?>
<?php include('includes/footer.php'); ?>
