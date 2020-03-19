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
<?php include('includes/header.php'); ?>
<?php include('includes/utility.php') ?>
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php 
$device_ctrl_param = array(
        "LanGwIP"    => "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress",
		"DeviceMode" => "Device.X_CISCO_COM_DeviceControl.DeviceMode",
		"subnetmask" => "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask",
	);
    $device_ctrl_value = KeyExtGet("Device.X_CISCO_COM_DeviceControl.", $device_ctrl_param);
$dhcpv4_param = array(
        "DHCPTime"   			=> "Device.DHCPv4.Server.Pool.1.LeaseTime",
        "WAN_GW_IPv4_Address" 	=> "Device.DHCPv4.Client.1.IPRouters",
        "beginAddr" 			=> "Device.DHCPv4.Server.Pool.1.MinAddress",
        "endAddr" 				=> "Device.DHCPv4.Server.Pool.1.MaxAddress",
	);
    $dhcpv4_value = KeyExtGet("Device.DHCPv4.", $dhcpv4_param);
$dhcpv6_param = array(
       	"DHCPV6Time" 	=> "Device.DHCPv6.Server.Pool.1.LeaseTime",
       	"state" 		=> "Device.DHCPv6.Server.X_CISCO_COM_Type",
       	"v6_begin_addr" => "Device.DHCPv6.Server.Pool.1.PrefixRangeBegin",
       	"v6_end_addr" 	=> "Device.DHCPv6.Server.Pool.1.PrefixRangeEnd",
	);
    $dhcpv6_value = KeyExtGet("Device.DHCPv6.Server.", $dhcpv6_param);
