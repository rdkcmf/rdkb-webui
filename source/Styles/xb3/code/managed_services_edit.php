<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: managed_services_add.php 2943 2009-08-25 20:58:43Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
	$i=$_GET['id'];
//	echo "<script>var ID=".$i.";</script>";
    $managed_services_param = array(
        "serviceName"   => "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Description",
        "protocol"      => "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".Protocol",
        "startPort"     => "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartPort",
        "endPort"       => "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndPort",
        "blockStatus"   => "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".AlwaysBlock", 
        "startTime"   => "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".StartTime", 
        "endTime"   => "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".EndTime", 
        "days"   => "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.".$i.".BlockDays", 
	);
    $managed_services_value = KeyExtGet("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.", $managed_services_param);


	$serviceName = $managed_services_value["serviceName"];
	$protocol = $managed_services_value["protocol"];
	$startPort = $managed_services_value["startPort"];
	$endPort = $managed_services_value["endPort"];
	$blockStatus = $managed_services_value["blockStatus"];
	
	global $startTime, $endTime, $days;
	if($blockStatus == "false") {
		$startTime = $managed_services_value["startTime"];
		$endTime = $managed_services_value["endTime"];
		$days = $managed_services_value["days"];
	}

	($blockStatus == "") && ($blockStatus = "true");
?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Parental Control > Managed Services > Add Blocked Service", "nav-services");
    $('#user_defined_service').focus();

	var ID = "<?php echo $i ?>";
	var jsServiceName = "<?php echo $serviceName ?>";
	var jsProtocol = "<?php echo $protocol ?>";
	var jsStartPort = "<?php echo $startPort ?>";
	var jsEndPort = "<?php echo $endPort ?>";
	var jsBlockStatus = <?php echo $blockStatus ?>;
	
	var jsStartTime, jsEndTime, jsDays;
	if(jsBlockStatus == false) {
		jsStartTime = "<?php echo $startTime ?>".split(":");
		jsEndTime = "<?php echo $endTime ?>".split(":");
		jsDays = "<?php echo $days ?>".split(",");
	}
