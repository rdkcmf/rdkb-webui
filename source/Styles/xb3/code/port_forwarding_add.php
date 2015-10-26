<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: port_forwarding_add.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php 
//add by yaosheng
$devices_param = array(
    "LanGwIP"   	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress",
	"LanSubnetMask"	=> "Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask",
	"DeviceMode"	=> "Device.X_CISCO_COM_DeviceControl.DeviceMode",
	);
    $devices_value = KeyExtGet("Device.X_CISCO_COM_DeviceControl.", $devices_param);


$v6_param = array(
    "state"   	=> "Device.DHCPv6.Server.X_CISCO_COM_Type",
	"v6_begin_addr"	=> "Device.DHCPv6.Server.Pool.1.PrefixRangeBegin",
	"v6_end_addr"	=> "Device.DHCPv6.Server.Pool.1.PrefixRangeEnd",
	);
    $v6_value = KeyExtGet("Device.DHCPv6.Server.", $v6_param);

$LanGwIP 	= $devices_value["LanGwIP"];
$LanSubnetMask 	= $devices_value["LanSubnetMask"];
$beginAddr 	= getStr("Device.DHCPv4.Server.Pool.1.MinAddress");
$endAddr 	= getStr("Device.DHCPv4.Server.Pool.1.MaxAddress");

$DeviceMode = $devices_value["DeviceMode"];
//$DeviceMode = "IPv6";
$state = $v6_value["state"];
//2040::/64, 2040:1::/64, 2040:1:2::/64 and 2040:1:2:3::/64
$prefix_arr = explode('::/', getStr("Device.IP.Interface.1.IPv6Prefix.1.Prefix"));
//$prefix_arr = explode('::/', "2040:1:2:3::/64");
$v6_begin_addr = $v6_value["v6_begin_addr"];
$v6_end_addr = $v6_value["v6_end_addr"];
?>

<style type="text/css">

label{
	margin-right: 10px !important;
}

.form-row input.ipv6-input {
	width: 35px;
}

</style>

