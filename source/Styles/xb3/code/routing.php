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
<?php include('includes/utility.php'); ?>
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php
	$RIP_param = array(
		'ItfName'        => "Device.Routing.RIP.InterfaceSetting.1.Interface",
		'SendVersion'    => "Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_SendVersion",
		'ReceiveVersion' => "Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_ReceiveVersion",
		'SimplePassword' => "Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_SimplePassword",
		'AuthType'       => "Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_AuthenticationType",
		'MD5KeyValue'    => "Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_Md5KeyValue",
		'MD5KeyID'       => "Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_Md5KeyID",
		'NeighborIP'     => "Device.Routing.RIP.InterfaceSetting.1.X_CISCO_COM_Neighbor",
		'SendRA'         => "Device.Routing.RIP.InterfaceSetting.1.SendRA",
		'AcceptRA'       => "Device.Routing.RIP.InterfaceSetting.1.AcceptRA",
		'Interval'       => "Device.Routing.RIP.X_CISCO_COM_UpdateInterval",
		'Metric'         => "Device.Routing.RIP.X_CISCO_COM_DefaultMetric",
		);
	$RIPEntry = KeyExtGet("Device.Routing.RIP.", $RIP_param);
	/*$RIPEntry = array(
		'Enable'         => 'True',
		'ItfName'        => 'Ethernet',
		'SendVersion'    => 'RIP2',
		'ReceiveVersion' => 'RIP2',
		'Interval'       => '15',
		'Metric'         => '10',
		'AuthType'       => 'MD5',
		'MD5KeyValue'    => 'pwd',
		'SimplePassword' => 'comcast',
		'MD5KeyID'       => '1',
		'NeighborIP'     => '0.0.0.0',
		'SendRA'         => 'true',
		'AcceptRA'       => 'false',
		);*/
	$jsRIPEntry = json_encode($RIPEntry);
