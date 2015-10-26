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

	$MoCA_param = array(
		"moca_enable"	=> "Device.MoCA.Interface.1.Enable",
		"scan_method"	=> "Device.MoCA.Interface.1.X_CISCO_COM_ChannelScanning",
		"channel"	=> "Device.MoCA.Interface.1.CurrentOperFreq",
		"beacon_power"	=> "Device.MoCA.Interface.1.BeaconPowerLimit",
		"taboo_freq"	=> "Device.MoCA.Interface.1.NodeTabooMask",
		"nc_enable"	=> "Device.MoCA.Interface.1.PreferredNC",
		"privacy_enable"=> "Device.MoCA.Interface.1.PrivacyEnabledSetting",
		"net_password"	=> "Device.MoCA.Interface.1.KeyPassphrase",
		"NetworkCoordinator"	=> "Device.MoCA.Interface.1.NetworkCoordinator",
		"NodeID"	=> "Device.MoCA.Interface.1.NodeID",
		"MACAddress"	=> "Device.MoCA.Interface.1.MACAddress",
		"NC_MACAddress"	=> "Device.MoCA.Interface.1.X_CISCO_NetworkCoordinatorMACAddress",
	);

	$MoCA_value = KeyExtGet("Device.MoCA.Interface.1.", $MoCA_param);

	$moca_enable	= $MoCA_value['moca_enable'];
	$scan_method	= $MoCA_value['scan_method'];
	$channel	= $MoCA_value['channel'];
	$beacon_power	= $MoCA_value['beacon_power'];
	$taboo_freq	= $MoCA_value['taboo_freq'];
	$nc_enable	= $MoCA_value['nc_enable'];
	$privacy_enable	= $MoCA_value['privacy_enable'];
	$net_password	= $MoCA_value['net_password'];

	//$qos_enable 	= "true";
	// $taboo_enable	= getStr("Device.MoCA.Interface.1.X_CISCO_COM_EnableTabooBit");
	// $qos_enable 	= getStr("Device.MoCA.Interface.1.QoS.X_CISCO_COM_Enabled");

	// $moca_enable	= "false";
	// $scan_method	= "true";
	// $channel		= "1275"; 
	// $beacon_power	= "false";
	// $taboo_enable	= "true";
	// $taboo_freq		= "FFffAAaa00010000"; 
	// $nc_enable		= "false";
	// $privacy_enable	= "false";
	// $net_password	= "1234567891011";
?>

<style type="text/css">

label{
	margin-right: 10px !important;
}

#content {
	display: none;
}

.moca_row1 {
}

