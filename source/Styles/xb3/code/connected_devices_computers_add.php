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
    gateway.page.init("Connected Devices > Devices > Edit Device", "nav-cdevices");
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
        $.validator.addMethod("allowed_char_HostName", function(value, element) {      
          return this.optional(element) || /^[a-z0-9-]+$/i.test(value);         
}, "<?php echo _("Special characters are not allowed, Only hyphen(-),letters and Numbers are allowed.")?>");
	$("#pageForm").validate({
	    rules : {
	    	staticIPAddress: {
	            required: true,
	            ipv4: true
	    	}
	    	,host_name:{
	    		required: true,
	    		maxlength: 64,
	    		allowed_char_HostName: true
	    	}
	    	,mac_address:{
	    		required: true,
	    		//multicastMAC: true,
	    		mac: true
	    	}
	    	,comments:{
	    		allowed_char: true
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
    		jAlert("<?php echo _("The comments should be no more than 63 characters !")?>");
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
				jAlert("<?php echo _('Reserved IP Address is not in valid range:\n')?>"+beginAddr+" ~ "+endAddr);
				return;
			}
		}
    	var deviceInfo = '{"addResvIP": "true", "Comments": "'+ Comments +'", "hostName": "' + hostName + '", "macAddress": "' + macAddress + '", "reseverd_ipAddr": "' + reseverd_ipAddr + '"}';
        //alert( deviceInfo);
        if($("#pageForm").valid()){
	        var mac2bit = macAddress.substr(0,2); //it's a string type variable
	        if( 1 == (mac2bit & 1) ){
		  		//mac2bit is odd
		  		jAlert("<?php echo _("The MAC address is invalid, please input again.")?>");
		  		return;
		  	}
			jProgress("<?php echo _('This may take several seconds')?>", 60); 
			$.ajax({
				type: "POST",
				url: "actionHandler/ajaxSet_add_device.php",
				data: { DeviceInfo: deviceInfo },
				dataType: "json",
				success: function(results){
					jHide();
					if (results=="<?php echo _('success');?>") { window.location.href="connected_devices_computers.php";}
					else if (results=="") {jAlert("<?php echo _('Failure! Please check your inputs.')?>");}
					else jAlert(results);
				},           
				error: function(){
					jHide();
					jAlert("<?php echo _("Failure, Please check your inputs and try again.")?>");
				}
		    });
  		}
    });  
});
</script>
<div id="content">
    <h1><?php echo _("Connected Devices > Devices > Add Device");?></h1>
    <div id="educational-tip">
		<p class="tip"><?php echo _("Connect a Device using a Reserved IP address.");?></p>
		<p class="hidden"><?php echo _("<strong>Host Name:</strong> Name of the Device being added.");?> </p>
				<p class="hidden"><?php echo _("<strong>MAC Address:</strong>  MAC address of the Device being added.");?></p>
				<p class="hidden"><?php echo _("<strong>Reserved IP address:</strong>  The IP address of the device being added must be within the Gateway's range of the DHCP IP address pool.To find your IP address range, go to <strong>Gateway > Connection > Local IP Network.</strong>");?></p>
	</div>
	<div class="module forms" id="computers-edit">
		<h2><?php echo _("Add Device with Reserved IP Address");?></h2>
        <form id="pageForm">
			<div class="form-row">
        		<span class="readonlyLabel"><?php echo _("Host Name:");?></span>
				<label for="host_name" class="acs-hide"></label>
        		<input type="text" name="host_name" id="host_name" maxlength="64" />
			</div>
			<div id="static-mac" class="form-row odd">
				<label for="mac_address"><?php echo _("MAC Address:");?></label>
				<input type="text" name="mac_address" id="mac_address"  />
			</div>
      		<div id="static-ip" class="form-row">
				<label for="staticIPAddress"><?php echo _("Reserved IP Address:");?></label>
				<input type="text" id="staticIPAddress" name="staticIPAddress" class="target" />
			</div>
			<div class="form-row odd">
				<label for="comments"><?php echo _("Comments:");?></label>
		        <textarea id="comments" name="comments" ros="6" cols="18" maxlength="63"></textarea>
			</div>
			<div class="form-row form-btn">
				<input type="button" id="saveBtn" class="btn submit" value="<?php echo _("Save")?>"/>
				<input type="reset" id="btn-cancel" class="btn alt reset" value="<?php echo _("Cancel")?>"/>
			</div>
		</form>
	</div> <!-- end .module -->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