$LanGwIP    = $device_ctrl_value["LanGwIP"];
$DHCPTime   = $dhcpv4_value["DHCPTime"];
$DHCPV6Time = $dhcpv6_value["DHCPV6Time"];
$ipv6_prefix = getStr("Device.IP.Interface.1.IPv6Prefix.1.Prefix");
$interface = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstDownstreamIpInterface");
$DeviceMode = $device_ctrl_value["DeviceMode"];
//CM GW IP Address
$CM_GW_IP_Address = getStr("Device.X_CISCO_COM_CableModem.Gateway");
//WAN GW IP Address (IPv4)
$WAN_GW_IPv4_Address = $dhcpv4_value["WAN_GW_IPv4_Address"];
//Virtual LAN_GW_IPv4_Address
exec("ifconfig lan0", $out);
foreach ($out as $v){
	if (strpos($v, 'inet addr')){
		$tmp = explode('Bcast', $v);
		$tmp = explode('addr:', $tmp[0]);
		$LAN_GW_IPv4_Address = trim($tmp[1]);
	}
}
// $interface = "Device.IP.Interface.2.";
// initial some variable to suppress some error
$ipv6_local_addr = "";
$ipv6_global_addr = "";
$idArr = explode(",", getInstanceIds($interface."IPv6Address."));
foreach ($idArr as $key => $value) {
  $ipv6addr = getStr($interface."IPv6Address.$value.IPAddress");
  if (stripos($ipv6addr, "fe80::") !== false) {
  	$ipv6_local_addr = $ipv6addr;
  }
  else{
  	$ipv6_global_addr = $ipv6addr;
  }
}
//$ipv6_local_addr = "fe80::20c:29ff:fe43:aac4/64";
//$ipv6_global_addr= "2002:48a3::48a3:ff63";
$tmp = substr($ipv6_local_addr, 6); //remove fe80::
$tmp1 = explode('/', $tmp); //trim /64
$local_ipv6 = $tmp1[0];
$local_ipv6_arr = explode(':', $local_ipv6);
$default_admin_ip= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.DefaultAdminIP");
$partnerId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
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
	gateway.page.init("Gateway > Connection > Local IP Configuration", "nav-local-ip-network");
	/*
	** view management: if admin login, pop up alert msg if change gw ip addr
	*/
	var login_user = "<?php echo $_SESSION["loginuser"]; ?>";
    //console.log(login_user);
	var jsGwIP = "<?php echo $LanGwIP; ?>";
	var jsLeaseTime = "<?php echo $DHCPTime; ?>";
	var jsV6LeaseTime = "<?php echo $DHCPV6Time; ?>";
	var old_beginning_ip4 = $("#ipv4_dhcp_beginning_address_4").val();
	var old_ending_ip4 = $("#ipv4_dhcp_ending_address_4").val();
	var default_admin_ip ="<?php echo $default_admin_ip; ?>";
	var partner_id = '<?php echo $partnerId; ?>';
	if(partner_id.indexOf('sky-')===0){
		stateful_check();
		ipv6_ula_check();
	}
        function ipv6_ula_check(){
		var ipv6_enable_check = '<?php echo getStr("Device.X_RDKCENTRAL-COM_DeviceControl.LanManagementEntry.LanIpv6Enable");?>';
		var ula_enable_check = '<?php echo getStr("Device.X_RDKCENTRAL-COM_DeviceControl.LanManagementEntry.LanIpv6UlaEnable");?>';
		if(ipv6_enable_check == 'true'){
			if(ula_enable_check == 'true'){
				$("#ula_enable").attr('checked','checked');
	            $('#ula_enable,#ULA_1,#ULA_2,#ULA_3,#ULA_4').attr('disabled',false);
	        }
	        else{
			$('#ULA_1,#ULA_2,#ULA_3,#ULA_4').attr('disabled',true);
	        }

		}else{
			$("#ula_enable").removeAttr('checked');
			$('#ula_enable,#ULA_1,#ULA_2,#ULA_3,#ULA_4').attr('disabled',true);
		}
	}
	function updateIPv4() {
		var ip1 = $("#ipv4_gateway_address_1").val();
		var ip2 = $("#ipv4_gateway_address_2").val();
		var ip3 = $("#ipv4_gateway_address_3").val();
		var ip4 = $("#ipv4_gateway_address_4").val();
		var beginning_ip, ending_ip;
		var subnet = $("select#ipv4_subnet_mask option:selected").val();
		var beginning_ip1, beginning_ip2, beginning_ip3, beginning_ip4;
		var ending_ip1, ending_ip2, ending_ip3, ending_ip4;
		beginning_ip1 = ending_ip1 = parseInt(ip1);
		beginning_ip2 = ending_ip2 = parseInt(ip2);
		beginning_ip3 = ending_ip3 = parseInt(ip3);
		beginning_ip4 = "2";
		ending_ip4 = "253";
		/*
		if (subnet == "255.255.255.252"){
			 //cache old values
			old_beginning_ip4 = $("#ipv4_dhcp_beginning_address_4").val();
			old_ending_ip4 = $("#ipv4_dhcp_ending_address_4").val();		}
		else {
			$("#ipv4_dhcp_beginning_address_4").val(old_beginning_ip4);
			$("#ipv4_dhcp_ending_address_4").val(old_ending_ip4);
		}*/
		if(subnet == "255.255.0.0") {
			beginning_ip3 = "0";
			beginning_ip4 = "2";
			ending_ip3 = "255";
			ending_ip4 = "253";
			$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_2").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_2").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_3").prop("disabled", false);
			$("#ipv4_dhcp_ending_address_3").prop("disabled", false);			
			$("#ipv4_gateway_address_2").prop("disabled", false);
			$("#ipv4_gateway_address_3").prop("disabled", true);
		} 
		/*else if (subnet == "255.255.255.128") {
			beginning_ip4 = "2";
			ending_ip4 = "125";
			if($("#ipv4_dhcp_ending_address_4").val()>125) $("#ipv4_dhcp_ending_address_4").val(125);
			$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_2").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_3").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_2").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_3").prop("disabled", true);
			$("#ipv4_gateway_address_2").prop("disabled", false);
			$("#ipv4_gateway_address_3").prop("disabled", false);
		}*/
		else if (subnet == "255.255.255.252") {
			beginning_ip4 = "2";
			ending_ip4 = "2";
			$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_2").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_3").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_2").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_3").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_4").val(2);
			$("#ipv4_dhcp_ending_address_4").val(2);
			$("#ipv4_gateway_address_2").prop("disabled", false);
			$("#ipv4_gateway_address_3").prop("disabled", false);
		}
		else if (subnet == "255.0.0.0") {
			beginning_ip3 = "0";
			beginning_ip4 = "2";
			ending_ip3 = "255";
			ending_ip4 = "253";
			beginning_ip2 = "0";
			ending_ip2 = "255";
			$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_2").prop("disabled", false);
			$("#ipv4_dhcp_beginning_address_3").prop("disabled", false);
			$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_2").prop("disabled", false);
			$("#ipv4_dhcp_ending_address_3").prop("disabled", false);
			$("#ipv4_gateway_address_2").prop("disabled", true);
			$("#ipv4_gateway_address_3").prop("disabled", true);
			$("#ipv4_gateway_address_4").prop("disabled", true);
		}
		else {
			// 255.255.255.0   Default
			beginning_ip4 = "2";
			ending_ip4 = "253";
			$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_2").prop("disabled", true);
			$("#ipv4_dhcp_beginning_address_3").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_2").prop("disabled", true);
			$("#ipv4_dhcp_ending_address_3").prop("disabled", true);
			$("#ipv4_gateway_address_2").prop("disabled", false);
			$("#ipv4_gateway_address_3").prop("disabled", false);
		}
		$("#ipv4_dhcp_beginning_address_1").val(beginning_ip1);
		$("#ipv4_dhcp_ending_address_1").val(ending_ip1);
		$("#ipv4_dhcp_beginning_address_2").val(beginning_ip2);
		$("#ipv4_dhcp_ending_address_2").val(ending_ip2);
		$("#ipv4_dhcp_beginning_address_3").val(beginning_ip3);
		$("#ipv4_dhcp_ending_address_3").val(ending_ip3);
		$("#ipv4_dhcp_beginning_address_4").val(beginning_ip4);
		$("#ipv4_dhcp_ending_address_4").val(ending_ip4);
	}//end of updateIPv4
	// Update range addresses automatically
	$(".gateway_address").blur(function() {
		updateIPv4();
	});
	$("#ipv4_subnet_mask").change(function() {
		updateIPv4();
	});
	function initPopulateDHCPv4(){
/*
		$("select#ipv4_subnet_mask").prop("disabled", false);
		$("select option").prop("disabled", false);
		$('#ipv4_dhcp_lease_time_measure').prop("disabled", false);
		if ($('#ipv4_dhcp_lease_time_measure').val() != "forever") 
			$('#ipv4_dhcp_lease_time_amount').prop("disabled", false);
*/
		var subnet = $("select#ipv4_subnet_mask option:selected").val();
		switch (subnet) {
			case "255.255.0.0":
				$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_2").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_3").prop("disabled", false);
				$("#ipv4_dhcp_beginning_address_4").prop("disabled", false);
				$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_2").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_3").prop("disabled", false);
				$("#ipv4_dhcp_ending_address_4").prop("disabled", false);
				$("#ipv4_gateway_address_1").prop("disabled", false);
				$("#ipv4_gateway_address_2").prop("disabled", false);
				$("#ipv4_gateway_address_3").prop("disabled", true);
				$("#ipv4_gateway_address_4").prop("disabled", true);
				break;
			/*case "255.255.255.128":
				$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_2").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_3").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_4").prop("disabled", false);
				$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_2").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_3").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_4").prop("disabled", false);			
				$("#ipv4_gateway_address_1").prop("disabled", false);
				$("#ipv4_gateway_address_2").prop("disabled", false);
				$("#ipv4_gateway_address_3").prop("disabled", false);
				$("#ipv4_gateway_address_4").prop("disabled", true);
				break;*/
			case "255.255.255.252":
				$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_2").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_3").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_4").prop("disabled", false);
				$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_2").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_3").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_4").prop("disabled", false);
				$("#ipv4_gateway_address_1").prop("disabled", false);
				$("#ipv4_gateway_address_2").prop("disabled", false);
				$("#ipv4_gateway_address_3").prop("disabled", false);
				$("#ipv4_gateway_address_4").prop("disabled", true);
				break;
			case "255.0.0.0":
				$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_2").prop("disabled", false);
				$("#ipv4_dhcp_beginning_address_3").prop("disabled", false);
				$("#ipv4_dhcp_beginning_address_4").prop("disabled", false);
				$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_2").prop("disabled", false);
				$("#ipv4_dhcp_ending_address_3").prop("disabled", false);
				$("#ipv4_dhcp_ending_address_4").prop("disabled", false);
				$("#ipv4_gateway_address_1").prop("disabled", false);
				$("#ipv4_gateway_address_2").prop("disabled", true);
				$("#ipv4_gateway_address_3").prop("disabled", true);
				$("#ipv4_gateway_address_4").prop("disabled", true);
				break;
			default:
				$("#ipv4_dhcp_beginning_address_1").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_2").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_3").prop("disabled", true);
				$("#ipv4_dhcp_beginning_address_4").prop("disabled", false);
				$("#ipv4_dhcp_ending_address_1").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_2").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_3").prop("disabled", true);
				$("#ipv4_dhcp_ending_address_4").prop("disabled", false);
				$("#ipv4_gateway_address_1").prop("disabled", false);
				$("#ipv4_gateway_address_2").prop("disabled", false);
				$("#ipv4_gateway_address_3").prop("disabled", false);
				$("#ipv4_gateway_address_4").prop("disabled", true);
		}
	}
	initPopulateDHCPv4();
	// Disable time text field, if lease time is forever
	$("#ipv4_dhcp_lease_time_measure").change(function() {
		var $select = $(this);
		var $time = $("#ipv4_dhcp_lease_time_amount");
		if($select.find("option:selected").val() == "forever") {
			$time.prop("disabled", true).addClass("disabled").val();
		} else {
			$time.prop("disabled", false).removeClass("disabled").val();
		}
	}).trigger("change");
	$("#ipv6_dhcp_lease_time_measure").change(function() {
		var $select = $(this);
		var $time = $("#ipv6_dhcp_lease_time_amount");
		if($select.find("option:selected").val() == "forever") {
			$time.prop("disabled", true).addClass("disabled").val();
		} else {
			$time.prop("disabled", false).removeClass("disabled").val();
		}
	}).trigger("change");
	jQuery.validator.addMethod("checkMaskBegin",function(value,element){		
		var netmask = $('#ipv4_subnet_mask').val();
		var ipaddr = $('#ipv4_gateway_address_1').val() + "." + $('#ipv4_gateway_address_2').val() + "." + $('#ipv4_gateway_address_3').val() + ".1";
		var dhcp_addr = $('#ipv4_dhcp_beginning_address_1').val() + "." + $('#ipv4_dhcp_beginning_address_2').val() + "." + $('#ipv4_dhcp_beginning_address_3').val() + "." + $('#ipv4_dhcp_beginning_address_4').val();
		isIp4Valid = ValidIp4Addr(dhcp_addr, ipaddr, netmask);
		$("p:contains('<?php echo _('DHCP Beginning address is beyond the valid range.')?>'):visible").remove();
		return isIp4Valid;
	}, "<?php echo _('DHCP Beginning address is beyond the valid range.')?>");
	jQuery.validator.addMethod("checkMaskEnd",function(value,element){		
		var netmask = $('#ipv4_subnet_mask').val();
		var ipaddr = $('#ipv4_gateway_address_1').val() + "." + $('#ipv4_gateway_address_2').val() + "." + $('#ipv4_gateway_address_3').val() + ".1";
		var dhcp_addr = $('#ipv4_dhcp_ending_address_1').val() + "." + $('#ipv4_dhcp_ending_address_2').val() + "." + $('#ipv4_dhcp_ending_address_3').val() + "." + $('#ipv4_dhcp_ending_address_4').val();
		isIp4Valid = ValidIp4Addr(dhcp_addr, ipaddr, netmask);
		$("p:contains('<?php echo _('DHCP Ending address is beyond the valid range.')?>'):visible").remove();
		return isIp4Valid;
	}, "<?php echo _('DHCP Ending address is beyond the valid range.')?>");
	$.validator.addMethod("hexadecimal", function(value, element) {
		return this.optional(element) || /^[a-fA-F0-9]+$/i.test(value);
	}, "<?php echo _('Only hexadecimal characters are valid. Acceptable characters are ABCDEF0123456789.')?>");
	$("#pageForm").validate({
		groups: {
	    	ip_set: "ipv4_gateway_address_1 ipv4_gateway_address_2 ipv4_gateway_address_3",
	    	b_range:"ipv4_dhcp_beginning_address_2, ipv4_dhcp_beginning_address_3, ipv4_dhcp_beginning_address_4",
	    	e_range:"ipv4_dhcp_ending_address_2, ipv4_dhcp_ending_address_3, ipv4_dhcp_ending_address_4"
		},
		rules: {
			ipv4_gateway_address_1: {
				required: true,
				min: 1,
				max: 255,
				digits: true
			},
			ipv4_gateway_address_2: {
				required: true,
				min: 0,
				max: 255,
				digits: true
			},
			ipv4_gateway_address_3: {
				required: true,
				min: 0,
				max: 255,
				digits: true
			}
			,ipv4_dhcp_beginning_address_2: {
			    required: true,
				min: 0,
				max: 255,
				digits: true,
				checkMaskBegin: true
			}
			,ipv4_dhcp_ending_address_2: {
			    required: true,
				min: 0,
				max: 255,
				digits: true,
				checkMaskEnd: true
			}
			,ipv4_dhcp_beginning_address_3: {
			    required: true,
				min: 0,
				max: 255,
				digits: true,
				checkMaskBegin: true
			}
			,ipv4_dhcp_ending_address_3: {
			    required: true,
				min: 0,
				max: 255,
				digits: true,
				checkMaskEnd: true
			}
			,ipv4_dhcp_beginning_address_4: {
			    required: true,
				min: 0,
				max: 253,
				digits: true,
				checkMaskBegin: true
			}
			,ipv4_dhcp_ending_address_4: {
			    required: true,
				min: 0,
				max: 253,
				digits: true,
				checkMaskEnd: true
			}
			,ipv4_dhcp_lease_time_amount: {
				required: function() {
					return $("#ipv4_dhcp_lease_time_measure option:selected").val() != "forever";
				},
			    	digits : true,
				min: function () {
					if($("#ipv4_dhcp_lease_time_measure option:selected").val() == "seconds") return 120;
					else if($("#ipv4_dhcp_lease_time_measure option:selected").val() == "minutes") return 2;
					else return 1;
				}
	        	}
		},
	    	highlight: function( element, errorClass, validClass ) {
			$(element).closest(".form-row").find("input").addClass(errorClass).removeClass(validClass);
		},
		unhighlight: function( element, errorClass, validClass ) {
			$(element).closest(".form-row").find("input").removeClass(errorClass).addClass(validClass);
		}
	});
	$("#pageFormV6").validate({
		groups:{
			DBA: "DBA_5 DBA_6 DBA_7 DBA_8",
			DEA: "DEA_5 DEA_6 DEA_7 DEA_8",
                        ULA: "ULA_1 ULA_2 ULA_3 ULA_4"
		},
		rules: {
	        DBA_5: {
	            required: function() {
	                return $('#Stateful').is(":checked");
	            }
	            ,hexadecimal: true
	        }
	        ,DBA_6: {
	            required: function() {
	                return $('#Stateful').is(":checked");
	            }
	            ,hexadecimal: true
	        }
	        ,DBA_7: {
	            required: function() {
	                return $('#Stateful').is(":checked");
	            }
	            ,hexadecimal: true
	        }
	        ,DBA_8: {
	            required: function() {
	                return $('#Stateful').is(":checked");
	            }
	            ,hexadecimal: true
	        }
	        ,DEA_5: {
	            required: function() {
	                return $('#Stateful').is(":checked");
	            }
	            ,hexadecimal: true
	        }
	        ,DEA_6: {
	            required: function() {
	                return $('#Stateful').is(":checked");
	            }
	            ,hexadecimal: true
	        }
	        ,DEA_7: {
	            required: function() {
	                return $('#Stateful').is(":checked");
	            }
	            ,hexadecimal: true
	        }
	        ,DEA_8: {
	            required: function() {
	                return $('#Stateful').is(":checked");
	            }
	            ,hexadecimal: true
		}
		,ULA_1:{
		    hexadecimal: true
	        }
	        ,ULA_2:{
		    hexadecimal: true
	        }
	        ,ULA_3:{
		    hexadecimal: true
	        }
	        ,ULA_4:{
		    hexadecimal: true
	        }
		,ipv6_dhcp_lease_time_amount: {
				required: function() {
					return $("#ipv6_dhcp_lease_time_measure option:selected").val() != "forever";
				},
			    	digits : true,
				min: function () {
					if($("#ipv6_dhcp_lease_time_measure option:selected").val() == "seconds") return 120;
					else if($("#ipv6_dhcp_lease_time_measure option:selected").val() == "minutes") return 2;
					else return 1;
				}
	        	}
		},
	    	highlight: function( element, errorClass, validClass ) {
			$(element).closest(".form-row").find("input").addClass(errorClass).removeClass(validClass);
		},
		unhighlight: function( element, errorClass, validClass ) {
			$(element).closest(".form-row").find("input").removeClass(errorClass).addClass(validClass);
		}
	}); //end of pageform v6
	$("#ipv4_dhcp_lease_time_measure, #ipv4_dhcp_lease_time_amount").change(function() {
	  	$("#pageForm").valid();
	});
	$("#ipv6_dhcp_lease_time_measure, #ipv6_dhcp_lease_time_amount").change(function() {
		$("#pageFormV6").valid();
	});
	$("#restore-default-settings-ipv4").click(function(e) {
		e.preventDefault();
		jConfirm(
		"<?php echo _('Are you sure you want to change LAN IPv4 to default settings?')?>"
		,"<?php echo _('Reset Default IPv4 Settings')?>"
		,function(ret) {
		if(ret) {
		var default_ip = default_admin_ip.split('.');
		$("#ipv4_gateway_address_1").val(default_ip[0]);
		$("#ipv4_gateway_address_2").val(default_ip[1]);
		$("#ipv4_gateway_address_3").val(default_ip[2]);
		$("#ipv4_gateway_address_4").val(default_ip[3]);
		$("#ipv4_subnet_mask").val("255.255.255.0");
		$("#ipv4_dhcp_beginning_address_1").val(default_ip[0]);
		$("#ipv4_dhcp_beginning_address_2").val(default_ip[1]);
		$("#ipv4_dhcp_beginning_address_3").val(0);
		$("#ipv4_dhcp_beginning_address_4").val(2);
		$("#ipv4_dhcp_ending_address_1").val(default_ip[0]);
		$("#ipv4_dhcp_ending_address_2").val(default_ip[1]);
		$("#ipv4_dhcp_ending_address_3").val(0);
		$("#ipv4_dhcp_ending_address_4").val(253);
		$("#ipv4_dhcp_lease_time_amount").val(1);
		$("#ipv4_dhcp_lease_time_measure").val("weeks");
		var ipaddr = default_admin_ip;
		var subnet_mask = "255.255.255.0"; 
		var dhcp_begin_addr = default_ip[0]+"."+default_ip[1]+".0.2";
		var dhcp_end_addr = default_ip[0]+"."+default_ip[1]+".0.253";
		var lease_time = 604800; // 1 week	
        var Config = '{"Ipaddr":"' + ipaddr + '", "Subnet_mask":"' + subnet_mask + '", "Dhcp_begin_addr":"' + dhcp_begin_addr + '", "Dhcp_end_addr":"' + dhcp_end_addr +'", "Dhcp_lease_time":"' + lease_time + '"}';
        if((login_user == "admin") && (jsGwIP != ipaddr)) {
    	jConfirm(	        
	        "<?php echo _('This may need you to relogin with new Gateway IP address')?>"
	        , "<?php echo _('Are you sure?')?>"
	        ,function(ret) {
	            if(ret) {	
	            	jProgress('<?php echo _('Please be patient...')?>', 600);               
            		$.ajax({
            			type: "POST",
            			url:  "actionHandler/ajaxSet_IP_configuration.php",
            			data: {
            				configInfo: Config
            			},
            			dataType: "json",
            			//timeout:  15000,
            			success: function(){ 
            			//jAlert("Please login with new IP address "); 	            			
            			},
            			error: function(){ 
            			//jAlert("Please login with new IP address ");             				
            			}
            		}); //end of ajax
            		setTimeout(function(){
						jHide();
        				window.location.replace('http://' + ipaddr + '/index.php');
					}, 90000);
	            } //end of if ret
		    }); //end of jConfirm
		} //end of login user
		else{
			setIPconfiguration(Config);
		}
		} //end of if ret
		});//end of jConfirm
	});
	var gw_ip_1 = parseInt($('#ipv4_gateway_address_1').val());
	if (gw_ip_1 == 172){
		//gw ip is B class ip address		
		if( $('#mask4').length>0 ) $('#mask4').remove();
		if( ! $('#mask2').length>0 ) mask2Option.insertAfter('#mask1');
	}
	else if (gw_ip_1 == 192){
		//gw ip is C class ip address
		if( $('#mask2').length>0 ) $('#mask2').remove();
		if( $('#mask4').length>0 ) $('#mask4').remove();
	}
	$('#ipv4_gateway_address_1').change(function(){
		var gw_ip1 = parseInt($('#ipv4_gateway_address_1').val());
	    var mask2Option = $('<option id="mask2" value="255.255.0.0">255.255.0.0</option>');
	    var mask4Option = $('<option id="mask4" value="255.0.0.0">255.0.0.0</option>');
	    if (gw_ip1 == 10){
			if( ! $('#mask2').length>0 ) mask2Option.insertAfter('#mask1');
			if( ! $('#mask4').length>0 ) mask4Option.insertAfter('#mask3');
	    }
	    else if (gw_ip1 == 172){
			//gw ip is B class ip address
			if( $('#mask4').length>0 ) $('#mask4').remove();
			if( ! $('#mask2').length>0 ) mask2Option.insertAfter('#mask1');
		}
		else if (gw_ip1 == 192){
			//gw ip is C class ip address
			if( $('#mask2').length>0 ) $('#mask2').remove();
			if( $('#mask4').length>0 ) $('#mask4').remove();
		}
	});