//	alert(jsServiceName+";"+jsProtocol+";"+jsStartPort+";"+jsEndPort+";"+jsBlockStatus+";"+jsStartTime+";"+jsEndTime+";"+jsDays);
	$("#always_switch").radioswitch({
		id: "always-switch",
		radio_name: "block",
		id_on: "yes",
		id_off: "no",
		title_on: "Select always block",
		title_off: "Unselect always block",
		size: "small",
		label_on: "Yes",
		label_off: "No",
		revertOrder: true,
		state: jsBlockStatus ? "on" : "off"
	}).change(function(event, data) {
		updateBlockTimeVisibility($("#always_switch").radioswitch("getState").on ? "yes" : "no")
	});

	function init() {
		$("#user_defined_service").val(jsServiceName);
		$('#protocol').val(jsProtocol);
		$('#start_port').val(jsStartPort);
		$('#end_port').val(jsEndPort);
		
		if(jsBlockStatus == false) {
			updateBlockTimeVisibility("no");
			if((parseInt(jsStartTime[0])>=12)) {
				var tmpHour = Math.abs(parseInt(jsStartTime[0]) - 12);
				(tmpHour === 0) && (tmpHour = 12);
				$('#time_start_hour').val(tmpHour.toString());
				$('#time_start_minute').val(jsStartTime[1]);
				$('#time_start_ampm').val("PM");
			} else {
				(parseInt(jsStartTime[0])===0) && (jsStartTime[0] = 12);
				$('#time_start_hour').val(jsStartTime[0]);
				$('#time_start_minute').val(jsStartTime[1]);
				$('#time_start_ampm').val("AM");
			}
			if((parseInt(jsEndTime[0])>=12)) {
				var tmpHour = Math.abs(parseInt(jsEndTime[0]) - 12);
				(tmpHour === 0) && (tmpHour = 12);
				$('#time_end_hour').val(tmpHour.toString());
				$('#time_end_minute').val(jsEndTime[1]);
				$('#time_end_ampm').val("PM");
			} else {
				(parseInt(jsEndTime[0])===0) && (jsEndTime[0] = 12);
				$('#time_end_hour').val(jsEndTime[0]);
				$('#time_end_minute').val(jsEndTime[1]);
				$('#time_end_ampm').val("AM");
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
			updateBlockTimeVisibility("yes");
		}
	}

	init();

	function updateBlockTimeVisibility(isBlocked) {
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

	jQuery.validator.addMethod("ltstart",function(value,element){
		return this.optional(element) || parseInt(value)>=parseInt($("#start_port").val());
	}, "Please enter a value more than or equal to Start Port.");
	$("#pageForm").validate({
	   rules: {
	       user_defined_service: {
	           required: true
	       }
	       ,start_port: {
	           required: true
	           ,digits: true
			   ,max:65535
			   ,min: 1
	       }
	       ,end_port: {
	           required: true
	           ,digits: true
			   ,max:65535
			   ,min: 1
			   ,ltstart: true
	       }
	        ,day: {
	       	  required: function() {
					return ( $('#no').is(':checked') );
    			}
	       }
	   }
	});


	$("#btn-cancel").click(function() {
		window.location = "managed_services.php";

	});

	$("#btn-sav").click(function(e) {
		if($("#user_defined_service").val()=="FTP") {
			e.preventDefault();

			var href = $(this).attr("href");
			var message = "Duplicate Service Name:"+$("#user_defined_service").val()+"!</br></br>Conflicting Service Block Rule!";

			jAlert(
				message
				, "Add/Edit Service to be Blocked Alert:"
				,function(ret) {
					if(ret) {
					// window.location = href;
					}
				});
		}
	});

	$("#btn-save").click(function(){
		if($("#pageForm").valid()) {
			var service = $('#user_defined_service').val();
			var protocol = $('#protocol').find("option:selected").val();
			var startPort = $('#start_port').val();
			var endPort = $('#end_port').val();
			var block = $('#always_switch').radioswitch("getState").on;
	//		alert(service+";"+protocol+";"+startPort+";"+endPort+";"+block);
			
			if(block) {
				jProgress('This may take several seconds', 60);
				$.ajax({
					type:"POST",
					url:"actionHandler/ajax_managed_services.php",
					data:{edit:"true",ID:ID,service:service,protocol:protocol,startPort:startPort,endPort:endPort,block:block},
					success:function(results){
						//jAlert(results);
						jHide();
						if (results=="Success!") { window.location.href="managed_services.php";}
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
	//			alert(service+";"+protocol+";"+startPort+";"+endPort+";"+block+";"+startTime+";"+endTime+";"+days);
				
				jProgress('This may take several seconds', 60);
				$.ajax({
					type:"POST",
					url:"actionHandler/ajax_managed_services.php",
					data:{edit:"true",ID:ID,service:service,protocol:protocol,startPort:startPort,endPort:endPort,block:block,startTime:startTime,endTime:endTime,days:days},
					success:function(results){
						//jAlert(results);
						jHide();
						if (results=="Success!") { window.location.href="managed_services.php";}
						else jAlert(results);
					},
					error:function(){
						jHide();
						jAlert("Failure, please try again.");
					}
				});
			}
		} else {
				alert("Not valid! Can not be saved.");
		}
	});

});
</script>

<div id="content">

	<h1>Parental Control > Managed Services >Edit Blocked Service</h1>



	<div class="module">
		<div class="forms">
			<h2>Edit Service to be Blocked</h2>

<form id="pageForm"  method="post">

            <div class="form-row">
				<label for="user_defined_service">User Defined Service:</label>
				<input type="text" id="user_defined_service" value="FTP" name="user_defined_service" class="text" />
			</div>

			<div class="form-row">
				<label for="protocol">Protocol:</label>
				<select name="protocol" id="protocol">
				    <option value="TCP" selected="selected">TCP</option>
				    <option value="UDP">UDP</option>
				    <option value="BOTH">TCP/UDP</option>
				</select>
			</div>

			<div class="form-row">
				<label for="start_port">Start Port:</label>
				<input type="text" id="start_port" value="21" name="start_port" class="text" />
			</div>

            <div class="form-row">
				<label for="end_port">End Port:</label>
				<input type="text" id="end_port" value="21" name="end_port" class="text" />
			</div>

			<div class="form-row">
				<label for="on">Always Block?</label>
				<span id="always_switch"></span>
			</div>

        	<div id="block-time">
        		<h3>Set Block Time</h3>

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

<h3>Set Blocked Days</h3>
<div class="select_all_none">
   <a rel="weekday" href="#select_all" id="weekday_select_all" class="">Select All</a> | <a rel="weekday" id="weekday_select_none" href="#select_none" class="">Select None</a>
</div>
<div class="form-row" id="weekday">
   <input class="blockedDay" type="checkbox" name="day" id="monday" value="Mon" checked="checked" /><label class="checkbox" for="monday">Monday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="tuesday" value="Tue" checked="checked" /><label class="checkbox" for="tuesday">Tuesday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="wednesday" value="Wed" checked="checked" /><label class="checkbox" for="wednesday">Wednesday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="thursday" value="Thu" checked="checked" /><label class="checkbox" for="thursday">Thursday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="friday" value="Fri" checked="checked" /><label class="checkbox" for="friday">Friday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="saturday" value="Sat" checked="checked" /><label class="checkbox" for="saturday">Saturday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="sunday" value="Sun" checked="checked" /><label class="checkbox" for="sunday">Sunday</label>
</div>
</div> <!-- end #block-time -->

            <div class="form-row form-btn">
            	<input type="button" id="btn-save" class="btn submit" value="Save"/>
            	<input type="button" id="btn-cancel" class="btn alt reset" value="Cancel"/>
            </div>
    	</div> <!-- end .form -->
	</div> <!-- end .module -->

</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
