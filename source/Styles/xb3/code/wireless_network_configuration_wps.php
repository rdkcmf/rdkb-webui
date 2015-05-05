<?php include('includes/header.php'); ?>

<!-- $Id: wireless_network_configuration_wps.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
// $ssids			= explode(",", getInstanceIds("Device.WiFi.SSID."));
$ssids			= explode(",", "1,2");	//Currently, only SSID.1(2.4G) and SSID.2(5G) are involved with WPS
$wps_enabled	= "false";
$wps_pin		= "";
$wps_method		= "PushButton";
$f_e_ssid		= "1";
// $wps_enabled	= "true";

//get the first WPS enabled SSID, in principle all WPS should be enabled or disabled simultaneously
foreach ($ssids as $i){
	if ("true" == getStr("Device.WiFi.AccessPoint.$i.WPS.Enable")){
		$wps_enabled	= "true";
		$wps_pin		= getStr("Device.WiFi.AccessPoint.$i.WPS.X_CISCO_COM_Pin");
		$wps_method		= getStr("Device.WiFi.AccessPoint.$i.WPS.ConfigMethodsEnabled");
		$f_e_ssid		= $i;
		break;
	}
}

//if wps_config is false, then show WPS disabled, and do not allow to enable it
$wps_config = "false";
foreach ($ssids as $i){
	if ("true"==getStr("Device.WiFi.SSID.$i.Enable") && "true"==getStr("Device.WiFi.Radio.".(2-intval($i)%2).".Enable")){
		$wps_config	= "true";
		$encrypt_mode	= getStr("Device.WiFi.AccessPoint.$i.Security.ModeEnabled");
		$encrypt_method	= getStr("Device.WiFi.AccessPoint.$i.Security.X_CISCO_COM_EncryptionMethod");
		$broadcastSSID	= getStr("Device.WiFi.AccessPoint.$i.SSIDAdvertisementEnabled");
		$filter_enable	= getStr("Device.WiFi.AccessPoint.$i.X_CISCO_COM_MACFilter.Enable");
		if (strstr($encrypt_mode, "WEP") || (strstr($encrypt_mode, "WPA") && $encrypt_method=="TKIP") || "false"==$broadcastSSID || "true"==$filter_enable){
			$wps_config	= "false";
			break;
		}
	}
}
// $wps_config = "true";
if ($_DEBUG) {
	$wps_enabled = "false";
	$wps_pin = "";
	$wps_method = "PushButton";
	$f_e_ssid = "1";
	$wps_config = "false";
}

?>

<script type="text/javascript">
function validChecksum(PIN)
{
	if (""==PIN) return false;
	var accum = 0;
	accum += 3 * (parseInt(PIN / 10000000) % 10);
	accum += 1 * (parseInt(PIN / 1000000) % 10);
	accum += 3 * (parseInt(PIN / 100000) % 10);
	accum += 1 * (parseInt(PIN / 10000) % 10);
	accum += 3 * (parseInt(PIN / 1000) % 10);
	accum += 1 * (parseInt(PIN / 100) % 10);
	accum += 3 * (parseInt(PIN / 10) % 10);
	accum += 1 * (parseInt(PIN / 1) % 10);
	return (0 == (accum % 10));
}

function set_config(target)
{
	var ssid_number	= $("#wps_ssid").attr("value");
	var wps_enabled	= $("#wps_switch").radioswitch("getState").on;
	var wps_method	= $("#pin_switch").radioswitch("getState").on ? "PushButton,PIN" : "PushButton";
	var pair_method = $("#connection_options").val();
	var pin_number	= $("#pin_number").attr("value");
		
	var jsConfig 	=	'{"ssid_number":"'+ssid_number
		+'", "target":"'+target
		+'", "wps_enabled":"'+wps_enabled
		+'", "wps_method":"'+wps_method
		+'", "pair_method":"'+pair_method
		+'", "pin_number":"'+pin_number
		+'"}';
			
	jProgress('This may take several seconds...', 60);
	
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_wps_config.php",
		data: { configInfo: jsConfig },
		success: function(msg) {
			jHide();
			if ("pair_client"==target){
				jAlert("WPS in Progress!");
			}
			if ("wps_enabled"==target || "wps_method"==target){
				location.reload();
			}			
		},
		error: function(){            
			jHide();
			jAlert("Failure, please try again.");
		}
	});
}

