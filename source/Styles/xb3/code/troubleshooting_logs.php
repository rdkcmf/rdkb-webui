<?php include('includes/header.php'); ?>

<!-- $Id: troubleshooting_logs.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<link rel="stylesheet" type="text/css" href="./cmn/css/lib/smartpaginator.css"/>
<script type="text/javascript" src="./cmn/js/lib/smartpaginator.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#system").show();
	$("#event").hide();
	$("#firewall").hide();
	
	$("#system_logs_today").show();
	$("#system_logs_yesterday").hide();
	$("#system_logs_week").hide();
	$("#system_logs_month").hide();
	$("#system_logs_last").hide();
	
	comcast.page.init("Troubleshooting > Logs", "nav-logs");
	
	$('input[value="Print"]').prop("disabled",true).addClass("disabled");
	$('input[value="Download"]').prop("disabled",true).addClass("disabled");
	
	$('input[value="Print"]').click(function() {
    		window.print();
	});

	$("#showlogs").click(function() {
	
		var mode=$("select#log_type").val();
		var timef=$("select#time_frame").val();
		
		jConfirm(
		'This action may take more than one minute. Do you want to continue?'
		, 'Are You Sure?'
		, function(ret){
			if(ret){
			
		if(mode == "system") {
			$("#event").hide();
			$("#firewall").hide();
			$("#system").show();
            if(timef == "Today"){
				$("#system_logs_today").show();
				$("#system_logs_yesterday").hide();
				$("#system_logs_week").hide();
				$("#system_logs_month").hide();
				$("#system_logs_last").hide();
			}
			else if(timef == "Yesterday"){
				$("#system_logs_today").hide();
            	$("#system_logs_yesterday").show();
				$("#system_logs_week").hide();
				$("#system_logs_month").hide();
				$("#system_logs_last").hide();
			}
			else if(timef == "Last week"){
				$("#system_logs_today").hide();
				$("#system_logs_yesterday").hide();
				$("#system_logs_week").show();
				$("#system_logs_month").hide();
				$("#system_logs_last").hide();
			}
			else if(timef == "Last month"){
				$("#system_logs_today").hide();
			  	$("#system_logs_yesterday").hide();
				$("#system_logs_week").hide();
				$("#system_logs_month").show();
				$("#system_logs_last").hide();
			}
			else if(timef == "Last 90 days"){
				$("#system_logs_today").hide();
				$("#system_logs_yesterday").hide();
				$("#system_logs_week").hide();
				$("#system_logs_month").hide();
				$("#system_logs_last").show();
			}
		}
		else if(mode == "event") {
			$("#event").show();
			$("#firewall").hide();
			$("#system").hide();
	        if(timef == "Today"){
				$("#event_logs_today").show();
				$("#event_logs_yesterday").hide();
				$("#event_logs_week").hide();
				$("#event_logs_month").hide();
				$("#event_logs_last").hide();
			}
			else if(timef == "Yesterday"){
				$("#event_logs_today").hide();
				$("#event_logs_yesterday").show();
				$("#event_logs_week").hide();
				$("#event_logs_month").hide();
				$("#event_logs_last").hide();
			}
			else if(timef == "Last week"){
				$("#event_logs_today").hide();
				$("#event_logs_yesterday").hide();
				$("#event_logs_week").show();
				$("#event_logs_month").hide();
				$("#event_logs_last").hide();
			}
			else if(timef == "Last month"){
				$("#event_logs_today").hide();
	            $("#event_logs_yesterday").hide();
				$("#event_logs_week").hide();
				$("#event_logs_month").show();
				$("#event_logs_last").hide();
			}
			else if(timef == "Last 90 days"){
				$("#event_logs_today").hide();
				$("#event_logs_yesterday").hide();
				$("#event_logs_week").hide();
				$("#event_logs_month").hide();
				$("#event_logs_last").show();
			}
		}
        else if(mode == "firewall") {
			$("#event").hide();
			$("#firewall").show();
		    $("#system").hide();
			if(timef == "Today"){
				//$("#firewall_time").text(" All logs for Today");
				$("#firewall_logs_today").show();
				$("#firewall_logs_yesterday").hide();
				$("#firewall_logs_week").hide();
				$("#firewall_logs_month").hide();
				$("#firewall_logs_last").hide();
			}
			else if(timef == "Yesterday"){
				//$("#firewall_time").text("All logs for Yesterday");
				$("#firewall_logs_today").hide();
				$("#firewall_logs_yesterday").show();
				$("#firewall_logs_week").hide();
				$("#firewall_logs_month").hide();
				$("#firewall_logs_last").hide();
			}
			else if(timef == "Last week"){
				//$("#firewall_time").text("All logs for Last week");
				$("#firewall_logs_today").hide();
				$("#firewall_logs_yesterday").hide();
				$("#firewall_logs_week").show();
				$("#firewall_logs_month").hide();
				$("#firewall_logs_last").hide();
			}
			else if(timef == "Last month"){
				//$("#firewall_time").text("All logs for Last month");
				$("#firewall_logs_today").hide();
				$("#firewall_logs_yesterday").hide();
				$("#firewall_logs_week").hide();
				$("#firewall_logs_month").show();
				$("#firewall_logs_last").hide();
			}
			else if(timef == "Last 90 days"){
				//$("#firewall_time").text("All logs for Last 90 days");
				$("#firewall_logs_today").hide();
				$("#firewall_logs_yesterday").hide();
				$("#firewall_logs_week").hide();
				$("#firewall_logs_month").hide();
				$("#firewall_logs_last").show();
			}
	    }
		
		$('input[value="Print"]:visible').prop("disabled",true).addClass("disabled");
		$('input[value="Download"]:visible').prop("disabled",true).addClass("disabled");		
		
		ajaxDo(mode,timef);
			}
		});
	});
});

