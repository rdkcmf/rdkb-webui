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
<!-- $Id: managed_devices_add_computer_blocked.php 2943 2009-08-25 20:58:43Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php
	$i=$_GET['id'];
	$index = explode('_', $i);
	if (!preg_match('/^\d{1,3}$/', $index[0])) die();
	if (!preg_match('/^\d{1,3}$/', $index[1]) && $UTC_local_Time_conversion && array_key_exists(1, $index)) die();
	$managed_devices_param = array(
		"name"			=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[0].".Description",
		"MACAddress"	=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[0].".MACAddress",
		"blockStatus"	=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[0].".AlwaysBlock",
		"StartTime"		=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[0].".StartTime",
		"EndTime"		=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[0].".EndTime",
		"BlockDays"		=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[0].".BlockDays",
	);
	$managed_devices_value = KeyExtGet("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.", $managed_devices_param);
	/*this code is removed because start time and end time is not properly showing whenever three time slot merge in GUI.*/
	/*if($UTC_local_Time_conversion){
		$i = $index[0];
		$managed_devices_get = array();
		$managed_devices_value1 = $managed_devices_value;
		array_push($managed_devices_get, $managed_devices_value1);
		if(array_key_exists(1, $index)){
			$managed_devices_param = array(
				"name"			=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[1].".Description",
				"MACAddress"	=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[1].".MACAddress",
				"blockStatus"	=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[1].".AlwaysBlock",
				"StartTime"		=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[1].".StartTime",
				"EndTime"		=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[1].".EndTime",
				"BlockDays"		=> "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[1].".BlockDays",
			);
			$managed_devices_value2 = KeyExtGet("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.", $managed_devices_param);
			$i = $i.'_'.$index[1];
			array_push($managed_devices_get, $managed_devices_value2);
		}
		$managed_devices_value = array();
		$managed_devices_get = days_time_conversion_get($managed_devices_get, 'MACAddress');
		foreach ($managed_devices_get as $key => $value) {
			foreach ($value as $k => $val) {
				$managed_devices_value[$k] = $val;
			}
			unset($val);
		}
		unset($value);
	}*/
	
	$managed_devices_get = array();
        $managed_devices_value1 = $managed_devices_value;
        array_push($managed_devices_get, $managed_devices_value1);

        $i = $index[0];
        $j = 1;
        if($UTC_local_Time_conversion){ 
          while(array_key_exists($j, $index)){
                $managed_devices_param = array(
                                "name"                  => "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[$j].".Description",
                                "MACAddress"    => "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[$j].".MACAddress",
                                "blockStatus"   => "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[$j].".AlwaysBlock",
                                "StartTime"             => "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[$j].".StartTime",
                                "EndTime"               => "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[$j].".EndTime",
                                "BlockDays"             => "Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.".$index[$j].".BlockDays",
                                );
                        $managed_devices_value2 = KeyExtGet("Device.X_Comcast_com_ParentalControl.ManagedDevices.Device.", $managed_devices_param);
                        $i = $i.'_'.$index[$j];
                        array_push($managed_devices_get, $managed_devices_value2);
                        $j=$j+1;
                }
        }
	
	$managed_devices_value = array();
	$managed_devices_get = days_time_conversion_get($managed_devices_get, 'MACAddress');
	foreach ($managed_devices_get as $key => $value){
		foreach ($value as $k => $val){
			$managed_devices_value[$k] = $val;
		}
		unset($val);
	}
	unset($value);
	
	$name = $managed_devices_value["name"]; 
	$mac = $managed_devices_value["MACAddress"]; 
	$blockStatus = $managed_devices_value["blockStatus"]; 
	global $startTime, $endTime, $days;
	if($blockStatus == "false") {
		$startTime = $managed_devices_value["StartTime"];
		$endTime = $managed_devices_value["EndTime"];
		$days = $managed_devices_value["BlockDays"];
	}
	($blockStatus == "") && ($blockStatus = "true");
	$productLink = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.CloudUI.link");
