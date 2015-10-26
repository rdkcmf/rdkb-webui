<?php include('includes/header.php'); ?>

<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Connection > CallP/QoS ", "nav-qos");

	$('#show_DSXlog').click(function(){
		jConfirm(
		"This action may take more than one minute. Do you want to continue?", 
		"Are You Sure?", 
		function(ret){
			if(ret){
				window.location = "DSXlog.php";
			}
		});
    });

	$('#show_callsignallog').click(function(){
		jConfirm(
		"This action may take more than one minute. Do you want to continue?", 
		"Are You Sure?", 
		function(ret){
			if(ret){
				window.location = "callsignallog.php";
			}
		});
    });
});

function save_config(target)
{
	var jsConfig = "";
	
	if ("switch_callsignallog" == target) {
		if ($('#switch_callsignallog').attr("value")=="Enable Logging")	{
			$('#switch_callsignallog').attr("value", "Disable Logging");
			jsConfig = '{"target":"'+target+'", "value":"'+"true"+'"}';
		}
		else {
			$('#switch_callsignallog').attr("value", "Enable Logging");
			jsConfig = '{"target":"'+target+'", "value":"'+"false"+'"}';
		}
		set_config(jsConfig);
	}
	else if ("clear_callsignallog" == target) {
		jConfirm(
			"Are you sure to clear call signal log?", 
			"Confirm", 
			function(ret){if(ret){
				jsConfig = '{"target":"'+target+'", "value":"'+"true"+'"}';
				set_config(jsConfig);
			}
		});
	}
	else if ("switch_DSXlog" == target) {
		if ($('#switch_DSXlog').attr("value")=="Enable Logging")	{
			$('#switch_DSXlog').attr("value", "Disable Logging");
			jsConfig = '{"target":"'+target+'", "value":"'+"true"+'"}';
		}
		else {
			$('#switch_DSXlog').attr("value", "Enable Logging");
			jsConfig = '{"target":"'+target+'", "value":"'+"false"+'"}';
		}
		set_config(jsConfig);
	}	
	else if ("clear_DSXlog" == target) {
		jConfirm(
			"Are you sure to clear DSX log?", 
			"Confirm", 
			function(ret){if(ret){
				jsConfig = '{"target":"'+target+'", "value":"'+"true"+'"}';
				set_config(jsConfig);
			}
		});
	}
}	

function set_config(jsConfig)
{
	// alert(jsConfig);
	jProgress('This may take several seconds...', 60);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_qos.php",
		data: { configInfo: jsConfig },
		success: function(msg) {
			location.reload();
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
    <h1>Gateway > Connection >CallP/QoS </h1>
    <div id="educational-tip">
		<p class="tip">This Page shows CallP/QoS statistics of USG.</p>
    </div>

    <div class="module data">
		<h2>CALLP </h2>
		<table class="data">
			<tr>
				<th>Line</th>
				<th>LC State</th>
				<th>CallP State</th>
				<th>Loop Current</th>
			</tr>
			<tr>
				<td>1</td>
				<td><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.1.CALLP.LCState"); ?></td>
				<td><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.1.CALLP.CallPState"); ?></td>
				<td><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.1.CALLP.LoopCurrent"); ?></td>
			</tr>
			<tr class="odd">
				<td>2</td>
				<td><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.2.CALLP.LCState"); ?></td>
				<td><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.2.CALLP.CallPState"); ?></td>
				<td><?php echo getStr("Device.X_CISCO_COM_MTA.LineTable.2.CALLP.LoopCurrent"); ?></td>
			</tr>
		</table>
    </div> <!-- end .module -->

	<div class="btn-group">
		&nbsp;&nbsp;&nbsp;&nbsp;<input id="show_callsignallog" name="show_callsignallog" type="button"  value="Show Call Signalling Log" class="btn" />
		<input id="switch_callsignallog" type="button" onClick="save_config('switch_callsignallog')"  value="<?php echo getStr('Device.X_CISCO_COM_MTA.CallSignallingLogEnable')!='true'?'Enable':'Disable'; ?> Logging" class="btn alt" />
        <input id="clear_callsignallog"  type="submit" onClick="save_config('clear_callsignallog')"   value="Clear" class="btn" />
    </div>
	
	<div class="module data">
		<h2>QoS </h2>
		<table class="data">
			<tr>
				<th>SFID</th>
				<th>Service Class Name</th>
				<th>Direction</th>
				<th>Primary Flow</th>
				<th>Traffic Type</th>
				<th>Packets</th>
			</tr>
			<?php
				$ids = array_filter(explode(",", getInstanceIds("Device.X_CISCO_COM_MTA.ServiceFlow.")));
				$odd = true;
				foreach ($ids as $id)
				{
					echo '<tr class="'.(($odd = !$odd)?'odd':'').'" >';
					echo '<td>'.getStr("Device.X_CISCO_COM_MTA.ServiceFlow.$id.SFID").'</td>';
					echo '<td>'.getStr("Device.X_CISCO_COM_MTA.ServiceFlow.$id.ServiceClass").'</td>';
					echo '<td>'.getStr("Device.X_CISCO_COM_MTA.ServiceFlow.$id.Direction").'</td>';
					echo '<td>'.getStr("Device.X_CISCO_COM_MTA.ServiceFlow.$id.DefaultFlow").'</td>';
					echo '<td>'.getStr("Device.X_CISCO_COM_MTA.ServiceFlow.$id.TrafficType").'</td>';
					echo '<td>'.getStr("Device.X_CISCO_COM_MTA.ServiceFlow.$id.NumberOfPackets").'</td>';
					echo '</tr>';
				}
			?>
		</table>
    </div> <!-- end .module -->

    <div class="btn-group">
		&nbsp;&nbsp;&nbsp;&nbsp;<input id="show_DSXlog" name="show_DSXlog" type="button"   value="Show DSX Log" class="btn" />
        <input id="switch_DSXlog" type="button" onClick="save_config('switch_DSXlog')" value="<?php echo getStr('Device.X_CISCO_COM_MTA.DSXLogEnable')!='true'?'Enable':'Disable'; ?> Logging" class="btn alt" />
        <input id="clear_DSXlog"  type="submit" onClick="save_config('clear_DSXlog')"  value="Clear" class="btn" />
    </div>
</div><!-- end #content -->


<?php include('includes/footer.php'); ?>