function adjust_acs_tb(tb_summary, th_array){
	var theTable = $("table:visible");
	
	//summary the table
	theTable.attr("summary", tb_summary);
	
	//replace td with th, assign id to th (must in a thead)
	var str = theTable.find("thead > tr").text();
	for (var i=0; i<th_array.length; i++){
		if (0==i){
			str  = '<th id="'+th_array[i]+'">'+str+'</th>';
		}
		else{
			str += '<th id="'+th_array[i]+'" class="acs-blue"></th>';
		}
	}
	theTable.find("thead > tr").html(str);
	
	//assign headers to td (must in a tbody)
	theTable.find("tbody > tr").each(function(){
		for (var i=0; i<th_array.length; i++){
			$(this).find('td:eq('+i+')').attr("headers", th_array[i]);
		}
	});
}

function ajaxDo(mode,timef){
	switch(timef){
		case "Today":
			timef2="today";
			break;
		case "Yesterday":
			timef2="yesterday";
			break;
		case "Last week":
			timef2="week";
			break;
		case "Last month":
			timef2="month";
			break;
		default:
			timef2="last";
	}
	jProgress('This may take several seconds.',180);
	$.ajax({
		type:"POST",
		url:"actionHandler/ajax_troubleshooting_logs.php",
		data:{mode:mode,timef:timef},
		dataType:"json",
		success:function(results){
			jHide();
			var length=0;
			var trClass="odd";
			$("#"+mode+"_logs_"+timef2+" > tbody").empty();
			$.each(results,function(key,value){
				if (mode=="system") {
					$("#"+mode+"_logs_"+timef2+" > tbody").append("<tr class='"+trClass+"'><td>"+value.Des+"</td><td>"+value.time+"</td><td>"+value.Level+"</td></tr>");
				} else if (mode=="event") {
					$("#"+mode+"_logs_"+timef2+" > tbody").append("<tr class='"+trClass+"'><td>"+value.Des+"</td><td>"+value.time+"</td><td>"+value.Level+"</td></tr>");
				} else {
					$("#"+mode+"_logs_"+timef2+" > tbody").append("<tr class='"+trClass+"'><td>"+value.Des+", "+value.Count+" Attempts, "+value.time+"</td><td>"+value.Type+"</td><td></td></tr>");
				}	// need to modify by new SNMP file 
				trClass=((trClass=="")?"odd":"");
				length++;
			});
			
			if(length>0){
				$('input[value="Print"]:visible').prop("disabled",false).removeClass("disabled");
				$('input[value="Download"]:visible').prop("disabled",false).removeClass("disabled");
			}
			// alert(length+mode+'_logs_'+timef2);
			if(length>20){
				$(".smart_paginator").empty();
				$(".smart_paginator:visible").smartpaginator({
					totalrecords:length,
					recordsperpage:20,
					datacontainer:mode+'_logs_'+timef2,
					dataelement:'tr',
					theme:'blue'
				});
			} 
			else {
				$(".smart_paginator:visible").hide();
			}
			//adjust current data table
			adjust_acs_tb("This is "+mode+" logs, for "+timef, Array("Discription", "Time", "Level"));
		},
		error: function(){
			jHide();
			jAlert("Something wrong, please try again.");
		}
	});
}
</script>

