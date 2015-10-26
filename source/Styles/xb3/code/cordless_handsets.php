<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: cordless_handsets.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
	$dat = array();
	$ids = array_trim(explode(",", getInstanceIds("Device.X_CISCO_COM_MTA.Dect.Handsets.")));

	foreach ($ids as $i){
		array_push($dat, array(
			'hs_id'		=> $i,
			'hs_name'	=> getStr("Device.X_CISCO_COM_MTA.Dect.Handsets.$i.HandsetName"),
			'hs_otn'	=> getStr("Device.X_CISCO_COM_MTA.Dect.Handsets.$i.OperatingTN"),
			'hs_stn'	=> getStr("Device.X_CISCO_COM_MTA.Dect.Handsets.$i.SupportedTN"),
			'hs_time'	=> getStr("Device.X_CISCO_COM_MTA.Dect.Handsets.$i.LastActiveTime"),
			'hs_stat'	=> getStr("Device.X_CISCO_COM_MTA.Dect.Handsets.$i.Status")
			));
	}
	
	$cat_iq		= getStr("Device.X_CISCO_COM_MTA.Dect.Enable");
	$cat_pin	= getStr("Device.X_CISCO_COM_MTA.Dect.PIN");
		
	$jsConfig = json_encode($dat);
?>

<style>
#tn_div label {
	text-align: left;
	font-style: bold;
}
#tn_div span {
	margin: 0 96px;
}
</style>

<script type="text/javascript">

$(document).ready(function() {
    comcast.page.init("Connected Devices > Cordless Handsets", "nav-cordless-handsets");
	
	init_data();
	
	var G_cat_iq = $("#catiq_switch").radioswitch("getState").on;

	var eventHandler = function(){
		var target = $(this).attr("posttag");
		
		var cat_iq		= $("#catiq_switch").radioswitch("getState").on;
		var cat_pin		= $("#DECT_PIN").val();
		var dereg_id	= $(this).attr("id");
		var cat_tn		= new Array();
		var reg_mode	= "noChange";
		
		$("#tn_div select").each(function(){
			cat_tn.push([$(this).attr("id").substr(3), $(this).val()]);
		});
		
		var ajax_data = {
				target	:target,				
				cat_iq	:cat_iq,
				cat_pin	:cat_pin,
				dereg_id:dereg_id,
				reg_mode:reg_mode,
				cat_tn	:JSON.stringify(cat_tn)
			};
		
		if ("save_iq" == target){
			if (G_cat_iq == cat_iq){
				return;
			}
			
			jProgress('Check telephony line status, please wait...', 60);
			$.post(
				"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
				{"get_statusx":"true"},
				function(msg)
				{
					jHide();
					if ("Off-Hook" == msg.linexhook && false == cat_iq){
						jConfirm(
							"<b>WARNING:</b><br/>Handset is on a call or is off-hook. Can't disable DECT now. Try later please..."
							, 'Can not proceed!'
							, function(ret){location.reload();}
						);
					}
					else{
						jConfirm(
							"<b>WARNING:</b><br/>Make sure that you are aware of the changes you are making. <br/>In a single line telephone configuration,CAT-iq should be enabled. <br/>In a two line telephone configuration, CAT-iq should be disabled. <br/>Enabling CAT-iq base in a two line telephone configuration in the device will impact the call seizure functionality for legacy home alarm systems. <br/><b>Are you sure you want to continue?</b>"
							, 'Are You Sure?'
							, function(ret){
								if(ret){
									ajax_do(ajax_data);
								}
								else{
									location.reload();
								}
							}
						);
					}
				},
				"json"     
			);
			
			if (!cat_iq){
				$("input, select").not("#catiq_switch input").attr("disabled", true);
			}
			else{
				$("input, select").not("#catiq_switch input").attr("disabled", false);
			}
			
			G_cat_iq = $("#catiq_switch").radioswitch("getState").on;
		}
		else if ("deregister" == target){
			jConfirm(
				"Are you sure you want to deregister DECT handset #"+$(this).attr("id")+"?"
				, 'Are You Sure?'
				, function(ret){
					if(ret){
						ajax_do(ajax_data);
					}
				}
			);		
		}
		else{
			ajax_do(ajax_data);
		}
	};
	$(".btn").click(eventHandler);
	$("#catiq_switch").change(eventHandler);

});

function ajax_do(ajax_data){
	jProgress('This may take several seconds...',60);
	ajaxrequest = $.ajax({
		type:	"POST",
		url:	"actionHandler/ajaxSet_cordless_handsets.php",
		dataType:"JSON",
		data:	ajax_data,
		success:function(result){
			jHide();
		},
		error:function(){
			jHide();
			jAlert("Sorry, please try again.");
		}
	});	
}

