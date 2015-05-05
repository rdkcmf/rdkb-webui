<?php include('includes/header.php'); ?>

<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
//start by licha
$enableDMZ = getStr("Device.NAT.X_CISCO_COM_DMZ.Enable");
$host   = getStr("Device.NAT.X_CISCO_COM_DMZ.InternalIP");
$hostv6 = getStr("Device.NAT.X_CISCO_COM_DMZ.IPv6Host");
//end by licha

//add by yaosheng 
$LanSubnetMask = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask");
$LanGwIP = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress");
//$LanGwIP = "172.16.10.1";
//$LanSubnetMask = "255.255.0.0";

//add by shunjie
("" == $enableDMZ) && ($enableDMZ = "false");
("" == $host)      && ($host = "0.0.0.0");

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
    comcast.page.init("Advanced > DMZ", "nav-dmz");

//start by licha
var jsEnableDMZ = <?php echo $enableDMZ ?>;
var jsHost = "<?php echo $host ?>".split(".");
var jsHostv6 = "<?php echo $hostv6; ?>";

var jsNetMask = "<?php echo $LanSubnetMask; ?>";
//alert(typeof(jsNetMask));
var jsGatewayIP = "<?php echo $LanGwIP; ?>";
var jsGwIP = "<?php echo $LanGwIP; ?>".split(".");
//alert(typeof(jsGwIP[0]));

jsGwIP[0] = parseInt(jsGwIP[0]);
jsGwIP[1] = parseInt(jsGwIP[1]);
jsGwIP[2] = parseInt(jsGwIP[2]);
//jsGwIP[3] = parseInt(jsGwIP[3]);

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
		//console.log('arr1_num: ' + arr1_num);
		//console.log('arr2_num: ' + arr2_num);
		//console.log('zero_num: ' + zero_num);

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
	$('[id^="'+id_prefix+'"]').each(function(){
		if ($(this).val().replace(/\s/g, '') != ""){
			ret = false;
			return false;
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

var validator =	$("#pageForm").validate({
		debug: true,
		onfocusout: false,
		onkeyup: false,
		groups: {
			ip_address: "dmz_host_address_1 dmz_host_address_2 dmz_host_address_3 dmz_host_address_4",
			ip6_address: "ip_address_1 ip_address_2 ip_address_3 ip_address_4 ip_address_5 ip_address_6 ip_address_7 ip_address_8"
		},
        rules: {
        	dmz_host_address_1:
        	{
        		required: true,
        		min: 0,
        		max: 255,
        		digits: true
        	},
        	dmz_host_address_2:
        	{
        		required: true,
        		min: 0,
        		max: 255,
        		digits: true
        	},
        	dmz_host_address_3:
        	{
        		required: true,
        		min: 0,
        		max: 255,
        		digits: true
        	},
        	dmz_host_address_4:
        	{
        		required: true,
        		min: 0,
        		max: 254,
        		digits: true
        	}
        	,ip_address_1:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            } 
            ,ip_address_2:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip_address_3:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip_address_4:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip_address_5:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip_address_6:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }   
            ,ip_address_7:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }  
            ,ip_address_8:{
            	required: isIp6AddrRequired,
            	hexadecimal: true            	
            }
        }
    });

	$("#dmz_switch").radioswitch({
		id: "dmz-switch",
		radio_name: "dmz",
		id_on: "dmz_enabled",
		id_off: "dmz_disabled",
		title_on: "Enable DMZ",
		title_off: "Disable DMZ",
		state: jsEnableDMZ ? "on" : "off"
	});
	$("#dmz_switch").change(function() {
		enableHandle(); 
	});

