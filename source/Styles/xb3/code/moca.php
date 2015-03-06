<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: wireless_network_configuration.usg.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php 
	$ret = init_psmMode("Gateway > Connection > MoCA", "nav-moca");
	if ("" != $ret){echo $ret;	return;}

$moca_enable	= getStr("Device.MoCA.Interface.1.Enable");
$qos_enable 	= "true";
?>

<style type="text/css">

label{
	margin-right: 10px !important;
}

#content {
	display: none;
}

.moca_row1 {
	position:relative;
	top:0px;
	left:0px;
}

.moca_row2 {
	position:relative;
	top:0px;
	left:230px;
}
</style>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Connection > MoCA", "nav-moca");
	
	$("#moca_switch").radioswitch({
		id: "moca-switch",
		radio_name: "enable_moca",
		id_on: "moca_enable",
		id_off: "moca_disable",
		title_on: "Enable MoCA",
		title_off: "Disable MoCA",
		state: <?php echo ($moca_enable === "true" ? "true" : "false"); ?> ? "on" : "off"
	});

	$("#qos_switch").radioswitch({
		id: "qos-switch",
		radio_name: "enable_moca1",
		id_on: "qos_enable",
		id_off: "qos_disable",
		title_on: "Enable QoS for MoCA",
		title_off: "Disable QoS for MoCA",
		state: <?php echo ($qos_enable === "true" ? "true" : "false"); ?> ? "on" : "off"
	});

	// $('#div_channel_switch').change(function()	//this is not compatible with IE8
	$(':input[name="channel_switch"]').change(function() //this will act twice
	{
		if ($("#scan_auto").prop("checked"))
		{
			$("#mode_option").prop("disabled", true);
		}
		else
		{
			$("#mode_option").prop("disabled", false);
		}
	});
	
	$(':input[name="taboo_switch"]').change(function()
	{
		if ($("#taboo_enable").prop("checked"))
		{
			$("[type='checkbox']:not('#password_show')").prop("disabled", false);
		}
		else
		{
			$("[type='checkbox']:not('#password_show')").prop("disabled", true);
		}
	});
	
	$(':input[name="privacy_switch"]').change(function()
	{
		if ($("#privacy_enable").prop("checked"))
		{
			$('#net_password').prop("disabled", false);
			$('#password_show').prop("disabled", false);
		}
		else
		{
			$('#net_password').prop("disabled", true);
			$('#password_show').prop("disabled", true);
		}
	});

 	$("#password_show").change(function() {
		if($("#password_show").prop("checked")) {
			document.getElementById("password_field").innerHTML = 
			'<input type="text"     size="23" id="net_password" name="net_password" class="text" value="' + $("#net_password").val() + '" />'
		}
		else {
			document.getElementById("password_field").innerHTML = 
			'<input type="password" size="23" id="net_password" name="net_password" class="text" value="' + $("#net_password").val() + '" />'
		}
	});
	
	//do has order!!!
	$("#moca_switch").change(function()
	{
		if ($(this).radioswitch("getState").on)
		{
			$(':input').not(".radioswitch_cont input").prop("disabled", false);
			$(':input[name="channel_switch"]').change();
			$(':input[name="taboo_switch"]').change();
			$(':input[name="privacy_switch"]').change();
		}
		else
		{
			$(':input:not("#submit_moca")').not(".radioswitch_cont input").prop("disabled", true);
		}	
	}).trigger("change");

    $("#pageForm").validate({
		rules: {
			net_password: {
				required: true
				,digits: true
				,maxlength: 17
				,minlength: 12
			}
		},
		
		submitHandler:function(form){
			next_step();
		}
    });

	// remove sections as per loginuser, content must be hidden before doc ready
	if ("admin" == "<?php echo $_SESSION["loginuser"]; ?>"){
		$("#div_channel_switch").hide();
		$("#div_channel_select").hide();
		$("#div_beacon_select").hide();
		$("#div_taboo_switch").hide();
		$("#div_taboo_list").hide();
		$("#div_nc_switch").hide();
		$("#div_qos_switch").hide();
		// for GUI version 3.0
		$("#privacy_switch").hide();
		$("#netPassword").hide();
		$("#net_password").prop("disabled", true);
	}
	else{
		$("#div_channel_status").hide();
		$("#div_nc_status").hide();
		// for GUI version 3.0
		$("#div_taboo_switch").hide();
		$("#div_qos_switch").hide();
	}
	
	//re-style each div
	$('.module div').removeClass("odd");
	$('.module > div:odd').addClass("odd");
	
	// now we can show target content
	$("#content").show();
});