function init_data(){
	var dat		= eval('(' + '<?php echo $jsConfig;?>' + ')');
	var cat_iq	= "<?php echo $cat_iq;?>";
	var cat_pin	= "<?php echo $cat_pin;?>";
	
	$.each(dat, function(key, val){
		$("#hs_table > tbody").append('<tr>\
				<td headers="hs-Handset">'+val["hs_name"]+'</td>\
				<td headers="hs-TN">'+val["hs_otn"]+'</td>\
				<td headers="hs-Activity">'+val["hs_time"]+'</td>\
				<td headers="hs-Status">'+("true"==val["hs_stat"]?"Active":"Inactive")+'</td>\
				<td headers="hs-Blank"><input class="btn" type="button" id="'+val["hs_id"]+'" value="DEREGISTER"  posttag="deregister" /></td>\
			</tr>'
		);
		
		$("#tn_div").append('<div class="form-row">\
				<label for="tn_'+val["hs_id"]+'">'+val["hs_name"]+'</label>\
				<select id="tn_'+val["hs_id"]+'"><option value="1">TN1</option><option value="2">TN2</option><option value="1+2">TN1&TN2</option></select>\
			</div>'
		);
		
		$('#tn_'+val["hs_id"]).val(val["hs_stn"]);
	});
	
	$("#hs_table > tbody > tr:odd, #tn_div > div:odd").addClass("odd");
	
	if ("true"==cat_iq){
	}
	else{
		$("input, select").attr("disabled", true);
	}
	
	$("#registernew").click(function(){
		location.href = "cordless_handsets_add.php";	
	});
	
	$("#DECT_PIN").val(cat_pin);
	
	$("#catiq_switch").radioswitch({
		id: "forwarding-switch",
		radio_name: "forwarding",
		id_on: "forwarding_enabled",
		id_off: "forwarding_disabled",
		title_on: "Enable CAT-iq",
		title_off: "Disable CAT-iq",
		state: "true" == cat_iq ? "on" : "off"
	}).attr("posttag", "save_iq");
	
	// show diff view as per user, note that homeuser have no chance to enable cat-iq
	if ("mso" != "<?php echo $_SESSION["loginuser"]; ?>"){
		$('.cat-iq, .save-pin, .save-tn, #tn_div').hide();
		$("#DECT_PIN").attr("disabled", true);
		if ("true" != cat_iq){
			$(".cat-iq").show().html('<p style="color: red;">CAT-iq function is disabled internally, please contact administrator!</p>');
		}
	}
}

</script>

<div id="content">
	<h1>Connected Devices > Cordless Handsets</h1>

	<div id="educational-tip">
		<p class="tip">Connect/Disconnect a certified CAT-iq 2.0 cordless handset (up to five) to the Gateway.  </p>
		<p class="hidden"><strong>REGISTER NEW HANDSET:</strong> Click to connect a new handset and follow the instructions. </p>
		<p class="hidden"><strong>DEREGISTER:</strong> Click to disassociate the handset from the Gateway. </p>
	</div>
<form action="cordless_handsets.php" method="post">
	<div class="module cat-iq">
		<div class="select-row">
    		<span class="readonlyLabel label">CAT-iq:</span>
			<span id="catiq_switch"></span>
    	</div>
	</div>
	</form>

	<div id=forwarding-items>
	<div class="module forms data">
		<h2>Cordless Handsets</h2>
		<table id="hs_table" cellpadding="0" cellspacing="0" class="data" summary="This table shows status of connected Cordless Handsets">
			<thead>
				<tr>
					<th id="hs-Handset">Handset</th>
					<th id="hs-TN">Registered TN</th>
					<th id="hs-Activity">Last Activity</th>
					<th id="hs-Status">Line Status</th>
					<th id="hs-Blank">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<!--tr>
					<td headers="hs-Handset">Handset 1</td>
					<td headers="hs-TN">TN1</td>
					<td headers="hs-Activity">23 min ago</td>
					<td headers="hs-Status">Active</td>
					<td headers="hs-Blank"><input class="btn" type="button" id="hs_1" value="DEREGISTER"></td>
				</tr-->
			</tbody>
			<tfoot>
				<tr style="display:none;">
					<td headers="hs-Handset">null</td>
					<td headers="hs-TN">null</td>
					<td headers="hs-Activity">null</td>
					<td headers="hs-Status">null</td>
					<td headers="hs-Blank">null</td>
				</tr>
			</tfoot>
		</table>
	</div> <!-- end .module -->
	
	<div class="form-btn">
		<input id="registernew" type="button" value="Register New Handset"  posttag="register" />
	</div>
	
	<div class="module">
		<div class="form-row odd">
			<label for="DECT_PIN">CAT-iq Base PIN:</label>
			<input type="text"  value="" size="5" name="DECT_PIN" id="DECT_PIN" />
		</div>
	</div>

	<div class="form-btn save-pin">
		<input type="button" value="save" class="btn"  posttag="save_pin" />
	</div>

	<div id="tn_div" class="module forms">
		<h2>Handset<span>&nbsp;</span>TN</h2>
		<!--div>
			<label for="tn_hs_1">Handset 1</label>
			<select id="tn_hs_1"><option value="1">TN1</option><option value="2">TN2</option><option value="1+2">TN1&TN2</option></select>
		</div-->
	</div>
	
	<div class="form-btn save-tn">
		<input type="button" value="save" class="btn"  posttag="save_tn" />
		<input type="button" id="btn-cancel" value="Cancel" class="btn alt reset" onclick="location.reload();"/>
	</div>

</div><!-- end #content -->
</div>

<?php include('includes/footer.php'); ?>