<div id="content">

	<h1>Troubleshooting > Logs</h1>
	<div id="educational-tip" class="noprint">
		<p class="tip">View information about the Gateway's performance and system operation.</p>
		<p class="hidden">Use the logs to troubleshoot issues and to identify potential security risks.</p>
	</div>


	<div class="module noprint">
		<h2>Log Filters</h2>

    <form action="troubleshooting_logs_sample.php" method="post">
			<label for="log_type" class="readonlyLabel">Log Type:</label>
			<select id="log_type" name="log_type">
				<option value="system" selected="selected">System Logs</option>
				<option value="event">Event Logs</option>
				<option value="firewall">Firewall Logs</option>
			</select>

			<label for="time_frame" class="readonlyLabel">Time Frame:</label>
			<select id="time_frame" name="time_frame">
				<option selected="selected">Today</option>
				<option>Yesterday</option>
				<option>Last week</option>
				<option>Last month</option>
				<option>Last 90 days</option>
			</select>

			<input  id="showlogs" type="button" value="Show Logs" class="btn alt" />
	</div>

	<div class="module forms data" id="system">
		<h2>System Logs</h2>

		<table id="system_logs_today" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3">All logs for Today</td>
				</tr>
			</thead>
				<tbody>
			</tbody>
		</table>
		
		<table id="system_logs_yesterday" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3">All logs from Yesterday</td>
				</tr>
			</thead>
				<tbody>
			</tbody>
		</table>

		<table id="system_logs_week" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3">All logs from Last Week</td>
				</tr>
			</thead>
				<tbody>
			</tbody>
		</table>

		<table id="system_logs_month" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3">All logs from Last Month</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table id="system_logs_last" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3">All logs for Last 90 Days</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<div class="smart_paginator"></div>
		<div class="btn-group">
			<input type="button" value="Print" class="btn alt"/>
			<input type="submit" value="Download" class="btn alt"/>
		</div>
	</div> <!-- end .module -->

	<div class="module forms data" id="event">
		<h2>Event Logs</h2>

		<table id="event_logs_today" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3">All logs for Today</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="event_logs_yesterday" cellpadding="0" cellspacing="0" class="data">
				<thead>
					<tr>
					<td class="acs-th" scope="col" colspan="3">All logs from Yesterday</td>
					</tr>
				</thead>
				<tbody>
				</tbody>
		</table>
		<table id="event_logs_week" cellpadding="0" cellspacing="0" class="data">
				<thead>
					<tr>
					<td class="acs-th" scope="col" colspan="3">All logs from Last Week</td>
					</tr>
				</thead>
				<tbody>
				</tbody>
		</table>

		<table id="event_logs_month" cellpadding="0" cellspacing="0" class="data">
				<thead>
					<tr>
					<td class="acs-th" scope="col" colspan="3">All logs from Last Month</td>
					</tr>
				</thead>
				<tbody>
				</tbody>
		</table>

		<table id="event_logs_last" cellpadding="0" cellspacing="0" class="data">
				<thead>
					<tr>
					<td class="acs-th" scope="col" colspan="3">All logs for Last 90 Days</td>
					</tr>
				</thead>
				<tbody>
				</tbody>
		</table>
		<div class="smart_paginator"></div>
		<div class="btn-group">
			<input type="button" value="Print" class="btn alt"/>
			<input type="submit" value="Download" class="btn alt"/>
			<!--a  id="download_event_logs" href="#" class="btn alt">Download</a-->
		</div>
	</div> <!-- end .module -->

	<div class="module forms data" id="firewall">
		<h2>Firewall Logs</h2>

		<table id="firewall_logs_today" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3">All logs for Today</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<table id="firewall_logs_yesterday" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3">All logs from Yesterday</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="firewall_logs_week" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3">All logs from Last Week</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="firewall_logs_month" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3">All logs from Last Month</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="firewall_logs_last" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3">All logs for Last 90 Days</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<div class="smart_paginator"></div>
		<div class="btn-group">
			<input type="button" value="Print" class="btn alt"/>
			<input type="submit" value="Download" class="btn alt"/>
		</div>
	</div> <!-- end .module -->

</form>
</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
