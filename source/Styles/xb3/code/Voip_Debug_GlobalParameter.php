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
	    gateway.page.init("Voice Diagnostics > Debug ", "nav-voice-debug-global");
		// now we can show target content
		$("#content").show();
	});
</script>
<?php
    //DM's get for VOICE SIP BASIC SERVICE PROVIDER 0 page
	function Dm_data_get(){
		return array(
			'Console_log' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_LoggingLevel"),
			'sip_status' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_VoiceProcessStatus"),
		);
	}
	$values_got = Dm_data_get();
?>

<div id="content">
	<h1>Voice Diagnostics > Debug > Global Parameters</h1>
	<div id="educational-tip">
		<p class="tip">"View technical Information related to VOICE DIAGNOSTICS  Debug"</p>



		<p class="hidden"><strong>Console Log :</strong></p>

	</div>
	<form id='sip_debug'>
		<div class="module forms">
			<h2>Debug Configuration</h2>
			<div class="form-row ">
				<label for="console_log">Voice Console Log Level :</label>
				<select id="console_log" name="console_log">
					<option value="Error">Error</option>
					<option value="Notice">Notice</option>
					<option value="Debug">Debug</option>
				</select>
			</div>
		</div>
		<div class="form-row form-btn">
				<input type="button" id='SIP_Start' name="Start" value='Start SIP Client' class ='btn sip_btn'/>
				<input type="button" id='SIP_Stop' name="Stop" value='Stop SIP Client' class ='btn sip_btn'/>
				<span style='float:right'>
					<input type="button" id='SIP_Apply' name="SIP_auth_pwd" value='Apply' class ='btn' />
				</span>
			</div>
	</form>
</div>
<script type="text/javascript">
	var php_value= <?php echo json_encode($values_got) ?>;
	function page_load_values(){
			$('#console_log').val(php_value['Console_log']);
			if(php_value['sip_status'] == 'Started'|| php_value['sip_status']=='Starting')
			$('#SIP_Start').prop('disabled', true);
	        	else if(php_value['sip_status'] == 'Stopped'|| php_value['sip_status']=='Stopping')
			$('#SIP_Stop').prop('disabled', true);
	}
	$('#SIP_Apply').on('click', function(){
		var mod_values='{"Console_log" :"'+$('#console_log').val()+'"}';
		var jsConfig = mod_values;
		jProgress("<?php echo _('This may take several seconds...')?>", 60);
               	$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_Debug_GlobalParameter.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_Debug_GlobalParameter.php';
			},
			error: function(){
				jHide();
			}
		});
	});
	$('.sip_btn').on('click', function(){
		var btn_val = $(this).attr('name');
		if(btn_val == 'Start'){
			$('#SIP_Start').prop('disabled', true);
		}
		else if(btn_val == 'Stop'){
			$('#SIP_Stop').prop('disabled', true);
		}
		sipclient(btn_val);
	});
	function sipclient(val){
		var SIP_val = val;
		var jsConfig ='{"SIP_val":"'+SIP_val+'"}';
		jProgress("<?php echo _('This may take several seconds...')?>", 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_Debug_GlobalParameter.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_Debug_GlobalParameter.php';
			},
			error: function(){
				jHide();
			}
		});
	}
	setTimeout(page_load_values,50);
</script>
<?php include('includes/footer.php'); ?>
