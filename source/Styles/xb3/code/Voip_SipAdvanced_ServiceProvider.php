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
	    gateway.page.init("Voice Diagnostics > Advanced Setup > Service Provider", "nav-voice-advance-service");
		// now we can show target content
		$("#content").show();
	});
</script>
<?php
    //DM's get for VOICE SIP BASIC SERVICE PROVIDER 0 page
	function Dm_data_get(){
		return array(
			'Sky_Network_Disconnect' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.X_RDK-Central_COM_NetworkDisconnect"),
			'Conference_URI' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.X_RDK-Central_COM_ConferencingURI"),
			'CallWaitingEnable' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.CallWaitingEnable"),
			'MWIEnable' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.MWIEnable"),
			'ConferenceCallingEnable' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.X_RDK-Central_COM_ConferenceCallingEnable"),
			'HoldEnable' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.X_RDK-Central_COM_HoldEnable"),
			'PhoneCallerIDEnable' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.CallingFeatures.X_RDK-Central_COM_PhoneCallerIDEnable"),
			'sip_status' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_VoiceProcessStatus"),
                        'DSCPMark_sip' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.DSCPMark"),
			'DSCPMark_rtp' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.RTP.DSCPMark"),
			'PriorityMark_sip' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.EthernetPriorityMark"),
			'PriorityMark_rtp' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.RTP.EthernetPriorityMark"),
			//'SkyEuroFlashCallWaitingEnable' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.X_RDK-Central_COM_EuroFlashCallWaitingEnable"),
		);
	}
	$values_got = Dm_data_get();
?>

