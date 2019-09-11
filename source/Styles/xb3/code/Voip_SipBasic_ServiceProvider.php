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
	    gateway.page.init("Voice Diagnostics > SIP Basic Setup > Service Provider", "nav-voice-sip-service");
		// now we can show target content
		$("#content").show();
	});
</script>
<?php
    //DM's get for VOICE SIP BASIC SERVICE PROVIDER 0 page
	function Dm_data_get(){
		return array(
			'SDigitTimer' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_SDigitTimer"),
			'ZDigitTimer'=>getStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_ZDigitTimer"),
			'Acc_Enabled'=>getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.Enable"),
			'Acc_Status'=> getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.Status"),
			'Directory' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.DirectoryNumber"),
			'Auth_pwd' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.SIP.AuthPassword"),
			'Auth_usr' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.SIP.AuthUserName"),
			'OutboundProxy' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.OutboundProxy"),
			'OutboundProxyPort' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.OutboundProxyPort"),
			'ProxyServer' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.ProxyServer"),
			'ProxyServerPort' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.ProxyServerPort"),
			'RegistrarServer' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.RegistrarServer"),
			'RegistrarServerPort' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.RegistrarServerPort"),
			'UserAgentDomain' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.SIP.UserAgentDomain"),
			'DigitMap' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_DigitMap"),
			'EmergencyDigitMap' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.X_RDK-Central_COM_EmergencyDigitMap"),
			'sip_status' => getStr("Device.Services.VoiceService.1.X_RDK-Central_COM_VoiceProcessStatus"),
                        'uri' => getStr("Device.Services.VoiceService.1.VoiceProfile.1.Line.1.SIP.URI"),
		);
	}
	$values_got = Dm_data_get();
?>
<div id="content">
	<h1>Voice Diagnostics > SIP Basic Setup > Service Provider </h1>
	<div id="educational-tip">
		<p class="tip">"View technical Information related to VOICE DIAGNOSTICS SIP Basic Service Provider"</p>
		<p class="hidden">Basic SIP configuration data.<br/>
		<strong>SIP Account :</strong>Set the user name and password of the SIP account. Click 'Apply' when done.<br/>The SIP client needs to be stopped and restarted for the changes to take effect.</p>
	</div>	
	<form id='sip_serviceprovider'>
		<div class="module forms">
			<h2>Voice SIP Configuration</h2>
			<div class="form-row ">
				<label for="SIP_domainName">SIP Domain Name :</label>
				<input type="text" id='SIP_domainName' name="SIP_domainName" value='' class ='text'/>
			</div>
			<div class="form-row odd ">
				<label for="SIP_digitMap">SIP Digit Map :</label>
				<input type="text" id='SIP_digitMap' name="SIP_digitMap" value='' class ='text'/>
			</div>
			<div class="form-row">
				<label for="SIP_Emer_digitMap">SIP Emergency Digit Map :</label>
				<input type="text" id='SIP_Emer_digitMap' name="SIP_Emer_digitMap" value='' class ='text'/>
			</div>
			<div class="form-row odd ">
				<label for="S_digit_timer">Sky S Digit Timer :</label>
				<input type="text" id='S_digit_timer' name="S_digit_timer" class='text'/>
			</div>
			<div class="form-row ">
				<label for="Z_digit_timer">Sky Z Digit Timer :</label>
				<input type="text" id='Z_digit_timer' name="Z_digit_timer" value='' class ='text'/>
			</div>
			<div class="form-row odd ">
				<label for="u_SIP_proxy">Use SIP Proxy :</label>
				<input type="checkbox" id='u_SIP_proxy' name="u_SIP_proxy"/>
			</div>
			<div class="form-row ">
				<label for="SIP_proxy">SIP Proxy :</label>
				<input type="text" id='SIP_proxy' name="SIP_proxy" value='' class ='text'/>
			</div>
			<div class="form-row odd ">
				<label for="SIP_proxy_port">SIP Proxy Port :</label>
				<input type="number" size="5" maxlength="5"  id='SIP_proxy_port' name="SIP_proxy_port" value='' class ='text'/>
			</div>
			<div class="form-row">
				<label for="u_SIP_outbound_proxy">Use SIP Outbound Proxy :</label>
				<input type="checkbox" id='u_SIP_outbound_proxy' name="u_SIP_outbound_proxy" value='' class ='text'/>
			</div>
			<div class="form-row odd ">
				<label for="SIP_outbound_proxy">SIP Outbound Proxy :</label>
				<input type="text" id='SIP_outbound_proxy' name="SIP_outbound_proxy" value='' class ='text'/>
			</div>
			<div class="form-row ">
				<label for="SIP_outbound_proxy_port">SIP Outbound Proxy Port :</label>
				<input type="number" size="5" maxlength="5" id='SIP_outbound_proxy_port' name="SIP_outbound_proxy_port" value='' class ='text'/>
			</div>
			<div class="form-row odd ">
				<label for="u_SIP_registrar">Use SIP Registrar :</label>
				<input type="checkbox" id='u_SIP_registrar' name="u_SIP_registrar" value='' class ='text'/>
			</div>
			<div class="form-row">
				<label for="SIP_registrar">SIP Registrar :</label>
				<input type="text" id='SIP_registrar' name="SIP_registrar" value='' class ='text'/>
			</div>
			<div class="form-row odd ">
				<label for="SIP_registrar_port">SIP Registrar Port :</label>
				<input type="number" id='SIP_registrar_port' name="SIP_registrar_port" value='' class ='text'/>
			</div>
			<h2 style='margin-top:10px'>SIP Account</h2>
			<div class="form-row odd ">
				<label for="SIP_acc_enabled">Account Enabled :</label>
				<input type="checkbox" id='SIP_acc_enabled' name="SIP_acc_enabled" />
			</div>
			<div class="form-row ">
				<label for="SIP_acc_status">Account Status :</label>
				<input type="text" id='SIP_acc_status' name="SIP_acc_status" class ='text' disabled/>
			</div>
			<div class="form-row odd ">
				<label for="SIP_Directory">Directory Number :</label>
				<input type="text" id='SIP_Directory' name="SIP_Directory" value='' class ='text'/>
			</div>
                        <div class="form-row">
				<label for="SIP_URI">URI :</label>
				<input type="text" id='SIP_URI' name="SIP_URI" value='' class ='text'/>
			</div>
			<div class="form-row odd">
				<label for="SIP_auth_name">Authentication Name :</label>
				<input type="text" id='SIP_auth_name' name="SIP_auth_name" value='' class ='text'/>
			</div>
			<div class="form-row">
				<label for="SIP_auth_pwd">Password :</label>
				<input type="password" id='SIP_auth_pwd' name="SIP_auth_pwd" value='' class ='text'/>
			</div>
			<div class="form-row odd">
				<label for="password_show"><?php echo _("Show Password:")?></label>
				<span class="checkbox"><input type="checkbox" id="password_show" name="password_show" /></span>
			</div>
			<div class="form-row">
                                <span><b>Note :</b>The fields 'SIP Outbound Proxy' and 'SIP Outbound Proxy Port' can be edited when 'Use SIP Outbound Proxy' box is checked.
