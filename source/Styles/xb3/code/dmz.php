<?php include('includes/header.php'); ?>

<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<style type="text/css">
	label{	margin-right: 10px !important;}
	.form-row input.ipv6-input {width: 35px;}
</style>

<?php
$enableDMZ		= getStr("Device.NAT.X_CISCO_COM_DMZ.Enable");
$host   		= getStr("Device.NAT.X_CISCO_COM_DMZ.InternalIP");
$hostv6 		= getStr("Device.NAT.X_CISCO_COM_DMZ.IPv6Host");

$LanSubnetMask	= getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask");
$LanGwIP 		= getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress");
$IPv6Prefix     = getStr("Device.IP.Interface.1.IPv6Prefix.1.Prefix");

("" == $enableDMZ) && ($enableDMZ = "false");

if ($_SESSION['_DEBUG']){
	$enableDMZ 		= "true";
	$host   		= "10.0.0.11";
	$hostv6 		= "0::1";	
	$host   		= "0.0.0.0";
	$hostv6 		= "x";
	$LanGwIP 		= "10.0.0.1";
	$LanSubnetMask 	= "255.255.255.0";
	$IPv6Prefix 	= "2042:cafe:0:b::/64";
}
$IPv6Prefix = substr($IPv6Prefix,0, strrpos($IPv6Prefix, "::"));

// these means disable, MUST show empty on GUI!!!
("0.0.0.0" == $host)	&& ($host = "");
("x" == $hostv6)	&& ($hostv6 = "");

?>

