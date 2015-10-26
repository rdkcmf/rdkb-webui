<?php include('includes/header.php'); ?>

<!-- $Id: connected_devices_computers_edit.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
$beginAddr 	= getStr("Device.DHCPv4.Server.Pool.1.MinAddress");
$endAddr 	= getStr("Device.DHCPv4.Server.Pool.1.MaxAddress");
?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Connected Devices > Devices > Edit Device", "nav-cdevices");
    $('#host_name').focus();

	var beginAddr	= "<?php echo $beginAddr; ?>";
	var endAddr	= "<?php echo $endAddr; ?>";
	var beginArr	= beginAddr.split(".");
	var endArr	= endAddr.split(".");

	/*jQuery.validator.addMethod("multicastMAC",function(value,element){
		
		var macValue = parseInt(value.substr(0,2));
		return ((macValue%2)== 0);
	}, "This MAC is reserved for multicast, please input again.");
	*/
	$("#pageForm").validate({
	    rules : {
	    	staticIPAddress: {
	            required: true,
	            ipv4: true
	    	}
	    	,host_name:{
	    		required: true,
	    		maxlength: 64
	    	}
	    	,mac_address:{
	    		required: true,
	    		//multicastMAC: true,
	    		mac: true
	    	}
	    }
	});

	$("#btn-cancel").click(function() {
    	window.location.href = "connected_devices_computers.php";
    });
    
    if ($.browser.msie){
		$("textarea").keypress(function(e){
		    var lengthF = $(this).val();

		    if (lengthF.length > 62){
		        e.preventDefault();
		    }
		});
    }    
    
    $('#saveBtn').click(function(e){

    	e.preventDefault();

    	var lengthF = $("textarea").val();
    	if (lengthF.length > 63){
    		jAlert("The comments should be no more than 63 characters !");
    		return;
    	}

    	var hostName = $('#host_name').val();
    	var macAddress = $('#mac_address').val();
    	var reseverd_ipAddr = $('#staticIPAddress').val();
    	var Comments = $('#comments').val();      

		//to check if "Reserved IP Address" is in "DHCP Pool range"
		var reseverd_ipArr	= reseverd_ipAddr.split(".");
		for(i=0;i<4;i++){
			if(parseInt(beginArr[i]) > parseInt(reseverd_ipArr[i]) || parseInt(reseverd_ipArr[i]) > parseInt(endArr[i])){
				jAlert("Reserved IP Address is not in valid range:\n"+beginAddr+" ~ "+endAddr);
				return;
			}
		}

    	var deviceInfo = '{"addResvIP": "true", "Comments": "'+ Comments +'", "hostName": "' + hostName + '", "macAddress": "' + macAddress + '", "reseverd_ipAddr": "' + reseverd_ipAddr + '"}';
        //alert( deviceInfo);
      
        if($("#pageForm").valid()){

	        var mac2bit = macAddress.substr(0,2); //it's a string type variable
	        if( 1 == (mac2bit & 1) ){
		  		//mac2bit is odd
		  		jAlert("The MAC address is invalid, please input again.");
		  		return;
		  	}

			jProgress('This may take several seconds', 60); 
			$.ajax({
				type: "POST",
				url: "actionHandler/ajaxSet_add_device.php",
				data: { DeviceInfo: deviceInfo },
				dataType: "json",
				success: function(results){
					jHide();
					if (results=="success") { window.location.href="connected_devices_computers.php";}
					else if (results=="") {jAlert('Failure! Please check your inputs.');}
					else jAlert(results);
				},           
				error: function(){
					jHide();
					jAlert("Failure, Please check your inputs and try again.");
				}
		    });
  		}
    });  
  
});
</script>

<div id="content">
    <h1>Connected Devices > Devices > Add Device</h1>
    <div id="educational-tip">
		<p class="tip">Connect a Device using a Reserved IP address.</p>
		<p class="hidden"><strong>Host Name:</strong> Name of the Device being added. </p>
				<p class="hidden"><strong>MAC Address:</strong>  MAC address of the Device being added.</p>
				<p class="hidden"><strong>Reserved IP address:</strong>  The IP address of the device being added must be within the Gateway's range of the DHCP IP address pool.To find your IP address range, go to <strong>Gateway > Connection > Local IP Network.</strong></p>
	</div>
	<div class="module forms" id="computers-edit">
		<h2>Add Device with Reserved IP Address</h2>
        <form id="pageForm">

			<div class="form-row">
        		<span class="readonlyLabel">Host Name:</span>
				<label for="host_name" class="acs-hide"></label>
        		<input type="text" name="host_name" id="host_name" maxlength="64" />
			</div>

			<div id="static-mac" class="form-row odd">
				<label for="mac_address">MAC Address:</label>
				<input type="text" name="mac_address" id="mac_address"  />
			</div>
      		<div id="static-ip" class="form-row">
				<label for="staticIPAddress">Reserved IP Address:</label>
				<input type="text" id="staticIPAddress" name="staticIPAddress" class="target" />
			</div>
			<div class="form-row odd">
				<label for="comments">Comments:</label>
		        <textarea id="comments" name="comments" ros="6" cols="18" maxlength="63"></textarea>
			</div>

			<div class="form-row form-btn">
				<input type="button" id="saveBtn" class="btn submit" value="Save"/>
				<input type="reset" id="btn-cancel" class="btn alt reset" value="Cancel"/>
			</div>
		</form>

	</div> <!-- end .module -->
</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