.moca_row2 {
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

	/*$("#qos_switch").radioswitch({
		id: "qos-switch",
		radio_name: "enable_moca1",
		id_on: "qos_enable",
		id_off: "qos_disable",
		title_on: "Enable QoS for MoCA",
		title_off: "Disable QoS for MoCA",
		state: <?php echo ($qos_enable === "true" ? "true" : "false"); ?> ? "on" : "off"
	});*/

	// $('#div_channel_switch').change(function()	//this is not compatible with IE8
	$(':input[name="channel_switch"]').change(function() //this will act twice
	{
		if ($("#scan_auto").prop("checked"))
		{
			$("#mode_option").prop("disabled", true);
			$('[id^="tf_"]').prop("disabled", true).attr('checked', false);
		}
		else
		{
			$("#mode_option").prop("disabled", false);
			$('[id^="tf_"]').prop("disabled", false);
			var channel = $('#mode_option').val();
			
			var tf_channel;
			if(channel%50 != 0) tf_channel = (channel - 1025)/25;
			else tf_channel = (channel - 1000)/25;

			$('[id^="tf_"]').prop("disabled", false).attr('checked', true);
			$("#tf_"+tf_channel).prop("disabled", true).attr('checked', false);
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

	$('#mode_option').change(function()
	{
		var channel = $('#mode_option').val();
		
		var tf_channel;
		if(channel%50 != 0) tf_channel = (channel - 1025)/25;
		else tf_channel = (channel - 1000)/25;

		$('[id^="tf_"]').prop("disabled", false).attr('checked', true);
		$("#tf_"+tf_channel).prop("disabled", true).attr('checked', false);
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
		$("#div_taboo_list").hide();
		$("#div_nc_switch").hide();
		//$("#div_qos_switch").hide();
		// for GUI version 3.0
		$("#privacy_switch").hide();
		$("#net_password_top").hide();
		$("#net_password").prop("disabled", true);
		$("#password_show_top").hide();
	}
	/*else{
		// for GUI version 3.0
		$("#div_qos_switch").hide();
	}*/
	
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
	var channel		= $('#mode_option').attr("value"); 
	var beacon_power	= $('#beacon_power').attr("value");
	//var taboo_enable	= $("#taboo_enable").prop("checked");
	var taboo_freq 		= "0000000000000000";
	var nc_enable		= $("#nc_enable").prop("checked");
	var privacy_enable	= $("#privacy_enable").prop("checked");
	var net_password	= $('#net_password').attr("value");
	//var qos_enable 		= $("#qos_switch").radioswitch("getState").on;
	
	var cahnnel_obj = {
		"1150":"0000000000004000",
		"1175":"0000000000008000",
		"1200":"0000000000010000",
		"1225":"0000000000020000",
		"1250":"0000000000040000",
		"1275":"0000000000080000",
		"1300":"0000000000100000",
		"1325":"0000000000200000",
		"1350":"0000000000400000",
		"1375":"0000000000800000",
		"1400":"0000000001000000",
		"1425":"0000000002000000",
		"1450":"0000000004000000",
		"1475":"0000000008000000",
		"1500":"0000000010000000",
		"1525":"0000000020000000",
		"1550":"0000000040000000",
		"1575":"0000000080000000",
		"1600":"0000000100000000",
		"1625":"0000000200000000"
	};

	channel = cahnnel_obj[channel];

	function js_str_and(a, b)	//js bit-and limited to 32, I have to write this
	{
		var c = String("");
		for (var i=0; i<Math.max(a.length, b.length); i++)
		{
			c += (parseInt(a.substr(i,1), 16) | parseInt(b.substr(i,1), 16)).toString(16);
		}
		return c;
	}
	
	for (var i=1; i<25; i++)
	{
		$("#tf_"+i).prop("checked") && (taboo_freq = js_str_and(taboo_freq, $("#tf_"+i).val()));
	}
	
	/*if (false==scan_method && js_str_and(channel, taboo_freq) == taboo_freq)
	{
		jAlert("In manual mode: Taboo frequency must exclude current channel!");
		return;
	}
	
	if ($(".moca11:not(:checked)").length < 1 || $(".moca20:not(:checked)").length < 1)
	{
		jAlert("Can't disable all MoCA 1.1 (or 2.0) frequency at the same time!");
		return;
	}*/

	var jsConfig = '{"moca_enable": "' + moca_enable 
	+ '", "scan_method": "' + scan_method 
	+ '", "channel": "' + channel 
	+ '", "beacon_power": "' + beacon_power 
	//+ '", "taboo_enable": "' + taboo_enable 
	+ '", "taboo_freq": "' + taboo_freq 
	+ '", "nc_enable": "' + nc_enable 
	+ '", "privacy_enable": "' + privacy_enable 
	+ '", "net_password": "' + net_password 
	//+ '", "qos_enable": "' + qos_enable 
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
				window.location.reload(true);
			}, 60000);
		},
		error: function(){
			jHide();
			jAlert("Failure, please try again.");
		}
	});
}

</script>