$(document).ready(function() {
    comcast.page.init("Gateway > Connection > Wireless > Add Wireless Client", "nav-wifi-config");

	var G_wps_enabled	= <?php echo ($wps_enabled === "true" ? "true" : "false"); ?>;
	var G_wps_method	= "<?php echo $wps_method; ?>";

	$("#wps_switch").radioswitch({
		id: "wps-switch",
		radio_name: "wps",
		id_on: "wps_enabled",
		id_off: "wps_disabled",
		title_on: "Enable WPS",
		title_off: "Disable WPS",
		state: G_wps_enabled ? "on" : "off"
	});
	$("#pin_switch").radioswitch({
		id: "pin-switch",
		radio_name: "pin_switch",
		id_on: "pin_enable",
		id_off: "pin_disable",
		title_on: "Enable WPS PIN",
		title_off: "Disable WPS PIN",
		state: G_wps_method !== "PushButton" ? "on" : "off"
	});
	
    $("#wps_switch").change(function(e, skipSave) {
		var wps_enabled = $(this).radioswitch("getState").on;

		if (wps_enabled) {
			$("#wps_form *").not(".radioswitch_cont, .radioswitch_cont *").removeClass("disabled").prop("disabled", false);
			$("#pin_switch").radioswitch("doEnable", true);
		}
		else {
			$("#wps_form *").not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled").prop("disabled", true);
			$("#pin_switch").radioswitch("doEnable", false);
			$("#wps_switch").radioswitch("doEnable", true);		//enable wps switch itself when keyboard
		}
		
		if (skipSave) return;
		
		if (wps_enabled == G_wps_enabled) return;
		set_config("wps_enabled");
		G_wps_enabled = wps_enabled;	
	});
	
    $("#pin_switch").change(function(e, skipSave) {
		var wps_method = $(this).radioswitch("getState").on ? "PushButton,PIN" : "PushButton";
		
		$("#connection_options").val("PushButton");	//swtich to default
		$("#div_pin_number").hide();
		
		if (wps_method != "PushButton") {	//means PIN is enabled
			$("#pair_method_pin").prop("disabled", false);
		}
		else {
			$("#pair_method_pin").prop("disabled", true);
		}
		
		if (skipSave) return;

		if (wps_method == G_wps_method) return;		
		set_config("wps_method");
		G_wps_method = wps_method;
	});
	
    $("#connection_options").change(function() {
		var pair_method = $("#connection_options").val();
	
		if (pair_method != "PushButton") {	//means PIN is current method
			$("#div_pin_number").show();
		}
		else {
			$("#div_pin_number").hide();
		}
	});	
	
	$("#wps_pair").click(function(){
		if (("PushButton" != $("#connection_options").val()) && !validChecksum($("#pin_number").attr("value")) ){
			jAlert("Invalid PIN!");
			return;
		}
		set_config("pair_client");
	});
	
	$("#wps_cancel").click(function(){
		jConfirm(
			"Are you sure you want to cancel WPS progress?"
			,"Confirm:"
			,function(ret) {
				if(ret) {
					set_config("pair_cancel");
				}
			}
		);
	});

	$("#wps_switch").trigger("change", [true]);
	$("#pin_switch").trigger("change", [true]);
	
	if ("false"=="<?php echo $wps_config;?>"){
	/*
		$(".wps_config").html('<h2>Add Wi-Fi Client (WPS)</h2>\
		<p style="color:red;font-size:130%;font-style:bold;">WPS function is disabled and can not be enabled now!</p>\
		<p>You can take these steps to enable WPS:</p>\
		<p/>(1) enable at least one private Wi-Fi interface</p>\
		<p/>(2) its security mode is not WEP/WPA-TKIP/WPA2-TKIP</p>\
		<p/>(3) its network name is not hidden</p>\
		<p/>(4) its MAC filter function is not enabled</p>\
		<p/>Then please refresh(or back to) this page and try again.</p>');
		return;
	*/
		$(".wps_config *").not(".radioswitch_cont, .radioswitch_cont *").unbind("click").prop("disabled", true).addClass("disabled").removeClass("selected");
		$(".wps_config .radioswitch_cont").radioswitch("doEnable", false);
	}
});
</script>

<div id="content">
	<h1>Gateway > Connection > Wi-Fi > Add Wi-Fi Client</h1>
	<div id="educational-tip">
		<p class="tip">If a Wi-Fi device supports Wi-Fi Protected Setup (WPS), use the Gateway's WPS feature to simplify connection to your network.</p>
		<p class="hidden">WPS is a standard for easy setup of secure wireless networks. To add a Wi-Fi device to your network, choose a WPS connection option, depending on your product.</p>
		<p class="hidden"><strong>Push Button:</strong> Press the WPS Button on the Gateway's top panel, or click the PAIR  button on this page. Within 2 minutes, press the WPS push button (either a physical button or a virtual button via software) on the Wi-Fi device to connect to the Gateway.</p>
		<p class="hidden"><strong>PIN Connectivity:</strong> For WPS capable devices supporting PIN, select <i>PIN Number</i> for <strong>Connection Options.</strong> Enter the PIN number generated by the wireless device in the <strong>Wireless Client's PIN</strong> field and click PAIR. If prompted for a PIN, enter the PIN from the label on the Gateway's bottom panel.</p>
	</div>

	<form method="post" id="wps_form">
		<div class="module forms enable wps_config">
			<h2>Add Wi-Fi Client (WPS)</h2>
			<div class="form-row" style="display: none;">
				<label for="ssid">SSID:</label>
				<select name="ssid" id="wps_ssid">
					<option value="<?php echo $f_e_ssid; ?>" selected="selected"><?php echo $f_e_ssid; ?></option>
				</select>
			</div>
			<div class="form-row">
				<span class="readonlyLabel label"> Wi-Fi Protected Setup (WPS):</span>
				<span id="wps_switch"></span>
			</div>
			<div class="form-row odd">
				<span class="readonlyLabel">AP PIN:</span> 
				<span class="value" id="wps_pin">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $wps_pin; ?></span>
			</div>
			<div class="form-row">
				<span class="readonlyLabel label">WPS Pin Method:</span>
				<span id="pin_switch"></span>
			</div>
			<div id="opt_switch" class="form-row odd">
				<label for="connection_options">Connection Options:</label>
				<select class="valid" id="connection_options">
					<option id="pair_method_push" value="PushButton" selected="selected">Push Button</option>
					<option id="pair_method_pin"  value="PIN">PIN Method</option>
				</select>
				<p class="footnote">To pair, select the Pair button and your wireless device will connect within two minutes.</p>
				<div id="div_pin_number" class="form-row">
					<label for="pin_number">Wireless Client's PIN:</label>
					<input type="text" id="pin_number" name="pin_number" class="text" />
				</div>			
			</div>
			<div class="form-row form-btn">
				<input id="wps_pair"   name="wps_pair"   type="button" value="Pair" class="btn" size="3" />
				<input id="wps_cancel" name="wps_cancel" type="button" value="CANCEL " class="btn" />
			</div>
		</div>
	</form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
