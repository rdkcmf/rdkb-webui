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
if ($_DEBUG) {
	$enableMD = 'true';
	$allowAll = 'true';
}

//add by shunjie
("" == $enableMD) && ($enableMD = "false");
("" == $allowAll) && ($allowAll = "false");

?>

<script  type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Parental Control > Managed Devices", "nav-devices");

	var jsEnableMD = <?php echo $enableMD ?>;
	var jsAllowAll = <?php echo $allowAll ?>;

	$("#managed_devices_switch").radioswitch({
		id: "managed-devices-switch",
		radio_name: "managed_devices",
		id_on: "managed_devices_enabled",
		id_off: "managed_devices_disabled",
		title_on: "Enable managed devices",
		title_off: "Disable managed devices",
		state: jsEnableMD ? "on" : "off"
	});

	$("#allow_block_switch").radioswitch({
		id: "allow-block-switch",
		radio_name: "access_type",
		id_on: "allow_all",
		id_off: "block_all",
		title_on: "Select allow all",
		title_off: "Select block all",
		label_on: "Allow All",
		label_off: "Block All",
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
					"Are you sure you want to delete this device?"
					,"Are You Sure?"
					,function(ret) {
						if(ret) {
							window.location.href = btnHander.attr('href');
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
					jAlert("Could not do it!");
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
				jAlert("Failure, please try again.");
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
		jProgress('This may take several seconds', 60);
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
				jAlert("Failure, please try again.");
			}
		});

	});

});

</script>