function enableHandle() {
	var isUDMZDisabled = $("#dmz_switch").radioswitch("getState").on === false;

	if(isUDMZDisabled) {
		$("#dmz_host_address_1").val(0);
		$("#dmz_host_address_2").val(0);
		$("#dmz_host_address_3").val(0);
		$("#dmz_host_address_4").val(0);
		
		document.getElementById('dmz_host_address_1').disabled = true;
		document.getElementById('dmz_host_address_2').disabled = true;
		document.getElementById('dmz_host_address_3').disabled = true;
		document.getElementById('dmz_host_address_4').disabled = true;

		$('.ipv6-input').prop("disabled", true).val('');
	 }
	 else {
		$("#dmz_host_address_1").val(jsHost[0]);
		$("#dmz_host_address_2").val(jsHost[1]);
		$("#dmz_host_address_3").val(jsHost[2]);
		$("#dmz_host_address_4").val(jsHost[3]);
		
		document.getElementById('dmz_host_address_1').disabled = false;
		document.getElementById('dmz_host_address_2').disabled = false;
		document.getElementById('dmz_host_address_3').disabled = false;
	    document.getElementById('dmz_host_address_4').disabled = false;

		$('.ipv6-input').prop("disabled", false);
		$('[id^=ip6_address_r]').each(function(index){
			$(this).val(ipv6_addr_arr ? ipv6_addr_arr[index] : '');
		});
	 }
}

enableHandle(); 

$('#save_setting').click(function() {
	var isValid = true;
	var isEnabledDMZ = $("#dmz_switch").radioswitch("getState").on;
	var host = IsBlank("dmz_host_address_") ? "0.0.0.0" : ($("#dmz_host_address_1").val()+"."+$("#dmz_host_address_2").val()+
														   "."+$("#dmz_host_address_3").val()+"."+$("#dmz_host_address_4").val());

    var host0 = parseInt($("#dmz_host_address_1").val());
    var host1 = parseInt($("#dmz_host_address_2").val());
    var host2 = parseInt($("#dmz_host_address_3").val());
    var host3 = parseInt($("#dmz_host_address_4").val());

	var hostv6 = IsBlank("ip6_address_r") ? 'x' : GetAddress(":", "ip6_address_r");

    if( isEnabledDMZ ){
		if (host !== "0.0.0.0" || hostv6 === "x") {
			if (host == jsGatewayIP){
				jAlert("DMZ Host IP can't be equal to the Gateway IP address !");
				return;
			}
			//alert(jsNetMask);
			else if(jsNetMask.indexOf('255.255.255') >= 0){
				//the first three field should be equal to gw ip field
				if((jsGwIP[0] != host0) || (jsGwIP[1] != host1) || (jsGwIP[2] != host2) || host3<2 || host3>254){
					var msg = 'Host IP is not in valid range:\n' + jsGwIP[0]+'.'+jsGwIP[1]+'.'+jsGwIP[2]+'.[2~254]';
					jAlert(msg);
					//jAlert('DMZ Host IP is not in valid range:\n' + jsGwIP[0]+'.'+jsGwIP[1]+'.'+jsGwIP[2]+'.[2~254]');
					return;
				}
			}
			else if(jsNetMask == "255.255.0.0"){
				if((jsGwIP[0] != host0) || (jsGwIP[1] != host1)){
					jAlert('DMZ Host IP is not in valid range:\n' + jsGwIP[0]+ '.' + jsGwIP[1] + '.[0~255]' + '.[2~254]');
					return;
				}
			}
			else{
				if(jsGwIP[0] != host0){
					jAlert("DMZ Host IP is not in valid range:\n");
					return;
				}
			}
		}
		if (hostv6 !== "x" || host === "0.0.0.0") {
			/* TODO: validate v6 ip address */
			if (hostv6 === "x") {
				jAlert("Either \"DMZ Host\" or \"DMZ IPv6 Host\" is required!");
				return;
			}
		}
	}

	isValid = $("#pageForm").valid();

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

//end by licha
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
                <label for="dmz_host_address_1">DMZ Host:</label>
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
			<label for="dmz_host_address">DMZ IPv6 Host:</label>
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
			<input id="save_setting" type="button" value="Save" class="btn right" />
		</div>
	</div><!-- end .module -->

	</form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