<script type="text/javascript" src="cmn/js/lib/jquery.alerts.progress.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > Port Forwarding > Add Service", "nav-port-forwarding");
    $('#service_name').focus();

    var jsNetMask	= "<?php echo $LanSubnetMask; ?>";
    var beginAddr	= "<?php echo $beginAddr; ?>";
    var endAddr		= "<?php echo $endAddr; ?>";
    var beginArr	= beginAddr.split(".");
    var endArr		= endAddr.split(".");

    var jsGwIP = "<?php echo $LanGwIP; ?>".split(".");
    var jsGatewayIP = "<?php echo $LanGwIP; ?>";
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
function isIp4AddrRequired()
{
	return !IsBlank('server_ip_address_');
}

	jQuery.validator.addMethod("blank",function(value,element){
		return this.optional(element) || (value!='Choose or input a service name');
	}, "Please enter a service name.");
	jQuery.validator.addMethod("ip",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 0 && value <= 255);
	}, "Please enter a valid IP address.");
	jQuery.validator.addMethod("ip4",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 1 && value <= 254);
	}, "Please enter a valid IP address.");
	jQuery.validator.addMethod("ip4_end",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 1 && value <= 253);
	}, "Please enter a valid IP address.");
	jQuery.validator.addMethod("port",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 0 && value <= 65535);
	}, "Please enter a port number less than 65536.");
	jQuery.validator.addMethod("ltstart",function(value,element){
		return this.optional(element) || value>=parseInt($("#start_port").val());
	}, "Please enter a value more than or equal to Start Port.");

	var validator = $("#pageForm").validate({
    	onfocusout: false,
		onkeyup: false,
		groups:{
			server_ipv4: "server_ip_address_1 server_ip_address_2 server_ip_address_3 server_ip_address_4",
			server_ipv6: "ip6_address_r1 ip6_address_r2 ip6_address_r3 ip6_address_r4 ip6_address_r5 ip6_address_r6 ip6_address_r7 ip6_address_r8"
		},
        rules: {        	
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
		var isValid = true;

        if($("#common_services").find("option:selected").val() == "other") {
        	var name = $('#service_name').val().replace(/^\s+|\s+$/g, '');
        	if (name.length == 0){
        		jAlert("Please input a service name !");
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
	   	  	jAlert("Please input valid server address !");
	   	  	return;
		}
		
		if (!IsBlank("server_ip_address_")) {
			//to check if "Server IPv4 Address" is in "DHCP Pool range"
			var IPv4_not_valid = false;
			for(i=0;i<4;i++){
				j = i+1;
				if(parseInt(beginArr[i]) > parseInt($("#server_ip_address_"+j).val()) || parseInt($("#server_ip_address_"+j).val()) > parseInt(endArr[i])){
					IPv4_not_valid = true;
					break;
				}
			}

			//IPv4 validation
			if (ip == jsGatewayIP){
				jAlert("Server IP can't be equal to the Gateway IP address !");
				return;
			} else if(IPv4_not_valid){
				jAlert("Server IP addr is not in valid range:\n"+beginAddr+" ~ "+endAddr);
				return;
			}
		}

		if (!IsBlank("ip6_address_r")) {
		//IPv6 validation
		//Check if IPv6 Mode - Stateless(Auto-Config), Stateful(Use Dhcp Server)
			if("<?php echo $state; ?>" == "Stateful"){
				//if Stateful(Use Dhcp Server) then accept inrange values
				var start = "<?php echo $v6_begin_addr; ?>";
				var start1 = start.split(":");
				var end = "<?php echo $v6_end_addr; ?>";
				var end1 = end.split(":");
				var ipv6res = ipv6addr.split(":");
				var ipv6res1 = ipv6res.splice(4, 4);

				for (i = 0; i < ipv6res1.length; i++) {
					var val = parseInt(ipv6res1[i].toUpperCase(), 16);
					var low = parseInt(start1[i].toUpperCase(), 16);
					var upp = parseInt(end1[i].toUpperCase(), 16);
					if(!((val >= low) && (val <= upp))){
						jAlert("Server IPv6 addr is not in valid range:\n <?php echo $prefix_arr[0].':'.$v6_begin_addr.' ~ '.$prefix_arr[0].':'.$v6_end_addr; ?>");
					  	return;
					}
				}
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

		if($("#pageForm").valid()) {
			jProgress('This may take several seconds.',60);
			$.ajax({
				type:"POST",
				url:"actionHandler/ajax_port_forwarding.php",
				data:{add:"true",name:name,type:type,ip:ip,ipv6addr:ipv6addr,startport:startport,endport:endport},
				dataType: "json",
				success:function(results){
					jHide();
					if (results=="Success!") { 
						window.location.href="port_forwarding.php";
					}
					else if (results=="") {jAlert('Failure! Please check your inputs.');}
					else jAlert(results);
				},
				error:function(){
					jHide();
					jAlert("Someting wrong, please try later!");
				}
			});
		} //end of pageform valid
	}); //end of save btn click

//=================================================
 // Monitor Common Services because it informs value and visibility of other field
    $("#common_services").change(function() {
        var $common_select = $(this);
        var $other = $("#service_name");

        if($common_select.find("option:selected").val() == "other") {
            $other.prop("disabled", false).removeClass("disabled").closest(".form-row").show();
            $("#start_port, #end_port").val("").prop("disabled", false); // Reset ports for user entered numbers
        } else {
            $other.prop("disabled", true).removeClass("disabled").val("").closest(".form-row").hide();
			// value in select must be start port + | + end port
			var ports = $common_select.find("option:selected").val();
			var start_port = ports.split("|")[0];
			var end_port = ports.split("|")[1];

			$("#start_port").val(start_port).prop("disabled", true);
			$("#end_port").val(end_port).prop("disabled", true);
        }
    }).trigger("change");

$('#device').click(function(){
	$.virtualDialog({
		title: "Select from below Connected Devices:",
		content: $("#device_list"),
		footer: '<div id="pop-btn-group">' +
					'<div style="float:left; position:relative; left:140px">' +
					'<input id="add_btn" type="button" value="Add"/>' +
					'</div>' +
					'<div style="position:relative; left:200px">' +
					'<input id="close_btn" type="button" value="Close" />' +
					'</div>' +
				'</div>'
	});
	$('#add_btn').click(function() {
		var ipv4_addr = $('input[type="radio"]:checked').parent().parent().find("td:eq(1)").text().replace(/^\s+|\s+$/g, '');
		var ipv6_addr = $('input[type="radio"]:checked').parent().parent().find("td:eq(2)").text().replace(/^\s+|\s+$/g, '');
		var ipv4_arr = ipv4_addr.split(".");
		var ipv6_arr = populateIPv6Addr(ipv6_addr);

		$("#server_ip_address_1").val(ipv4_arr[0]);
		$("#server_ip_address_2").val(ipv4_arr[1]);
		$("#server_ip_address_3").val(ipv4_arr[2]);
		$("#server_ip_address_4").val(ipv4_arr[3]);

		$("#ip6_address_r1").val(ipv6_arr[0]);
		$("#ip6_address_r2").val(ipv6_arr[1]);
		$("#ip6_address_r3").val(ipv6_arr[2]);
		$("#ip6_address_r4").val(ipv6_arr[3]);
		$("#ip6_address_r5").val(ipv6_arr[4]);
		$("#ip6_address_r6").val(ipv6_arr[5]);
		$("#ip6_address_r7").val(ipv6_arr[6]);
		$("#ip6_address_r8").val(ipv6_arr[7]);

		$.virtualDialog("hide");
	});
	$("#close_btn").click(function(){
		$.virtualDialog("hide");
	});
	$('#add-0').focus();
});

	if(DeviceMode == "Ipv4"){
		$("#ip6_address_r5, #ip6_address_r6, #ip6_address_r7, #ip6_address_r8").prop("disabled", true);
    	} else {
		$("#ip6_address_r5, #ip6_address_r6, #ip6_address_r7, #ip6_address_r8").prop("disabled", false);
	}

});//end of document ready
</script>

<div id="content">
	<h1>Advanced > Port Forwarding > Add Service</h1>
    <div id="educational-tip">
        <p class="tip"> Add a rule for port forwarding services by user.</p>
        <p class="hidden">Port forwarding permits communications from external hosts by forwarding them to a particular port.</p>
		<p class="hidden">Port forwarding settings can affect the Gateway's performance.</p>
    </div>
	<form method="post" id="pageForm" action="">
	<div class="module forms">
		<h2>Add Port Forward</h2>

		<div  class="form-row odd">
					<label for="common_services">Common Service:</label>
					<select  id="common_services" name="common_services">
					<option  value="21|21" >FTP</option>
					<option  value="5190|5190">AIM</option>
					<option  value="80|80" >HTTP</option>
					<option  value="1723|1723">PPTP</option>
					<option  value="443|443">HTTPs</option>
					<option  value="23|23">Telnet</option>
					<option  value="22|22">SSH</option>
					<option  value="other" class="other" selected="selected">Other</option>
					</select>
				</div>
				
				<div class="form-row ">
			<label  for="service_name">Service Name:</label> 
			<input type="text"  class="text" value="" id="service_name" name="service_name" />
		</div>

		<div  class="form-row odd">
			<label for="service_type">Service Type:</label>
			<select id="service_type">
				<option value="tcp_udp" selected="selected">TCP/UDP</option>
				<option value="tcp">TCP</option>
				<option value="udp">UDP</option>
			</select>
		</div>

		<div class="form-row ">
			<label for="server_ip_address_1">Server IPv4 Address:</label>
	        <input type="text" size="2"  maxlength="3"  id="server_ip_address_1" name="server_ip_address_1" class="ipv4-addr smallInput" />
	        <label for="server_ip_address_2" class="acs-hide"></label>
			.<input type="text" size="2" maxlength="3"  id="server_ip_address_2" name="server_ip_address_2" class="ipv4-addr smallInput" />
	        <label for="server_ip_address_3" class="acs-hide"></label>
			.<input type="text" size="2" maxlength="3"  id="server_ip_address_3" name="server_ip_address_3" class="ipv4-addr smallInput" />
	        <label for="server_ip_address_4" class="acs-hide"></label>
			.<input type="text" size="2" maxlength="3"  id="server_ip_address_4" name="server_ip_address_4" class="ipv4-addr smallInput" />
		</div>

		<?php  
	      		//2040::/64, 2040:1::/64, 2040:1:2::/64 and 2040:1:2:3::/64
                  	$prefix_arr = explode('::/', getStr("Device.IP.Interface.1.IPv6Prefix.1.Prefix"));
			//$prefix_arr = explode('::/', "2040:1:2:3::/64");
			$ipv6_prefix_arr = explode(':', $prefix_arr[0]);
                  	$ipa_size = count($ipv6_prefix_arr);
		?>

		<div class="form-row odd">		
			<label for="ip6_address_r1">Server IPv6 Address:</label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r1" name="ip_address_1" disabled="disabled" class="ipv6-addr ipv6-input" value="<?php if($DeviceMode!='Ipv4') {echo $ipv6_prefix_arr[0];} ?>"/>:
	        <label for="ip6_address_r2" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r2" name="ip_address_2" disabled="disabled" class="ipv6-addr ipv6-input" value="<?php if($DeviceMode!='Ipv4') {if($ipa_size > 1) echo $ipv6_prefix_arr[1]; else echo '0';} ?>"/>:
	        <label for="ip6_address_r3" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r3" name="ip_address_3" disabled="disabled" class="ipv6-addr ipv6-input" value="<?php if($DeviceMode!='Ipv4') {if($ipa_size > 2) echo $ipv6_prefix_arr[2]; else echo '0';} ?>"/>:
	        <label for="ip6_address_r4" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r4" name="ip_address_4" disabled="disabled" class="ipv6-addr ipv6-input" value="<?php if($DeviceMode!='Ipv4') {if($ipa_size > 3) echo $ipv6_prefix_arr[3]; else echo '0';} ?>"/>:
	        <label for="ip6_address_r5" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r5" name="ip_address_5" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r6" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r6" name="ip_address_6" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r7" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r7" name="ip_address_7" class="ipv6-addr ipv6-input"/>:
	        <label for="ip6_address_r8" class="acs-hide"></label>
			<input type="text" size="1" maxlength="4" id="ip6_address_r8" name="ip_address_8" class="ipv6-addr ipv6-input"/>
    	</div>

		<div class="form-row ">
			<label for="start_port">Start Port:</label>  <input type="text" class="port" value="" id="start_port" name="start_port" />
		</div>
		<div class="form-row odd">
			<label for="end_port">End Port:</label>  <input type="text" class="port" value="" id="end_port" name="end_port" />
		</div>

		<div class="form-row">
			<strong><p>Select a device to add IPv4 and IPv6 address</p></strong>
			<input  id="device" type="button" value="Connected Device" class="btn"  style="position:relative;top:0px;right: 0px;"/>
		</div>

		<div class="form-btn">
			<input type="button" id="btn-save" value="save" class="btn submit"/>
			<input type="button" id="btn-cancel" value="Cancel" class="btn alt reset"/>
		</div>

	</div> <!-- end .module -->
	</form>
</div><!-- end #content -->

<div id="device_list" style="display: none;">
	<table summary="This table lists connected devices">
	<tr>
		<th id="device-nmae">Device Name</th>
		<th id="ipv4-addr">IPv4 Address</th>
		<th id="ipv6-addr">IPv6 Address</th>
		<th id="add-radio">Add</th>					
		<th colspan="2">&nbsp;</th>
	</tr>

	<?php 
		$hostsInstance = getInstanceIds("Device.Hosts.Host.");
		$hostsInstanceArr = explode(",", $hostsInstance);
		$hostNums = getStr("Device.Hosts.HostNumberOfEntries");
		if ($_DEBUG) {
			$hostNums = "2";
		}

		for ($i=0; $i < $hostNums; $i++) { 

			$HostName  = getStr("Device.Hosts.Host.$hostsInstanceArr[$i].HostName");
			$IPAddress = getStr("Device.Hosts.Host.$hostsInstanceArr[$i].IPAddress");
			$Active    = getStr("Device.Hosts.Host.$hostsInstanceArr[$i].Active");
			$IPv6Addr = "";
			$IPv6Num = getStr("Device.Hosts.Host.$hostsInstanceArr[$i].IPv6AddressNumberOfEntries");
			if (((int)$IPv6Num) > 0) {
				$ids = explode(",", getInstanceIds("Device.Hosts.Host.$hostsInstanceArr[$i].IPv6Address."));
				foreach ($ids as $j) {
					if ($j === '') break;
					$val = getStr("Device.Hosts.Host.$hostsInstanceArr[$i].IPv6Address.$j.IPAddress");
					if (stripos($val, "fe80:") === 0) continue;
					$IPv6Addr = $val;
					break;
				}
			}
			if ($_DEBUG) {
				$HostName = "Host ".($i+1);
				$IPAddress = "1.1.1.".($i+1);
				$IPv6Addr = "2001::89$i";
				$Active = "true";
			}
			
			if($Active == 'true'){
				echo '<tr>';
				echo '<td headers="devcie-name">'. $HostName . '</td>';
				echo '<td headers="ipv4-addr">'. $IPAddress . '</td>';
				echo '<td headers="ipv6-addr">'. $IPv6Addr. '</td>';
				echo '<td headers="add-radio"><input type="radio" value="add" id="add-' .$i. '" name="select-device"><label for="add-' .$i. '" class="acs-hide"></label></td>';
				echo '</tr>';
			} 
		}

	?>

	<tfoot>
		<tr class="acs-hide">
			<td headers="device-name">null</td>
			<td headers="ipv4-addr">null</td>
			<td headers="ipv6-addr">null</td>
			<td headers="add-radio">null</td>
		</tr>
	</tfoot>

   </table>
</div>

<?php include('includes/footer.php'); ?>
