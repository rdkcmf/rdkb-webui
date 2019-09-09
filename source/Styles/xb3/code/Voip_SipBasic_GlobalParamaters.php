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
	    gateway.page.init("Voice Diagnostics > SIP Basic Setup > Global Parameters", "nav-voice-sip-global");
		// now we can show target content
		$("#content").show();
	});
</script>
<?php
    //DM's get for VOICE SIP BASIC SERVICE PROVIDER 0 page
	function Dm_data_get(){
		return array(
			'BoundIfName' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_BoundIfName"),
			'IpAddressFamily' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_IpAddressFamily"),
			'line_register' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_DisableLoopCurrentUntilRegistered"),
			'sip_status' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_VoiceProcessStatus"),
		);
	}
	$values_got = Dm_data_get();
?>
<div id="content">
	<h1>Voice Diagnostics > SIP Basic Setup > Global Parameters </h1>
	<div id="educational-tip">
		<p class="tip">"View technical Information related to VOICE DIAGNOSTICS SIP Global Parameters"</p>
		<p class="hidden"><strong>Global Parameters</strong>Select the interface and address family to be used by the SIP client. Once applied, the SIP client needs to be restarted for the change to take effect.
		<p>
	</div>
	<form id='sip_globalparameters'>
		<div class="module forms">
			<h2>Global Parameters</h2>
			<div class="form-row ">
				<label for="global_BoundIfName">Bound Interface Name :</label>
				<select id="global_BoundIfName" name="global_BoundIfName">
					<option value="Any_WAN">Any WAN</option>
					<option value="LAN">LAN</option>
				</select>
			</div>
			<div class="form-row odd ">
				<label for="global_IpAddressFamily">IP Address Family :</label>
				<select id="global_IpAddressFamily" name="global_IpAddressFamily">
				<option value="IPv4">IPv4</option>
				<option value="IPv6">IPv6</option>
			</select>
			</div>
			<div class='form-row'>
                            <label for="line">Sky Disconnect Line Current Until Registered</label>
                            <input type="checkbox" id='sky_line_registered' name="sky_line_registered" />
                        </div>
			<div class="form-row odd">
				<span><b>Note :</b>Interface and address family changes requires the SIP client to be stopped and then started to take effect</span>
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
		$('#global_BoundIfName').val(php_value['BoundIfName']);
		$('#global_IpAddressFamily').val(php_value['IpAddressFamily']);
		if(php_value['line_register'] ==true || php_value['line_register'] == 'true')
			$('#sky_line_registered').attr('checked', true);
		if(php_value['sip_status'] == 'Started'|| php_value['sip_status']=='Starting' )
			$('#SIP_Start').prop('disabled', true);
		else if(php_value['sip_status'] == 'Stopped'||  php_value['sip_status']=='Stopping')
			$('#SIP_Stop').prop('disabled', true);
	}
	$('#SIP_Apply').on('click', function(){
		var mod_values='{"BoundIfName" :"'+ $('#global_BoundIfName').val()
			+'","IpAddressFamily":"'+$('#global_IpAddressFamily').val()
			+'","line_register":"'+$('#sky_line_registered').is(':checked')
			+'"}';
                var jsConfig = mod_values;
    	        jProgress('<?php echo _('This may take several seconds...')?>', 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_SIPBasic_GlobalParameter.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_SipBasic_GlobalParamaters.php';
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
			url: "actionHandler/ajaxSet_SIPBasic_GlobalParameter.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_SipBasic_GlobalParamaters.php';
			},
			error: function(){
				jHide();
			}
		});
	}
	setTimeout(page_load_values,50);
</script>
<?php include('includes/footer.php'); ?>
