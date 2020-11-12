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
<!-- $Id: network_diagnostic_tools.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<style type="text/css">
.gateway_address{
	width: 31px;
}
label{
	margin-right: 10px !important;
}
#pageForm3 span, #pageForm3 label, #pageForm4 label{
	width: 100px;
}

.traceError{
	width: 350px;
}

.noError{
	width: 554px;
	height:250px;
}

#vpop_container{ 
  	top: 126px !important;
  }  
</style>
<script type="text/javascript">
$(document).ready(function() {
	gateway.page.init("Troubleshooting > Network Diagnostic Tools", "nav-diagnostic-tools");
	$.validator.addMethod("url_no_http", function(value, element) {
		//A valid URL per the URL spec.
		return this.optional(element)||/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/.test(value);
	}, "<?php echo _("Please enter a valid Hostname.")?>");
    $("#pageForm1").validate({
		debug: true,
		rules: {
			destination_address: {
				url_no_http: true
			},
			count1: {
				required: true,
				digits: true,
				range: [1, 4]
			}
		}
    });	
    $("#pageForm2").validate({
		debug: true,
		groups: {
			ipv4_address_x: "ipv4_address_1 ipv4_address_2 ipv4_address_3 ipv4_address_4"
		},
		rules: {
			ipv4_address_1: {
				required: true,
				digits: true,
				range: [0, 255]
			},
			ipv4_address_2: {
				required: true,
				digits: true,
				range: [0, 255]
			},
			ipv4_address_3: {
				required: true,
				digits: true,
				range: [0, 255]
			},
			ipv4_address_4: {
				required: true,
				digits: true,
				range: [0, 255]
			},			
			count2: {
				required: true,
				digits: true,
				range: [1, 4]
			}
		}
    });	
    $("#pageForm3").validate({
		debug: true,
		groups: {
			ipv6_address_x: "ipv6_address_1 ipv6_address_2 ipv6_address_3 ipv6_address_4 ipv6_address_5 ipv6_address_6 ipv6_address_7 ipv6_address_8"
		},
		rules: {
			count3: {
				required: true,
				digits: true,
				range: [1, 4]
			},
			ipv6_address_1:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_2:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_3:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_4:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_5:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_6:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_7:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_8:{
				required: true,
				hexadecimal: true
			}			
		}
    });	
    $("#pageForm4").validate({
		debug: true,
		groups: {
			ipv4_address_x: "ipv4_address_1 ipv4_address_2 ipv4_address_3 ipv4_address_4",
			ipv6_address_x: "ipv6_address_1 ipv6_address_2 ipv6_address_3 ipv6_address_4 ipv6_address_5 ipv6_address_6 ipv6_address_7 ipv6_address_8"
		},
		rules: {
			ipv4_address_1: {
				required: true,
				digits: true,
				range: [0, 255]
			},
			ipv4_address_2: {
				required: true,
				digits: true,
				range: [0, 255]
			},
			ipv4_address_3: {
				required: true,
				digits: true,
				range: [0, 255]
			},
			ipv4_address_4: {
				required: true,
				digits: true,
				range: [0, 255]
			},	
			ipv6_address_1:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_2:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_3:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_4:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_5:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_6:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_7:{
				required: true,
				hexadecimal: true
			},
			ipv6_address_8:{
				required: true,
				hexadecimal: true
			}
		}
    });	
	$("#test_connectivity").click(function(){
		if ($("#pageForm1").valid()) {
			var destination_address=$("#destination_address").val();
			var count1=$("#count1").val();
			jProgress('<?php echo _("This may take several seconds...")?>',120);
			ajaxrequest=$.ajax({
				type:"POST",
				url:"actionHandler/ajax_network_diagnostic_tools.php",
				data:{test_connectivity:"true",destination_address:destination_address,count1:count1},
				dataType:"json",
				success:function(results){
					jHide();
					$("#connectivity_internet").html(results.connectivity_internet);
					$("#packets_sent").html(count1);
					$("#packets_received").html(results.success_received);
				},
				error:function(){
					jHide();
					jAlert("<?php echo _("Sorry, please try again.")?>");
				}
			});	
		}
	});
	$("#check_ipv4").click(function(){
		if ($("#pageForm2").valid()) {
			var destination_ipv4=$("#ipv4_address_1").val()+"."+$("#ipv4_address_2").val()+"."+$("#ipv4_address_3").val()+"."+$("#ipv4_address_4").val();
			var count2=$("#count2").val();
			jProgress('<?php echo _("This may take several seconds...")?>',60);
			ajaxrequest=$.ajax({
				type:"POST",
				url:"actionHandler/ajax_network_diagnostic_tools.php",
				data:{check_ipv4:"true",destination_ipv4:destination_ipv4,count2:count2},
				dataType:"json",
				success:function(results){
					jHide();
					$("#connectivity_ipv4").html(results.connectivity_ipv4);
				},
				error:function(){
					jHide();
					jAlert("<?php echo _("Sorry, please try again.")?>");
				}
			});
		}
	});
	$("#check_ipv6").click(function(){
		if ($("#pageForm3").valid()) {
			var destination_ipv6=$("#ipv6_address_1").val()
			+":"+$("#ipv6_address_2").val()
			+":"+$("#ipv6_address_3").val()
			+":"+$("#ipv6_address_4").val()
			+":"+$("#ipv6_address_5").val()
			+":"+$("#ipv6_address_6").val()
			+":"+$("#ipv6_address_7").val()
			+":"+$("#ipv6_address_8").val();
			var count3=$("#count3").val();
			jProgress('<?php echo _("This may take several seconds...")?>',120);
			ajaxrequest=$.ajax({
				type:"POST",
				url:"actionHandler/ajax_network_diagnostic_tools.php",
				data:{check_ipv6:"true",destination_ipv6:destination_ipv6,count3:count3},
				dataType:"json",
				success:function(results){
					jHide();
					$("#connectivity_ipv6").html(results.connectivity_ipv6);
				},
				error:function(){
					jHide();
					jAlert("<?php echo _("Sorry, please try again.")?>");
				}
			});
		}
	});
	$("#trace_ipv4").click(function(){
		if ($("#pageForm4 input[id^='trace_ipv4_']").valid()) {
			var trace_ipv4_dst=$("#trace_ipv4_address_1").val()
			+"."+$("#trace_ipv4_address_2").val()
			+"."+$("#trace_ipv4_address_3").val()
			+"."+$("#trace_ipv4_address_4").val();
			jProgress('<?php echo _("This may take several seconds...")?>',120);
			// if another var name, jprogress can't auto abort, but have to abort manually.
			ajaxrequest = $.ajax({
				type:"POST",
				url:"actionHandler/ajax_network_diagnostic_tools.php",
				data:{trace_ipv4:"true", trace_ipv4_dst:trace_ipv4_dst},
				dataType:"json",
				success:function(results){
					var trace_ipv4_status = results.trace_ipv4_status;
					var trace_ipv4_result = results.trace_ipv4_result;
					$("#pop_trace").text("<?php echo _("Status:")?> "+trace_ipv4_status+" !\n");
					jHide();
					checkTraceError(trace_ipv4_status);
					if ("Complete" == trace_ipv4_status){
						var i = 0;
						var hInt = setInterval(function(){
							$("#pop_trace").append(trace_ipv4_result[i++]+'\n');
							if (i >= trace_ipv4_result.length){
								clearInterval(hInt);
							}
						}, 500);					
					}
				},
				error:function(error){
					if(error.statusText=="abort"){
						var trace_ipv4_status = "Error! Traceroute Failed";
						$("#pop_trace").text("<?php echo _("Status:")?> "+trace_ipv4_status+" !\n");
						jHide();
						checkTraceError(trace_ipv4_status);
					}else {
					jHide();
					jAlert("<?php echo _("Sorry, please try again.")?>");
				}
			});
		}
	});
	$("#trace_ipv6").click(function(){
		if ($("#pageForm4 input[id^='trace_ipv6_']").valid()) {
			var trace_ipv6_dst=$("#trace_ipv6_address_1").val()
			+":"+$("#trace_ipv6_address_2").val()
			+":"+$("#trace_ipv6_address_3").val()
			+":"+$("#trace_ipv6_address_4").val()
			+":"+$("#trace_ipv6_address_5").val()
			+":"+$("#trace_ipv6_address_6").val()
			+":"+$("#trace_ipv6_address_7").val()
			+":"+$("#trace_ipv6_address_8").val();
			jProgress('<?php echo _("This may take several seconds...")?>',120);
			// if another var name, jprogress can't auto abort, but have to abort manually.
			ajaxrequest = $.ajax({
				type:"POST",
				url:"actionHandler/ajax_network_diagnostic_tools.php",
				data:{trace_ipv6:"true", trace_ipv6_dst:trace_ipv6_dst},
				dataType:"json",
				success:function(results){
					var trace_ipv6_status = results.trace_ipv6_status;
					var trace_ipv6_result = results.trace_ipv6_result;
					$("#pop_trace").text("<?php echo _("Status:")?> "+trace_ipv6_status+" !\n");
					jHide();				
					checkTraceError(trace_ipv6_status);
					if ("Complete" == trace_ipv6_status){
						var i = 0;
						var hInt = setInterval(function(){
							$("#pop_trace").append(trace_ipv6_result[i++]+'\n');
							if (i >= trace_ipv6_result.length){
								clearInterval(hInt);
							}
						}, 500);					
					}
				},
				error:function(){
					jHide();
					jAlert("<?php echo _("Sorry, please try again.")?>");
				}
			});
		}
	});	
});
function showTracerouteDialog(width) {
	$.virtualDialog({
		title: "Traceroute Tool",
		content: $("#traceroute_dialog"),
		footer: '<input id="pop_button" type="button" value="<?php echo _("Close")?>" style="float: right;" />',
		width: width
	});
	$("#pop_button").off("click").on("click", function(){
		$.virtualDialog("hide");
	});
}

function checkTraceError(traceResults){
	var states_trace=["Error_CannotResolveHostName","Error_MaxHopCountExceeded","Error! Traceroute Failed","Error_Internal","Error_Other","Error"];
	//var errorPresent=false;
	if(states_trace.includes(traceResults)){
		showTracerouteDialog("400px");
          	if($("#pop_trace").hasClass("noError")){
		     $("#pop_trace").removeClass( "noError" );
		}
		$("#pop_trace").addClass("traceError");
		return;
	} 
	if($("#pop_trace").hasClass("traceError")){ 
          	$("#pop_trace").removeClass( "traceError" );
        }
	showTracerouteDialog("600px");
	$("#pop_trace").addClass("noError");

	return;
}

</script>
<?php
	$ConnectivityTestURL = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.NetworkDiagnosticTools.ConnectivityTestURL");
?>
<div id="content">
	<h1><?php echo _("Troubleshooting > Network Diagnostic Tools")?></h1>
	<div id="educational-tip">
		<p class="tip"><?php echo _("Troubleshoot your network connectivity.")?></p>
		<p class="hidden"><?php echo _("<strong>Test Connectivity Results:</strong> Checks your connectivity to the Internet.")?></p>
		<p class="hidden"><?php echo _("<strong>Check IPv4 and IPv6 Address Results:</strong> Identifies accessibility to specific IP addresses.")?></p>
		<p class="hidden"><?php echo _("<strong>Traceroute Results:</strong> Displays the route of packets across an Internet Protocol (IP) network.")?></p>
	</div>
	<form method="post" id="pageForm1">
	<div class="module forms">
		<h2><?php echo _("Test Connectivity Results")?></h2>
    	<div class="form-row">
			<span class="readonlyLabel"><?php echo _("Connectivity to the Internet:")?></span> <span class="value" id="connectivity_internet"><?php echo _("Not Tested")?></span>
		</div>
		<div class="form-row odd">
			<span class="readonlyLabel"><?php echo _("Packets Sent:")?></span> <span class="value" id="packets_sent"><?php echo _("Not Tested")?></span>
		</div>
		<div class="form-row">
			<span class="readonlyLabel"><?php echo _("Packets Received:")?></span> <span class="value" id="packets_received"><?php echo _("Not Tested")?></span>
		</div>
		<div class="form-row odd">
			<label for="destination_address"> <?php echo _("Destination Address:")?> </label>
			<input type="text" value="<?php echo $ConnectivityTestURL; ?>" id="destination_address" name="destination_address"  size="25" />
			<span for="count" ><b> <?php echo _("Count:")?> </b></span>
			<label for="count1"  class="acs-hide"></label>
			<input type="text" value=4 id="count1" name="count1" maxlength="1" size="1" />
		</div>
		<div class="form-row">
			<input type="button" class="btn" id="test_connectivity" value="<?php echo _("Test  Connectivity")?>"/>
		</div>
	</div> <!-- end .module -->
	</form>
	<form method="post" id="pageForm2">
	<div class="module forms">
		<h2><?php echo _("Check for IPv4 Address Results")?></h2>
		<div class="form-row">
			<label for="ipv4_address_1"><?php echo _("IPv4 Address")?>:</label>
			<input type="text" maxlength="3" id="ipv4_address_1" name="ipv4_address_1" class="gateway_address"  value="" />.
			<label for="ipv4_address_2"  class="acs-hide"></label>
			<input type="text" maxlength="3" id="ipv4_address_2" name="ipv4_address_2" class="gateway_address"  value="" />.
			<label for="ipv4_address_3"  class="acs-hide"></label>
	        <input type="text" maxlength="3" id="ipv4_address_3" name="ipv4_address_3" class="gateway_address"  value="" />.
			<label for="ipv4_address_4"  class="acs-hide"></label>
	        <input type="text" maxlength="3" id="ipv4_address_4" name="ipv4_address_4" class="gateway_address"  value="" />
	        <span for="count" ><b> <?php echo _("Count:")?> </b></span>
			<label for="count2"  class="acs-hide"></label>
			<input type="text" value=4 id="count2" name="count2" maxlength="1" size="1" />
		</div>
    	<div class="form-row odd">
			<span class="readonlyLabel"><?php echo _("Connectivity:")?></span> <span class="value" id="connectivity_ipv4"><?php echo _("Not Tested")?></span>
		</div>
		<div class="form-row">
			<input type="button" class="btn" id="check_ipv4" value="<?php echo _("Check for IP Addresses")?>"/>
		</div>
	</div> <!-- end .module -->
	</form>
	<form method="post" id="pageForm3">
<?php 
	// $interface = getStr("com.cisco.spvtg.ccsp.pam.Helper.FirstDownstreamIpInterface");
	// $ipv6InsArr = explode(",", getInstanceIds($interface . 'IPv6Address.'));
	// $ipv6Brlan0 = getStr($interface . "IPv6Address.".$ipv6InsArr['0'].".IPAddress");
	// fe80::223:beff:fe75:9db6/64 
?>
	<div class="module forms">
			<h2><?php echo _("Check for IPv6 Address Results")?></h2>
			<div class="form-row">
				<label for="ipv6_address_1"><?php echo _("IPv6 Address:")?></label>
	            <input type="text" class="gateway_address" name="ipv6_address_1" id="ipv6_address_1" maxlength="4" value="">:
				<label for="ipv6_address_2"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_2" id="ipv6_address_2" maxlength="4" value="">:
				<label for="ipv6_address_3"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_3" id="ipv6_address_3" maxlength="4" value="">:
				<label for="ipv6_address_4"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_4" id="ipv6_address_4" maxlength="4" value="">:
				<label for="ipv6_address_5"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_5" id="ipv6_address_5" maxlength="4" value="">:
				<label for="ipv6_address_6"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_6" id="ipv6_address_6" maxlength="4" value="">:
				<label for="ipv6_address_7"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_7" id="ipv6_address_7" maxlength="4" value="">:
				<label for="ipv6_address_8"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_8" id="ipv6_address_8" maxlength="4" value="">
		        <span for="count1"><b> <?php echo _("Count:")?> </b></span>
				<label for="count3"  class="acs-hide"></label>
				<input type="text" value=4 id="count3" name="count3" maxlength="1" size="1" />
	   		</div>
	    	<div class="form-row odd">
				<span  class="readonlyLabel"><?php echo _("Connectivity:")?></span>
				<span class="value" id="connectivity_ipv6"<?php echo _(">Not Tested")?></span>
		    </div>
			<div class="form-row">
				<input type="button" class="btn" id="check_ipv6" value="<?php echo _("Check for IP Addresses")?>"/>
			</div>
	</div>	
	</form>
	<form method="post" id="pageForm4">
	<div class="module forms">
			<h2><?php echo _("Traceroute Results")?></h2>	
			<div class="form-row" id="ipv4">
				<label for="trace_ipv4_address_1"><?php echo _("IPv4 Address")?>:</label>
					<input type="text" maxlength="3" id="trace_ipv4_address_1" name="ipv4_address_1" class="gateway_address" value="" />.
				<label for="trace_ipv4_address_2"  class="acs-hide"></label>
					<input type="text" maxlength="3" id="trace_ipv4_address_2" name="ipv4_address_2" class="gateway_address" value="" />.
				<label for="trace_ipv4_address_3"  class="acs-hide"></label>
					<input type="text" maxlength="3" id="trace_ipv4_address_3" name="ipv4_address_3" class="gateway_address" value="" />.
				<label for="trace_ipv4_address_4"  class="acs-hide"></label>
					<input type="text" maxlength="3" id="trace_ipv4_address_4" name="ipv4_address_4" class="gateway_address" value="" />
				<input id="trace_ipv4" name="trace_ipv4" type="button" value="<?php echo _("Start Traceroute")?>" class="btn" style="float: right;" />
			</div>	
			<div class="form-row odd">
				<label for="trace_ipv6_address_1"><?php echo _("IPv6 Address:")?></label>
	            <input type="text" class="gateway_address" name="ipv6_address_1" id="trace_ipv6_address_1" maxlength="4" value="">:
				<label for="trace_ipv6_address_2"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_2" id="trace_ipv6_address_2" maxlength="4" value="">:
				<label for="trace_ipv6_address_3"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_3" id="trace_ipv6_address_3" maxlength="4" value="">:
				<label for="trace_ipv6_address_4"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_4" id="trace_ipv6_address_4" maxlength="4" value="">:
				<label for="trace_ipv6_address_5"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_5" id="trace_ipv6_address_5" maxlength="4" value="">:
				<label for="trace_ipv6_address_6"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_6" id="trace_ipv6_address_6" maxlength="4" value="">:
				<label for="trace_ipv6_address_7"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_7" id="trace_ipv6_address_7" maxlength="4" value="">:
				<label for="trace_ipv6_address_8"  class="acs-hide"></label>
				<input type="text" class="gateway_address" name="ipv6_address_8" id="trace_ipv6_address_8" maxlength="4" value="">
				<input id="trace_ipv6" name="trace_ipv6" type="button" value="<?php echo _("Start Traceroute")?>" class="btn" style="float: right;" />
	   		</div>			
	</div> <!-- end .module -->	
	</form>
</div><!-- end #content -->
<div id="traceroute_dialog" class="content_message" style="display: none;">
	<p><?php echo _("Traceroute Results:")?></p>
	<label for="pop_trace" class="acs-hide"></label>
	<textarea id="pop_trace" name="pop_trace" readonly="readonly" style="resize: none;"><?php echo _("Loading...")?>
	</textarea>
</div>
<?php include('includes/footer.php'); ?>
