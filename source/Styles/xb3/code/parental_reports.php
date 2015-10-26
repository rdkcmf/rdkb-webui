<?php include('includes/header.php'); ?>

<!-- $Id: troubleshooting_logs.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript" src="./cmn/js/lib/smartpaginator.js"></script>
<link rel="stylesheet" type="text/css" href="./cmn/css/lib/smartpaginator.css"/>
<script type="text/javascript">
$(document).ready(function() {
	$("#all").show();
	$("#site").hide();
	$("#service").hide();
	$("#device").hide();

	$("#all_report_today").show();
	$("#all_report_yesterday").hide();
	$("#all_report_week").hide();
	$("#all_report_month").hide();
	$("#all_report_last").hide();
	
	$('input[value="Print"]').prop("disabled",true).addClass("disabled");
	$('input[value="Download"]').prop("disabled",true).addClass("disabled");
	
	comcast.page.init("Parental Control > Reports", "nav-parental-reports");

    $('input[value="Print"]').click(function() {
    	window.print();
    });

    $("#generate-report").click(function() {
		
		var mode=$("select#report_type").val();
		var timef=$("select#time_frame").val();
		
		jConfirm(
		'This action may take more than one minute. Do you want to continue?'
		,'Are You Sure?'
		,function(ret){
			if(ret){

		if(mode == "site") {
			$("#device").hide();
			$("#all").hide();
			$("#service").hide();
			$("#site").show();
			if(timef == "Today")
			{
				$("#site_report_today").show();
				$("#site_report_yesterday").hide();
				$("#site_report_week").hide();
				$("#site_report_month").hide();
				$("#site_report_last").hide();
			}
			else if(timef == "Yesterday")
			{
				$("#site_report_today").hide();
				$("#site_report_yesterday").show();
				$("#site_report_week").hide();
				$("#site_report_month").hide();
				$("#site_report_last").hide();
			}
			else if(timef == "Last week")
			{
				$("#site_report_today").hide();
				$("#site_report_yesterday").hide();
				$("#site_report_week").show();
				$("#site_report_month").hide();
				$("#site_report_last").hide();
			}
			else if(timef == "Last month")
			{
				$("#site_report_today").hide();
				$("#site_report_yesterday").hide();
				$("#site_report_week").hide();
				$("#site_report_month").show();
				$("#site_report_last").hide();
			}
			else if(timef == "Last 90 days")
			{
				$("#site_report_today").hide();
				$("#site_report_yesterday").hide();
				$("#site_report_week").hide();
				$("#site_report_month").hide();
				$("#site_report_last").show();
			}
		}
		else if(mode == "all") {
			$("#service").hide();
			$("#all").show();
			$("#site").hide();
			$("#device").hide();
			if(timef == "Today")
			{
				$("#all_report_today").show();
				$("#all_report_yesterday").hide();
				$("#all_report_week").hide();
				$("#all_report_month").hide();
				$("#all_report_last").hide();
			}
			if(timef == "Yesterday")
			{
				$("#all_report_today").hide();
				$("#all_report_yesterday").show();
				$("#all_report_week").hide();
				$("#all_report_month").hide();
				$("#all_report_last").hide();
			}
			else if(timef == "Last week")
			{
				$("#all_report_today").hide();
				$("#all_report_yesterday").hide();
				$("#all_report_week").show();
				$("#all_report_month").hide();
				$("#all_report_last").hide();
			}
			else if(timef == "Last month")
			{
				$("#all_report_today").hide();
				$("#all_report_yesterday").hide();
				$("#all_report_week").hide();
				$("#all_report_month").show();
				$("#all_report_last").hide();
			}
			else if(timef == "Last 90 days")
			{
				$("#all_report_today").hide();
				$("#all_report_yesterday").hide();
				$("#all_report_week").hide();
				$("#all_report_month").hide();
				$("#all_report_last").show();
			}
		}
		else if(mode == "service") {
			$("#service").show();
			$("#all").hide();
			$("#site").hide();
			$("#device").hide();
			if(timef == "Today")
			{
				$("#service_report_today").show();
				$("#service_report_yesterday").hide();
				$("#service_report_week").hide();
				$("#service_report_month").hide();
				$("#service_report_last").hide();
			}
			if(timef == "Yesterday")
			{
				$("#service_report_today").hide();
				$("#service_report_yesterday").show();
				$("#service_report_week").hide();
				$("#service_report_month").hide();
				$("#service_report_last").hide();
			}
			else if(timef == "Last week")
			{
				$("#service_report_today").hide();
				$("#service_report_yesterday").hide();
				$("#service_report_week").show();
				$("#service_report_month").hide();
				$("#service_report_last").hide();
			}
			else if(timef == "Last month")
			{
				$("#service_report_today").hide();
				$("#service_report_yesterday").hide();
				$("#service_report_week").hide();
				$("#service_report_month").show();
				$("#service_report_last").hide();
			}
			else if(timef == "Last 90 days")
			{
				$("#service_report_today").hide();
				$("#service_report_yesterday").hide();
				$("#service_report_week").hide();
				$("#service_report_month").hide();
				$("#service_report_last").show();
			}
		}
		else if(mode == "device") {
			$("#site").hide();
			$("#all").hide();
			$("#device").show();
			$("#service").hide();
			if(timef == "Today")
			{
				//$("#firewall_time").text(" Reports for Today");
				$("#device_report_today").show();
				$("#device_report_yesterday").hide();
				$("#device_report_week").hide();
				$("#device_report_month").hide();
				$("#device_report_last").hide();
			}	
			if(timef == "Yesterday")
			{
				//$("#firewall_time").text("Reports from Yesterday");
				$("#device_report_today").hide();
				$("#device_report_yesterday").show();
				$("#device_report_week").hide();
				$("#device_report_month").hide();
				$("#device_report_last").hide();
			}
			else if(timef == "Last week")
			{
				//$("#firewall_time").text("Reports from Last week");
				$("#device_report_today").hide();
				$("#device_report_yesterday").hide();
				$("#device_report_week").show();
				$("#device_report_month").hide();
				$("#device_report_last").hide();
			}
			else if(timef == "Last month")
			{
				//$("#firewall_time").text("Reports from Last month");
				$("#device_report_today").hide();
				$("#device_report_yesterday").hide();
				$("#device_report_week").hide();
				$("#device_report_month").show();
				$("#device_report_last").hide();
			}
			else if(timef == "Last 90 days")
			{
				//$("#firewall_time").text("Reports from Last 90 days");
				$("#device_report_today").hide();
				$("#device_report_yesterday").hide();
				$("#device_report_week").hide();
				$("#device_report_month").hide();
				$("#device_report_last").show();
			}
		}
		
		$('input[value="Print"]:visible').prop("disabled",true).addClass("disabled");
		$('input[value="Download"]:visible').prop("disabled",true).addClass("disabled");
		
		ajaxDo(mode,timef);
		
			}
		});
    });
}); //end of document ready


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
	jProgress('This may take several seconds.',120);
	ajaxrequest=$.ajax({
		type:"POST",
		url:"actionHandler/ajax_parental_reports.php",
		data:{mode:mode,timef:timef},
		dataType:"json",
		success:function(results){
			jHide();
			var length=0;
			var trClass="odd";
			
			$("#"+mode+"_report_"+timef2+" > tbody").empty();
			$.each(results,function(key,value){
				$("#"+mode+"_report_"+timef2+" > tbody").append("<tr class='"+trClass+"'><td>"+value.Des+", "+value.Count+" Attemps, "+value.time+"</td><td>"+value.Type+"</td></tr>");
				trClass=((trClass=="")?"odd":"");
				length++;
			});
			if(length>0){
				$('input[value="Print"]:visible').prop("disabled",false).removeClass("disabled");
				$('input[value="Download"]:visible').prop("disabled",false).removeClass("disabled");
			}
			if(length>20){
				$(".smart_paginator").empty();
				$(".smart_paginator:visible").smartpaginator({
					totalrecords:length,
					recordsperpage:20,
					datacontainer:mode+'_report_'+timef2,
					dataelement:'tr',
					theme:'blue'
				});
			} else {
				$(".smart_paginator:visible").hide();
			}
			//adjust current data table
			adjust_acs_tb("This is "+mode+" logs for "+timef, Array("log-details", "log-type"));
		},
		error: function(){
			jHide();
			jAlert("Something wrong, please try later.");
		}
	});
}; //end of ajaxDo
</script>

