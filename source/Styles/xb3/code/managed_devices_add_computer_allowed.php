<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: managed_devices_add_computer.php 2943 2009-08-25 20:58:43Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Parental Control > Managed Devices > Add Allowed Device", "nav-devices");
    $("input[name='computer']").focus();

	/*	$("input[name='computer']").click(function(event,value) {
		alert(event+";"+value);
	});*/

	$("#always_switch").radioswitch({
		id: "always-switch",
		radio_name: "allow",
		id_on: "yes",
		id_off: "no",
		title_on: "Select always allow",
		title_off: "Unselect always allow",
		size: "small",
		label_on: "Yes",
		label_off: "No",
		revertOrder: true,
		state: "on"
	}).change(function(event, data) {
		updateAllowTimeVisibility($("#always_switch").radioswitch("getState").on ? "yes" : "no")
	});

	function updateAllowTimeVisibility(isAllowed) {
		if(isAllowed == "yes") {
            $("#allow-time *").prop("disabled", true).addClass("disabled");
        } else {
            $("#allow-time *").prop("disabled", false).removeClass("disabled");
        }
	}
	updateAllowTimeVisibility($("#always_switch").radioswitch("getState").on ? "yes" : "no")

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
		
		rules: {
			custom_name: {
				required: {
					depends: function() {
						return($("input:radio[name=computer]:checked").val() == "custom")
					}
				}
			},
			custom_mac: {
				required: {
					depends: function() {
						return($("input:radio[name=computer]:checked").val() == "custom")
					}
				},
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
			var type = "Allow";
		
			var name, mac;
			
			var isMacValid = true;
			
			var computers = document.getElementsByName("computer");
			var len = computers.length;
			for(var i=0;i<len;i++) {
				if(computers[i].checked) {
					if(computers[i].id == "custom") {
						name = $('#custom_name').val();
						mac = $('#custom_mac').val();
					}
					else {
						name = computers[i].value;
						mac = computers[i].id;
					}
					
					if(parseInt(mac.split(":")[0], 16)%2 || mac=="00:00:00:00:00:00")
						isMacValid = false;
				}
			}

			var block = $("#always_switch").radioswitch("getState").on;
	//		alert(name+";"+mac+";"+block);
			
			if(isMacValid) {
				if(block) {
					jProgress('This may take several seconds', 60);
					$.ajax({
						type:"POST",
						url:"actionHandler/ajax_managed_devices.php",
						data:{add:"true",type:type,name:name,mac:mac,block:block},
						success:function(results){
							//jAlert(results);
							jHide();
							if (results=="Success!") { window.location.href="managed_devices.php";}
							else jAlert(results);
						},
						error:function(){
							jHide();
							jAlert("Failure, please try again.");
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

					if (startTime_unit === "PM" && startHour !== 12) {      
						startHour += 12;
					}
					else if (startTime_unit === "AM" && startHour === 12) {
						startHour = 0;
					}

					if (endTime_unit === "PM" && endHour !== 12) {      
						endHour += 12;
					}
					else if (endTime_unit === "AM" && endHour === 12) {
						endHour = 0;
					}

					if ((startHour>endHour) || ((startHour==endHour) && (sminute>=eminute))) {
						jAlert("Start time should be smaller than End time !");
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
					
					jProgress('This may take several seconds', 60);
					$.ajax({
						type:"POST",
						url:"actionHandler/ajax_managed_devices.php",
						data:{add:"true",type:type,name:name,mac:mac,block:block,startTime:startTime,endTime:endTime,days:days},
						success:function(results){
							//jAlert(results);
							jHide();
							if (results=="Success!") { window.location.href="managed_devices.php";}
							else jAlert(results);
						},
						error:function(){
							jHide();
							jAlert("Failure, please try again.");
						}
					});
				} 
			} else {
				jAlert("MAC is not valid! Can not be saved.");
			}
		} else {
				jAlert("Not valid! Can not be saved.");
		}
	});

	$("#pageForm").submit(function(e) {

		if($("#custom_ip").val().toLowerCase()=="01:23:45:67:89:ba" || $("#custom_ip").val().toLowerCase()=="01:23:45:67:89:bb" )
		{
			e.preventDefault();

			var href = $(this).attr("href");
			var message = "Conflicting Block MAC Address: \""+$("#custom_ip").val()+"\"!";

			jAlert(
				message
				, "Add/Edit Device to be Blocked Alert:"
				,function(ret) {
					if(ret) {
					// window.location = href;
					}

			});
		}
	});
});
</script>

<div id="content">
	<h1>Parental Control > Managed Devices > Add Allowed Device</h1>

<form id="pageForm" action="managed_devices.php" method="post">
	<div class="module">
		<div class="forms">
			<h2>Add Device to be Allowed</h2>

            <h3>Set Allowed Device</h3>
            <label style="margin:20px 0 0 20px">Auto-Learned Devices:</label>
			<div class="form-row">
			<table id="add_allowed_device" class="data">
		    	<tr>
		    		<th class="number">&nbsp;</th>
			        <th class="computer_name">Computer Name</th>
			        <th class="ip">MAC Address</th>
			    </tr>
			    <?php 
                    $rootObjName    = "Device.Hosts.Host.";
                    $paramNameArray = array("Device.Hosts.Host.");
                    $mapping_array  = array("HostName", "PhysAddress");
		    		$hostsInstance = array();
                    $hostsInstanceArr = getParaValues($rootObjName, $paramNameArray, $mapping_array);

					$hostIDs=explode(",",getInstanceIDs("Device.Hosts.Host."));
					$iclass="";
					if (empty($hostIDs) || empty($hostIDs[0])) {
						$hostIDs = array();
					}
					foreach ($hostIDs as $key=>$i) {
						$hostsInstance["$i"] = $hostsInstanceArr["$key"];
					}
					foreach ($hostIDs as $key=>$i) {
						if ($iclass=="") {$iclass="odd";} else {$iclass="";}
						$hostName = $hostsInstance["$i"]["HostName"]; 
						$hostMac = $hostsInstance["$i"]["PhysAddress"]; 
						

						echo "
						<tr class=$iclass>
							<th class=\"row-label alt\"><input name=\"computer\" id=\"$hostMac\" type=\"radio\" value=\"$hostName\" /></th>
							<td>".$hostName."</td>
							<td>".$hostMac."</td>
						</tr>";
					} 
				?>

				</table>
				<label style="margin:20px 0 0 15px">Custom Device:</label>
				<div class="form-row">
					<table id="add_allowed_device" class="data">
						<tr>
							<th class="number">&nbsp;</th>
							<th class="computer_name">Computer Name</th>
							<th class="ip">MAC Address</th>
						</tr>
						<tr class="odd">
							<th class="row-label alt"><input type="radio" name="computer" checked="checked" value="custom" id="custom" /></th>
							<td><input type="text" name="custom_name" id="custom_name" /></td>
							<td><input type="text" name="custom_mac" id="custom_mac" /></td>

						</tr>

					</table>
				</div>
			<div class="form-row">
				<label for="on">Always Allow?</label>
				<span id="always_switch"></span>
			</div>

        	<div id="allow-time">
        		<h3>Set Allow Time</h3>

        		<div class="form-row">
        	<label for="time_start_hour">Start from:</label>
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
                <option value"AM">AM</option>
                <option value"PM">PM</option>
        </select>
        </div>
        <div class="form-row">
           <label for="time_end_hour">End on:</label>
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
                <option value"AM">AM</option>
                <option value"PM" selected="selected">PM</option>
        </select>
        </div>

		<h3>Set Allow Days</h3>
		<div class="select_all_none">
		   <a rel="weekday" href="#select_all" id="weekday_select_all" class="">Select All</a> | <a rel="weekday" id="weekday_select_none" href="#select_none" class="">Select None</a>
		</div>
		<div class="form-row" id="weekday">
		   <input type="checkbox" name="day" id="monday" value="Mon" checked="checked" /><label class="checkbox" for="monday">Monday</label><br />
		   <input type="checkbox" name="day" id="tuesday" value="Tue" checked="checked" /><label class="checkbox" for="tuesday">Tuesday</label><br />
		   <input type="checkbox" name="day" id="wednesday" value="Wed" checked="checked" /><label class="checkbox" for="wednesday">Wednesday</label><br />
		   <input type="checkbox" name="day" id="thursday" value="Thu" checked="checked" /><label class="checkbox" for="thursday">Thursday</label><br />
		   <input type="checkbox" name="day" id="friday" value="Fri" checked="checked" /><label class="checkbox" for="friday">Friday</label><br />
		   <input type="checkbox" name="day" id="saturday" value="Sat" checked="checked" /><label class="checkbox" for="saturday">Saturday</label><br />
		   <input type="checkbox" name="day" id="sunday" value="Sun" checked="checked" /><label class="checkbox" for="sunday">Sunday</label>
		</div>
		</div> <!-- end #block-time -->

            <div class="form-row form-btn">
            	<input type="button" id="btn-save" name="save" class="btn submit" value="Save"/>
            	<input type="button" id="btn-cancel" name="cancel" class="btn alt reset" value="Cancel"/>
            </div>
	</div> <!-- end .module -->
	</div>
    	</div> <!-- end .form -->
    </form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