Similarly for 'Use SIP Registrar' and the two fields below.<br/><br/>The <b>Password:</b> must be re-entered everytime before pressing <b>APPLY</b></span>
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
		$('#S_digit_timer').val(php_value['SDigitTimer']);
		$('#Z_digit_timer').val(php_value['ZDigitTimer']);
		$('#SIP_acc_status').val(php_value['Acc_Status']);
		$('#SIP_Directory').val(php_value['Directory']);
                $('#SIP_URI').val(php_value['uri']);
		$('#SIP_auth_name').val(php_value['Auth_usr']);
		$('#SIP_auth_pwd').val('<EMPTY>').css('color','red');
		$('#SIP_outbound_proxy').val(php_value['OutboundProxy']);
		$('#SIP_outbound_proxy_port').val(php_value['OutboundProxyPort']);
		$('#SIP_proxy').val(php_value['ProxyServer']);
		$('#SIP_proxy_port').val(php_value['ProxyServerPort']);
		$('#SIP_registrar').val(php_value['RegistrarServer']);
		$('#SIP_registrar_port').val(php_value['RegistrarServerPort']);
		$('#SIP_domainName').val(php_value['UserAgentDomain']);
		$('#SIP_digitMap').val(php_value['DigitMap']);
		$('#SIP_Emer_digitMap').val(php_value['EmergencyDigitMap']);
		if(php_value['Acc_Enabled']=='Enabled')
			$('#SIP_acc_enabled').prop('checked', true);
		if(php_value['sip_status'] == 'Started'|| php_value['sip_status'] =='Starting' )
			$('#SIP_Start').prop('disabled', true);
		else if(php_value['sip_status'] == 'Stopped' || php_value['sip_status'] =='Stopping')
			$('#SIP_Stop').prop('disabled', true);
		checkboxes();
	}
        $('#SIP_auth_pwd').on('click',function(){
		$(this).val('').css('color','black');
	});
	$("#password_show").change(function() {
		var pass_val = $("#SIP_auth_pwd").val();
		if ($("#password_show").is(":checked")) {
			$("#SIP_auth_pwd").attr('type','text');
			$("#SIP_auth_pwd").val(pass_val);
		}
		else {
			$("#SIP_auth_pwd").attr('type','password');
			$("#SIP_auth_pwd").val(pass_val);
		}
	});
	function checkboxes(){
		if(php_value['OutboundProxy']!="" || php_value['OutboundProxyPort']!=0){
			$('#u_SIP_outbound_proxy').prop('checked', true);
			$('#SIP_outbound_proxy, #SIP_outbound_proxy_port').prop("disabled", false);
		}
		else{
			$('#u_SIP_outbound_proxy').prop('checked', false);
			$('#SIP_outbound_proxy, #SIP_outbound_proxy_port').prop("disabled", true);
		}

		if(php_value['ProxyServer']!="" || php_value['ProxyServerPort']!=0){
			$('#u_SIP_proxy').prop('checked', true);
			$('#SIP_proxy, #SIP_proxy_port').prop("disabled", false);
		}
		else{
			$('#u_SIP_proxy').prop('checked', false);
			$('#SIP_proxy, #SIP_proxy_port ').prop("disabled", true);
		}

		if(php_value['RegistrarServer']!="" || php_value['RegistrarServerPort']!=0){
			$('#u_SIP_registrar').prop('checked', true);
			$('#SIP_registrar, #SIP_registrar_port').prop("disabled", false);
		}
		else{
			$('#u_SIP_registrar').prop('checked', false);
			$('#SIP_registrar, #SIP_registrar_port').prop("disabled", true);
		}	
	}
	$('#u_SIP_proxy').on('click', function(){
		if($('#u_SIP_proxy').is(':checked')){
			$('#SIP_proxy, #SIP_proxy_port').prop("disabled", false);
			$('#SIP_proxy').val(php_value['ProxyServer']);
		    $('#SIP_proxy_port').val(php_value['ProxyServerPort']);
		}
		else{
			$('#SIP_proxy, #SIP_proxy_port ').prop("disabled", true);
			$('#SIP_proxy').val('');
		    $('#SIP_proxy_port').val(0);
		}
	});	
	$('#u_SIP_registrar').on('click', function(){
		if($('#u_SIP_registrar').is(':checked')){
			$('#SIP_registrar, #SIP_registrar_port').prop("disabled", false);
			$('#SIP_registrar').val(php_value['RegistrarServer']);
		    $('#SIP_registrar_port').val(php_value['RegistrarServerPort']);
		}
		else{
			$('#SIP_registrar, #SIP_registrar_port').prop("disabled", true);
			$('#SIP_registrar').val('');
		    $('#SIP_registrar_port').val(0);
		}		
	});	
	$('#u_SIP_outbound_proxy').on('click', function(){
		if($('#u_SIP_outbound_proxy').is(':checked')){
			$('#SIP_outbound_proxy, #SIP_outbound_proxy_port').prop("disabled", false);
			$('#SIP_outbound_proxy').val(php_value['OutboundProxy']);
		    $('#SIP_outbound_proxy_port').val(php_value['OutboundProxyPort']);
		}
		else{
			$('#SIP_outbound_proxy, #SIP_outbound_proxy_port').prop("disabled", true);
			$('#SIP_outbound_proxy').val('');
			$('#SIP_outbound_proxy_port').val(0);
		}
	});
	$('#SIP_Apply').on('click', function(){
		var Authpwd = $('#SIP_auth_pwd').val();
		if(Authpwd == '<EMPTY>')Authpwd = "";
		var mod_values='{"Auth_usr":"'+$('#SIP_auth_name').val()
			+'","Auth_pwd":"'+Authpwd
			+'","OutboundProxy":"'+ $('#SIP_outbound_proxy').val()
			+'","OutboundProxyPort":"'+$('#SIP_outbound_proxy_port').val()
			+'","ProxyServer":"'+$('#SIP_proxy').val()
			+'","ProxyServerPort":"'+$('#SIP_proxy_port').val()
			+'","ProxyServer":"'+$('#SIP_proxy').val()
			+'","ProxyServerPort":"'+$('#SIP_proxy_port').val()
			+'","RegistrarServer":"'+$('#SIP_registrar').val()
			+'","RegistrarServerPort":"'+$('#SIP_registrar_port').val()
			+'","UserAgentDomain":"'+$('#SIP_domainName').val()
			+'","DigitMap":"'+$('#SIP_digitMap').val()
			+'","SDigitTimer":"'+$('#S_digit_timer').val()
			+'","ZDigitTimer":"'+$('#Z_digit_timer').val()
			+'","Acc_Enabled":"'+($('#SIP_acc_enabled').is(':checked')? 'Enabled' :'Disabled')
			+'","Directory":"'+$('#SIP_Directory').val()
                        +'","uri":"'+$('#SIP_URI').val()
			+'","EmergencyDigitMap":"'+$('#SIP_Emer_digitMap').val()+'"}';
         	var jsConfig = mod_values;
        	jProgress("<?php echo _('This may take several seconds...')?>", 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_SIPBasic_ServiceProvider.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_SipBasic_ServiceProvider.php';
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
			url: "actionHandler/ajaxSet_SIPBasic_ServiceProvider.php",
			data: { configInfo: jsConfig },
			success: function(msg) {
				jHide();
				msg_parseJSON = $.parseJSON(msg);
				if(msg_parseJSON.error_message){
					jAlert(msg_parseJSON.error_message);
					setTimeout(page_load_values,50);
				} else location.href = 'Voip_SipBasic_ServiceProvider.php';
			},
			error: function(){
				jHide();
			}
		});
	}
	setTimeout(page_load_values,50);
</script>
<?php include('includes/footer.php'); ?>