/* 
 This function checks dhcpv4 ending address should be larger than begin address
 @DBArr = Array(dhcp begin dot address partial 2, 3, 4)
 @DEArr = Array(dhcp ending dot address partial 2, 3, 4)
 */
function validate_v4addr_pool(DBArr, DEArr) {
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
		}
	}
	return flag;
}
$('#submit_ipv4').click(function(e){
	e.preventDefault();
	var dhcp4B2 = parseInt($("#ipv4_dhcp_beginning_address_2").val());
	var dhcp4B3 = parseInt($("#ipv4_dhcp_beginning_address_3").val());
	var dhcp4B4 = parseInt($("#ipv4_dhcp_beginning_address_4").val());
	var dhcp4E2 = parseInt($("#ipv4_dhcp_ending_address_2").val());
	var dhcp4E3 = parseInt($("#ipv4_dhcp_ending_address_3").val());
	var dhcp4E4 = parseInt($("#ipv4_dhcp_ending_address_4").val());
	var DBArr = Array(dhcp4B2, dhcp4B3, dhcp4B4);
	var DEArr = Array(dhcp4E2, dhcp4E3, dhcp4E4);
    if (! validate_v4addr_pool(DBArr, DEArr)) {
        jAlert('<?php echo _("Beginning Address can\'t be larger than ending address!")?>');
        return;
    }
    var gw_ip1 = parseInt($('#ipv4_gateway_address_1').val());
    var gw_ip2 = parseInt($('#ipv4_gateway_address_2').val());
    var gw_ip3 = parseInt($('#ipv4_gateway_address_3').val());
    if( ((gw_ip1 != 10) && (gw_ip1 != 172) && (gw_ip1 != 192)) || ((gw_ip1 == 172) && ((gw_ip2<16) || (gw_ip2>31)))  || ((gw_ip1== 192) && ((gw_ip2 != 168) || (gw_ip3== 147)) ) ){
		jAlert("<?php echo _('Gateway IP is not in valid private IP range')?>\n [10.0.0.1 ~ 10.255.255.253,\n172.16.0.1 ~ 172.31.255.254,\n192.168.0.1 ~ 192.168.146.254,\n192.168.148.1 ~ 192.168.255.254]");
    	return;
    }
    if ((gw_ip1==172) && (gw_ip2==16) && (gw_ip3==12)) {
    	jAlert("<?php echo _('This IP address is reserved for Home Security, please input again')?>");
    	return;
    }

    if ((gw_ip1==192) && (gw_ip2==168) && (gw_ip3==245)) {
    	jAlert("<?php echo _('This IP address is reserved for XFi Pods, please input again')?>");
    	return;
    }
	var ipaddr = $('#ipv4_gateway_address_1').val() + "." + $('#ipv4_gateway_address_2').val() + "." + $('#ipv4_gateway_address_3').val() + ".1";
	var subnet_mask = $('#ipv4_subnet_mask').val();
	var dhcp_begin_addr = $('#ipv4_dhcp_beginning_address_1').val() + "." + $('#ipv4_dhcp_beginning_address_2').val() + "." + $('#ipv4_dhcp_beginning_address_3').val() + "." + $('#ipv4_dhcp_beginning_address_4').val();
	var dhcp_end_addr = $('#ipv4_dhcp_ending_address_1').val() + "." + $('#ipv4_dhcp_ending_address_2').val() + "." + $('#ipv4_dhcp_ending_address_3').val() + "." + $('#ipv4_dhcp_ending_address_4').val();
	var dhcp_lease_num = $('#ipv4_dhcp_lease_time_amount').val();
	var dhcp_lease_unit = $('#ipv4_dhcp_lease_time_measure').val();
	var dhcp_lease_time = calcuate_lease_time(dhcp_lease_num, dhcp_lease_unit);
	var CM_GW_IP_Address = "<?php echo $CM_GW_IP_Address;?>";
	var WAN_GW_IPv4_Address = "<?php echo $WAN_GW_IPv4_Address;?>";
	var LAN_GW_IPv4_Address = "<?php echo $LAN_GW_IPv4_Address;?>";
	if(ipaddr == CM_GW_IP_Address){
		jAlert("<?php echo _('This IP address is reserved for CM Gateway IP Address, please input again!')?>");
	    	return;
	}
	else if(ipaddr == WAN_GW_IPv4_Address){
		jAlert("<?php echo _('This IP address is reserved for WAN Gateway IPv4 Address, please input again!')?>");
    		return;
	}
	else if(ipaddr == LAN_GW_IPv4_Address){
		jAlert("<?php echo _('This IP address is reserved for Virtual LAN IPv4 Address, please input again!')?>");
    		return;
	}
	else if(dhcp_begin_addr == dhcp_end_addr){
		jAlert("<?php echo _('DHCP beginning and ending address cannot be same, Please input again!')?>");
    		return;
	}
	var IPv4Config = '{"Ipaddr":"' + ipaddr + '", "Subnet_mask":"' + subnet_mask + '", "Dhcp_begin_addr":"' + dhcp_begin_addr 
			             + '", "manual_dns":"false", "Dhcp_end_addr":"' + dhcp_end_addr + '", "Dhcp_lease_time":"' + dhcp_lease_time + '"}';
    //alert(IPv4Config)
    if((login_user == "admin") && (jsGwIP != ipaddr)) {
      if($("#pageForm").valid()){
    	jConfirm(	        
	        "<?php echo _('This may need you to relogin with new Gateway IP address')?>"
	        , "<?php echo _('Are you sure?')?>"
	        ,function(ret) {
	            if(ret) {
	            	jProgress('<?php echo _('Please be patient...')?>', 600);	               
            		$.ajax({
            			type: "POST",
            			url:  "actionHandler/ajaxSet_IP_configuration.php",
            			data: {
            				configInfo: IPv4Config
            			},
            			dataType: "json",
            			//timeout:  15000,
            			success: function(){ 
            			//jAlert("Please login with new IP address ");             			    
            			},
            			error: function(){ 
            			//jAlert("Please login with new IP address ");             				
            			}
        			});//end of ajax
        			setTimeout(function(){
						jHide();
						//console.log('http://' + ipaddr + '/index.php');
        				window.location.replace('http://' + ipaddr + '/index.php');
					}, 90000);
            	} //end of if ret
	    }); //end of jConfirm
          } //end of pageForm
	} //end of login user
	else{
		setIPconfiguration(IPv4Config);
	}
});
function setIPconfiguration(configuration){
	if($("#pageForm").valid()){
		jProgress('<?php echo _('This may take several seconds...')?>', 120);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_IP_configuration.php",
			data: { configInfo: configuration },
			success: function(){            
				jHide();
				window.location.href = "local_ip_configuration.php";
			},
			error: function(){            
				jHide();
				//jAlert("Failure, please try again.");
			}
		});
	}
}
function setIPv6configuration(configuration){
	if($("#pageFormV6").valid()){
		jProgress('<?php echo _('This may take several seconds...')?>', 120);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_IP_configuration.php",
			data: { configInfo: configuration },
			success: function(){            
				jHide();
				window.location.href = "local_ip_configuration.php";
			},
			error: function(){            
				jHide();
				//jAlert("Failure, please try again.");
			}
		});
	}
}
function calcuate_lease_time(num, unit){
    switch (unit) {
	case 'seconds':
		return num;
		break;
	case 'minutes':
		return num * 60;
		break;
	case 'hours':
		return num * 3600;
		break;
	case 'days': 
		return num * 3600 * 24;
		break;	
	case 'weeks': 
		return num * 3600 * 24 * 7;
		break;	
	case 'forever':
	    return -1;
	    break;	
   }
}
	display_time_format(jsLeaseTime);
	display_time_format_V6(jsV6LeaseTime);