<div id="content">
	<h1>Parental Control > Reports</h1>
	<div id="educational-tip" class="noprint">
		<p class="tip">Generate, download, and print reports based on your parental controls. </p>
	</div>

	<div class="module noprint">
		<h2>Report Filters</h2>

<form action="parental_reports_sample.php" method="post">

			<label for="report_type" class="readonlyLabel">Report Type:</label>
			<select id="report_type" name="report_type">
				<option value="all" selected="selected">All</option>
				<option value="site">Managed Sites</option>
				<option value="service">Managed Services</option>
				<option value="device">Managed Devices</option>
			</select>

			<label for="time_frame" class="readonlyLabel">Time Frame:</label>
			<select id="time_frame" name="time_frame">
				<option selected="selected">Today</option>
				<option>Yesterday</option>
				<option>Last week</option>
				<option>Last month</option>
				<option>Last 90 days</option>
			</select>
			<input  id="generate-report" type="button" value="GENERATE REPORT" class="btn"/>
			
	</div>

	<div class="module forms data" id="site">
		<h2>Managed Sites Reports</h2>

		<table id="site_report_today" cellpadding="0" cellspacing="0" class="data" style="display:none" >
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3">Reports for Today</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table id="site_report_yesterday" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3">Reports from Yesterday</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table id="site_report_week" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3">Reports from Last week</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table id="site_report_month" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3">Reports from Last month</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table id="site_report_last" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3">Reports from Last 90 days</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<div class="smart_paginator"></div> <!-- or class-->
		<div class="btn-group">
			<input type="button" value="Print" class="btn alt"/>
			<input type="submit" value="Download" class="btn alt"/>
		</div>
	</div> <!-- end .module -->

	<div class="module forms data" id="service">
		<h2>Managed Services Reports</h2>
		<table id="service_report_today" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports for Today</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="service_report_yesterday" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports from Yesterday</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="service_report_week" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports from Last week</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table id="service_report_month" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports from Last month</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table id="service_report_last" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports from Last 90 days</td>
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

	<div class="module forms data" id="all">
		<h2>All Reports</h2>

		<table id="all_report_today" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports for Today</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="all_report_yesterday" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports from Yesterday</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="all_report_week" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports from Last week</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table id="all_report_month" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports from Last month</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<table id="all_report_last" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3">Reports from Last 90 days</td>
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

	<div class="module forms data" id="device">
		<h2>Managed Devices Reports</h2>

		<table id="device_report_today" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="firewall_time_today" colspan="2">Reports for Today</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<table id="device_report_yesterday" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="firewall_time_yesterday" colspan="2">Reports from Yesterday</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<table id="device_report_week" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td class="acs-th"  scope="col" id="firewall_time_lweek" colspan="2">Reports from Last Week</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<table id="device_report_month" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td class="acs-th"  scope="col" id="firewall_time_lmonth" colspan="2">Reports from Last Month</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<table id="device_report_last" cellpadding="0" cellspacing="0" class="data" >
			<thead>
				<tr>
					<td class="acs-th"  scope="col" id="firewall_time_l90days" colspan="2">Reports from Last 90 days</td>
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
