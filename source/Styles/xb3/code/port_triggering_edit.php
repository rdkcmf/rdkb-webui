<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: port_triggering_add.php 3117 2009-10-15 20:23:13Z cporto $ -->

<div id="sub-header">
<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Port Triggering Add - Advanced", "nav-port-triggering");
    $('#service_name').focus();

	jQuery.validator.addMethod("port",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 0 && value <= 65535);
	}, "Please enter a port number less than 65536.");
	jQuery.validator.addMethod("triggermore",function(value,element){
		var fstartport=parseInt($("#from_start_port").val());
		return this.optional(element) || value>=fstartport;
	}, "Please enter a value more than or equal to Trigger Starting Port.");
	jQuery.validator.addMethod("targetmore",function(value,element){
		var tstartport=parseInt($("#to_start_port").val());
		return this.optional(element) || value>=tstartport;
	}, "Please enter a value more than or equal to Target Starting Port.");	
	
    $("#pageForm").validate({
        rules: {
            service_name: {
                required: true
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
		jProgress('This may take several seconds.',60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_port_triggering.php",
			data:{edit:"true",ID:ID,name:name,type:type,fsp:fsp,fep:fep,tsp:tsp,tep:tep},
			dataType: "json",
			success:function(results){
				jHide();
				if (results=="Success!") { window.location.href="port_triggering.php";}
				else if (results=="") {jAlert('Failure! Please check your inputs.');}
				else jAlert(results);
			},
			error:function(){
				jHide();
				jAlert("Something wrong, please try later!");
			}
		});
		}
	});	
});
</script>
<?php $i=$_GET['id'];
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
	<h1>Advanced > Port Triggering > Edit Port Trigger</h1>
    <div id="educational-tip">
		<p class="tip">Edit a rule for port triggering services by user.</p>
		<p class="hidden">Port triggering monitors outbound traffic on your network. When traffic is detected on a particular outbound port, the Gateway remembers that computer's IP address, triggers the inbound port to accept the incoming traffic, and directs the communications to the same computer.</p>
		<p class="hidden">Port triggering settings can affect the Gateway's performance.</p>
	</div>
	<form method="post" id="pageForm" action="">
	<div class="module forms">
		<h2>Edit Port Trigger</h2>

		<div class="form-row odd">
			<label for="service_name">Service Name:</label> <input type="text" class="text" value="<?php echo $port_trigger_value["service_name"]; ?>" id="service_name" name="service_name" />
		</div>

		<div class="form-row">
			<label for="service_type">Service Type:</label>
			<select id="service_type">

			<?php $type = $port_trigger_value["type"]; ?>

			<option value="tcp_udp" <?php if($type === 'BOTH') echo 'selected'; ?>>TCP/UDP</option>
			<option value="tcp" <?php if($type === 'TCP') echo 'selected'; ?>>TCP</option>
			<option value="udp" <?php if($type === 'UDP') echo 'selected'; ?>>UDP</option>
			
			</select>
		</div>

		<div class="form-row odd">
			<label for="from_start_port">Trigger From Starting Port:</label>  <input type="text" class="text" value="<?php echo $port_trigger_value["from_start_port"]; ?>" id="from_start_port" name="from_start_port" />
		</div>
		<div class="form-row">
			<label for="from_end_port">Trigger From Ending Port:</label>  <input type="text" class="text" value="<?php echo $port_trigger_value["from_end_port"]; ?>" id="from_end_port" name="from_end_port" />
		</div>
	    <div class="form-row odd">
			<label for="to_start_port">Target To Starting Port:</label>  <input type="text" class="text" value="<?php echo $port_trigger_value["to_start_port"];?>" id="to_start_port" name="to_start_port" />
		</div>
		<div class="form-row">
			<label for="to_end_port">Target To Ending Port:</label>  <input type="text" class="text" value="<?php echo $port_trigger_value["to_end_port"];?>" id="to_end_port" name="to_end_port" />
		</div>
		<div class="form-btn">
			<input type="button" id="btn-save" value="Save" class="btn submit"/>
			<input type="reset" id="btn-cancel" value="Cancel" class="btn alt reset"/>
		</div>

	</div> <!-- end .module -->
	</form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