?>
<script type="text/javascript">
$(document).ready(function() {
	gateway.page.init("Parental Control > Managed Devices > Add Blocked Device", "nav-devices");
	var ID = "<?php echo $i ?>";
	var jsName = "<?php echo $name ?>";
	var jsMac = "<?php echo $mac ?>";
	var jsBlockStatus = <?php echo $blockStatus ?>;
	var jsStartTime, jsEndTime, jsDays;
	if(jsBlockStatus == false) {
		jsStartTime = "<?php echo $startTime ?>".split(":");
		jsEndTime = "<?php echo $endTime ?>".split(":");
		jsDays = "<?php echo $days ?>".split(",");
	}
//	alert(ID+";"+jsName+";"+jsMac+";"+jsBlockStatus+";"+jsStartTime+";"+jsEndTime+";"+jsDays);
	$("#always_switch").radioswitch({
		id: "always-switch",
		radio_name: "block",
		id_on: "yes",
		id_off: "no",
		title_on: "<?php echo _("Select always block")?>",
		title_off: "<?php echo _("Unselect always block")?>",
		size: "small",
		label_on: "<?php echo _("Yes")?>",
		label_off: "<?php echo _("No")?>",
		revertOrder: true,
		state: jsBlockStatus ? "on" : "off"
	}).change(function(event, data) {
		updateBlockedTimeVisibility($("#always_switch").radioswitch("getState").on ? "yes" : "no")
	});
	function init() {
		$("#computer_name").val(jsName);
		$('#mac_address').val(jsMac);
		if(jsBlockStatus == false) {
			$("#no").prop("checked", true);
			jsStartTime[0]	= parseInt(jsStartTime[0]);
			jsEndTime[0]	= parseInt(jsEndTime[0]);
			if((parseInt(jsStartTime[0])>=12)) {
				var tmpHour = Math.abs(parseInt(jsStartTime[0]) - 12);
				(tmpHour === 0) && (tmpHour = 12);
				$('#time_start_hour').val(tmpHour.toString());
				$('#time_start_minute').val(jsStartTime[1]);
				$('#time_start_ampm').val("<?php echo _("PM")?>");
			} else {
				(parseInt(jsStartTime[0])===0) && (jsStartTime[0] = 12);
				$('#time_start_hour').val(jsStartTime[0]);
				$('#time_start_minute').val(jsStartTime[1]);
				$('#time_start_ampm').val("<?php echo _("AM")?>");
			}
			if((parseInt(jsEndTime[0])>=12)) {
				var tmpHour = Math.abs(parseInt(jsEndTime[0]) - 12);
				(tmpHour === 0) && (tmpHour = 12);
				$('#time_end_hour').val(tmpHour.toString());
				$('#time_end_minute').val(jsEndTime[1]);
				$('#time_end_ampm').val("<?php echo _("PM")?>");
			} else {
				(parseInt(jsEndTime[0])===0) && (jsEndTime[0] = 12);
				$('#time_end_hour').val(jsEndTime[0]);
				$('#time_end_minute').val(jsEndTime[1]);
				$('#time_end_ampm').val("<?php echo _("AM")?>");
			}
			$("#weekday input").prop("checked", false);
			var checkObject = document.getElementsByName("day");                          
			for(var j = 0; j < jsDays.length; j++)             
			{             
				for (var i = 0; i < checkObject.length; i++)              
				{             
				    if(checkObject[i].value == jsDays[j])             
				    {             
				        checkObject[i].checked = true;             
				        break;             
				    }             
				}             
			}  
		} else {
			updateBlockedTimeVisibility("yes");
		}
	}
	init();
	function updateBlockedTimeVisibility(isBlocked) {
		if(isBlocked == "yes") {
            $("#block-time *").prop("disabled", true).addClass("disabled");
        } else {
            $("#block-time *").prop("disabled", false).removeClass("disabled");
        }
	}
	$("#weekday_select_all").click(function() {
		if(!$(this).is(".disabled")) {
			$("#weekday input").prop("checked", true);
	  	}
	});
    $("#weekday_select_none").click(function() {
    	if(!$(this).is(".disabled")) {
			$("#weekday input").prop("checked", false);
		}
	});
	$("#pageForm").validate({
		debug: true,
		rules: {
			computer_name: {
				required: true,
				allowed_char: true,
			},
			mac_address: {
				required: true,
				mac: true
			}
			,day: {
	       	  required: function() {
					return ( $('#no').is(':checked') );
    			}
	       }
		}
	});
	$("#btn-cancel").click(function() {
		window.location = "managed_devices.php";
	});
	$("#btn-save").click(function(){
		if($("#pageForm").valid()) {
			var name = $('#computer_name').val();
			var mac = $('#mac_address').val();
			var block = $('#always_switch').radioswitch("getState").on;
	//		alert(name+";"+mac+";"+block);
			var isMacValid = true;
			if(parseInt(mac.split(":")[0], 16)%2 || mac=="00:00:00:00:00:00")
				isMacValid = false;
			if(isMacValid) {
				if(block) {
					jProgress('<?php echo _("This may take several seconds")?>', 60);
					$.ajax({
						type:"POST",
						url:"actionHandler/ajax_managed_devices.php",
						data:{edit:"true",ID:ID,name:name,mac:mac,block:block},
						success:function(results){
							//jAlert(results);
							jHide();
							if (results=="<?php echo _("Success!")?>") { window.location.href="managed_devices.php";}
							else jAlert(results);
						},
						error:function(){
							jHide();
							jAlert("<?php echo _("Failure, please try again.")?>");
						}
					});
				} 
				else {
					var startTime_unit = $('#time_start_ampm').val();
					var endTime_unit   = $('#time_end_ampm').val();
					var startHour = parseInt($('#time_start_hour').val());
					var endHour   = parseInt($('#time_end_hour').val());
					var sminute   = parseInt($('#time_start_minute').val());
					var eminute   = parseInt($('#time_end_minute').val());
					if (startTime_unit === "<?php echo _("PM")?>" && startHour !== 12) {      
						startHour += 12;
					}
					else if (startTime_unit === "<?php echo _("AM")?>" && startHour === 12) {
						startHour = 0;
					}
					if (endTime_unit === "<?php echo _("PM")?>" && endHour !== 12) {      
						endHour += 12;
					}
					else if (endTime_unit === "<?php echo _("AM")?>" && endHour === 12) {
						endHour = 0;
					}
					if ((startHour>endHour) || ((startHour==endHour) && (sminute>=eminute))) {
						jAlert("<?php echo _("Start time should be smaller than End time !")?>");
						return;
					} 	
					(0 === startHour) && (startHour = '00');
					(0 === endHour)   && (endHour   = '00');
					(0 === sminute)   && (sminute   = '00');
					(0 === eminute)   && (eminute   = '00');
					var startTime = startHour + ':' + sminute;
					var endTime   = endHour   + ':' + eminute;	
					var days = "";//Mon, Tue, Wed, Thu, Fri, Sat, Sun.
					var len = $("input[name='day']:checked").length;
					$("input[name='day']:checked").each(function(){
						days = days+$(this).val();
						if(--len)
							days += ",";
					});
		//			alert(name+";"+mac+";"+block+";"+startTime+";"+endTime+";"+days);
					jProgress('<?php echo _("This may take several seconds")?>', 60);
					$.ajax({
						type:"POST",
						url:"actionHandler/ajax_managed_devices.php",
						data:{edit:"true",ID:ID,name:name,mac:mac,block:block,startTime:startTime,endTime:endTime,days:days},
						success:function(results){
							//jAlert(results);
							jHide();
							if (results=="<?php echo _("Success!")?>") { window.location.href="managed_devices.php";}
							else jAlert(results);
						},
						error:function(){
							jHide();
							jAlert("<?php echo _("Failure, please try again.")?>");
						}
					});
				} 
			} else {
				jAlert("<?php echo _("MAC is not valid! Can not be saved.")?>");
			}
		} else {
				jAlert("<?php echo _("Not valid! Can not be saved.")?>");
		}
	});
});
</script>
<?php if($CloudUIEnable == "true"){ ?>
<div id="content">
	<h1><?php echo _("Parental Control > Managed Devices > Edit Blocked Device")?></h1>
	<div class="module forms enable">
		<div id="content" style="text-align: center;">
			<br>
			<h3><?php echo sprintf(_("Managing your home network settings is now easier than ever.<br>Visit <a href='http://%s'>%s</a> to enable parental controls for devices connected<br>to your home network, among many other features and settings."),$productLink, $productLink)?></h3>
			<br>
		</div>
	</div>
</div><!-- end #content -->
<?php } else { ?>
<div id="content">
	<h1><?php echo _("Parental Control > Managed Devices > Edit Blocked Device")?></h1>
	<form id="pageForm" method="post">
	<div class="module">
		<div class="forms">
			<h2><?php echo _("Edit Device to be Blocked")?></h2>
            <div class="form-row">
				<label for="device"><?php echo _("Computer Name:")?></label>
				<input type="text" id="computer_name" value="name" name="computer_name" class="text" />
			</div>
			<div class="form-row">
				<label for="device"><?php echo _("MAC Address:")?></label>
				<input type="text" id="mac_address" value="mac" name="mac_address" class="text" />
			</div>
			<div class="form-row">
				<label for="on"><?php echo _("Always Block?")?></label>
				<span id="always_switch"></span>
			</div>
        	<div id="block-time">
        		<h3><?php echo _("Set Block Time")?></h3>
        		<div class="form-row">
        	<label for="time_start_hour"><?php echo _("Start from:")?></label>
           <select id="time_start_hour" name="time_start_hour">
                <option value"12">12</option>
                <option value"1">1</option>
                <option value"2">2</option>
                <option value"3">3</option>
                <option value"4">4</option>
                <option value"5">5</option>
                <option value"6">6</option>
                <option value"7">7</option>
                <option value"8">8</option>
                <option value"9">9</option>
                <option value"10">10</option>
                <option value"11">11</option>
        </select>
         <label for="time_start_minute" class="acs-hide"></label>
        <select id="time_start_minute" name="time_start_minute">
                <option value"00">00</option>
                <option value"15">15</option>
                <option value"30">30</option>
                <option value"45">45</option>
        </select>
         <label for="time_start_ampm" class="acs-hide"></label>
        <select id="time_start_ampm" name="time_start_ampm">
                <option value"AM"><?php echo _("AM")?></option>
                <option value"PM"><?php echo _("PM")?></option>
        </select>
        </div>
        <div class="form-row">
           <label for="time_end_hour"><?php echo _("End on:")?></label>
           <select id="time_end_hour" name="time_end_hour">
                <option value"12">12</option>
                <option value"1">1</option>
                <option value"2">2</option>
                <option value"3">3</option>
                <option value"4">4</option>
                <option value"5">5</option>
                <option value"6">6</option>
                <option value"7">7</option>
                <option value"8">8</option>
                <option value"9">9</option>
                <option value"10">10</option>
                <option value"11" selected="selected">11</option>
        </select>
        <label for="time_end_minute" class="acs-hide"></label>
        <select id="time_end_minute" name="time_end_minute">
                <option value"00">00</option>
                <option value"15">15</option>
                <option value"30">30</option>
                <option value"45">45</option>
                <option value"59" selected="selected">59</option>
        </select>
        <label for="time_end_ampm" class="acs-hide"></label>
        <select id="time_end_ampm" name="time_end_ampm">
                <option value"AM"><?php echo _("AM")?></option>
                <option value"PM" selected="selected"><?php echo _("PM")?></option>
        </select>
        </div>
<h3><?php echo _("Set Block Days")?></h3>
<div class="select_all_none">
   <a rel="weekday" href="#select_all" id="weekday_select_all" class=""><?php echo _("Select All")?></a> | <a rel="weekday" id="weekday_select_none" href="#select_none" class=""><?php echo _("Select None")?></a>
</div>
<div class="form-row" id="weekday">
   <input type="checkbox" name="day" id="monday" value="Mon" checked="checked" /><label class="checkbox" for="monday"><?php echo _("Monday")?></label><br />
   <input type="checkbox" name="day" id="tuesday" value="Tue" checked="checked" /><label class="checkbox" for="tuesday"><?php echo _("Tuesday")?></label><br />
   <input type="checkbox" name="day" id="wednesday" value="Wed" checked="checked" /><label class="checkbox" for="wednesday"><?php echo _("Wednesday")?></label><br />
   <input type="checkbox" name="day" id="thursday" value="Thu" checked="checked" /><label class="checkbox" for="thursday"><?php echo _("Thursday")?></label><br />
   <input type="checkbox" name="day" id="friday" value="Fri" checked="checked" /><label class="checkbox" for="friday"><?php echo _("Friday")?></label><br />
   <input type="checkbox" name="day" id="saturday" value="Sat" checked="checked" /><label class="checkbox" for="saturday"><?php echo _("Saturday")?></label><br />
   <input type="checkbox" name="day" id="sunday" value="Sun" checked="checked" /><label class="checkbox" for="sunday"><?php echo _("Sunday")?></label>
</div>
</div> <!-- end #block-time -->
            <div class="form-row form-btn">
            	<input type="button" id="btn-save" name="save" class="btn submit" value="<?php echo _("Save")?>"/>
            	<input type="button" id="btn-cancel" name="cancel" class="btn alt reset" value="<?php echo _("Cancel")?>"/>
            </div>
    	</div> <!-- end .form -->
	</div> <!-- end .module -->
    </form>
</div><!-- end #content -->
<?php } ?>
<?php include('includes/footer.php'); ?>