function display_time_format(num){
	if (num == "") return;
    var timeNum = parseInt(num);
	var TimeVal  = '#ipv4_dhcp_lease_time_amount';
	if ( (timeNum % 604800) == 0) {
		$(TimeVal).val(timeNum / 604800);
		$('#ipv4_dhcp_lease_time_measure option[value="weeks"]').prop("selected", true);
	}
	else if( (timeNum % 86400) == 0 ) {
		$(TimeVal).val(timeNum / 86400);
		$('#ipv4_dhcp_lease_time_measure option[value="days"]').prop("selected", true);
	}
	else if( (timeNum % 3600) == 0 ) {
		$(TimeVal).val(timeNum / 3600);
		$('#ipv4_dhcp_lease_time_measure option[value="hours"]').prop("selected", true);
	}
	else if( (timeNum % 60) == 0 ) {
		$(TimeVal).val(timeNum / 60);
		$('#ipv4_dhcp_lease_time_measure option[value="minutes"]').prop("selected", true);
	}
	else if( timeNum == -1) {
		$(TimeVal).prop("disabled", true);
		$('#ipv4_dhcp_lease_time_measure option[value="forever"]').prop("selected", true);
	}
	else {
		$(TimeVal).val(timeNum);
		$('#ipv4_dhcp_lease_time_measure option[value="seconds"]').prop("selected", true);
	}
}
function display_time_format_V6(num){
	if (num == "") return;
    var timeNum = parseInt(num);
	var TimeVal  = '#ipv6_dhcp_lease_time_amount';
	if ( (timeNum % 604800) == 0) {
		$(TimeVal).val(timeNum / 604800);
		$('#ipv6_dhcp_lease_time_measure option[value="weeks"]').prop("selected", true);
	}
	else if( (timeNum % 86400) == 0 ) {
		$(TimeVal).val(timeNum / 86400);
		$('#ipv6_dhcp_lease_time_measure option[value="days"]').prop("selected", true);
	}
	else if( (timeNum % 3600) == 0 ) {
		$(TimeVal).val(timeNum / 3600);
		$('#ipv6_dhcp_lease_time_measure option[value="hours"]').prop("selected", true);
	}
	else if( (timeNum % 60) == 0 ) {
		$(TimeVal).val(timeNum / 60);
		$('#ipv6_dhcp_lease_time_measure option[value="minutes"]').prop("selected", true);
	}
	else if( timeNum == -1) {
		$(TimeVal).prop("disabled", true);
		$('#ipv6_dhcp_lease_time_measure option[value="forever"]').prop("selected", true);
	}
	else {
		$(TimeVal).val(timeNum);
		$('#ipv6_dhcp_lease_time_measure option[value="seconds"]').prop("selected", true);
	}
}
function populateIPv6Addr(v6addr){
	//console.log(v6addr);
	if (!isValidIp6Str(v6addr)) {
		return [];
	}
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
	var ipv6_global_addr = "<?php echo $ipv6_global_addr; ?>";
	var ipv6_global_arr = populateIPv6Addr(ipv6_global_addr);
	$("#GGA_1").val(ipv6_global_arr[0]);
    $("#GGA_2").val(ipv6_global_arr[1]);
    $("#GGA_3").val(ipv6_global_arr[2]);
    $("#GGA_4").val(ipv6_global_arr[3]);
    $("#GGA_5").val(ipv6_global_arr[4]);
    $("#GGA_6").val(ipv6_global_arr[5]);
    $("#GGA_7").val(ipv6_global_arr[6]);
    $("#GGA_8").val(ipv6_global_arr[7]);
function updateIPv6(){
	if ($('#Stateful').is(":checked")) {
	    $('#DBA_5').prop("disabled", false);
	    $('#DBA_6').prop("disabled", false);
	    $('#DBA_7').prop("disabled", false);
	    $('#DBA_8').prop("disabled", false);
	    $('#DEA_5').prop("disabled", false);
	    $('#DEA_6').prop("disabled", false);
	    $('#DEA_7').prop("disabled", false);
	    $('#DEA_8').prop("disabled", false);
	    if ($('#ipv6_dhcp_lease_time_measure').val() != "forever") 
	    	$('#ipv6_dhcp_lease_time_amount').prop("disabled", false);
	    $('#ipv6_dhcp_lease_time_measure').prop("disabled", false);
	}
	else{
		$('#DBA_5').prop("disabled", true);
	    $('#DBA_6').prop("disabled", true);
	    $('#DBA_7').prop("disabled", true);
	    $('#DBA_8').prop("disabled", true);
	    $('#DEA_5').prop("disabled", true);
	    $('#DEA_6').prop("disabled", true);
	    $('#DEA_7').prop("disabled", true);
	    $('#DEA_8').prop("disabled", true);
	    $('#ipv6_dhcp_lease_time_amount').prop("disabled", true);
	    $('#ipv6_dhcp_lease_time_measure').prop("disabled", true);
	}
}
function stateful_check(){
	if($('#Stateful').is(':checked')){
		$("#ula_dis").hide();$("#Stateless").removeAttr('checked');
	}
	else{
		$("#ula_dis").show();$("#Stateless").attr('checked','checked');
	}
}
updateIPv6();
$('#Stateful').click(function(){
	updateIPv6();
	$("#pageFormV6").valid();
	$("#ipv6_dhcp_lease_time_amount").removeClass("error");
	if(partner_id.indexOf('sky-')===0){
	    stateful_check();
	}
});
if(partner_id.indexOf('sky-')===0){
	$('input[name="ipv6_enable"]').click(function(){
            if($("#ipv6_enable").prop("checked")){
                $("#ula_enable").attr('checked','checked');
                $('#ula_enable,#ULA_1,#ULA_2,#ULA_3,#ULA_4').attr('disabled',false);
	    }
	    else{
		$("#ula_enable").removeAttr('checked');
		$('#ula_enable,#ULA_1,#ULA_2,#ULA_3,#ULA_4').attr('disabled',true);
	    }
        });
       $('input[name="ula_enable"]').click(function(){
           if($("#ula_enable").prop("checked")){
		$('#ULA_1,#ULA_2,#ULA_3,#ULA_4').attr('disabled',false);
	    }
	    else{
		$('#ULA_1,#ULA_2,#ULA_3,#ULA_4').attr('disabled',true);
	     }
       });
}
/*$('input[name="ULA"]').click(function(){
	if($("input[name='ULA'][value='ula_auto']").prop("checked")){
		$('#ULA_1_1 ,#ULA_2,#ULA_3,#ULA_4').attr('disabled',true);
	}
	else if($("input[name='ULA'][value='ula_manual']").prop("checked")){
		$('#ULA_1_1 ,#ULA_2,#ULA_3,#ULA_4').attr('disabled',false);
	}
});*/
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
$('#submit_ipv6').click(function(e){
	e.preventDefault();
	//convert to int with radix 16
    var DBA_5 = parseInt($('#DBA_5').val(), 16);
    var DBA_6 = parseInt($('#DBA_6').val(), 16);
    var DBA_7 = parseInt($('#DBA_7').val(), 16);
    var DBA_8 = parseInt($('#DBA_8').val(), 16);
    var DEA_5 = parseInt($('#DEA_5').val(), 16);
    var DEA_6 = parseInt($('#DEA_6').val(), 16);
    var DEA_7 = parseInt($('#DEA_7').val(), 16);
    var DEA_8 = parseInt($('#DEA_8').val(), 16);
    var DBArr = Array(DBA_5, DBA_6, DBA_7, DBA_8);
    var DEArr = Array(DEA_5, DEA_6, DEA_7, DEA_8);
    if (! validate_v6addr_pool(DBArr, DEArr)) {
    	jAlert("<?php echo _('DHCPv6 beginning address can\'t be larger than ending address!')?>");
    	return;
    }
    var Stateful = $('#Stateful').is(":checked"); //bool, true/false
    var dhcpv6_begin_addr = $('#DBA_5').val() + ":" + $('#DBA_6').val() + ":" + $('#DBA_7').val() + ":" + $('#DBA_8').val();
    var dhcpv6_end_addr   = $('#DEA_5').val() + ":" + $('#DEA_6').val() + ":" + $('#DEA_7').val() + ":" + $('#DEA_8').val();
    var dhcp_lease_num = $('#ipv6_dhcp_lease_time_amount').val();
	var dhcp_lease_unit = $('#ipv6_dhcp_lease_time_measure').val();
	var dhcpv6_lease_time = calcuate_lease_time(dhcp_lease_num, dhcp_lease_unit);  
	var IPv6Config = '{"IPv6": "Yes", "Stateful": "' + Stateful + '", "dhcpv6_begin_addr": "' + dhcpv6_begin_addr + '", "dhcpv6_end_addr": "' + dhcpv6_end_addr +'", "dhcpv6_lease_time": "' + dhcpv6_lease_time + '"}';
	if(partner_id.indexOf('sky-')===0){
    	var ipv6_enable = $('#ipv6_enable').is(':checked');
	var ula_enable = $('#ula_enable').is(':checked');
        var ula_prefix='';
	if($('#ULA_1').val().indexOf('fc')===0|| $('#ULA_1').val().indexOf('fd')===0){
             for(i=1; i<=4;i++){
                if($('#ULA_'+i).val()!==''){
                     ula_prefix += $('#ULA_'+i).val()+':';
		}
	      }
	    ula_prefix = ula_prefix.slice(0,-1);
	}
        else{jAlert("<?php echo _('Prefix value should start with [fc] or [fd]')?>");return false;}
        var IPv6Config = '{"IPv6": "Yes", "Stateful": "' + Stateful + '", "dhcpv6_begin_addr": "' + dhcpv6_begin_addr + '", "dhcpv6_end_addr": "' + dhcpv6_end_addr +'", "dhcpv6_lease_time": "' + dhcpv6_lease_time +'", "ipv6_enable": "' + ipv6_enable +'", "ula_enable": "' + ula_enable +'", "ula_prefix": "' + ula_prefix +'"}';
    }
   	setIPv6configuration(IPv6Config);
});
$('#restore_ipv6').click(function(e) {
	e.preventDefault();
	jConfirm(
	"<?php echo _('Are you sure you want to change LAN IPv6 to default settings?')?>"
	,"<?php echo _('Reset Default IPv6 Settings')?>"
	,function(ret) {
	if(ret) {
		$('#DBA_5').val(0);
		$('#DBA_6').val(0);
		$('#DBA_7').val(0);
		$('#DBA_8').val(1);
		$('#DEA_5').val(0);
		$('#DEA_6').val(0);
		$('#DEA_7').val(0);
		$('#DEA_8').val("fffe");
		$("#ipv6_dhcp_lease_time_amount").val(1);
		$("#ipv6_dhcp_lease_time_measure").val("weeks");
		var dhcpv6_begin_addr = "0:0:0:1";
		var dhcpv6_end_addr = "0:0:0:fffe";
		var dhcpv6_lease_time = 604800;    // 1 week	
        var IPv6Config = '{"IPv6": "Yes", "restore": "true", "dhcpv6_begin_addr": "' + dhcpv6_begin_addr + '", "dhcpv6_end_addr": "' + dhcpv6_end_addr +'", "dhcpv6_lease_time": "' + dhcpv6_lease_time + '"}';
    	setIPv6configuration(IPv6Config);
	} //end of if ret
	}); //end of jconfirm
});//end of click restore ipv6
	//if DeviceMode is Ipv4 then DHCPv6 parameters should be grayed out on the page
	var DeviceMode = "<?php echo $DeviceMode; ?>";
	if(DeviceMode == "Ipv4"){
		$('#Stateful').prop("disabled", true);
		$('#DBA_5').prop("disabled", true);
		$('#DBA_6').prop("disabled", true);
		$('#DBA_7').prop("disabled", true);
		$('#DBA_8').prop("disabled", true);
		$('#DEA_5').prop("disabled", true);
		$('#DEA_6').prop("disabled", true);
		$('#DEA_7').prop("disabled", true);
		$('#DEA_8').prop("disabled", true);
		$('#ipv6_dhcp_lease_time_amount').prop("disabled", true);
		$('#ipv6_dhcp_lease_time_measure').prop("disabled", true);
	}
}); //end of document ready
</script>
<div id="content">
	<h1><?php echo _('Gateway > Connection > Local IP Configuration')?></h1>
	<div id="educational-tip">
			<p class="tip"><?php echo _('Manage your home network settings.')?></p>
			<p class="hidden"><?php echo _('<strong>Gateway address:</strong> Enter the IP address of the Gateway.')?></p>
			<p class="hidden"><?php echo _('<strong>Subnet Mask:</strong> The subnet mask is associated with the IP address. Select the appropriate subnet mask based on the number of devices that will be connected to your network.')?></p>
	        <p class="hidden"><?php echo _('<strong>DHCP Beginning and Ending Addresses:</strong> The DHCP server in the Gateway allows the router to manage IP address assignment for the connected devices.')?></p>
			<p class="hidden"><?php echo _('<strong>DHCP Lease time:</strong> The lease time is the length of time the Gateway offers an IP address to a connected device. The lease is renewed while it is connected to the network. After the time expires, the IP address is freed and may be assigned to any new device that connects to the Gateway.')?></p>
	</div>
	<form action="#TBD" method="post" id="pageForm">
    <div class="module forms">
   	    <h2>IPv4</h2>
			<div id="dhcp-portion">	
    		<div class="form-row">
    			<label for="ipv4_gateway_address_1"><?php echo _('Gateway Address:')?></label>
    			<?php  
    			   $ipv4_addr = $LanGwIP;
    			   $ipArr = explode(".", $ipv4_addr);    			   
    			?>
                <input type="text" size="3" maxlength="3"  value="<?php echo $ipArr['0']; ?>" id="ipv4_gateway_address_1" name="ipv4_gateway_address_1" class="gateway_address smallInput"> 
    	        <label for="ipv4_gateway_address_2" class="acs-hide"></label>
    	        .<input type="text" size="3" maxlength="3" value="<?php echo $ipArr['1']; ?>" id="ipv4_gateway_address_2" name="ipv4_gateway_address_2" class="gateway_address smallInput"> 
    	        <label for="ipv4_gateway_address_3" class="acs-hide"></label>
    	        .<input type="text" size="3" maxlength="3" value="<?php echo $ipArr['2']; ?>" id="ipv4_gateway_address_3" name="ipv4_gateway_address_3" class="gateway_address smallInput"> 
    	        <label for="ipv4_gateway_address_4" class="acs-hide"></label>
    	        .<input type="text" size="3" maxlength="3" value="<?php echo $ipArr['3']; ?>" id="ipv4_gateway_address_4" disabled="disabled" name="ipv4_gateway_address_4" class="gateway_address smallInput" />
    		</div>
    		<div class="form-row odd">
    			<label for="ipv4_subnet_mask"><?php echo _('Subnet Mask:')?></label>
    			<?php 
                    $subnetmask = $device_ctrl_value["subnetmask"];
    			?>
    			<select name="ipv4_subnet_mask" id="ipv4_subnet_mask">
    				<option id='mask1' value="255.255.255.0" <?php if("255.255.255.0" == $subnetmask) echo 'selected'; ?> >255.255.255.0</option>
    				<option id="mask2" value="255.255.0.0" <?php if("255.255.0.0" == $subnetmask) echo 'selected';  ?> >255.255.0.0</option>
    				<!--option id="mask3" value="255.255.255.128" <?php //if("255.255.255.128" == $subnetmask) echo 'selected'; ?> >255.255.255.128</option-->
    				<option id="mask4" value="255.0.0.0" <?php if("255.0.0.0" == $subnetmask) echo 'selected'; ?> >255.0.0.0</option>
    				<!-- <option id="mask5" value="255.255.255.252" <?php //if("255.255.255.252" == $subnetmask) echo 'selected'; ?> >255.255.255.252</option> -->
    			</select>
    	    </div>
    		<div class="form-row">
    			<label for="ipv4_dhcp_beginning_address_1"><?php echo _('DHCP Beginning Address:')?></label>
