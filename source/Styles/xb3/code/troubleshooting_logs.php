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
<?php include('includes/header.php'); ?>
<!-- $Id: troubleshooting_logs.php 3159 2010-01-11 20:10:58Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<link rel="stylesheet" type="text/css" href="./cmn/css/comcastPaginator.css"/>
<script type="text/javascript" src="./cmn/js/comcastPaginator.js"></script>
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
	gateway.page.init("Troubleshooting > Logs", "nav-logs");
	$('.print').prop("disabled",true).addClass("disabled");
	$('.downloadbtn').prop("disabled",true).addClass("disabled");
	$('.print').click(function() {
    		window.print();
	});
	$("#showlogs").click(function() {
		var mode=$("select#log_type").val();
		var timef=$("select#time_frame").val();
		jConfirm(
		'<?php echo _("This action may take more than one minute. Do you want to continue?")?>'
		, '<?php echo _("Are You Sure?")?>'
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
		$('.print:visible').prop("disabled",true).addClass("disabled");
		$('.downloadbtn:visible').prop("disabled",true).addClass("disabled");		
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
	jProgress('<?php echo _("This may take several seconds.")?>',180);
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
				$('.print:visible').prop("disabled",false).removeClass("disabled");
				$('.downloadbtn:visible').prop("disabled",false).removeClass("disabled");
			}
			// alert(length+mode+'_logs_'+timef2);
			if(length>20){
				$('#'+mode+'_logs_'+timef2).comcastPaginator();
			}
			//adjust current data table
			adjust_acs_tb("This is "+mode+" logs, for "+timef, Array("Discription", "Time", "Level"));
		},
		error: function(){
			jHide();
			jAlert("<?php echo _("Something went wrong, please try again.")?>");
		}
	});
}
</script>
<div id="content">
	<h1><?php echo _("Troubleshooting > Logs")?></h1>
	<div id="educational-tip" class="noprint">
		<p class="tip"><?php echo _("View information about the Gateway's performance and system operation.")?></p>
		<p class="hidden"><?php echo _("Use the logs to troubleshoot issues and to identify potential security risks.")?></p>
	</div>
	<div class="module noprint">
		<h2><?php echo _("Log Filters")?></h2>
    <form action="troubleshooting_logs_download.php" method="post">
			<label for="log_type" class="readonlyLabel"><?php echo _("Log Type:")?></label>
			<select id="log_type" name="log_type">
				<option value="system" selected="selected"><?php echo _("System Logs")?></option>
				<option value="event"><?php echo _("Event Logs")?></option>
				<option value="firewall"><?php echo _("Firewall Logs")?></option>
			</select>
			<label for="time_frame" class="readonlyLabel"><?php echo _("Time Frame:")?></label>
			<select id="time_frame" name="time_frame">
				<option value="Today" selected="selected"><?php echo _("Today")?></option>
				<option value="Yesterday"><?php echo _("Yesterday")?></option>
				<option value="Last week"><?php echo _("Last week")?></option>
				<option value="Last month"><?php echo _("Last month")?></option>
				<option value="Last 90 days"><?php echo _("Last 90 days")?></option>
			</select>
			<input  id="showlogs" type="button" value="<?php echo _("Show Logs")?>" class="btn alt" />
	</div>
	<div class="module forms data" id="system">
		<h2><?php echo _("System Logs")?></h2>
		<table id="system_logs_today" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs for Today")?></td>
				</tr>
			</thead>
				<tbody>
			</tbody>
		</table>
		<table id="system_logs_yesterday" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs from Yesterday")?></td>
				</tr>
			</thead>
				<tbody>
			</tbody>
		</table>
		<table id="system_logs_week" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs from Last Week")?></td>
				</tr>
			</thead>
				<tbody>
			</tbody>
		</table>
		<table id="system_logs_month" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs from Last Month")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="system_logs_last" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs for Last 90 Days")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<div class="btn-group">
			<input type="button" value="<?php echo _("Print")?>" class="btn alt print"/>
			<input type="submit" value="<?php echo _("Download")?>" class="btn alt downloadbtn"/>
		</div>
	</div> <!-- end .module -->
	<div class="module forms data" id="event" style="display:none">
		<h2><?php echo _("Event Logs")?></h2>
		<table id="event_logs_today" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
				<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs for Today")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="event_logs_yesterday" cellpadding="0" cellspacing="0" class="data" style="display:none">
				<thead>
					<tr>
					<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs from Yesterday")?></td>
					</tr>
				</thead>
				<tbody>
				</tbody>
		</table>
		<table id="event_logs_week" cellpadding="0" cellspacing="0" class="data" style="display:none">
				<thead>
					<tr>
					<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs from Last Week")?></td>
					</tr>
				</thead>
				<tbody>
				</tbody>
		</table>
		<table id="event_logs_month" cellpadding="0" cellspacing="0" class="data" style="display:none">
				<thead>
					<tr>
					<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs from Last Month")?></td>
					</tr>
				</thead>
				<tbody>
				</tbody>
		</table>
		<table id="event_logs_last" cellpadding="0" cellspacing="0" class="data" style="display:none">
				<thead>
					<tr>
					<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs for Last 90 Days")?></td>
					</tr>
				</thead>
				<tbody>
				</tbody>
		</table>
		<div class="btn-group">
			<input type="button" value="<?php echo _("Print")?>" class="btn alt print"/>
			<input type="submit" value="<?php echo _("Download")?>" class="btn alt downloadbtn"/>
			<!--a  id="download_event_logs" href="#" class="btn alt">Download</a-->
		</div>
	</div> <!-- end .module -->
	<div class="module forms data" id="firewall" style="display:none">
		<h2><?php echo _("Firewall Logs")?></h2>
		<table id="firewall_logs_today" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs for Today")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="firewall_logs_yesterday" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs from Yesterday")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="firewall_logs_week" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs from Last Week")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="firewall_logs_month" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs from Last Month")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="firewall_logs_last" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col" colspan="3"><?php echo _("All logs for Last 90 Days")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<div class="btn-group">
			<input type="button" value="<?php echo _("Print")?>" class="btn alt print"/>
			<input type="submit" value="<?php echo _("Download")?>" class="btn alt downloadbtn"/>
		</div>
	</div> <!-- end .module -->
</form>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
