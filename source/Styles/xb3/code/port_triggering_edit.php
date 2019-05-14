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
<!-- $Id: port_triggering_add.php 3117 2009-10-15 20:23:13Z cporto $ -->
<div id="sub-header">
<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Port Triggering Add - Advanced", "nav-port-triggering");
    $('#service_name').focus();
	jQuery.validator.addMethod("port",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 0 && value <= 65535);
	}, "<?php echo _("Please enter a port number less than 65536.")?>");
	jQuery.validator.addMethod("triggermore",function(value,element){
		var fstartport=parseInt($("#from_start_port").val());
		return this.optional(element) || value>=fstartport;
	}, "<?php echo _("Please enter a value more than or equal to Trigger Starting Port.")?>");
	jQuery.validator.addMethod("targetmore",function(value,element){
		var tstartport=parseInt($("#to_start_port").val());
		return this.optional(element) || value>=tstartport;
	}, "<?php echo _("Please enter a value more than or equal to Target Starting Port.")?>");	
    $("#pageForm").validate({
        rules: {
            service_name: {
                required: true,
				allowed_char: true
            }
            ,from_start_port: {
                required: true
				,port: true
				,digits: true
				,min: 1
            }
            ,from_end_port: {
                required: true
				,port: true
				,digits: true
				,min: 1
				,triggermore: true
            }
            ,to_start_port: {
                required: true
				,port: true
				,digits: true
				,min: 1
            }
            ,to_end_port: {
                required: true
				,port: true
				,digits: true
				,min: 1
				,targetmore: true
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
    	window.location.href = "port_triggering.php";
    });
	$("#btn-save").click(function(){
		var name=$('#service_name').val();
		var type=$('#service_type').find("option:selected").text();
		var fsp=$('#from_start_port').val();
		var fep=$('#from_end_port').val();
		var tsp=$('#to_start_port').val();
		var tep=$('#to_end_port').val();
		if($("#pageForm").valid()){
		jProgress('<?php echo _("This may take several seconds.")?>',60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_port_triggering.php",
			data:{edit:"true",ID:ID,name:name,type:type,fsp:fsp,fep:fep,tsp:tsp,tep:tep},
			dataType: "json",
			success:function(results){
				jHide();
				if (results=="<?php echo _("Success!")?>") { window.location.href="port_triggering.php";}
				else if (results=="") {jAlert('<?php echo _("Failure! Please check your inputs.")?>');}
				else jAlert(results);
			},
			error:function(){
				jHide();
				jAlert("<?php echo _("Something went wrong, please try later!")?>");
			}
		});
		}
	});	
});
</script>
<?php
	if (!preg_match('/^\d{1,3}$/', $_GET['id'])) die();
	$i=$_GET['id'];
	echo "<script>var ID=".$i.";</script>";
    $port_trigger_param = array(
        "service_name"      => "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".Description",
        "type"              => "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerProtocol",
        "from_start_port"   => "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortStart",
        "from_end_port"     => "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".TriggerPortEnd",
        "to_start_port"     => "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortStart",
        "to_end_port"       => "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.".$i.".ForwardPortEnd",
	);
    $port_trigger_value = KeyExtGet("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.", $port_trigger_param);
?>
<div id="content">
	<h1><?php echo _("Advanced > Port Triggering > Edit Port Trigger")?></h1>
    <div id="educational-tip">
		<p class="tip"><?php echo _("Edit a rule for port triggering services by user.")?></p>
		<p class="hidden"><?php echo _("Port triggering monitors outbound traffic on your network. When traffic is detected on a particular outbound port, the Gateway remembers that computer's IP address, triggers the inbound port to accept the incoming traffic, and directs the communications to the same computer.")?></p>
		<p class="hidden"><?php echo _("Port triggering settings can affect the Gateway's performance.")?></p>
	</div>
	<form method="post" id="pageForm" action="">
	<div class="module forms">
		<h2><?php echo _("Edit Port Trigger")?></h2>
		<div class="form-row odd">
			<label for="service_name"><?php echo _("Service Name:")?></label> <input type="text" class="text" value="<?php echo $port_trigger_value["service_name"]; ?>" id="service_name" name="service_name" />
		</div>
		<div class="form-row">
			<label for="service_type"><?php echo _("Service Type:")?></label>
			<select id="service_type">
			<?php $type = $port_trigger_value["type"]; ?>
			<option value="tcp_udp" <?php if($type === 'BOTH') echo 'selected'; ?>>TCP/UDP</option>
			<option value="tcp" <?php if($type === 'TCP') echo 'selected'; ?>>TCP</option>
			<option value="udp" <?php if($type === 'UDP') echo 'selected'; ?>>UDP</option>
			</select>
		</div>
		<div class="form-row odd">
			<label for="from_start_port"><?php echo _("Trigger From Starting Port:")?></label>  <input type="text" class="text" value="<?php echo $port_trigger_value["from_start_port"]; ?>" id="from_start_port" name="from_start_port" />
		</div>
		<div class="form-row">
			<label for="from_end_port"><?php echo _("Trigger From Ending Port:")?></label>  <input type="text" class="text" value="<?php echo $port_trigger_value["from_end_port"]; ?>" id="from_end_port" name="from_end_port" />
		</div>
	    <div class="form-row odd">
			<label for="to_start_port"><?php echo _("Target To Starting Port:")?></label>  <input type="text" class="text" value="<?php echo $port_trigger_value["to_start_port"];?>" id="to_start_port" name="to_start_port" />
		</div>
		<div class="form-row">
			<label for="to_end_port"><?php echo _("Target To Ending Port:")?></label>  <input type="text" class="text" value="<?php echo $port_trigger_value["to_end_port"];?>" id="to_end_port" name="to_end_port" />
		</div>
		<div class="form-btn">
			<input type="button" id="btn-save" value="<?php echo _("Save")?>" class="btn submit"/>
			<input type="reset" id="btn-cancel" value="<?php echo _("Cancel")?>" class="btn alt reset"/>
		</div>
	</div> <!-- end .module -->
	</form>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