?>
<style type="text/css">
label{
	margin-right: 10px !important;
}
#authentication_key {
	width: 125px;
}
</style>
<script type="text/javascript">
var jsRIPEntry = <?php echo $jsRIPEntry; ?>;
$(document).ready(function() {
    gateway.page.init("Advanced > Routing", "nav-routing");
    var $interface_name  = $('#interface_name');
	var $send_version    = $('#send_version');
	var $receive_version = $('#receive_version');
	var $update_interval = $('#update_interval');
	var $default_metric  = $('#default_metric');
	var $authentication_type = $('#authentication_type');
	var $authentication_key = $('#authentication_key');
	var $authentication_id = $('#authentication_id');
	var $Neighbor1 = $('#Neighbor1');
	var $Neighbor2 = $('#Neighbor2');
	var $Neighbor3 = $('#Neighbor3');
	var $Neighbor4 = $('#Neighbor4');
	var NeighbourArr = jsRIPEntry.NeighborIP.split('.');
	function initPopulate(){
		$interface_name.val(jsRIPEntry.ItfName);
		if (jsRIPEntry.SendRA == 'false') {
			$send_version.val('NA');
		}
		else {
			$send_version.val(jsRIPEntry.SendVersion);
		}
		if (jsRIPEntry.AcceptRA == 'false') {
			$receive_version.val('NA');
		}
		else {
			$receive_version.val(jsRIPEntry.ReceiveVersion);
		}
		$update_interval.val(jsRIPEntry.Interval);
		$default_metric.val(jsRIPEntry.Metric);
		$authentication_type.val(jsRIPEntry.AuthType);
		if ($authentication_type.val() == 'MD5') {
			$authentication_key.val(jsRIPEntry.MD5KeyValue);
			$authentication_id.val(jsRIPEntry.MD5KeyID);
		}
		else if ($authentication_type.val() == 'SimplePassword') {
			$authentication_key.val(jsRIPEntry.SimplePassword);
		}
		$Neighbor1.val(NeighbourArr[0]);
		$Neighbor2.val(NeighbourArr[1]);
		$Neighbor3.val(NeighbourArr[2]);
		$Neighbor4.val(NeighbourArr[3]);
	}
    initPopulate();
    function initEventHandler(){
    	if ($authentication_type.val() == 'MD5') {
    		$authentication_key.prop("disabled", false);
    		$authentication_id.prop("disabled", false);
    	}
    	else if ($authentication_type.val() == 'NoAuth') {
    		$authentication_key.prop("disabled", true);
    		$authentication_id.prop("disabled", true);
    	}
    	else {
    		$authentication_key.prop("disabled", false);
    		$authentication_id.prop("disabled", true);
    	}
    }
    initEventHandler();
    $authentication_type.change(function(){
    	if ($authentication_type.val() == 'MD5') {
    		$authentication_key.prop("disabled", false);
    		$authentication_id.prop("disabled", false);
    		$authentication_key.val(jsRIPEntry.MD5KeyValue);
    		$authentication_id.val(jsRIPEntry.MD5KeyID);
    	}
    	else if ($authentication_type.val() == 'NoAuth') {
    		$authentication_key.prop("disabled", true);
    		$authentication_id.prop("disabled", true);
    		$authentication_key.val('');
    		$authentication_id.val('');
    	}
    	else {
    		$authentication_key.prop("disabled", false);
    		$authentication_id.prop("disabled", true);
    		$authentication_key.val(jsRIPEntry.SimplePassword);
    		$authentication_id.val('');
    	}
    })    
	$("#pageForm").validate({
		groups: {
	    	ip_set: "Neighbor1 Neighbor2 Neighbor3 Neighbor4"
		},
	    rules: {
	       update_interval: {
	           required: true,
	           digits:true,
	           min: 5,
	           max:2147483647
	       },	
	       Neighbor1:{
	       	   required: true,
	       	   digits: true,
	       	   min:0,
	       	   max:255
	       },      
	       Neighbor2:{
	       	   required: true,
	       	   digits: true,
	       	   min:0,
	       	   max:255
	       },
	       Neighbor3:{
	       	   required: true,
	       	   digits: true,
	       	   min:0,
	       	   max:255
	       },
	       Neighbor4:{
	       	   required: true,
	       	   digits: true,
	       	   min:0,
	       	   max:254
	       },
	       authentication_key:{	       	   
	       	   required: true,
	       	   maxlength: 32,
	       	   allowed_char: true
	       },
	       authentication_id:{
	       	   required: true,
	       	   digits: true,
	       	   min:0
	       }
	   }
	});
$('#save_btn').click(function() {
	var ifName      = $("#interface_name").val();
	var sendVer     = $("#send_version").val();
	var recVer      = $("#receive_version").val();
	var interval    = $("#update_interval").val();
	var metric      = $("#default_metric").val();
	var authType    = $("#authentication_type").val();
	var auth_key    = $("#authentication_key").val();
	var auth_id     = $("#authentication_id").val();
	var NeighborIP  = $("#Neighbor1").val() + "." + $("#Neighbor2").val() + "." + $("#Neighbor3").val() + "." + $("#Neighbor4").val();
	if($("#Neighbor1").val()=="0" && NeighborIP!="0.0.0.0") {
		alert("Neighbor IP is invalid, please input again.");
	} 
	else {
		var ripInfo;
		if(authType == "NoAuth") {
			ripInfo = '{"IfName":"'+ifName+'", "SendVer":"'+sendVer+'", "RecVer":"'+recVer+'", "Interval":"'+interval+'", "Metric":"'+metric+'", "AuthType":"'+authType+'", "NeighborIP":"'+NeighborIP+'"}';
		} else {	
			ripInfo = '{"IfName":"'+ifName+'", "SendVer":"'+sendVer+'", "RecVer":"'+recVer+'", "Interval":"'+interval+'", "Metric":"'+metric+'", "AuthType":"'+authType+'", "auth_key":"'+auth_key+'", "auth_id":"'+auth_id+'", "NeighborIP":"'+NeighborIP+'"}';
		}
		saveRIP(ripInfo);
	}
});
function saveRIP(information) {
	if($("#pageForm").valid()){
		jProgress('<?php echo _("This may take several seconds")?>', 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_RIP_configuration.php",
			data: { ripInfo: information },
			success: function(){            
				jHide();
				window.location.href = "routing.php";
			},
			error: function(){            
				jHide();
				jAlert("<?php echo _("Failure, please try again.")?>");
			}
		});
	} //end of pageForm valid
}
});
</script>
<div id="content">
	<h1><?php echo _("Advanced > Routing")?></h1>
	<div id="educational-tip">
		<p class="tip"><?php echo _("The RIP protocol is used to exchange the routing information between the gateway and headend.")?></p>
		<p class="hidden"><?php echo _("<strong>Interface Name:</strong> Select the interface that the rip information will send from.")?></p>
		<p class="hidden"><?php echo _("<strong>RIP Send Version:</strong> Select the rip Send Version.")?></p>
		<p class="hidden"> <?php echo _("<strong>RIP Receive Version:</strong> Select the rip Receive Version.")?></p>
		<p class="hidden"><?php echo _("<strong>Update Interval:</strong> Enter the time that the rip information will resend.")?></p>
		<p class="hidden"><?php echo _("<strong>Default Metric:</strong> Select the Default Metric.")?></p>
		<p class="hidden"><?php echo _("<strong>Authentication Type:</strong> Select the Authentication Type.")?></p>
		<p class="hidden"><?php echo _("<strong>Authentication Key & ID:</strong> Enter the Authentication Key & ID.")?></p>
		<p class="hidden"><?php echo _("<strong>Neighbour:</strong> Enter the IP address of the router that you wish to unicast to.")?></p>
	</div>
	<div class="module forms">
		<h2><?php echo _("RIP(Routing information Protocol)")?></h2>
	<form id="pageForm" action="routing.php" method="post">
	<!-- <div class="form-row">
		<span class="readonlyLabel label">Status:</span>
		<ul id="Routing-switch" class="radio-btns enable">
    	 	<a tabindex='0'>
                <li>
				<input id="Routing_enabled" name="Routing" type="radio" checked="checked" value="Enabled"/>
				<label for="Routing_enabled" >Enabled</label>
			</li>
			</a>	
    	 	<a tabindex='0'>
				<li class="radio-off">
				<input id="Routing_disabled" name="Routing" type="radio" value="Disabled"/>
				<label for="Routing_disabled">Disabled</label>
			</li>
			</a>	
          </ul>
	</div> -->
	<div id="Routing-items">
		<div class="form-row">
			<label for="interface_name" class="readonlyLabel"><?php echo _("Interface Name:")?></label>
			<select id="interface_name">
				<option value="Cable"><?php echo _("Cable")?></option>
				<option value="Ethernet"><?php echo _("Ethernet")?></option>
			</select>
		</div>
		<div class="form-row odd">
			<label for="send_version" class="readonlyLabel"><?php echo _("RIP Send Version:")?></label>
			<select id="send_version">
				<option value="NA"><?php echo _("Do Not Send")?></option>
				<option value="RIP1">RIP1</option>
			</select>
		</div>
		<div class="form-row">
			<label for="receive_version" class="readonlyLabel"><?php echo _("RIP Receive Version:")?></label>
			<select id="receive_version">
				<option value="NA"><?php echo _("Do Not Receive")?></option>
				<option value="RIP1">RIP1</option>
			</select>
		</div>
		<div class="form-row odd">
			<label for="update_interval"><?php echo _("Update Interval:")?></label> 
			<input type="text" class="text smallInput" maxlength="10" name="update_interval" id="update_interval" /> <?php echo _("sec")?>
		</div>
		<div class="form-row">
			<label for="default_metric" class="readonlyLabel"><?php echo _("Default Metric:")?></label>
			<select id="default_metric">
				<option value=1>1</option>
				<option value=2>2</option>
				<option value=3>3</option>
				<option value=4>4</option>
				<option value=5>5</option>
				<option value=6>6</option>
				<option value=7>7</option>
				<option value=8>8</option>
				<option value=9>9</option>
				<option value=10>10</option>
				<option value=11>11</option>
				<option value=12>12</option>
				<option value=13>13</option>
				<option value=14>14</option>
				<option value=15>15</option>
			</select>
		</div>
		<div class="form-row odd">
			<label for="authentication_type" class="readonlyLabel"><?php echo _("Authentication Type:")?></label>
			<select id="authentication_type">
				<option value="NoAuth"><?php echo _("No Authentication")?></option>
				<option value="SimplePassword"><?php echo _("Simple Password")?></option>
				<option value="MD5">MD5</option>
			</select>
		</div>
		<div class="form-row">
			<label for="authentication_key"><?php echo _("Authentication Key & ID:")?></label>
			<input type="Password" maxlength="32" id="authentication_key" name="authentication_key" class="authentication_key"  disabled="disabled"/><?php echo _("<strong> ID:</strong>")?>
			<label for="authentication_id" class="acs-hide"></label>			
			<input type="text" size="5" maxlength="3" id="authentication_id" name="authentication_id" class="authentication_id smallInput" disabled="disabled"/>
		</div>
		<div id="Neighbor" class="form-row odd">
			<label for="Neighbor"><?php echo _("Neighbor:")?></label>
			<input type="text" size="3" maxlength="3" id="Neighbor1"  name="Neighbor1" class="smallInput" />
			.<input type="text" size="3" maxlength="3" id="Neighbor2"  name="Neighbor2" class="smallInput" />
			.<input type="text" size="3" maxlength="3" id="Neighbor3"  name="Neighbor3" class="smallInput" />
			.<input type="text" size="3" maxlength="3"  id="Neighbor4"  name="Neighbor4" class="smallInput" />
		</div>
	</div> <!-- end .module -->
	<div class="form-btn">
		<input id="save_btn" type="button" value="<?php echo _("Save")?>" class="btn" /> 
	</div>
</form>
</div>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
