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
<style type="text/css">
#content {
	display: none;
}
</style>
<script type="text/javascript">
	$(document).ready(function() {
	    gateway.page.init("Voice Diagnostics ", "nav-hidden");
		// now we can show target content
		$("#content").show();
	});
</script>
<?php
    //DM's get for VOICE SIP BASIC SERVICE PROVIDER 0 page
	function Dm_data_get(){
		return array(
			'line_register' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_DisableLoopCurrentUntilRegistered"),			
		);
	}
	$values_got = Dm_data_get();		
?>
<div id="content">
	<h1>Global Parameters</h1>
	<form >
		<div class="module forms">			
			<div class=" ">
			<label for="sky_line_registered" id='voip_container'> <?php echo _('Sky Disconnect Line Current Until Registered'); ?>
				<input type="checkbox" id='sky_line_registered' name="sky_line_registered" />
				<span class="checkmark"></span></label>
			</div>
			<div class="form-row form-btn">
				<input type="button" id='btn_apply' name="SIP_auth_pwd" value='Apply' class ='btn' />
			</div>
		</div>		
	</form>	
</div>
<script type="text/javascript">	
	var php_value= <?php echo json_encode($values_got) ?>;	

	function page_load_values(){
		if(php_value['line_register'] ==true || php_value['line_register'] == 'true')
			$('#sky_line_registered').attr('checked', true);			
	}
    $('#btn_apply').on('click', function(){    	
    	var set_value  = $('#sky_line_registered').is(':checked');
	var jsConfig = '{"current_line":"'+set_value+'"}';
	jProgress('<?php echo _("This may take several seconds...")?>', 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_voip_hidden.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				// location.reload();
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_hidden.php';
			},
			error: function(){            
				jHide();
				jAlert("Error in Saving DisableLoopCurrent ");
			}
		});
    });

	setTimeout(page_load_values,50);
</script>
<?php include('includes/footer.php'); ?>

