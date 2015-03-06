<?php include('includes/header.php'); ?>

<!-- $Id: at_a_glance.dory.php 2943 2009-08-25 20:58:43Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
<?php
$recipient_mail = 	getStr("Device.X_CISCO_COM_Security.EmailSendTo");
$firewall_breach = 	getStr("Device.X_CISCO_COM_Security.EmailFirewallBreach");
$parental_breach = 	getStr("Device.X_CISCO_COM_Security.EmailParentalControlBreach");
$alerts_warnings = 	getStr("Device.X_CISCO_COM_Security.EmailAlertsOrWarnings");
$send_logs = 		getStr("Device.X_CISCO_COM_Security.EmailSendLogs");
$smtp_address = 	getStr("Device.X_CISCO_COM_Security.EmailServer");
$comcast_address = 	getStr("Device.X_CISCO_COM_Security.EmailFromAddress");
$comcast_username = getStr("Device.X_CISCO_COM_Security.EmailUserName");
$comcast_password = getStr("Device.X_CISCO_COM_Security.EmailPassword");

// $recipient_mail = 	"string1";
// $firewall_breach = 	"true";
// $parental_breach = 	"true";
// $alerts_warnings = 	"false";
// $send_logs = 		"true";
// $smtp_address = 	"string2";
// $comcast_address = 	"string3";
// $comcast_username = "string4";
// $comcast_password = "string5";
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
			email_domain: true
			},
			comcast_address: {
			required: true,
			email: true,
			email_domain: true
			},
			smtp_address: {
			required: true,
			isIP: true
			}
		},
		messages: {
			recipient_mail: {
			required: "We need an email address to send to",
			email: 'Email address must be in the format of name@domain.com, upto 60 char'
			},
			comcast_address: {
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
	var comcast_address = 	$("#comcast_address").val();
	var comcast_username = 	$("#comcast_username").val();
	var comcast_password = 	$("#comcast_password").val();

	var jsConfig = '{"recipient_mail":"'+recipient_mail+'", "firewall_breach":"'+firewall_breach+'", "parental_breach":"'+parental_breach 
		+'", "alerts_warnings":"'+alerts_warnings+'", "send_logs":"'+send_logs+'", "smtp_address":"'+smtp_address 
		+'", "comcast_address":"'+comcast_address+'", "comcast_username":"'+comcast_username+'", "comcast_password":"'+comcast_password+'"}';	
		
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
				<div class="form-row">
					<table id="add_allowed_device" class="data">
						<tr>
							<td class="data" colspan="30">Notification Types:</td>
						</tr>
						<tr class="odd">
							<td><label for="on">Firewall Breach&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
							<span id="firewallbreach_switch"></span>
							</td>
						</tr>
						<tr >
							<td><label for="on">Parental Control Breach&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
							<span id="pcbreach_switch"></span>
							</td>
						</tr>
						<tr class="odd">
							<td><label for="on">Alerts or Warnings&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
							<span id="alert_switch"></span>
							</td>
						</tr>
						<tr >
							<td><label for="on">Send Logs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
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
				<div class="form-row odd"><label for="comcast_address">Comcast Email Address:</label>
					<input type="text" id="comcast_address" name="comcast_address" value="<?php echo $comcast_address;?>" >
				</div>
				<div class="form-row"><label for="comcast_username">Comcast Username:</label>
					<input type="text" id="comcast_username" name="comcast_username" value="<?php echo $comcast_username;?>" >
				</div>
				<div class="form-row odd"><label for="comcast_password">Comcast Password:</label>
					<input type="password" id="comcast_password" name="comcast_password" value="<?php echo $comcast_password;?>" >
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