<div id="content">
	<h1>Voice Diagnostics > Advanced Setup > Service Provider </h1>
	<div id="educational-tip">
		<p class="tip">"View technical Information related to VOICE DIAGNOSTICS SIP Advanced Service Provider"</p>
		<p class="hidden">Enable or disable Sky specific call features. Once configured, click 'Apply' then restart the SIP client.</p>
	</div>
	<form id='sip_adv_serviceprovider'>
		<div class="module forms">
			<h2>Voice SIP Advanced Service Provider</h2>
			<div class="form-row ">
				<label for="SIP_callwaiting">Call Waiting :</label>
				<input type="checkbox" id='SIP_callwaiting' name="SIP_callwaiting" />
			</div>
			<div class="form-row odd ">
				<label for="sky_conferencing">Sky Conferencing :</label>
				<input type="checkbox" id='sky_conferencing' name="sky_conferencing" />
			</div>
			<div class="form-row">
				<label for="sky_hold">Sky Hold :</label>
				<input type="checkbox" id='sky_hold' name="sky_hold" />
			</div>
			<div class="form-row odd">
				<label for="sky_phne_callr_id">Sky Phone Caller ID :</label>
				<input type="checkbox" id='sky_phne_callr_id' name="sky_phne_callr_id" />
			</div>
			<div class="form-row ">
				<label for="msg_waiting">Message Waiting :</label>
				<input type="checkbox" id='msg_waiting' name="msg_waiting" />
			</div>
			<div class="form-row odd">
				<label for="sky_ntwrk_discnt">Enable Sky Network Disconnect :</label>
				<input type="checkbox" id='sky_ntwrk_discnt' name="sky_ntwrk_discnt" />
			</div>
			<div class="form-row ">
				<label for="dscp_sip">DSCP for SIP :</label>
				<select id='dscp_sip' name='dscp_sip'>
					<option value='46'>EF (101110)</option>
					<option value='14'>AF13 (001110)</option>
					<option value='12'>AF12 (001100)</option>
					<option value='10'>AF11 (001010)</option>
					<option value='8'>CS1 (001000)</option>
					<option value='22'>AF23 (010110)</option>
					<option value='20'>AF22 (010100)</option>
					<option value='18'>AF21 (010010)</option>
					<option value='16'>CS2 (010000)</option>
					<option value='30'>AF33 (011110)</option>
					<option value='28'>AF32 (011100)</option>
					<option value='26'>AF31 (011010)</option>
					<option value='24'>CS3 (011000)</option>
					<option value='38'>AF43 (100110)</option>
					<option value='36'>AF42 (100100)</option>
					<option value='34'>AF41 (100010)</option>
					<option value='32'>CS4 (100000)</option>
					<option value='40'>CS5 (101000)</option>
					<option value='48'>CS6 (110000)</option>
					<option value='56'>CS7 (111000)</option>
				</select>
			</div>
			<div class="form-row odd">
				<label for="dscp_rtp">DSCP for RTP :</label>
				<select id='dscp_rtp' name='dscp_rtp'>
					<option value='46'>EF (101110)</option>
					<option value='14'>AF13 (001110)</option>
					<option value='12'>AF12 (001100)</option>
					<option value='10'>AF11 (001010)</option>
					<option value='8'>CS1 (001000)</option>
					<option value='22'>AF23 (010110)</option>
					<option value='20'>AF22 (010100)</option>
					<option value='18'>AF21 (010010)</option>
					<option value='16'>CS2 (010000)</option>
					<option value='30'>AF33 (011110)</option>
					<option value='28'>AF32 (011100)</option>
					<option value='26'>AF31 (011010)</option>
					<option value='24'>CS3 (011000)</option>
					<option value='38'>AF43 (100110)</option>
					<option value='36'>AF42 (100100)</option>
					<option value='34'>AF41 (100010)</option>
					<option value='32'>CS4 (100000)</option>
					<option value='40'>CS5 (101000)</option>
					<option value='48'>CS6 (110000)</option>
					<option value='56'>CS7 (111000)</option>
				</select>
			</div>
                        <div class="form-row ">
				<label for="priority_sip">Ethernet Priority for SIP :</label>
				<select id='priority_sip' name="priority_sip">
					<option value='0'>0</option>
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
					<option value='5'>5</option>
					<option value='6'>6</option>
					<option value='7'>7</option>
				</select>
			</div>
			<div class="form-row odd">
				<label for="priority_rtp">Ethernet Priority for RTP :</label>
				<select id='priority_rtp' name="priority_rtp">
					<option value='0'>0</option>
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
					<option value='5'>5</option>
					<option value='6'>6</option>
					<option value='7'>7</option>
				</select>
			</div>
			<div class="form-row">
				<label for="sky_conferencing_uri">Conference URI :</label>
				<input type="text" id='sky_conferencing_uri' name="sky_conferencing_uri"  class='text'/>
			</div>
			<!--<div class="form-row odd">
				<label for="eur_sky_flsh_waitng_mode">Enable Sky European Flash Call Waiting Mode :</label>
				<input type="checkbox" id='eur_sky_flsh_waitng_mode' name="eur_sky_flsh_waitng_mode"/>
			</div>-->
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
		if(php_value['CallWaitingEnable']=='true')
			$('#SIP_callwaiting').attr('checked',true);
		if(php_value['ConferenceCallingEnable']=='true')
			$('#sky_conferencing').attr('checked',true);
		if(php_value['HoldEnable']=='true')
			$('#sky_hold').attr('checked',true);
		if(php_value['PhoneCallerIDEnable']=='true')
			$('#sky_phne_callr_id').attr('checked',true);
		if(php_value['MWIEnable']=='true')
			$('#msg_waiting').attr('checked',true);
		if(php_value['SkyEuroFlashCallWaitingEnable']=='true')
			$('#eur_sky_flsh_waitng_mode').attr('checked',true);
		if(php_value['sip_status'] == 'Started'|| php_value['sip_status']=='Starting')
			$('#SIP_Start').prop('disabled', true);
		else if(php_value['sip_status'] == 'Stopped'|| php_value['sip_status']=='Stopping')
			$('#SIP_Stop').prop('disabled', true);
		if(php_value['Sky_Network_Disconnect']=='true')
			$('#sky_ntwrk_discnt').attr('checked',true);		
		$('#sky_conferencing_uri').val(php_value['Conference_URI']);
		$('#dscp_sip').val(php_value['DSCPMark_sip']);
		$('#dscp_rtp').val(php_value['DSCPMark_rtp']);
		$('#priority_sip').val(php_value['PriorityMark_sip']);
		$('#priority_rtp').val(php_value['PriorityMark_rtp']);
	}
	
	function get_set_val(){
		var ret={
			CallWaitingEnable : $('#SIP_callwaiting').is(':checked'),
			ConferenceCallingEnable : $('#sky_conferencing').is(':checked'),
			HoldEnable : $('#sky_hold').is(':checked'),
			PhoneCallerIDEnable: $('#sky_phne_callr_id').is(':checked'),
			MWIEnable : $('#msg_waiting').is(':checked'),
			SkyEuroFlashCallWaitingEnable:$('#eur_sky_flsh_waitng_mode').is(':checked'),
			Sky_Network_Disconnect:$('#sky_ntwrk_discnt').is(':checked'),
			Conference_URI:$('#sky_conferencing_uri').val(),
			DSCP_SIP:$('#dscp_sip').val(),
			DSCP_RTP:$('#dscp_rtp').val(),
			Priority_SIP:$('#priority_sip').val(),
			Priority_RTP:$('#priority_rtp').val(),
		};
		return ret;
	}
	$('#SIP_Apply').on('click',function(){
		var values = get_set_val();
		var jsConfig = '{"CallWaitingEnable":"'+values['CallWaitingEnable']
		 +'","ConferenceCallingEnable":"'+ values['ConferenceCallingEnable']
		 +'","HoldEnable":"'+values['HoldEnable']
		 +'","PhoneCallerIDEnable":"'+values['PhoneCallerIDEnable']
		 +'","MWIEnable":"'+values['MWIEnable']
		 +'","Sky_Network_Disconnect":"'+values['Sky_Network_Disconnect']
		 +'","Conference_URI":"'+values['Conference_URI']
		 +'","DSCPMark_sip":"'+values['DSCP_SIP']
		 +'","DSCPMark_rtp":"'+values['DSCP_RTP']
		 +'","PriorityMark_sip":"'+values['Priority_SIP']
		 +'","PriorityMark_rtp":"'+values['Priority_RTP']
		 +'","SkyEuroFlashCallWaitingEnable":"'+values['SkyEuroFlashCallWaitingEnable']+'"}';
    	         jProgress("<?php echo _('This may take several seconds...')?>", 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_SIPAdvanced_ServiceProvider.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				// location.reload();
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_SipAdvanced_ServiceProvider.php';
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
			url: "actionHandler/ajaxSet_SIPAdvanced_ServiceProvider.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_SipAdvanced_ServiceProvider.php';
			},
			error: function(){
				jHide();
			}
		});
	}
	setTimeout(page_load_values,50);
</script>
<?php include('includes/footer.php'); ?>
