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
<!-- $Id: at_a_glance.dory.php 2943 2009-08-25 20:58:43Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php
$email_notif_param = array(
        "recipient_mail"    => "Device.X_CISCO_COM_Security.EmailSendTo",
        "firewall_breach"   => "Device.X_CISCO_COM_Security.EmailFirewallBreach",
        "parental_breach"   => "Device.X_CISCO_COM_Security.EmailParentalControlBreach",
        "alerts_warnings"   => "Device.X_CISCO_COM_Security.EmailAlertsOrWarnings",
        "send_logs"         => "Device.X_CISCO_COM_Security.EmailSendLogs",
        "smtp_address"      => "Device.X_CISCO_COM_Security.EmailServer",
        "address"   => "Device.X_CISCO_COM_Security.EmailFromAddress",
        "username"  => "Device.X_CISCO_COM_Security.EmailUserName",
        "password"  => "Device.X_CISCO_COM_Security.EmailPassword",
	);
    $email_notif_value = KeyExtGet("Device.X_CISCO_COM_Security.", $email_notif_param);
$recipient_mail = 	$email_notif_value["recipient_mail"]; //getStr("Device.X_CISCO_COM_Security.EmailSendTo");
$firewall_breach = 	$email_notif_value["firewall_breach"]; //getStr("Device.X_CISCO_COM_Security.EmailFirewallBreach");
$parental_breach = 	$email_notif_value["parental_breach"]; //getStr("Device.X_CISCO_COM_Security.EmailParentalControlBreach");
$alerts_warnings = 	$email_notif_value["alerts_warnings"]; //getStr("Device.X_CISCO_COM_Security.EmailAlertsOrWarnings");
$send_logs = 		$email_notif_value["send_logs"];       //getStr("Device.X_CISCO_COM_Security.EmailSendLogs");
$smtp_address = 	$email_notif_value["smtp_address"]; //getStr("Device.X_CISCO_COM_Security.EmailServer");
$address = 	$email_notif_value["address"]; //getStr("Device.X_CISCO_COM_Security.EmailFromAddress");
$username = $email_notif_value["username"]; //getStr("Device.X_CISCO_COM_Security.EmailUserName");
$password = $email_notif_value["password"]; //getStr("Device.X_CISCO_COM_Security.EmailPassword");
// $recipient_mail = 	"string1";
// $firewall_breach = 	"true";
// $parental_breach = 	"true";
// $alerts_warnings = 	"false";
// $send_logs = 		"true";
// $smtp_address = 	"string2";
// $address = 	"string3";
// $username = "string4";
// $password = "string5";
?>
<script type="text/javascript">
var o_firewallbreach = <?php echo $firewall_breach === 'true' ? 'true' : 'false';?>;
var o_pcbreach = <?php echo $parental_breach === 'true' ? 'true' : 'false';?>;
var o_alertwarning = <?php echo $alerts_warnings === 'true' ? 'true' : 'false';?>;
var o_sendlogs = <?php echo $send_logs === 'true' ? 'true' : 'false';?>;
$(document).ready(function() {
	comcast.page.init("Gateway > Email Notification", "nav-email-notification");	
	$("#firewallbreach_switch").radioswitch({
		id: "firewallbreach-switch",
		radio_name: "block1",
		id_on: "firewall_breach",
		id_off: "block1_no",
		title_on: "Select firewall breach",
		title_off: "Unselect firewall breach",
		size: "small",
		label_on: "Yes",
		label_off: "No",
		revertOrder: true,
		state: o_firewallbreach ? "on" : "off"
	});
	$("#pcbreach_switch").radioswitch({
		id: "pcbreach-switch",
		radio_name: "block2",
		id_on: "parental_breach",
		id_off: "block2_no",
		title_on: "Select parental control breach",
		title_off: "Unselect parental control breach",
		size: "small",
		label_on: "Yes",
		label_off: "No",
		revertOrder: true,
		state: o_pcbreach ? "on" : "off"
	});
	$("#alert_switch").radioswitch({
		id: "alert-switch",
		radio_name: "block3",
		id_on: "alerts_warnings",
		id_off: "block3_no",
		title_on: "Select alerts or warnings",
		title_off: "Unselect alerts or warnings",
		size: "small",
		label_on: "Yes",
		label_off: "No",
		revertOrder: true,
		state: o_alertwarning ? "on" : "off"
	});
	$("#log_switch").radioswitch({
		id: "log-switch",
		radio_name: "block",
		id_on: "send_logs",
		id_off: "block_no",
		title_on: "Select send logs",
		title_off: "Unselect send logs",
		size: "small",
		label_on: "Yes",
		label_off: "No",
		revertOrder: true,
		state: o_sendlogs ? "on" : "off"
	});
	jQuery.validator.addMethod (
		"isIP", 
		function(value, element) { 
			var ip = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/; 
			var ipArr = value.split(".");
			if ((ipArr[0] == 0 && ipArr[1] == 0 && ipArr[2] == 0 && ipArr[3] == 0) ||
				(ipArr[0] == 255 && ipArr[1] == 255 && ipArr[2] == 255 && ipArr[3] == 255) ) 
			{
				return false
			}
			return this.optional(element) || ip.test(value);
		}, 
		"IP format must be 0.0.0.0~255.255.255.255"
	); 
	jQuery.validator.addMethod (
		"email_domain", 
		function(value, element) {
			var prefix = value.substr(0,value.indexOf("@"));
			var suffix = value.substr(value.indexOf("@")+1);
			return this.optional(element) || (suffix.indexOf("--")==-1 && suffix.indexOf("~")==-1 && suffix.length<60 && /^[0-9a-zA-Z]/i.test(prefix))
		}, 
		'Email address must be in the format of name@domain.com, upto 60 char'
	); 
	$("#pageForm").validate({
    	debug: true,
		rules: {
			recipient_mail: {
				required: true,
				email: true,
				email_domain: true,
				allowed_char: true
			},
			address: {
				required: true,
				email: true,
				email_domain: true,
				allowed_char: true
			},
			smtp_address: {
				required: true,
				isIP: true
			},
			username: {
				allowed_char: true
			},
			password: {
				allowed_char: true
			}
		},
		messages: {
			recipient_mail: {
			required: "We need an email address to send to",
			email: 'Email address must be in the format of name@domain.com, upto 60 char'
			},
			address: {
			required: "We need an email address to send to",
			email: 'Email address must be in the format of name@domain.com, upto 60 char'
			}
		},
		submitHandler: function() {
			button_save();
		}
	});
});
function button_save()
{
	var recipient_mail = 	$("#recipient_mail").val();
	var firewall_breach = 	$("#firewallbreach_switch").radioswitch("getState").on;
	var parental_breach = 	$("#pcbreach_switch").radioswitch("getState").on;
	var alerts_warnings = 	$("#alert_switch").radioswitch("getState").on;
	var send_logs = 		$("#log_switch").radioswitch("getState").on;
	var smtp_address = 		$("#smtp_address").val();
	var address = 	$("#address").val();
	var username = 	$("#username").val();
	var password = 	$("#password").val();
	var jsConfig = '{"recipient_mail":"'+recipient_mail+'", "firewall_breach":"'+firewall_breach+'", "parental_breach":"'+parental_breach 
		+'", "alerts_warnings":"'+alerts_warnings+'", "send_logs":"'+send_logs+'", "smtp_address":"'+smtp_address 
		+'", "address":"'+address+'", "username":"'+username+'", "password":"'+password+'"}';	
	// alert(jsConfig);
	jProgress('This may take several seconds...', 60);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_email_notification.php",
		data: { configInfo: jsConfig },
		success: function(msg)
		{            
			jHide();
		},
		error: function(){            
			jHide();
			jAlert("Failure, please try again.");
		}
	});
}
</script>
<div id="content">
	<h1>Gateway > Email Notification</h1>
	<div id="educational-tip">
		<p class="tip">Configure email notification.</p>
		<p class="hidden"> <strong>Recepient Email:</strong> Enter the Recipient Email to receive the log.</p>
		<p class="hidden"> <strong>Notification Types:</strong> Click the button to select the notification types of log to send or not.</p>
		<p class="hidden"> <strong>Mail Server Configuration:</strong> Configure the mail server settings</p>
	</div>
	<div class="module">
		<form action="#TBD" id="pageForm" method="post">
			<div class="forms">
				<h2>Email Notification</h2>
				<div class="form-row">
					<label for="recipient_mail">Recipient Email:</label>
					<input type="text" id="recipient_mail" name="recipient_mail" value="<?php echo $recipient_mail;?>" >
				</div>
				<div class="form-row odd">
				<label for="notification_types">Notification Types:</label></div>
				<div class="form-row" style='padding:8px 0px'>
					<table >
						<tr class="odd">
							<td style='padding:1px 149px'><label for="on">Firewall Breach&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
							<span id="firewallbreach_switch"></span>
							</td>
						</tr>
						<tr >
							<td style='padding:1px 149px'><label for="on">Parental Control Breach&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
							<span id="pcbreach_switch"></span>
							</td>
						</tr>
						<tr class="odd">
							<td style='padding:1px 149px'><label for="on">Alerts or Warnings&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
							<span id="alert_switch"></span>
							</td>
						</tr>
						<tr >
							<td style='padding:1px 149px'><label for="on">Send Logs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
							<span id="log_switch"></span>
							</td>
						</tr>
					</table>
				</div>
			</div> <!-- end .form -->
			<div class="forms data">
				<h2>Mail Server Configuration</h2>
				<div class="form-row"><label for="smtp_address">SMTP Server Address:</label>
					<input type="text" id="smtp_address" name="smtp_address" value="<?php echo $smtp_address;?>" >
				</div>
				<div class="form-row odd"><label for="address">Comcast Email Address:</label>
					<input type="text" id="address" name="address" value="<?php echo $address;?>" >
				</div>
				<div class="form-row"><label for="username">Comcast Username:</label>
					<input type="text" id="username" name="username" value="<?php echo $username;?>" >
				</div>
				<div class="form-row odd"><label for="password">Comcast Password:</label>
					<input type="password" id="password" name="password" value="<?php echo $password;?>" >
				</div>
			</div> <!-- end .module -->
			<div class="form-btn">
				<input type="submit" class="btn submit"    value="Save"   id="button_save" />
				<input type="button" class="btn alt reset" value="Cancel" id="button_cancel" name="button_cancel" onClick="location.reload();" />
			</div>		
		</form>
	</div><!-- end #content -->
</div>
<?php include('includes/footer.php'); ?>