<script type="text/javascript">
$(document).ready(function() {
	comcast.page.init("Advanced > DMZ", "nav-dmz");
	
	var jsEnableDMZ = <?php echo $enableDMZ ?>;
	var jsHost = "<?php echo $host ?>".split(".");
	var jsHostv6 = "<?php echo $hostv6; ?>";

	var jsNetMask = "<?php echo $LanSubnetMask; ?>";
	//alert(typeof(jsNetMask));
	var jsGatewayIP = "<?php echo $LanGwIP; ?>";
	var jsGwIP = "<?php echo $LanGwIP; ?>".split(".");
	var local_v6_prefix = <?php echo(json_encode($IPv6Prefix)) ?>;
	//alert(typeof(jsGwIP[0]));

	jsGwIP[0] = parseInt(jsGwIP[0]);
	jsGwIP[1] = parseInt(jsGwIP[1]);
	jsGwIP[2] = parseInt(jsGwIP[2]);
	jsGwIP[3] = parseInt(jsGwIP[3]);

//dmz ipv6 host specific
function populateIPv6Addr(v6addr){
	//console.log(v6addr);
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

	//populate ipv6 address
	var ipv6_addr_arr = jsHostv6.indexOf(':') < 0 ? null : populateIPv6Addr(jsHostv6);

	function IsBlank(id_prefix){
		var ret = true;
		//for ip6_address_r dont check if prifix is blank
		if(id_prefix == "ip6_address_r"){
			$('[id^="'+id_prefix+'"]').each(function(){
				if(local_v6_prefix!=""){
					if ((local_v6_prefix.split(":").length < $(this).context.id.substr(13)) && $(this).val().replace(/\s/g, '') != ""){
						ret = false;
						return false;
					}
				}
			});
		}
		else {
			$('[id^="'+id_prefix+'"]').each(function(){
				if ($(this).val().replace(/\s/g, '') != ""){
					ret = false;
					return false;
				}
			});
		}
		
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
		return IsBlank('dmz_host_address_');
	}

	function isIp4AddrRequired()
	{
		return IsBlank('ip6_address_r');
	}

	$.validator.addMethod("hexadecimal", function(value, element) {
		return this.optional(element) || /^[a-fA-F0-9]+$/i.test(value);
	}, "Only hexadecimal characters are valid. Acceptable characters are ABCDEF0123456789.");
			
	var validator =	$("#pageForm").validate({
		debug: true,
		onfocusout: false,
		onkeyup: false,
		errorPlacement: function(error, element) {    error.appendTo( element.parent("div") );  },
		errorElement: "p",
		groups: {
			ip_address: "dmz_host_address_1 dmz_host_address_2 dmz_host_address_3 dmz_host_address_4",
			ip6_address: "ip_address_1 ip_address_2 ip_address_3 ip_address_4 ip_address_5 ip_address_6 ip_address_7 ip_address_8"
		}
	});
		
	$('[id^=dmz_host_address_]').each(function(index, elem){
		$(this).rules( "add", {
			required: isIp4AddrRequired,
			min: 0,
			max: 255-((3==index)?2:0),
			digits: true
		});
	});	

	$('[id^=ip6_address_r]').each(function(){
		$(this).rules( "add", {
			required: isIp6AddrRequired,
			hexadecimal: true 
		});
	});


	// $("#dmz_switch").prop("checked", jsEnableDMZ);
	$("#dmz_switch").radioswitch({
		id: "dmz-switch",
		radio_name: "dmz",
		id_on: "dmz_enabled",
		id_off: "dmz_disabled",
		title_on: "Enable DMZ",
		title_off: "Disable DMZ",
		state: jsEnableDMZ ? "on" : "off"
	});	

	$('[id^=dmz_host_address_]').each(function(index){
		$(this).val(jsHost[index]);
	});

	$('[id^=ip6_address_r]').each(function(index){
		if(jsEnableDMZ) $(this).val(ipv6_addr_arr ? ipv6_addr_arr[index] : '');
	});

	$("#dmz_switch").change(function() {
		var isEnabledDMZ = $("#dmz_switch").radioswitch("getState").on;
		$('[id^=dmz_host_address_]').prop("disabled", !isEnabledDMZ);
		$('[id^=ip6_address_r]').prop("disabled", !isEnabledDMZ);
		if(local_v6_prefix!=""){
			$('[id^=ip6_address_r]').each(function(index){
				if(local_v6_prefix.split(":").length >= $(this).context.id.substr(13)){
					$(this).prop("disabled", true);
				}
			});
		}
		else {
			$('[id^=ip6_address_r]').prop("disabled", true);
		}

		if(isEnabledDMZ) {
			populate_IPv6();
			$('[id^=dmz_host_address_]').each(function(index){
				$(this).val(jsHost[index]);
			});
			$("#pageForm").valid();
		}
		else {
			$('[id^=dmz_host_address_]').val("0").removeClass("error");
			$('[id^=ip6_address_r]').val("0").removeClass("error");
			var validator = $( "#pageForm" ).validate();
			validator.resetForm();
		}
	}).trigger("change");

$('#save_setting').click(function() {
	var isValid = true;

	var isEnabledDMZ = $("#dmz_switch").radioswitch("getState").on;
	var host = IsBlank("dmz_host_address_") ? "0.0.0.0" : GetAddress(".", "dmz_host_address_");

    var host0 = parseInt($("#dmz_host_address_1").val());
    var host1 = parseInt($("#dmz_host_address_2").val());
    var host2 = parseInt($("#dmz_host_address_3").val());
    var host3 = parseInt($("#dmz_host_address_4").val());

	var hostv6 = IsBlank("ip6_address_r") ? 'x' : GetAddress(":", "ip6_address_r");

	if (isEnabledDMZ) {
		// check the basic rules
		isValid = $("#pageForm").valid();
		
		// check some extra IPv4 rule. TODO: add IPv6 checking
		if(isValid && !IsBlank("dmz_host_address_")) {
			if (host == jsGatewayIP){
				jAlert("DMZ v4 Host IP can't be equal to the Gateway IP address !");
				isValid = false;
			}
			else if(jsNetMask.indexOf('255.255.255') >= 0){
				//the first three field should be equal to gw ip field
				if((jsGwIP[0] != host0) || (jsGwIP[1] != host1) || (jsGwIP[2] != host2) || host3<2 || host3>253){
					jAlert('DMZ v4 Host IP is not in valid range:\n' + jsGwIP[0] + '.' + jsGwIP[1] + '.' + jsGwIP[2] + '.[2~253]');
					isValid = false;
				}
			}
			else if(jsNetMask == "255.255.0.0"){
				if((jsGwIP[0] != host0) || (jsGwIP[1] != host1)){
					jAlert('DMZ v4 Host IP is not in valid range:\n' + jsGwIP[0] + '.' + jsGwIP[1] + '.[0~255]' + '.[2~253]');
					isValid = false;
				}
			}
			else{
				if(jsGwIP[0] != host0){
					jAlert("DMZ v4 Host IP is not in valid range:\n");
					isValid = false;
				}
			}
		}
		
		// check validation before check IPv6 prefix - Now prefix is fixed
		/*if(local_v6_prefix && isValid && !IsBlank("ip6_address_r")) {
			if (hostv6.indexOf(local_v6_prefix)!=0) {
				jAlert("DMZ v6 Host IP is not in valid range:\nShould start with prefix: "+local_v6_prefix);
				isValid = false;
			}
		}*/
	}

	
	// ready to save
    if (isValid) {
	    var dmzInfo = '{"IsEnabledDMZ":"'+isEnabledDMZ+'", "Host":"'+host+'", "hostv6":"'+hostv6+'"}';
    	saveQoS(dmzInfo);
    }
});



function saveQoS(information){
//alert(information);
	jProgress('This may take several seconds', 60);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_DMZ_configuration.php",
		data: { dmzInfo: information },
		success: function(){            
			jHide();
			window.location.reload(true);
		},
		error: function(){            
			jHide();
			jAlert("Failure, please try again.");
		}
	});
	
}

function populate_IPv6(){
	if(local_v6_prefix){
		var local_v6_addr_arr = local_v6_prefix.indexOf(':') < 0 ? null : populateIPv6Addr(local_v6_prefix);
		$('[id^=ip6_address_r]').each(function(index){
			if(local_v6_prefix.split(":").length >= $(this).context.id.substr(13)){
				$(this).val(local_v6_addr_arr ? local_v6_addr_arr[index] : '');
				$(this).prop("disabled", true);
			}
			else {
				if(ipv6_addr_arr != null && jsHostv6 != "0:0:0:0:0:0:0:0"){
					$(this).val(ipv6_addr_arr[index]);
				}
				else {
					$(this).val("");
				}
			}		
		});
	}
	else {
		$('[id^=ip6_address_r]').prop("disabled", true).val("");
	}
}

populate_IPv6();

	$("#pageForm").keyup(function() {
		$("#pageForm").valid();
	});

	if(!jsEnableDMZ){
		$('[id^=dmz_host_address_]').val("0");
		$('[id^=ip6_address_r]').val("0");
	}
});
</script>