<!--     			<span id="ipv4_dhcp_beginning_address" class="readonlyValue"></span> -->
                <?php  
    			   $beginAddr = $dhcpv4_value["beginAddr"];
                           $SecWebUI = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_RFC.Feature.SecureWebUI.Enable");
                           if (!strcmp($SecWebUI, "true")) {
                                 $dhcp_startout = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress");
                                 $beginArr = explode(".", $dhcp_startout);
                                 $beginArr['3'] = "2";
                           }
                           else
                           {
                                 $beginArr = explode(".", $beginAddr);
                           } 
    			?>  
    			<input type="text" size="3" maxlength="3" value="<?php echo $beginArr['0']; ?>" id="ipv4_dhcp_beginning_address_1" name="ipv4_dhcp_beginning_address_1" class="smallInput" />
    	        <label for="ipv4_dhcp_beginning_address_2" class="acs-hide"></label>
    	        .<input type="text" size="3" maxlength="3" value="<?php echo $beginArr['1']; ?>" id="ipv4_dhcp_beginning_address_2" name="ipv4_dhcp_beginning_address_2" class="smallInput" />
    	        <label for="ipv4_dhcp_beginning_address_3" class="acs-hide"></label>
    	        .<input type="text" size="3" maxlength="3" value="<?php echo $beginArr['2']; ?>" id="ipv4_dhcp_beginning_address_3" name="ipv4_dhcp_beginning_address_3" class="smallInput" />
    	        <label for="ipv4_dhcp_beginning_address_4" class="acs-hide"></label>
    	        .<input type="text" size="3" maxlength="3" value="<?php echo $beginArr['3']; ?>" id="ipv4_dhcp_beginning_address_4" name="ipv4_dhcp_beginning_address_4" class="smallInput" />
    		</div>
    		<div class="form-row odd">
                <label for="ipv4_dhcp_ending_address_1"><?php echo _('DHCP Ending Address:')?></label>
                <?php  
    			   $endAddr = $dhcpv4_value["endAddr"];
                           if (!strcmp($SecWebUI, "true")) {
                                 $dhcp_endout = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress");
                                 $endArr = explode(".", $dhcp_endout);
                                 $endArr['3'] = "253";
                           }
                           else
                           {
                                 $endArr = explode(".", $endAddr);
                           }
    			?> 
				<input type="text" size="3" maxlength="3" value="<?php echo $endArr['0']; ?>" id="ipv4_dhcp_ending_address_1" name="ipv4_dhcp_ending_address_1" class="smallInput" />
    	        <label for="ipv4_dhcp_ending_address_2" class="acs-hide"></label>
    	        .<input type="text" size="3" maxlength="3" value="<?php echo $endArr['1']; ?>" id="ipv4_dhcp_ending_address_2" name="ipv4_dhcp_ending_address_2" class="smallInput" />
    	        <label for="ipv4_dhcp_ending_address_3" class="acs-hide"></label>
    	        .<input type="text" size="3" maxlength="3" value="<?php echo $endArr['2']; ?>" id="ipv4_dhcp_ending_address_3" name="ipv4_dhcp_ending_address_3" class="smallInput" />
    	        <label for="ipv4_dhcp_ending_address_4" class="acs-hide"></label>
    	        .<input type="text" size="3" maxlength="3" value="<?php echo $endArr['3']; ?>" id="ipv4_dhcp_ending_address_4" name="ipv4_dhcp_ending_address_4" class="smallInput"  />
    		 </div>
    		<div class="form-row" id="ipv4_dhcp_lease_time">
    			<label for="ipv4_dhcp_lease_time_amount"><?php echo _('DHCP Lease Time:')?></label>                 
    			<input type="text" maxlength="3" id="ipv4_dhcp_lease_time_amount" name="ipv4_dhcp_lease_time_amount" class="smallInput" />
    			<label for="ipv4_dhcp_lease_time_measure" class="acs-hide"></label>
                <select id="ipv4_dhcp_lease_time_measure" name="ipv4_dhcp_lease_time_measure">
    	            <option value="seconds"><?php echo _('Seconds')?></option>
    	            <option value="minutes"><?php echo _('Minutes')?></option>
    	            <option value="hours"><?php echo _('Hours')?></option>
    	            <option value="days"><?php echo _('Days')?></option>
    	            <option value="weeks"><?php echo _('Weeks')?></option>
    	            <option value="forever"><?php echo _('Forever')?></option>
    	        </select>
    		</div>
    	</div> <!-- end of dhcp portion -->
  	    		<div class="form-btn">
					<input id="submit_ipv4" type="button" value="<?php echo _('Save Settings')?>" class="btn" />
					<input id="restore-default-settings-ipv4" type="button" value="<?php echo _('Restore Default Settings')?>" class="btn alt" />
				</div>
    		</div> <!-- End Module -->
			</form>
			<form action="#TBD" method="post" id="pageFormV6">
    		 <div class="module forms">
    		 <h2>IPv6</h2>
    	   <div class="form-row odd">				
				<label for="LLGA_1"><?php echo _('Link-Local Gateway Address:')?></label>
				<input type="text"  class="ipv6-input" size="2" maxlength="2"  id="LLGA_1" name="LLGA_1" disabled="disabled" value="fe80"/>
				<label for="LLGA_2" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="2" id="LLGA_2" name="LLGA_2"  disabled="disabled" value="0"/>
	    	    <label for="LLGA_3" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="2" id="LLGA_3" name="LLGA_3" disabled="disabled" value="0"/>
	    	    <label for="LLGA_4" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="2" id="LLGA_4" name="LLGA_4" disabled="disabled" value="0"/>
	    	    <label for="LLGA_5" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="2" id="LLGA_5" name="LLGA_5" disabled="disabled" value="<?php echo $local_ipv6_arr[0]; ?>" />
	    	    <label for="LLGA_6" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="2" id="LLGA_6" name="LLGA_6" disabled="disabled" value="<?php echo $local_ipv6_arr[1]; ?>" />
	    	    <label for="LLGA_7" class="acs-hide"></label>
					:<input type="text" class="ipv6-input" size="2" maxlength="2" id="LLGA_7" name="LLGA_7" disabled="disabled" value="<?php echo $local_ipv6_arr[2]; ?>" />
	    	    <label for="LLGA_8" class="acs-hide"></label>
					:<input type="text" class="ipv6-input" size="2" maxlength="2" id="LLGA_8" name="LLGA_8" disabled="disabled" value="<?php echo $local_ipv6_arr[3]; ?>" />
				<br/>
				</div> 
				<div class="form-row ">
				<label for="GGA_1"><?php echo _('Global Gateway Address:')?></label>
				<input type="text"  class="ipv6-input" size="2" maxlength="4"  id="GGA_1" name="GGA_1" disabled="disabled" >
	    	    <label for="GGA_2" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="GGA_2" name="GGA_2" disabled="disabled" >
	    	    <label for="GGA_3" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="GGA_3" name="GGA_3" disabled="disabled" >
	    	    <label for="GGA_4" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="GGA_4" name="GGA_4" disabled="disabled" > 
	    	    <label for="GGA_5" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="GGA_5" name="GGA_5" disabled="disabled" >
	    	    <label for="GGA_6" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="GGA_6" name="GGA_6" disabled="disabled" >
	    	    <label for="GGA_7" class="acs-hide"></label>
					:<input type="text" class="ipv6-input" size="2" maxlength="4" id="GGA_7" name="GGA_7" disabled="disabled" >
	    	    <label for="GGA_8" class="acs-hide"></label>
					:<input type="text" class="ipv6-input" size="2" maxlength="4" id="GGA_8" name="GGA_8" disabled="disabled" >
				<br/>
				</div> 
			<div class="form-row odd">	<p><strong><?php echo _('LAN IPv6 Address Assignment')?></strong></p></div>
			<div class="form-row ">
			<?php 			    
			    $state = $dhcpv6_value["state"];
			?>
				<input type="checkbox"  name="State" value="Stateless" <?php if(strpos($partnerId, "sky-") !== false && $state == 'Stateless') echo 'checked="checked"'; else if (strpos($partnerId, "sky-") === false) echo 'checked="checked"'; ?> id="Stateless" disabled="disabled" />
				<label for="Stateless" class="acs-hide"></label> <b><?php echo _('Stateless(Auto-Config)')?></b>
				<input type="checkbox"  name="State" value="Stateful" <?php if($state == 'Stateful') echo 'checked="checked"'; ?> id="Stateful" />
				<label for="Stateful" class="acs-hide"></label> <b><?php echo _('Stateful(Use Dhcp Server)')?></b>
			</div>
    	   <div class="form-row odd">
			    <?php  
			      //2040::/64, 2040:1::/64, 2040:1:2::/64 and 2040:1:2:3::/64
                  $prefix_arr = explode('::/', getStr("Device.IP.Interface.1.IPv6Prefix.1.Prefix"));
                  $ipv6_prefix_arr = explode(':', $prefix_arr[0]);
                  $ipa_size = count($ipv6_prefix_arr);
			      $v6_begin_addr = $dhcpv6_value["v6_begin_addr"];
			      $v6_beg_add_arr = explode(':', $v6_begin_addr);
			    ?>	
				<label for="DBA_1"><?php echo _('DHCPv6 Beginning Address:')?></label>
				<input type="text"  class="ipv6-input" size="2" maxlength="4"  id="DBA_1" name="DBA_1" disabled="disabled" value="<?php echo $ipv6_prefix_arr[0]; ?>" />
	    	    <label for="DBA_2" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DBA_2" name="DBA_2" disabled="disabled" value="<?php if($ipa_size > 1) echo $ipv6_prefix_arr[1]; else echo "0"; ?>" />
	    	    <label for="DBA_3" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DBA_3" name="DBA_3" disabled="disabled" value="<?php if($ipa_size > 2) echo $ipv6_prefix_arr[2]; else echo "0"; ?>" />
	    	    <label for="DBA_4" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DBA_4" name="DBA_4" disabled="disabled" value="<?php if($ipa_size > 3) echo $ipv6_prefix_arr[3]; else echo "0"; ?>" />
	    	    <label for="DBA_5" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DBA_5" name="DBA_5" disabled="disabled" value="<?php echo $v6_beg_add_arr[0]; ?>" />
	    	    <label for="DBA_6" class="acs-hide"></label>
        	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DBA_6" name="DBA_6" disabled="disabled" value="<?php echo $v6_beg_add_arr[1]; ?>" />
	    	    <label for="DBA_7" class="acs-hide"></label>
					:<input type="text" class="ipv6-input" size="2" maxlength="4" id="DBA_7" name="DBA_7" disabled="disabled" value="<?php echo $v6_beg_add_arr[2]; ?>" />
	    	    <label for="DBA_8" class="acs-hide"></label>
					:<input type="text" class="ipv6-input" size="2" maxlength="4" id="DBA_8" name="DBA_8" disabled="disabled" value="<?php echo $v6_beg_add_arr[3]; ?>" />
	    	    <label for="DBA_9" class="acs-hide"></label>
					/<input type="text" class="ipv6-input" size="2" maxlength="4" id="DBA_9" name="DBA_9" disabled="disabled" value="64"/>
				<br/>
				</div> 		
    	   <div class="form-row ">
				<?php  
			      $v6_end_addr = $dhcpv6_value["v6_end_addr"];
			      $v6_end_add_arr = explode(':', $v6_end_addr);
			    ?>	
				<label for="DEA_1"><?php echo _('DHCPv6 Ending Address:')?></label>
				<input type="text"  class="ipv6-input" size="2" maxlength="4"  id="DEA_1" name="DEA_1" disabled="disabled" value="<?php echo $ipv6_prefix_arr[0]; ?>" />
	    	    <label for="DEA_2" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DEA_2" name="DEA_2" disabled="disabled" value="<?php if($ipa_size > 1) echo $ipv6_prefix_arr[1]; else echo "0"; ?>" />
	    	    <label for="DEA_3" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DEA_3" name="DEA_3" disabled="disabled" value="<?php if($ipa_size > 2) echo $ipv6_prefix_arr[2]; else echo "0"; ?>" />
	    	    <label for="DEA_4" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DEA_4" name="DEA_4" disabled="disabled" value="<?php if($ipa_size > 3) echo $ipv6_prefix_arr[3]; else echo "0"; ?>" />
	    	    <label for="DEA_5" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DEA_5" name="DEA_5" disabled="disabled" value="<?php echo $v6_end_add_arr[0]; ?>" />
	    	    <label for="DEA_6" class="acs-hide"></label>
	    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="DEA_6" name="DEA_6" disabled="disabled" value="<?php echo $v6_end_add_arr[1]; ?>" />
	    	    <label for="DEA_7" class="acs-hide"></label>
					:<input type="text" class="ipv6-input" size="2" maxlength="4" id="DEA_7" name="DEA_7" disabled="disabled" value="<?php echo $v6_end_add_arr[2]; ?>" />
	    	    <label for="DEA_8" class="acs-hide"></label>
					:<input type="text" class="ipv6-input" size="2" maxlength="4" id="DEA_8" name="DEA_8" disabled="disabled" value="<?php echo $v6_end_add_arr[3]; ?>" />
	    	    <label for="DEA_9" class="acs-hide"></label>
					/<input type="text" class="ipv6-input" size="2" maxlength="4" id="DEA_9" name="DEA_9" disabled="disabled" value="64"/>
				<br/>
				</div> 		
    		<div class="form-row odd" id="ipv6_dhcp_lease_time">
    			<label for="ipv6_dhcp_lease_time_amount"><?php echo _('DHCPv6 Lease Time:')?></label>
    			<input type="text" size="3" maxlength="3" id="ipv6_dhcp_lease_time_amount" name="ipv6_dhcp_lease_time_amount" class="smallInput" />
                <label for="ipv6_dhcp_lease_time_measure" class="acs-hide"></label>
                <select id="ipv6_dhcp_lease_time_measure" name="ipv6_dhcp_lease_time_measure">
    	            <option value="seconds"><?php echo _('Seconds')?></option>
    	            <option value="minutes"><?php echo _('Minutes')?></option>
    	            <option value="hours"><?php echo _('Hours')?></option>
    	            <option value="days"><?php echo _('Days')?></option>
    	            <option selected value="weeks"><?php echo _('Weeks')?></option>
    	            <option value="forever"><?php echo _('Forever')?></option>
    	        </select>
		</div>
    		<!-- IPv6 Enable / Disable Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIpv6Enable -->
    		<?php
    				if(strpos($partnerId, "sky-") !== false){
    					$ipv6_enb = getStr("Device.X_RDKCENTRAL-COM_DeviceControl.LanManagementEntry.LanIpv6Enable");
    		  			$ula_enb = getStr("Device.X_RDKCENTRAL-COM_DeviceControl.LanManagementEntry.LanIpv6UlaEnable");

    		?>
    		<div class="form-row" id='ipv6_enb_dis'>
    			<label for="ipv6_enable"><?php echo _('IPv6 Enable:')?></label>
    			<input type="checkbox" id='ipv6_enable' name="ipv6_enable" <?php if($ipv6_enb=='true') echo 'checked="checked"'; ?>  />
    		</div>
    		<!-- ULA Prefix -->
    		 <?php
			      //2040::/64, 2040:1::/64, 2040:1:2::/64 and 2040:1:2:3::/64
                  $ula_prefix_arr = explode('::/', getStr("Device.X_RDKCENTRAL-COM_DeviceControl.LanManagementEntry.LanIpv6UlaPrefix"));
                  $ula_v6_prefix_arr = explode(':', $ula_prefix_arr[0]);
                  $ula_size = count($ula_v6_prefix_arr);
                 // $ula_v6_fd_array = str_split($ula_v6_prefix_arr[0]);
                 // $ula_v6_fd = $ula_v6_fd_array[0].$ula_v6_fd_array[1];
                 // $ula_v6_d7 = $ula_v6_fd_array[2].$ula_v6_fd_array[3];
			      // $v6_begin_addr = $dhcpv6_value["v6_begin_addr"];
			      // $v6_beg_add_arr = explode(':', $v6_begin_addr);
			    ?>
	    	<div id='ula_dis'>
	    		<div class="form-row odd" id='ula_enb_dis'>
	    			<label for="ula_enable"><?php echo _('ULA Enable:')?></label>
	    			<input type="checkbox" id='ula_enable' name="ula_enable"<?php if($ula_enb=='true') echo 'checked="checked"'; ?> />
	    		</div>
	    		<div class="form-row ">
				<?php
				    $state = $dhcpv6_value["state"];
				?>
				       <!-- 	<input type="radio"  name="ULA" value="ula_auto" checked="checked" id="ula_auto" />
					<label for="ula_auto" class="acs-hide"></label> <b><?php echo _('ULA Prefix(Automatic)')?></b>
					<input type="radio"  name="ULA" value="ula_manual" id="ula_manual" />
					<label for="ula_manual" class="acs-hide"></label> <b><?php echo _('ULA Prefix(Manual)')?></b> -->
				</div>
	    		<div class="form-row odd">
	    			<label for="ULA"><?php echo _('ULA Prefix:')?></label>
	    				<input type="text"  class="ipv6-input" size="2" maxlength="4"  id="ULA_1" name="ULA_1"  value="<?php if($ula_size > 1) echo $ula_v6_prefix_arr[0]; else echo "0"; ?>" />
		    	    <label for="ULA_2" class="acs-hide"></label>
		    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="ULA_2" name="ULA_2"  value="<?php if($ula_size > 1) echo $ula_v6_prefix_arr[1]; else echo "0"; ?>" />
		    	    <label for="ULA_3" class="acs-hide"></label>
		    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="ULA_3" name="ULA_3"  value="<?php if($ula_size > 2) echo $ula_v6_prefix_arr[2]; else echo "0"; ?>" />
		    	    <label for="ULA_4" class="acs-hide"></label>
		    	        :<input type="text" class="ipv6-input" size="2" maxlength="4" id="ULA_4" name="ULA_4"  value="<?php if($ula_size > 3) echo $ula_v6_prefix_arr[3]; else echo "0"; ?>" />
		    	    <label for="ULA_9" class="acs-hide"></label>
						::&nbsp;/<input type="text" class="ipv6-input" size="2" maxlength="4" id="ULA_9" name="ULA_9" disabled="disabled" value="64"/>
	    		</div>
	    	</div>
    	<?php } ?>

  	    		<div class="form-btn">
					<input type="button" id="submit_ipv6" value="<?php echo _('Save Settings')?>" class="btn" />
					<input id="restore_ipv6" type="button" value="<?php echo _('Restore Default Settings')?>" class="btn alt" />
				</div>				
 	    </div> <!-- end .module -->
 	   	</form>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
