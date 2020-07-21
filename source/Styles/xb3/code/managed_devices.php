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
<?php
$CloudUIEnable = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CloudUIEnable");
?>
<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: managed_devices.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div  id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php 
	$ret = init_psmMode("Parental Control > Managed Devices", "nav-devices");
	if ("" != $ret){echo $ret;	return;}
?>
<?php
$enableMD = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.Enable");
$allowAll = getStr("Device.X_Comcast_com_ParentalControl.ManagedDevices.AllowAll");
$productLink = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.CloudUI.link");
/*if ($_DEBUG) {
	$enableMD = 'true';
	$allowAll = 'true';
}*/
//add by shunjie
("" == $enableMD) && ($enableMD = "false");
("" == $allowAll) && ($allowAll = "false");
?>
<script  type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Parental Control > Managed Devices", "nav-devices");
	var jsEnableMD = <?php echo $enableMD ?>;
	var jsAllowAll = <?php echo $allowAll ?>;
	$("#managed_devices_switch").radioswitch({
		id: "managed-devices-switch",
		radio_name: "managed_devices",
		id_on: "managed_devices_enabled",
		id_off: "managed_devices_disabled",
		title_on: "<?php echo _("Enable managed devices")?>",
		title_off: "<?php echo _("Disable managed devices")?>",
		state: jsEnableMD ? "on" : "off"
	});
	$("#allow_block_switch").radioswitch({
		id: "allow-block-switch",
		radio_name: "access_type",
		id_on: "allow_all",
		id_off: "block_all",
		title_on: "<?php echo _("Select allow all")?>",
		title_off:"<?php echo _("Select block all")?>",
		label_on: "<?php echo _("Allow All")?>",
		label_off: "<?php echo _("Block All")?>",
		state: jsAllowAll ? "on" : "off"
	});
    $("a.confirm").unbind('click');
	$(".btn").click(function (e) {
		e.preventDefault();
		if ($(this).hasClass('disabled')) {
			return false; // Do something else in here if required
		}
		else
		{
			var btnHander = $(this);
			if (btnHander.attr("id").indexOf("delete")!=-1)	{
				jConfirm(
					"<?php echo _("Are you sure you want to delete this device?")?>"
					,"<?php echo _("Are You Sure?")?>"
					,function(ret) {
						if(ret) {
							delVal = btnHander.attr('href').substring(btnHander.attr('href').indexOf("=")+1);
							jProgress('<?php echo _("This may take several seconds.")?>',60);
							$.ajax({
								type:"POST",
								url:"actionHandler/ajax_managed_devices.php",
								data:{del:delVal},
								success:function(){
									jHide();
									window.location.reload();
								},
								error:function(){
									jHide();
									jAlert("<?php echo _("Error! Please try later!")?>");
								}
							});
						}
					}
				);
			}
			else {
				window.location.href = $(this).attr('href');
			}
		}
	});
	// only run once on init
	if (false == jsEnableMD)
	{
		$('.main_content *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
		$("#allow_block_switch").radioswitch("doEnable", false);
	}	
	if(jsAllowAll == true) {
		updateAllowedDevicesVisibility("allow_all");
	} else if(jsAllowAll == false) {
		updateAllowedDevicesVisibility("block_all");
	}
	function updateAllowedDevicesVisibility(accessType) {
		if(accessType == "allow_all") {
            $("#allowed-devices").hide();
            $("#blocked-devices").show();
        } else {
            $("#blocked-devices").hide();
            $("#allowed-devices").show();
        }
	}
	$("#managed_devices_switch").change(function() {
		var UMDStatus = $("#managed_devices_switch").radioswitch("getState").on ? "Enabled" : "Disabled";
		jProgress('This may take several seconds', 60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_managed_devices.php",
			data:{set:"true",UMDStatus:UMDStatus},
			success:function(results){
				//jAlert(results);
				jHide();
				if (UMDStatus!=results){ 
					jAlert("<?php echo _("Could not do it!")?>");
					$("#managed_devices_switch").radioswitch("doSwitch", results === 'Enabled' ? 'on' : 'off');
				}
				var isUMGDDisabled = $("#managed_devices_switch").radioswitch("getState").on === false;
				if(isUMGDDisabled) {
					// $("#allowed-devices").prop("disabled",true).addClass("disabled");
					// $("#blocked-devices").prop("disabled",true).addClass("disabled");
					$('.main_content *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
					$("#allow_block_switch").radioswitch("doEnable", false);
				} else {
					// $("#allowed-devices").prop("disabled",false).removeClass("disabled");
					// $("#blocked-devices").prop("disabled",false).removeClass("disabled");
					//shunjie, only enable to reload the radio-btn status
					// $('.main_content *').removeClass("disabled");
					location.reload();
				}
			},
			error:function(){
				jHide();
				jAlert("<?php echo _("Failure, please try again.")?>");
			}
		});
	});
	$("#allow_block_switch").change(function() {
		var isAllowBlock = $("#allow_block_switch").radioswitch("getState").on;
		if(isAllowBlock)
			updateAllowedDevicesVisibility("allow_all");
		else
			updateAllowedDevicesVisibility("block_all");
		var AllowBlock = isAllowBlock ? "allow_all" : "block_all";
		jProgress('<?php echo _("This may take several seconds")?>', 60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_managed_devices.php",
			data:{allow_block:"true",AllowBlock:AllowBlock},
			success:function(results){
				//jAlert(results);
				jHide();
				window.location.href="managed_devices.php";
/*				if (AllowBlock!=results){ 
					jAlert("Could not do it!");
					$("input[ name='access_type']").each(function(){
						if($(this).val()==results){$(this).parent().addClass("selected");$(this).prop("checked",true);}
						else{$(this).parent().removeClass("selected");$(this).prop("checked",false);}
					});
				}*/
			},
			error:function(){
				jHide();
				jAlert("<?php echo _("Failure, please try again.")?>");
			}
		});
	});
});
</script>
<?php if($CloudUIEnable == "true"){ ?>
<div  id="content">
	<h1><?php echo _("Parental Control > Managed Devices")?></h1>
	<div class="module forms enable">
		<div id="content" style="text-align: center;">
			<br>
			<h3><?php echo sprintf(_("Managing your home network settings is now easier than ever.<br>Visit <a href='http://%s'>%s</a> to enable parental controls for devices connected<br>to your home network, among many other features and settings."),$productLink, $productLink)?></h3>
			<br>
		</div>
	</div> <!-- end .module -->
</div><!-- end #content -->
<?php } else { ?>
<div  id="content" class="main_content">
	<h1><?php echo _("Parental Control > Managed Devices")?></h1>
	<div  id="educational-tip">
			<p class="tip"><?php echo _("Manage access by specific devices on your network.")?></p>
			<p class="hidden"><?php echo _("Select <strong>Enable</strong> to manage network devices, or <strong>Disable</strong> to turn off.")?></p>
			<p class="hidden"><?php echo _("<strong>Access Type:</strong> If you don't want your devices to be restricted, select <strong>Allow All</strong>. Then select <strong>+ADD BLOCKED DEVICE</strong> to add only the device you want to restrict.")?></p>
			<p class="hidden"><?php echo _("If you want your devices to be restricted, select <strong>Block All.</strong> Click <strong>+ADD ALLOWED DEVICE</strong> to add the device you don't want to restrict.")?></p>
	</div>
	<div class="module forms enable">
		<h2><?php echo _("Managed Devices")?></h2>
		<div class="form-row">
			<span class="readonlyLabel label"><?php echo _("Managed Devices:")?></span>
			<span id="managed_devices_switch"></span>
		</div>
		<div class="form-row">
			<label for="access_type"><?php echo _("Access Type:")?></label>
			<span id="allow_block_switch"></span>
		</div>
	</div>
	<?php 
	$rootObjName	= "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.";
	$paramNameArray	= array("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
	$mapping_array	= array("Type", "Description", "MACAddress", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");
	$blockedDevicesInstanceArr = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
	if($UTC_local_Time_conversion) $blockedDevicesInstanceArr = days_time_conversion_get($blockedDevicesInstanceArr, array('Type', 'MACAddress'));
		$allowCnt=0;
		$blockCnt=0;
		$arrayAllowName=array();
		$arrayBlockName=array();
		$blockedDevicesInstance = array();
		foreach ($blockedDevicesInstanceArr as $key=>$value) {
			$value["Description"] = htmlspecialchars($value["Description"], ENT_NOQUOTES, 'UTF-8');
			$type = $value["Type"]; 
			if($type == "Allow") {
				$arrayAllowID[$allowCnt] = $value["__id"];
				$arrayAllowName[$allowCnt] = $value["Description"];
				$arrayAllowMAC[$allowCnt] = $value["MACAddress"]; 
				$blockStatus = $value["AlwaysBlock"]; 
				if($blockStatus == "true")
					$arrayAllowStatus[$allowCnt] = "Always";
				else if($blockStatus == "false") {
					//$arrayAllowStatus[$allowCnt] = "Period";
					$stime = $value["StartTime"]; 
					$etime = $value["EndTime"]; 
					$bdays = $value["BlockDays"]; 
				    $arrayAllowStatus[$allowCnt] = $stime."-".$etime.",".$bdays;
				}
				$allowCnt++;
			} 
			else if($type == "Block") {
				$arrayBlockID[$blockCnt] = $value["__id"];
				$arrayBlockName[$blockCnt] = $value["Description"]; 
				$arrayBlockMAC[$blockCnt] = $value["MACAddress"]; 
				$blockStatus = $value["AlwaysBlock"]; 
				if($blockStatus == "true")
					$arrayBlockStatus[$blockCnt] = "Always";
				else if($blockStatus == "false"){
					//$arrayBlockStatus[$blockCnt] = "Period";
					$stime = $value["StartTime"]; 
					$etime = $value["EndTime"]; 
					$bdays = $value["BlockDays"]; 
				    $arrayBlockStatus[$blockCnt] = $stime."-".$etime.",".$bdays;
				}
				$blockCnt++;
			}			
		}
	?>
	<div  id="allowed-devices" class="module data">
		<h2><?php echo _("Allowed Devices")?></h2>
		<p class="button"><a tabindex='0' href="managed_devices_add_computer_allowed.php" class="btn"  id="add-allowed-devices"><?php echo _("+ ADD ALLOWED DEVICE")?></a></p>
		<table class="data" summary="This table lists allowed devices">
		    <tr>
		    	<th id="allowed-number" class=" number">&nbsp;</th>
		        <th id="allowed-device-name" class=""><?php echo _("Computer Name")?></th>
		        <th id="allowed-mac-address" class=""><?php echo _("MAC Address")?></th>
		        <th id="allowed-time" class=""><?php echo _("When Allowed")?></th>
		        <th id="allowed-edit-button" class=" edit">&nbsp;</th>
        		<th id="allowed-delete-button" class=" delete">&nbsp;</th>
		    </tr>
		    <?php 
				$iclass="even";
				for($i=0;$i<count($arrayAllowName);$i++) {
					if ($iclass=="even") {$iclass="odd";} else {$iclass="even";}
					$j = $i + 1;				
					echo "
					<tr class=$iclass>
						<td headers='allowed-number' class=\"row-label alt number\">$j</td>
						<td headers='allowed-device-name'>".$arrayAllowName[$i]."</td>
						<td headers='allowed-mac-address'>".$arrayAllowMAC[$i]."</td>
						<td headers='allowed-time'>"._($arrayAllowStatus[$i])."</td>
						<td headers='allowed-edit-button' class=\"edit\"><a tabindex='0' href=\"managed_devices_edit_allowed.php?id=$arrayAllowID[$i]\" class=\"btn\"  id=\"edit_$arrayAllowID[$i]\">"._("Edit")."</a></td>
						<td headers='allowed-delete-button' class=\"delete\"><a tabindex='0' href=\"actionHandler/ajax_managed_devices.php?del=$arrayAllowID[$i]\" class=\"btn confirm\" title=\""._("Delete this device")."\" id=\"delete_$arrayAllowID[$i]\">x</a></td>
					</tr>"; 
				} 
			?>
		    <tfoot>
				<tr class="acs-hide">
					<td headers="allowed-number">null</td>
					<td headers="allowed-device-name">null</td>
					<td headers="allowed-mac-address">null</td>
					<td headers="allowed-time">null</td>
					<td headers="allowed-edit-button">null</td>
					<td headers="allowed-delete-button">null</td>
				</tr>
			</tfoot>
		</table>
	</div> <!-- end .module -->
	<div  id="blocked-devices" class="module data">
		<h2><?php echo _("Blocked Devices")?></h2>
		<p class="button"><a tabindex='0' href="managed_devices_add_computer_blocked.php" class="btn"  id="add-blocked-devices"><?php echo _("+ ADD BLOCKED DEVICE")?></a></p>
		<table class="data" summary="This table lists blocked devices">
		    <tr> 	
        		<th id="blocked-number" class=" number">&nbsp;</th>
		        <th id="blocked-device-name" class=""><?php echo _("Computer Name")?></th>
		        <th id="blocked-mac-address" class=""><?php echo _("MAC Address")?></th>
		        <th id="blocked-time" class=""><?php echo _("When Blocked")?></th>
		        <th id="blocked-edit-button" class=" edit">&nbsp;</th>
        		<th id="blocked-delete-button" class=" delete">&nbsp;</th>
		    </tr> 
		    <?php 
				$iclass="";
				for($i=0;$i<count($arrayBlockName);$i++) {
					if ($iclass=="") {$iclass="odd";} else {$iclass="";}	
					$j = $i + 1;			
					echo "
					<tr class=$iclass>
						<td headers='blocked-number' class=\"row-label alt number\">$j</td>
						<td headers='blocked-device-name'>".$arrayBlockName[$i]."</td>
						<td headers='blocked-mac-address'>".$arrayBlockMAC[$i]."</td>
						<td headers='blocked-time'>"._($arrayBlockStatus[$i])."</td>
						<td headers='blocked-edit-button' class=\"edit\"><a tabindex='0' href=\"managed_devices_edit_blocked.php?id=$arrayBlockID[$i]\" class=\"btn\"  id=\"edit_$arrayBlockID[$i]\">"._("Edit")."</a></td>
						<td headers='blocked-delete-button' class=\"delete\"><a tabindex='0' href=\"actionHandler/ajax_managed_devices.php?del=$arrayBlockID[$i]\" class=\"btn confirm\" title=\""._("Delete this device")."\" id=\"delete_$arrayBlockID[$i]\">x</a></td>
					</tr>"; 
				} 
			?>
		    <tfoot>
				<tr class="acs-hide">
					<td headers="blocked-number">null</td>
					<td headers="blocked-device-name">null</td>
					<td headers="blocked-mac-address">null</td>
					<td headers="blocked-time">null</td>
					<td headers="blocked-edit-button">null</td>
					<td headers="blocked-delete-button">null</td>
				</tr>
			</tfoot>
		</table>
	</div> <!-- end .module -->
</div><!-- end #content -->
<?php } ?>
<?php include('includes/footer.php'); ?>