<div id="content" >
    <h1>Gateway > Connection > MoCA</h1>
	<div id="educational-tip">
		<p class="tip">You have the option to enable or disable the Gateway's MoCA Network. </p>
		<?php
		if("admin" != $_SESSION["loginuser"]){
			echo "<p class=\"hidden\"><strong>MoCA Privacy: </strong> You can enable or disable MoCA Privacy. If Privacy is enabled, all the devices connecting to the Gateway via MoCA will use the MoCA Network Password. </p><p class=\"hidden\"><strong>Network Password:</strong> This is the password for the MoCA network, and will only be used when MoCA Privacy is enabled. </p>";
		}
		?>
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
				<option id="d1"   value="1150"  selected="selected"                                          >D1(1150 MHz)</option>       
				<option id="d2"   value="1200"  <?php if ($channel == "1200") echo 'selected="selected"'; ?> >D2(1200 MHz)</option>       
				<option id="d3"   value="1250"  <?php if ($channel == "1250") echo 'selected="selected"'; ?> >D3(1250 MHz)</option>       
				<option id="d4"   value="1300"  <?php if ($channel == "1300") echo 'selected="selected"'; ?> >D4(1300 MHz)</option>       
				<option id="d5"   value="1350"  <?php if ($channel == "1350") echo 'selected="selected"'; ?> >D5(1350 MHz)</option>       
				<option id="d6"   value="1400"  <?php if ($channel == "1400") echo 'selected="selected"'; ?> >D6(1400 MHz)</option>       
				<option id="d7"   value="1450"  <?php if ($channel == "1450") echo 'selected="selected"'; ?> >D7(1450 MHz)</option>       
				<option id="d8"   value="1500"  <?php if ($channel == "1500") echo 'selected="selected"'; ?> >D8(1500 MHz)</option>       
				<option disabled="disabled">-- MoCA 2.0 --</option>                                                         
				<option id="d1a"  value="1175"  <?php if ($channel == "1175") echo 'selected="selected"'; ?> >D1a(1175 MHz)</option>    
				<option id="d2a"  value="1225"  <?php if ($channel == "1225") echo 'selected="selected"'; ?> >D2a(1225 MHz)</option>    
				<option id="d3a"  value="1275"  <?php if ($channel == "1275") echo 'selected="selected"'; ?> >D3a(1275 MHz)</option>    
				<option id="d4a"  value="1325"  <?php if ($channel == "1325") echo 'selected="selected"'; ?> >D4a(1325 MHz)</option>    
				<option id="d5a"  value="1375"  <?php if ($channel == "1375") echo 'selected="selected"'; ?> >D5a(1375 MHz)</option>    
				<option id="d6a"  value="1425"  <?php if ($channel == "1425") echo 'selected="selected"'; ?> >D6a(1425 MHz)</option>    
				<option id="d7a"  value="1475"  <?php if ($channel == "1475") echo 'selected="selected"'; ?> >D7a(1475 MHz)</option>    
				<option id="d8a"  value="1525"  <?php if ($channel == "1525") echo 'selected="selected"'; ?> >D8a(1525 MHz)</option>
				<option id="d9"   value="1550"  <?php if ($channel == "1550") echo 'selected="selected"'; ?> >D9(1550 MHz)</option>				
				<option id="d9a"  value="1575"  <?php if ($channel == "1575") echo 'selected="selected"'; ?> >D9a(1575 MHz)</option>
				<option id="d10"  value="1600"  <?php if ($channel == "1600") echo 'selected="selected"'; ?> >D10(1600 MHz)</option>				
				<option id="d10a" value="1625"  <?php if ($channel == "1625") echo 'selected="selected"'; ?> >D10a(1625 MHz)</option> 
			</select>
		</div>

		<?php	$channel_show = "D1(1150 MHz)";
			if ($channel == "1200") $channel_show = "D2(1200 MHz)";
			else if ($channel == "1250") $channel_show = "D3(1250 MHz)";
			else if ($channel == "1300") $channel_show = "D4(1300 MHz)";
			else if ($channel == "1350") $channel_show = "D5(1350 MHz)";
			else if ($channel == "1400") $channel_show = "D6(1400 MHz)";
			else if ($channel == "1450") $channel_show = "D7(1450 MHz)";
			else if ($channel == "1500") $channel_show = "D8(1500 MHz)";
			else if ($channel == "1175") $channel_show = "D1a(1175 MHz)";
			else if ($channel == "1225") $channel_show = "D2a(1225 MHz)";
			else if ($channel == "1275") $channel_show = "D3a(1275 MHz)";
			else if ($channel == "1325") $channel_show = "D4a(1325 MHz)";
			else if ($channel == "1375") $channel_show = "D5a(1375 MHz)";
			else if ($channel == "1425") $channel_show = "D6a(1425 MHz)";
			else if ($channel == "1475") $channel_show = "D7a(1475 MHz)";
			else if ($channel == "1525") $channel_show = "D8a(1525 MHz)";
			else if ($channel == "1550") $channel_show = "D9(1550 MHz)";
			else if ($channel == "1575") $channel_show = "D9a(1575 MHz)";
			else if ($channel == "1600") $channel_show = "D10(1600 MHz)";
			else if ($channel == "1625") $channel_show = "D10a(1625 MHz)";

			if("true"==$nc_enable) $PNC_Show = "Yes";
			else $PNC_Show = "No";
			
			if("admin" == $_SESSION["loginuser"]) {
				echo '<div class="form-row"> <label >Channel:</label> <span class="readonlyValue">'.$channel_show.'</span> </div>';
				echo '<div class="form-row "> <label>Preferred Network Controller:</label> <span class="readonlyValue">'.$PNC_Show.'</span> </div>';
			}
		?>

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

		<div class="form-row odd" id="div_taboo_list">
			<label>Taboo Frequency:</label>
			<div class="moca_row1" style="position:relative;top:0px;right:0px">
				<input class="moca11" type="checkbox" id="tf_2"  value="0000000000000400" <?php if (php_str_and($taboo_freq, "0000000000000400") == "0000000000000400") echo "checked=\"checked\""; ?> /> <label for="2" class="acs-hide"></label> <b>1050MHz</b>&nbsp;&nbsp;
				<input class="moca11" type="checkbox" id="tf_4"  value="0000000000001000" <?php if (php_str_and($taboo_freq, "0000000000001000") == "0000000000001000") echo "checked=\"checked\""; ?> /> <label for="4" class="acs-hide"></label> <b>1100MHz</b>&nbsp;&nbsp;
				<input class="moca11" type="checkbox" id="tf_6"  value="0000000000004000" <?php if (php_str_and($taboo_freq, "0000000000004000") == "0000000000004000") echo "checked=\"checked\""; ?> /> <label for="6" class="acs-hide"></label> <b>1150MHz</b>&nbsp;&nbsp;
				<input class="moca11" type="checkbox" id="tf_8"  value="0000000000010000" <?php if (php_str_and($taboo_freq, "0000000000010000") == "0000000000010000") echo "checked=\"checked\""; ?> /> <label for="8" class="acs-hide"></label> <b>1200MHz</b>
			</div>

			<div class="moca_row1" style="position:relative;top:0px;right:0px">
				<input class="moca11" type="checkbox" id="tf_10" value="0000000000040000" <?php if (php_str_and($taboo_freq, "0000000000040000") == "0000000000040000") echo "checked=\"checked\""; ?> /> <label for="10" class="acs-hide"></label> <b>1250MHz</b>&nbsp;&nbsp;
				<input class="moca11" type="checkbox" id="tf_12" value="0000000000100000" <?php if (php_str_and($taboo_freq, "0000000000100000") == "0000000000100000") echo "checked=\"checked\""; ?> /> <label for="12" class="acs-hide"></label> <b>1300MHz</b>&nbsp;&nbsp;
				<input class="moca11" type="checkbox" id="tf_14" value="0000000000400000" <?php if (php_str_and($taboo_freq, "0000000000400000") == "0000000000400000") echo "checked=\"checked\""; ?> /> <label for="14" class="acs-hide"></label> <b>1350MHz</b>&nbsp;&nbsp;
				<input class="moca11" type="checkbox" id="tf_16" value="0000000001000000" <?php if (php_str_and($taboo_freq, "0000000001000000") == "0000000001000000") echo "checked=\"checked\""; ?> /> <label for="16" class="acs-hide"></label> <b>1400MHz</b>
			</div>

			<div class="moca_row1" style="position:relative;top:0px;right:-230px">
				<input class="moca11" type="checkbox" id="tf_18" value="0000000004000000" <?php if (php_str_and($taboo_freq, "0000000004000000") == "0000000004000000") echo "checked=\"checked\""; ?> /> <label for="18" class="acs-hide"></label> <b>1450MHz</b>&nbsp;&nbsp;
				<input class="moca11" type="checkbox" id="tf_20" value="0000000010000000" <?php if (php_str_and($taboo_freq, "0000000010000000") == "0000000010000000") echo "checked=\"checked\""; ?> /> <label for="20" class="acs-hide"></label> <b>1500MHz</b>&nbsp;&nbsp;
				<input class="moca11" type="checkbox" id="tf_24" value="0000000100000000" <?php if (php_str_and($taboo_freq, "0000000100000000") == "0000000100000000") echo "checked=\"checked\""; ?> /> <label for="24" class="acs-hide"></label> <b>1600MHz</b>
			</div>

			<div class="moca_row2" style="position:relative;top:0px;right:-230px">
				<input class="moca20" type="checkbox" id="tf_1"  value="0000000000000200" <?php if (php_str_and($taboo_freq, "0000000000000200") == "0000000000000200") echo "checked=\"checked\""; ?> /> <label for="1" class="acs-hide"></label> <b>1025MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_3"  value="0000000000000800" <?php if (php_str_and($taboo_freq, "0000000000000800") == "0000000000000800") echo "checked=\"checked\""; ?> /> <label for="3" class="acs-hide"></label> <b>1075MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_5"  value="0000000000002000" <?php if (php_str_and($taboo_freq, "0000000000002000") == "0000000000002000") echo "checked=\"checked\""; ?> /> <label for="5" class="acs-hide"></label> <b>1125MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_7"  value="0000000000008000" <?php if (php_str_and($taboo_freq, "0000000000008000") == "0000000000008000") echo "checked=\"checked\""; ?> /> <label for="7" class="acs-hide"></label> <b>1175MHz</b>
			</div>

			<div class="moca_row2" style="position:relative;top:0px;right:-230px">
				<input class="moca20" type="checkbox" id="tf_9"  value="0000000000020000" <?php if (php_str_and($taboo_freq, "0000000000020000") == "0000000000020000") echo "checked=\"checked\""; ?> /> <label for="9"  class="acs-hide"></label> <b>1225MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_11" value="0000000000080000" <?php if (php_str_and($taboo_freq, "0000000000080000") == "0000000000080000") echo "checked=\"checked\""; ?> /> <label for="11" class="acs-hide"></label> <b>1275MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_13" value="0000000000200000" <?php if (php_str_and($taboo_freq, "0000000000200000") == "0000000000200000") echo "checked=\"checked\""; ?> /> <label for="13" class="acs-hide"></label> <b>1325MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_15" value="0000000000800000" <?php if (php_str_and($taboo_freq, "0000000000800000") == "0000000000800000") echo "checked=\"checked\""; ?> /> <label for="15" class="acs-hide"></label> <b>1375MHz</b>
			</div>

			<div class="moca_row2" style="position:relative;top:0px;right:-230px">
				<input class="moca20" type="checkbox" id="tf_17" value="0000000002000000" <?php if (php_str_and($taboo_freq, "0000000002000000") == "0000000002000000") echo "checked=\"checked\""; ?> /> <label for="17" class="acs-hide"></label> <b>1425MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_19" value="0000000008000000" <?php if (php_str_and($taboo_freq, "0000000008000000") == "0000000008000000") echo "checked=\"checked\""; ?> /> <label for="19" class="acs-hide"></label> <b>1475MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_21" value="0000000020000000" <?php if (php_str_and($taboo_freq, "0000000020000000") == "0000000020000000") echo "checked=\"checked\""; ?> /> <label for="21" class="acs-hide"></label> <b>1525MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_22" value="0000000040000000" <?php if (php_str_and($taboo_freq, "0000000040000000") == "0000000040000000") echo "checked=\"checked\""; ?> /> <label for="22" class="acs-hide"></label> <b>1550MHz</b>&nbsp;&nbsp;
				<input class="moca20" type="checkbox" id="tf_23" value="0000000080000000" <?php if (php_str_and($taboo_freq, "0000000080000000") == "0000000080000000") echo "checked=\"checked\""; ?> /> <label for="23" class="acs-hide"></label> <b>1575MHz</b>
			</div>
		</div>

		<div class="form-row " id="div_nc_switch">
			<label>Preferred Network Controller:</label>
			<input type="radio"  id="nc_enable"  name="Network" value="enabled"  checked="checked" /> <label for="nc_enable" class="acs-hide"></label><b>Enabled</b>
			<input type="radio"  id="nc_disable" name="Network" value="disabled" <?php if ("false"==$nc_enable) echo 'checked="checked"'; ?> /> <label for="nc_disable" class="acs-hide"></label><b>Disabled</b>
		</div>

		<div class="form-row" id="privacy_switch" >
			<label for="Privacy">MoCA Privacy:</label>
			<input type="radio"  id="privacy_enable"  name="privacy_switch" value="enabled"  checked="checked" /> <label for="privacy_enable" class="acs-hide"></label><b>Enabled</b>
			<input type="radio"  id="privacy_disable" name="privacy_switch" value="disabled" <?php if ("false"==$privacy_enable) echo 'checked="checked"'; ?> /> <label for="privacy_disable" class="acs-hide"></label><b>Disabled</b>
		</div>

		<div class="form-row add" id="net_password_top">
			<label for="net_password">Network Password:</label>
			<span id="password_field">
				<input type="password" size="23" id="net_password" name="net_password" class="text" value="<?php echo $net_password; ?>" />
			</span>&nbsp;<span style="font-size: .8em;">12 Digits Min,17 Digits Max<span/>
		</div>
		
		<div class="form-row" id="password_show_top">
			<label for="password_show">Show Network Password:</label>
			<span class="checkbox" style="margin: 0"><input type="checkbox" id="password_show" name="password_show" /> </span>
		</div> 
		

		<div class="form-row odd">
			<label for="network_controller_mac">Network Controller MAC:</label>
			<span id="network_controller_mac" class="readonlyValue"><?php
				/*if Node ID of the Network Coordinator is same with Node ID of the Local Node, 
					Network Controller MAC should be MAC Address of the Local Node, 
				else 	Network Controller MAC should be Network Coordinator MAC Address.*/
				if($MoCA_value['NetworkCoordinator'] == $MoCA_value['NodeID']){
					echo $MoCA_value['MACAddress'];
				} else {
					echo $MoCA_value['NC_MACAddress'];
				}
			?></span>
		</div>
		
		<!--div class="select-row odd" id="div_qos_switch">
			<label>QoS for MoCA:</label>
			<span id="qos_switch"></span>
		</div-->

		<div class="form-btn">
			<input id="submit_moca" type="submit" value="Save" class="btn" />
		</div>
    </div> <!-- end .module -->
	</fieldset>
    </form>
</div><!-- end #content -->

<?php include('includes/footer.php'); //sleep(3);?>