function next_step() 
{
	var moca_enable		= $("#moca_switch").radioswitch("getState").on;
	var scan_method		= $("#scan_auto").prop("checked");
	var channel			= $('#mode_option').attr("value"); 
	var beacon_power	= $('#beacon_power').attr("value");
	var taboo_enable	= $("#taboo_enable").prop("checked");
	var taboo_freq 		= "0000000000000000";
	var nc_enable		= $("#nc_enable").prop("checked");
	var privacy_enable	= $("#privacy_enable").prop("checked");
	var net_password	= $('#net_password').attr("value");
	var qos_enable 		= $("#qos_switch").radioswitch("getState").on;
	
	function js_str_and(a, b)	//js bit-and limited to 32, I have to write this
	{
		var c = String("");
		for (var i=0; i<Math.max(a.length, b.length); i++)
		{
			c += (parseInt(a.substr(i,1), 16) | parseInt(b.substr(i,1), 16)).toString(16);
		}
		return c;
	}
	
	for (var i=1; i<21; i++)
	{
		$("#"+i).prop("checked") && (taboo_freq = js_str_and(taboo_freq, $("#"+i).val()));
	}
	
	if (true==taboo_enable && false==scan_method && js_str_and(channel, taboo_freq) == taboo_freq)
	{
		jAlert("In manual mode: Taboo frequency must exclude current channel!");
		return;
	}
	
	if (true==taboo_enable && ($(".moca11:not(:checked)").length < 1 || $(".moca20:not(:checked)").length < 1))
	{
		jAlert("Can't disable all MoCA 1.1 (or 2.0) frequency at the same time!");
		return;
	}

	var jsConfig = '{"moca_enable": "' + moca_enable 
	+ '", "scan_method": "' + scan_method 
	+ '", "channel": "' + channel 
	+ '", "beacon_power": "' + beacon_power 
	+ '", "taboo_enable": "' + taboo_enable 
	+ '", "taboo_freq": "' + taboo_freq 
	+ '", "nc_enable": "' + nc_enable 
	+ '", "privacy_enable": "' + privacy_enable 
	+ '", "net_password": "' + net_password 
	+ '", "qos_enable": "' + qos_enable 
	+'", "thisUser":"'+"<?php echo $_SESSION["loginuser"]; ?>"
	+ '"} ';

	// alert(jsConfig);
	jProgress('Waiting for backend fully executed, please be patient...', 100);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_moca_config.php",
		data: { configInfo: jsConfig },
		success: function() {   
			setTimeout(function(){
				jHide();
			}, 60000);
		},
		error: function(){
			jHide();
			jAlert("Failure, please try again.");
		}
	});
}

</script>

<?php
$scan_method	= getStr("Device.MoCA.Interface.1.X_CISCO_COM_ChannelScanning");
$channel		= getStr("Device.MoCA.Interface.1.CurrentOperFreq");
$beacon_power	= getStr("Device.MoCA.Interface.1.BeaconPowerLimit");
// $taboo_enable	= getStr("Device.MoCA.Interface.1.X_CISCO_COM_EnableTabooBit");
$taboo_freq		= getStr("Device.MoCA.Interface.1.NodeTabooMask");
$nc_enable		= getStr("Device.MoCA.Interface.1.PreferredNC");
$privacy_enable	= getStr("Device.MoCA.Interface.1.PrivacyEnabledSetting");
$net_password	= getStr("Device.MoCA.Interface.1.KeyPassphrase");
// $qos_enable 	= getStr("Device.MoCA.Interface.1.QoS.X_CISCO_COM_Enabled");

// $moca_enable	= "false";
// $scan_method	= "true";
// $channel		= "1275"; 
// $beacon_power	= "false";
$taboo_enable	= "true";
// $taboo_freq		= "FFffAAaa00010000"; 
// $nc_enable		= "false";
// $privacy_enable	= "false";
// $net_password	= "1234567891011";