<div id="content">
	<h1>Advanced > DMZ</h1>

	<div id="educational-tip">
		<p class="tip">Configure DMZ to allow a single computer on your LAN to open all of its ports.</p>
	</div>

	<form action="dmz.php" method="post" id="pageForm">

	<div class="module forms">
		<h2>DMZ</h2>
		<div class="form-row odd">

			<label for="dmz">DMZ:</label>
			<span id="dmz_switch"></span>
		</div>
		<div class="form-row">
                <label for="dmz_host_address_1">DMZ v4 Host:</label>
				<input type="text" size="3" maxlength="3" id="dmz_host_address_1"  value="" name="dmz_host_address_1" class="gateway_address smallInput" />.
				<label for="dmz_host_address_2" class="acs-hide"></label>
    	        <input type="text" size="3" maxlength="3" id="dmz_host_address_2"  value="" name="dmz_host_address_2" class="gateway_address smallInput" />.
				<label for="dmz_host_address_3" class="acs-hide"></label>
    	        <input type="text" size="3" maxlength="3" id="dmz_host_address_3"  value="" name="dmz_host_address_3" class="gateway_address smallInput" />.
				<label for="dmz_host_address_4" class="acs-hide"></label>
    	        <input type="text" size="3" maxlength="3" id="dmz_host_address_4"  value="" name="dmz_host_address_4" class="gateway_address smallInput"  />

    	        <!--
				<select id="dmz_host_address_1" name="dmz_host_address_1" disabled="disabled">
    	            <option value="10.0">10.0</option>
    	            <option value="192.168">192.168</option>
    	            <option value="172.16">172.16</option>
    	        </select>
    	        .<input type="text" size="3" maxlength="3" value="0" id="dmz_host_address_3" name="dmz_host_address_3" class="" />
    	        .<input type="text" size="3" maxlength="3" value="1" id="dmz_host_address_4" name="dmz_host_address_4" class="" />
				-->
    		 </div>

    	<div class="form-row odd">		
			<label for="dmz_host_address">DMZ v6 Host:</label>
			<input type="text" value ="" size="2" maxlength="4" id="ip6_address_r1" name="ip_address_1" class="ipv6-input"/>:
			<input type="text" value ="" size="2" maxlength="4" id="ip6_address_r2" name="ip_address_2" class="ipv6-input"/>:
			<input type="text" value ="" size="2" maxlength="4" id="ip6_address_r3" name="ip_address_3" class="ipv6-input"/>:
			<input type="text" value ="" size="2" maxlength="4" id="ip6_address_r4" name="ip_address_4" class="ipv6-input"/>:
			<input type="text" value ="" size="2" maxlength="4" id="ip6_address_r5" name="ip_address_5" class="ipv6-input"/>:
			<input type="text" value ="" size="2" maxlength="4" id="ip6_address_r6" name="ip_address_6" class="ipv6-input"/>:
			<input type="text" value ="" size="2" maxlength="4" id="ip6_address_r7" name="ip_address_7" class="ipv6-input"/>:
			<input type="text" value ="" size="2" maxlength="4" id="ip6_address_r8" name="ip_address_8" class="ipv6-input"/>
    	</div>	
    		 <div class="form-btn">
			<input id="save_setting" name="save_setting" type="button" value="Save" class="btn right" />
		</div>
	</div><!-- end .module -->

	</form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
