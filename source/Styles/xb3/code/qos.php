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
<?php include('includes/header.php'); 
if(PREPAID == TRUE){
	echo '<script type="text/javascript">alert("'._("No MTA support for this device").'"); window.history.back(); </script>';
	exit(0);
}
?>
<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Gateway > Connection > CallP/QoS ", "nav-qos");
	$('#show_DSXlog').click(function(){
		jConfirm(
		"<?php echo _("This action may take more than one minute. Do you want to continue?")?>", 
		"<?php echo _("Are You Sure?")?>", 
		function(ret){
			if(ret){
				window.location = "DSXlog.php";
			}
		});
    });
	$('#show_callsignallog').click(function(){
		jConfirm(
		"<?php echo _("This action may take more than one minute. Do you want to continue?")?>", 
		"<?php echo _("Are You Sure?")?>", 
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
		if ($('#switch_callsignallog').attr("value")=="<?php echo _("Enable Logging")?>")	{
			$('#switch_callsignallog').attr("value", "<?php echo _("Disable Logging")?>");
			jsConfig = '{"target":"'+target+'", "value":"'+"true"+'"}';
		}
		else {
			$('#switch_callsignallog').attr("value", "<?php echo _("Enable Logging")?>");
			jsConfig = '{"target":"'+target+'", "value":"'+"false"+'"}';
		}
		set_config(jsConfig);
	}
	else if ("clear_callsignallog" == target) {
		jConfirm(
			"<?php echo _("Are you sure to clear call signal log?")?>", 
			"Confirm", 
			function(ret){if(ret){
				jsConfig = '{"target":"'+target+'", "value":"'+"true"+'"}';
				set_config(jsConfig);
			}
		});
	}
	else if ("switch_DSXlog" == target) {
		if ($('#switch_DSXlog').attr("value")=="<?php echo _("Enable Logging")?>")	{
			$('#switch_DSXlog').attr("value", "<?php echo _("Disable Logging")?>");
			jsConfig = '{"target":"'+target+'", "value":"'+"true"+'"}';
		}
		else {
			$('#switch_DSXlog').attr("value", "<?php echo _("Enable Logging")?>");
			jsConfig = '{"target":"'+target+'", "value":"'+"false"+'"}';
		}
		set_config(jsConfig);
	}	
	else if ("clear_DSXlog" == target) {
		jConfirm(
			"<?php echo _("Are you sure to clear DSX log?")?>", 
			"<?php echo _("Confirm")?>", 
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
	jProgress('<?php echo _("This may take several seconds...")?>', 60);
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
			jAlert("<?php echo _("Failure, please try again.")?>");
		}
	});
}
</script>
<div id="content">
    <h1><?php echo _("Gateway > Connection > CallP/QoS")?> </h1>
    <div id="educational-tip">
		<p class="tip"><?php echo _("This Page shows CallP/QoS statistics of USG.")?></p>
    </div>
    <div class="module data">
		<h2>CALLP </h2>
		<table class="data">
			<tr>
				<th><?php echo _("Line")?></th>
				<th><?php echo _("LC State")?></th>
				<th><?php echo _("CallP State")?></th>
				<th><?php echo _("Loop Current")?></th>
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
		&nbsp;&nbsp;&nbsp;&nbsp;<input id="show_callsignallog" name="show_callsignallog" type="button"  value="<?php echo _("Show Call Signalling Log")?>" class="btn" />
		<input id="switch_callsignallog" type="button" onClick="save_config('switch_callsignallog')"  value="<?php echo getStr('Device.X_CISCO_COM_MTA.CallSignallingLogEnable')!='true'?_('Enable Logging'):_('Disable Logging'); ?>" class="btn alt" />
        <input id="clear_callsignallog"  type="submit" onClick="save_config('clear_callsignallog')"   value="<?php echo _("Clear")?>" class="btn" />
    </div>
	<div class="module data">
		<h2>QoS </h2>
		<table class="data">
			<tr>
				<th>SFID</th>
				<th><?php echo _("Service Class Name")?></th>
				<th><?php echo _("Direction")?></th>
				<th><?php echo _("Primary Flow")?></th>
				<th><?php echo _("Traffic Type")?></th>
				<th><?php echo _("Packets")?></th>
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
		&nbsp;&nbsp;&nbsp;&nbsp;<input id="show_DSXlog" name="show_DSXlog" type="button"   value="<?php echo _("Show DSX Log")?>" class="btn" />
        <input id="switch_DSXlog" type="button" onClick="save_config('switch_DSXlog')" value="<?php echo getStr('Device.X_CISCO_COM_MTA.DSXLogEnable')!='true'?_('Enable Logging'):_('Disable Logging'); ?>" class="btn alt" />
        <input id="clear_DSXlog"  type="submit" onClick="save_config('clear_DSXlog')"  value="<?php echo _("Clear")?>" class="btn" />
    </div>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>