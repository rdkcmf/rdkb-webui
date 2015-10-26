<?php include('includes/header.php'); ?>
<?php include('includes/utility.php') ?>
<!-- $Id: port_triggering_add.php 3117 2009-10-15 20:23:13Z cporto $ -->

<div id="sub-header">
<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php

$i = $_GET['id'];
$portMapping_param = array(
        "service_name" 		=> "Device.NAT.PortMapping.$i.Description",
		"Service_Type"		=> "Device.NAT.PortMapping.$i.Protocol",
		"Server_IP_Address"	=> "Device.NAT.PortMapping.$i.InternalClient",
		"Start_Public_Port"	=> "Device.NAT.PortMapping.$i.ExternalPort",
		"End_Public_Port"	=> "Device.NAT.PortMapping.$i.ExternalPortEndRange",
		"Private_Ports"		=> "Device.NAT.PortMapping.$i.InternalPort",
		"Enable_Port_Range"	=> "Device.NAT.PortMapping.$i.Enable",
	);
    $portMapping_value = KeyExtGet("Device.NAT.PortMapping.", $portMapping_param);
$service_name 		= $portMapping_value["service_name"];
$Service_Type		= $portMapping_value["Service_Type"];
$Server_IP_Address	= $portMapping_value["Server_IP_Address"];
$Start_Public_Port	= $portMapping_value["Start_Public_Port"];
$End_Public_Port	= $portMapping_value["End_Public_Port"];
$Private_Ports		= $portMapping_value["Private_Ports"];
$Enable_Port_Range	= $portMapping_value["Enable_Port_Range"];

?>

<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
 
	comcast.page.init("Port Forwarding Add ", "nav-HS-port-forwarding");

	$('#service_name').focus();

	jQuery.validator.addMethod("ip",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 0 && value <= 255);
	}, "Please enter a valid IP address.");
	jQuery.validator.addMethod("ip4",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 1 && value <= 254);
	}, "Please enter a valid IP address.");
	jQuery.validator.addMethod("port",function(value,element){
		return this.optional(element) || (value.match(/^\d+$/g) && value >= 1 && value <= 65535);
	}, "Please enter a port number in range 1~65535.");
	jQuery.validator.addMethod("ltstart",function(value,element){
		return this.optional(element) || value>=parseInt($("#start_public_port").val());
	}, "Please enter a value more than or equal to Start Public Port.");
	
    $("#pageForm").validate({
		rules: {
            service_name: {
				required: true
			}
			,start_public_port: {
                required: true,
				port: true
            }
            ,end_public_port: {
                required: true,
				port: true,
            }
			,private_port: {
				required: true,
				port: true
			}
            ,ip_address_1: {
                required: true,
				ip: true
			}
			,ip_address_2: {
                required: true,
				ip: true
            }
			,ip_address_3: {
                required: true,
				ip: true
            }
			,ip_address_4: {
                required: true,
				ip4: true
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
    	window.location = "hs_port_forwarding.php";
    });

    $('#start_public_port').change(function(){
    	var endport = $('#start_public_port').val();
    	$('#end_public_port').val(endport);
    });
    
	$("#btn-save").click(function(){
		var id='<?php echo $i; ?>';
		var name=$('#service_name').val();
		var type=$('#service_type').find("option:selected").text();
		var ip=$('#ip_address_1').val()+'.'+$('#ip_address_2').val()+'.'+$('#ip_address_3').val()+'.'+$('#ip_address_4').val();
		var startport=$('#start_public_port').val();
		var endport=$('#end_public_port').val();
		var priport=$('#private_port').val();
		var enportrange=$('#enable_port_range').is(':checked'); // true or false (bool)

		if($("#pageForm").valid()){
			jProgress('This may take several seconds.',60);
			$.ajax({
				type:"POST",
				url:"actionHandler/ajax_hs_port_forwarding.php",
				data:{edit:"true",id:id,name:name,type:type,ip:ip,startport:startport,endport:endport,priport:priport,enportrange:enportrange},
				success:function(result){
					jHide();
					if (result=="Success!") { window.location.href="hs_port_forwarding.php";}
					else if (result=="") {jAlert('Failure! Please check your inputs.');}
					else jAlert(result);
				},
				error:function(){
					jHide();
					jAlert("Somethint wrong, please try later!");
				}
			});
		} //end of if
	});
});
</script>

<div id="content">
	<h1>Advanced > HS Port Forwarding > Edit Service</h1>
    <div id="educational-tip">
		<p class="tip">Edit port forwarding related to Home Security Device.</p>
   		<p class="hidden">Users can configure the RG to provide the port forwarding services which allow the Internet users to
		access local services such as the Web server or FTP server at your local site. This is done by
		redirecting the combination of the WAN IP address and the service port to the local private IP and its
        service port.</p>
	</div>

	<form method="post" id="pageForm" action="">
	<div class="module forms">
		<h2>Edit HS Port Forward</h2>

		<div class="form-row odd">
			<label for="service_name">Service Name:</label> <input type="text" class="text" value="<?php echo $service_name; ?>" id="service_name" name="service_name" />
		</div>

		<div class="form-row">
			<label for="service_type">Service Type:</label>
			<select id="service_type">
				<option <?php if($Service_Type == "BOTH") echo 'selected'; ?> >TCP/UDP</option>
				<option <?php if($Service_Type == "TCP") echo 'selected'; ?> >TCP</option>
				<option <?php if($Service_Type == "UDP") echo 'selected'; ?> >UDP</option>
			</select>
		</div>
		
		<?php
			$ip = explode(".",$Server_IP_Address);
		?>
		<div class="form-row odd">
			<label for="ip_address_1">Server IP Address:</label>
            <input type="text" size="2"  id="ip_address_1" name="ip_address_1"  value="172" disabled="disabled" class="" />
            <label for="ip_address_2" class="acs-hide"></label>
	        .<input type="text" size="2" id="ip_address_2" name="ip_address_2"  value="16" disabled="disabled" class="" />
            <label for="ip_address_3" class="acs-hide"></label>
	        .<input type="text" size="2"  id="ip_address_3" name="ip_address_3" value="12"  disabled="disabled" class="" />
            <label for="ip_address_4" class="acs-hide"></label>
	        .<input type="text" size="2" id="ip_address_4" name="ip_address_4" value="<?php echo$ip[3]; ?>" class="" />
		</div>

		<div class="form-row">
			<label for="start_public_port"> Start Public Port:</label>  <input type="text" class="text" value="<?php echo $Start_Public_Port; ?>" id="start_public_port" name="start_public_port" size="10"/>
		</div>
		<div class="form-row odd">
			<label for="end_public_port">End Public Port:</label>  <input type="text" class="text" value="<?php echo $End_Public_Port; ?>" id="end_public_port" name="end_public_port" disabled size="10"/>
		</div>
	    <div class="form-row ">
			<label for="private_port">Private Port(s):</label>  <input type="text" class="text" value="<?php echo $Private_Ports; ?>" id="private_port" name="private_port" size="10" />
		</div>
		<div class="form-row odd">
			<label for="enable_port_range">Enable Port Range: </label><input type="checkbox" id="enable_port_range" name="enable_port_range" value="Enable Private Port Range" disabled="disabled" <?php if($Enable_Port_Range == 'true') echo "checked='checked'"?>/><b>  Enable Private Port Range </b>
		</div>
		<div class="form-btn">
			<input type="button" id="btn-save" value="Save" class="btn submit"/>
			<input type="reset" id="btn-cancel" value="Cancel" class="btn alt reset"/>
		</div>
	</div> <!-- end .module -->
	</form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