<div  id="content" class="main_content">
	<h1>Parental Control > Managed Devices</h1>
	<div  id="educational-tip">
			<p class="tip">Manage access by specific devices on your network.</p>
			<p class="hidden">Select <strong>Enable</strong> to manage network devices, or <strong>Disable</strong> to turn off.</p>
			<p class="hidden"><strong>Access Type:</strong> If you don't want your devices to be restricted, select <strong>Allow All</strong>. Then select <strong>+ADD BLOCKED DEVICE</strong> to add only the device you want to restrict.</p>
			<p class="hidden">If you want your devices to be restricted, select <strong>Block All.</strong> Click <strong>+ADD ALLOWED DEVICE</strong> to add the device you don't want to restrict.</p>
	</div>
	<div class="module forms enable">

		<h2>Managed Devices</h2>
		<div class="form-row">
			<span class="readonlyLabel label">Managed Devices:</span>
			<span id="managed_devices_switch"></span>
		</div>
		<div class="form-row">
			<label for="access_type">Access Type:</label>
			<span id="allow_block_switch"></span>
		</div>

	</div>
	
	<?php 
    $rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.";
	$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.");
	$mapping_array  = array("Type", "Description", "MACAddress", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");
	
	$blockedDevicesInstanceArr = getParaValues($rootObjName, $paramNameArray, $mapping_array);
		$MDIDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device."));
		$allowCnt=0;
		$blockCnt=0;
		$arrayAllowName=array();
		$arrayBlockName=array();
		$blockedDevicesInstance = array();
		foreach ($MDIDs as $key=>$i) {
			$blockedDevicesInstance["$i"] = $blockedDevicesInstanceArr["$key"];
		}
		foreach ($MDIDs as $key=>$i) {
			$type = $blockedDevicesInstance["$i"]["Type"]; 
			if($type == "Allow") {
				$arrayAllowID[$allowCnt] = $i;
				$arrayAllowName[$allowCnt] = $blockedDevicesInstance["$i"]["Description"];
				$arrayAllowMAC[$allowCnt] = $blockedDevicesInstance["$i"]["MACAddress"]; 
				$blockStatus = $blockedDevicesInstance["$i"]["AlwaysBlock"]; 
				if($blockStatus == "true")
					$arrayAllowStatus[$allowCnt] = "Always";
				else if($blockStatus == "false") {
					//$arrayAllowStatus[$allowCnt] = "Period";
					$stime = $blockedDevicesInstance["$i"]["StartTime"]; 
					$etime = $blockedDevicesInstance["$i"]["EndTime"]; 
					$bdays = $blockedDevicesInstance["$i"]["BlockDays"]; 
				    $arrayAllowStatus[$allowCnt] = $stime."-".$etime.",".$bdays;
				}
					
				$allowCnt++;
			} 
			else if($type == "Block") {
				$arrayBlockID[$blockCnt] = $i;
				$arrayBlockName[$blockCnt] = $blockedDevicesInstance["$i"]["Description"]; 
				$arrayBlockMAC[$blockCnt] = $blockedDevicesInstance["$i"]["MACAddress"]; 
				$blockStatus = $blockedDevicesInstance["$i"]["AlwaysBlock"]; 
				if($blockStatus == "true")
					$arrayBlockStatus[$blockCnt] = "Always";
				else if($blockStatus == "false"){
					//$arrayBlockStatus[$blockCnt] = "Period";
					$stime = $blockedDevicesInstance["$i"]["StartTime"]; 
					$etime = $blockedDevicesInstance["$i"]["EndTime"]; 
					$bdays = $blockedDevicesInstance["$i"]["BlockDays"]; 
				    $arrayBlockStatus[$blockCnt] = $stime."-".$etime.",".$bdays;
				}
				$blockCnt++;
			}			
		}
	?>
	
	<div  id="allowed-devices" class="module data">
		<h2>Allowed Devices</h2>
		<p class="button"><a tabindex='0' href="managed_devices_add_computer_allowed.php" class="btn"  id="add-allowed-devices">+ ADD ALLOWED DEVICE</a></p>
		<table class="data" summary="This table lists allowed devices">
		    <tr>
		    	<th id="allowed-number" class=" number">&nbsp;</th>
		        <th id="allowed-device-name" class="">Computer Name</th>
		        <th id="allowed-mac-address" class="">MAC Address</th>
		        <th id="allowed-time" class="">When Allowed</th>
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
						<td headers='allowed-time'>".$arrayAllowStatus[$i]."</td>
						<td headers='allowed-edit-button' class=\"edit\"><a tabindex='0' href=\"managed_devices_edit_allowed.php?id=$arrayAllowID[$i]\" class=\"btn\"  id=\"edit_$arrayAllowID[$i]\">Edit</a></td>
						<td headers='allowed-delete-button' class=\"delete\"><a tabindex='0' href=\"actionHandler/ajax_managed_devices.php?del=$arrayAllowID[$i]\" class=\"btn confirm\" title=\"Delete this device\" id=\"delete_$arrayAllowID[$i]\">x</a></td>
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
		<h2>Blocked Devices</h2>
		<p class="button"><a tabindex='0' href="managed_devices_add_computer_blocked.php" class="btn"  id="add-blocked-devices">+ ADD BLOCKED DEVICE</a></p>
		<table class="data" summary="This table lists blocked devices">
		    <tr> 	
        		<th id="blocked-number" class=" number">&nbsp;</th>
		        <th id="blocked-device-name" class="">Computer Name</th>
		        <th id="blocked-mac-address" class="">MAC Address</th>
		        <th id="blocked-time" class="">When Blocked</th>
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
						<td headers='blocked-time'>".$arrayBlockStatus[$i]."</td>
						<td headers='blocked-edit-button' class=\"edit\"><a tabindex='0' href=\"managed_devices_edit_blocked.php?id=$arrayBlockID[$i]\" class=\"btn\"  id=\"edit_$arrayBlockID[$i]\">Edit</a></td>
						<td headers='blocked-delete-button' class=\"delete\"><a tabindex='0' href=\"actionHandler/ajax_managed_devices.php?del=$arrayBlockID[$i]\" class=\"btn confirm\" title=\"Delete this device\" id=\"delete_$arrayBlockID[$i]\">x</a></td>
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

<?php include('includes/footer.php'); ?>
