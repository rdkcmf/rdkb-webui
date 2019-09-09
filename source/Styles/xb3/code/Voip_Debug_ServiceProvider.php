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
	    gateway.page.init("Voice Diagnostics > Debug ", "nav-voice-debug-service");
		// now we can show target content
		$("#content").show();
	});
</script>
<?php
    //DM's get for VOICE SIP BASIC SERVICE PROVIDER 0 page
	function Dm_data_get(){
		return array(
			'Ingress' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.VoiceProcessing.ReceiveGain"),
			'Egress' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.VoiceProcessing.TransmitGain"),
			'server_IP_addr' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_LogServer"),
			'server_port' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_LogServerPort"),
			'sip_status' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_VoiceProcessStatus"),
		);
	}
	$values_got = Dm_data_get();
?>

<div id="content">
	<h1>Voice Diagnostics > Debug > Service Provider</h1>
	<div id="educational-tip">
		<p class="tip">"View technical Information related to VOICE DIAGNOSTICS  Debug"</p>
		<p class="hidden"><strong>Debug Configuration :</strong>Configure the server IP address and port where SIP logs are to be sent.</p>
		<p class="hidden"><strong>Line :</strong>Additional gain or loss to be applied to speech, in 0.1 dB steps.<br/>The SIP client needs to be stopped and restarted for the changes to take effect.</p>
	</div>
	<form id='sip_debug'>
		<div class="module forms">
			<h2>Debug Configuration</h2>
			<div class="form-row ">
				<label for="SIP_log_svr_ip_addr">SIP Log Server IP Address :</label>
				<input type="text" id='SIP_log_svr_ip_addr' name="SIP_log_svr_ip_addr" value='' class ='text'/>
			</div>
			<div class="form-row odd ">
				<label for="SIP_log_svr_port">SIP Log Server Port :</label>
				<input type="text" id='SIP_log_svr_port' name="SIP_log_svr_port" value='' class ='text'/>
			</div>
			<h2 style='margin-top:10px'>Line</h2>
			<div class="form-row odd ">
				<label for="SIP_ingress">Ingress gain :</label>
				<select id='ingress'></select>
			</div>
			<div class="form-row ">
				<label for="egress">Egress gain :</label>
				<select id='egress' name="egress" name="egress"></select>
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
        php_value['Ingress'] = parseInt(php_value["Ingress"])/10;
	php_value['Egress'] = parseInt(php_value["Egress"])/10;
	function page_load_values(){
			$('#SIP_log_svr_ip_addr').val(php_value['server_IP_addr']);
			$('#SIP_log_svr_port').val(php_value['server_port']);
			$('#ingress').val(php_value['Ingress']);
			$('#egress').val(php_value['Egress']);
			if(php_value['sip_status'] == 'Started'|| php_value['sip_status']=='Starting'){
				$('#SIP_Start').prop('disabled', true);
			}
			else if(php_value['sip_status'] == 'Stopped'|| php_value['sip_status']=='Stopping'){
				$('#SIP_Stop').prop('disabled', true);
			}
	}
	function add_dropdown(){
		for(var i=3.0; i>=-12.0; i-= 0.1){
			var r = parseFloat(i.toFixed(1));
			$('#ingress , #egress').append('<option value='+r+'>'+r+'</option>');
		}
	}
	$('#SIP_Apply').on('click', function(){
		var mod_values='{"Ingress" :"'+$('#ingress').val()*10
		+'","Egress" :"'+ $('#egress').val()*10
		+'","server_IP_addr" :"'+$('#SIP_log_svr_ip_addr').val()
		+'","server_port" :"'+$('#SIP_log_svr_port').val()+'"}';
		var jsConfig = mod_values;
		jProgress("<?php echo _('This may take several seconds...')?>", 60);
               	$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_voip_debug.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_Debug_ServiceProvider.php';
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
			url: "actionHandler/ajaxSet_voip_debug.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_Debug_ServiceProvider.php';
			},
			error: function(){
				jHide();
			}
		});
	}
	setTimeout(add_dropdown,25);
	setTimeout(page_load_values,50);
</script>
<?php include('includes/footer.php'); ?>