?>

<div id="content" >
    <h1>Gateway > Connection > MoCA</h1>
	<div id="educational-tip">
		<p class="tip">You have the option to enable or disable the Gateway's MoCA Network. </p>
		<p class="hidden"><strong>MoCA Privacy: </strong> You can enable or disable MoCA Privacy. If Privacy is enabled, all the devices connecting to the Gateway via MoCA will use the MoCA Network Password. </p>
		<p class="hidden"><strong>Network Password:</strong> This is the password for the MoCA network, and will only be used when MoCA Privacy is enabled. </p>
	</div>
	
    <form id="pageForm">
	<fieldset>
    <legend class="acs-hide">MoCA information</legend>

    <div class="module forms enable">
        <h2>MoCA</h2>

		<div class="select-row">
			<label>MoCA:</label>
			<span id="moca_switch"></span>
		</div>
		
		<div class="form-row odd" id="div_channel_switch">
			<label for="channel_selection">Channel Selection:</label>
			<input tabindex='0' id="scan_auto"   type="radio" value="auto"   name="channel_switch" checked="checked">
			<label for="scan_auto" class="acs-hide"></label>
			<b>Scan</b>
			<input id="scan_manual" type="radio" value="manual" name="channel_switch" <?php if ("false"==$scan_method) echo 'checked="checked"'; ?> />
			<label for="scan_manual" class="acs-hide"></label>
			<b>Manual</b>
		</div>
		
		<div class="form-row" id="div_channel_select">
			<label for="mode_option">Channel:</label>
			<select id="mode_option" disabled="disabled">			
				<option disabled="disabled">-- MoCA 1.1 --</option>
				<option id="d1"   value="0000000000004000"  selected="selected"                                          >D1(1150 MHz)</option>       
				<option id="d2"   value="0000000000010000"  <?php if ($channel == "1200") echo 'selected="selected"'; ?> >D2(1200 MHz)</option>       
				<option id="d3"   value="0000000000040000"  <?php if ($channel == "1250") echo 'selected="selected"'; ?> >D3(1250 MHz)</option>       
				<option id="d4"   value="0000000000100000"  <?php if ($channel == "1300") echo 'selected="selected"'; ?> >D4(1300 MHz)</option>       
				<option id="d5"   value="0000000000400000"  <?php if ($channel == "1350") echo 'selected="selected"'; ?> >D5(1350 MHz)</option>       
				<option id="d6"   value="0000000001000000"  <?php if ($channel == "1400") echo 'selected="selected"'; ?> >D6(1400 MHz)</option>       
				<option id="d7"   value="0000000004000000"  <?php if ($channel == "1450") echo 'selected="selected"'; ?> >D7(1450 MHz)</option>       
				<option id="d8"   value="0000000010000000"  <?php if ($channel == "1500") echo 'selected="selected"'; ?> >D8(1500 MHz)</option>       
				<option disabled="disabled">-- MoCA 2.0 --</option>                                                                                           
				<option id="d1a"  value="0000000000008000"  <?php if ($channel == "1175") echo 'selected="selected"'; ?> >D1a(1175 MHz)</option>    
				<option id="d2a"  value="0000000000020000"  <?php if ($channel == "1225") echo 'selected="selected"'; ?> >D2a(1225 MHz)</option>    
				<option id="d3a"  value="0000000000080000"  <?php if ($channel == "1275") echo 'selected="selected"'; ?> >D3a(1275 MHz)</option>    
				<option id="d4a"  value="0000000000200000"  <?php if ($channel == "1325") echo 'selected="selected"'; ?> >D4a(1325 MHz)</option>    
				<option id="d5a"  value="0000000000800000"  <?php if ($channel == "1375") echo 'selected="selected"'; ?> >D5a(1375 MHz)</option>    
				<option id="d6a"  value="0000000002000000"  <?php if ($channel == "1425") echo 'selected="selected"'; ?> >D6a(1425 MHz)</option>    
				<option id="d7a"  value="0000000008000000"  <?php if ($channel == "1475") echo 'selected="selected"'; ?> >D7a(1475 MHz)</option>    
				<option id="d8a"  value="0000000020000000"  <?php if ($channel == "1525") echo 'selected="selected"'; ?> >D8a(1525 MHz)</option>
				<option id="d9"   value="0000000040000000"  <?php if ($channel == "1550") echo 'selected="selected"'; ?> >D9(1550 MHz)</option>				
				<option id="d9a"  value="0000000080000000"  <?php if ($channel == "1575") echo 'selected="selected"'; ?> >D9a(1575 MHz)</option>
				<option id="d10"  value="0000000100000000"  <?php if ($channel == "1600") echo 'selected="selected"'; ?> >D10(1600 MHz)</option>				
				<option id="d10a" value="0000000200000000"  <?php if ($channel == "1625") echo 'selected="selected"'; ?> >D10a(1625 MHz)</option> 
			</select>
		</div>
 
		<div class="form-row odd" id="div_channel_status">
			<label for="mode_opertion">Channel:</label>
			<span class="readonlyValue">
			<?php
			switch ($channel)
			{
			//MoCA 1.1
			case "1150":
				echo "D1(1150 MHz)";
				break;  
			case "1200":
				echo "D2(1200 MHz)";
				break;  
			case "1250":
				echo "D3(1250 MHz)";
				break;
			case "1300":
				echo "D4(1300 MHz)";
				break;
			case "1350":
				echo "D5(1350 MHz)";
				break;
			case "1400":
				echo "D6(1400 MHz)";
				break;
			case "1450":
				echo "D7(1450 MHz)";
				break;
			case "1500":
				echo "D8(1500 MHz)";
				break;
			case "1550":
				echo "D9(1550 MHz)";
				break;
			case "1600":
				echo "D10(1600 MHz)";
				break;
			//MoCA 2.0
			case "1175":
				echo "D1a(1175 MHz)";
				break;
			case "1225":
				echo "D2a(1225 MHz)";
				break;
			case "1275":
				echo "D3a(1275 MHz)";
				break;
			case "1325":
				echo "D4a(1325 MHz)";
				break;
			case "1375":
				echo "D5a(1375 MHz)";
				break;
			case "1425":
				echo "D6a(1425 MHz)";
				break;
			case "1475":
				echo "D7a(1475 MHz)";
				break;
			case "1525":
				echo "D8a(1525 MHz)";
				break;
			case "1575":
				echo "D9a(1575 MHz)";
				break;
			case "1625":
				echo "D10a(1625 MHz)";
				break;
			default:
				echo "D1(1150 MHz)";
			}
			?>			
			</span>
		</div>
		
		<div class="form-row odd" id="div_beacon_select">
			<label for="beacon_power">Beacon Power Reduction(dB):</label>
			<select id="beacon_power">
				<option selected="selected"                                           >0</option>
				<option <?php if ($beacon_power == 3)  echo 'selected="selected"'; ?> >3</option>
				<option <?php if ($beacon_power == 6)  echo 'selected="selected"'; ?> >6</option>
				<option <?php if ($beacon_power == 9)  echo 'selected="selected"'; ?> >9</option>
				<option <?php if ($beacon_power == 12) echo 'selected="selected"'; ?> >12</option>
				<option <?php if ($beacon_power == 15) echo 'selected="selected"'; ?> >15</option>
			</select>
		</div>

		<div class="form-row" id="div_taboo_switch">
			<label>Taboo Bit:</label>
			<input type="radio"  id="taboo_enable"  name="taboo_switch" value="enabled"  checked="checked" />
			<label for="taboo_enable" class="acs-hide"></label>
			<b>Enabled</b>
			<input type="radio"  id="taboo_disable" name="taboo_switch" value="disabled" <?php if ("false"==$taboo_enable) echo 'checked="checked"'; ?> />
			<label for="taboo_disable" class="acs-hide"></label>
			<b>Disabled</b>
		</div>

		<div class="form-row odd" id="div_taboo_list">
			<label>Taboo Frequency:</label>
			<div class="moca_row1">
				<input class="moca11" type="checkbox" id="1"  value="0000000000004000" <?php if (php_str_and($taboo_freq, "0000000000004000") == "0000000000004000") echo "checked=\"checked\""; ?> /> <label for="1" class="acs-hide"></label> <b>D1(1150MHz)</b>
				<input class="moca11" type="checkbox" id="2"  value="0000000000010000" <?php if (php_str_and($taboo_freq, "0000000000010000") == "0000000000010000") echo "checked=\"checked\""; ?> /> <label for="2" class="acs-hide"></label> <b>D2(1200MHz)</b>
				<input class="moca11" type="checkbox" id="3"  value="0000000000040000" <?php if (php_str_and($taboo_freq, "0000000000040000") == "0000000000040000") echo "checked=\"checked\""; ?> /> <label for="3" class="acs-hide"></label> <b>D3(1250MHz)</b>
				<input class="moca11" type="checkbox" id="4"  value="0000000000100000" <?php if (php_str_and($taboo_freq, "0000000000100000") == "0000000000100000") echo "checked=\"checked\""; ?> /> <label for="4" class="acs-hide"></label> <b>D4(1300MHz)</b>
			</div>

			<div class="moca_row1">
				<input class="moca11" type="checkbox" id="5"  value="0000000000400000" <?php if (php_str_and($taboo_freq, "0000000000400000") == "0000000000400000") echo "checked=\"checked\""; ?> /> <label for="5" class="acs-hide"></label> <b>D5(1350MHz)</b>
				<input class="moca11" type="checkbox" id="6"  value="0000000001000000" <?php if (php_str_and($taboo_freq, "0000000001000000") == "0000000001000000") echo "checked=\"checked\""; ?> /> <label for="6" class="acs-hide"></label> <b>D6(1400MHz)</b>
				<input class="moca11" type="checkbox" id="7"  value="0000000004000000" <?php if (php_str_and($taboo_freq, "0000000004000000") == "0000000004000000") echo "checked=\"checked\""; ?> /> <label for="7" class="acs-hide"></label> <b>D7(1450MHz)</b>
				<input class="moca11" type="checkbox" id="8"  value="0000000010000000" <?php if (php_str_and($taboo_freq, "0000000010000000") == "0000000010000000") echo "checked=\"checked\""; ?> /> <label for="8" class="acs-hide"></label> <b>D8(1500MHz)</b>
			</div>

			<div class="moca_row2">
				<input class="moca20" type="checkbox" id="9"  value="0000000000008000" <?php if (php_str_and($taboo_freq, "0000000000008000") == "0000000000008000") echo "checked=\"checked\""; ?> /> <label for="9" class="acs-hide"></label> <b>D1a(1175MHz)</b>
				<input class="moca20" type="checkbox" id="10" value="0000000000020000" <?php if (php_str_and($taboo_freq, "0000000000020000") == "0000000000020000") echo "checked=\"checked\""; ?> /> <label for="10" class="acs-hide"></label> <b>D2a(1225MHz)</b>
				<input class="moca20" type="checkbox" id="11" value="0000000000080000" <?php if (php_str_and($taboo_freq, "0000000000080000") == "0000000000080000") echo "checked=\"checked\""; ?> /> <label for="11" class="acs-hide"></label> <b>D3a(1275MHz)</b>
			</div>

			<div class="moca_row2">
				<input class="moca20" type="checkbox" id="12" value="0000000000200000" <?php if (php_str_and($taboo_freq, "0000000000200000") == "0000000000200000") echo "checked=\"checked\""; ?> /> <label for="12" class="acs-hide"></label> <b>D4a(1325MHz)</b>
				<input class="moca20" type="checkbox" id="13" value="0000000000800000" <?php if (php_str_and($taboo_freq, "0000000000800000") == "0000000000800000") echo "checked=\"checked\""; ?> /> <label for="13" class="acs-hide"></label> <b>D5a(1375MHz)</b>
				<input class="moca20" type="checkbox" id="14" value="0000000002000000" <?php if (php_str_and($taboo_freq, "0000000002000000") == "0000000002000000") echo "checked=\"checked\""; ?> /> <label for="14" class="acs-hide"></label> <b>D6a(1425MHz)</b>
			</div>

			<div class="moca_row2">
				<input class="moca20" type="checkbox" id="15" value="0000000008000000" <?php if (php_str_and($taboo_freq, "0000000008000000") == "0000000008000000") echo "checked=\"checked\""; ?> /> <label for="15" class="acs-hide"></label> <b>D7a(1475MHz)</b>
				<input class="moca20" type="checkbox" id="16" value="0000000020000000" <?php if (php_str_and($taboo_freq, "0000000020000000") == "0000000020000000") echo "checked=\"checked\""; ?> /> <label for="16" class="acs-hide"></label> <b>D8a(1525MHz)</b>
				<input class="moca20" type="checkbox" id="17" value="0000000040000000" <?php if (php_str_and($taboo_freq, "0000000040000000") == "0000000040000000") echo "checked=\"checked\""; ?> /> <label for="17" class="acs-hide"></label> <b>D9(1550MHz)</b>
			</div>

			<div class="moca_row2">
				<input class="moca20" type="checkbox" id="18" value="0000000080000000" <?php if (php_str_and($taboo_freq, "0000000080000000") == "0000000080000000") echo "checked=\"checked\""; ?> /> <label for="18" class="acs-hide"></label> <b>D9a(1575MHz)</b>
				<input class="moca20" type="checkbox" id="19" value="0000000100000000" <?php if (php_str_and($taboo_freq, "0000000100000000") == "0000000100000000") echo "checked=\"checked\""; ?> /> <label for="19" class="acs-hide"></label> <b>D10(1600MHz)</b>
				<input class="moca20" type="checkbox" id="20" value="0000000200000000" <?php if (php_str_and($taboo_freq, "0000000200000000") == "0000000200000000") echo "checked=\"checked\""; ?> /> <label for="20" class="acs-hide"></label> <b>D10a(1625MHz)</b>
			</div>
		</div>

		<div class="form-row " id="div_nc_switch">
			<label>Preferred Network Controller:</label>
			<input type="radio"  id="nc_enable"  name="Network" value="enabled"  checked="checked" /> <label for="nc_enable" class="acs-hide"></label><b>Enabled</b>
			<input type="radio"  id="nc_disable" name="Network" value="disabled" <?php if ("false"==$nc_enable) echo 'checked="checked"'; ?> /> <label for="nc_disable" class="acs-hide"></label><b>Disabled</b>
		</div>
		
		<div class="form-row " id="div_nc_status">
			<label>Preferred Network Controller:</label>
			<span class="readonlyValue"><?php echo ("true"==$nc_enable)?"Yes":"No"; ?></span>
		</div>

		<div class="form-row odd" id="privacy_switch" >
			<label for="Privacy">MoCA Privacy:</label>
			<input type="radio"  id="privacy_enable"  name="privacy_switch" value="enabled"  checked="checked" /> <label for="privacy_enable" class="acs-hide"></label><b>Enabled</b>
			<input type="radio"  id="privacy_disable" name="privacy_switch" value="disabled" <?php if ("false"==$privacy_enable) echo 'checked="checked"'; ?> /> <label for="privacy_disable" class="acs-hide"></label><b>Disabled</b>
		</div>

		<div id="netPassword">
			<div class="form-row">
				<label for="net_password">Network Password:</label>
				<span id="password_field">
					<input type="password" size="23" id="net_password" name="net_password" class="text" value="<?php echo $net_password; ?>" />
				</span>&nbsp;&nbsp;&nbsp; 12 Digits Min,17 Digits Max
			</div>
			<div class="form-row odd">
				<label for="password_show">Show Network Password:</label>
				<span class="checkbox" style="margin: 0"><input type="checkbox" id="password_show" name="password_show" /> </span>
			</div> 
		</div>

		<div class="form-row ">
			<label for="network_controller_mac">Network Controller MAC:</label>
			<span id="network_controller_mac" class="readonlyValue"> 
			<?php echo getStr("Device.MoCA.Interface.1.X_CISCO_NetworkCoordinatorMACAddress"); ?></span>
		</div>
		
		<div class="select-row odd" id="div_qos_switch">
			<label>QoS for MoCA:</label>
			<span id="qos_switch"></span>
		</div>

		<div class="form-btn">
			<input id="submit_moca" type="submit" value="Save" class="btn" />
		</div>
    </div> <!-- end .module -->
	</fieldset>
    </form>
</div><!-- end #content -->

<?php include('includes/footer.php'); //sleep(3);?>
