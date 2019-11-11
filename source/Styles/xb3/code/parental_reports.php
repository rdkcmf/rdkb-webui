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
	$("#all").show();
	$("#site").hide();
	$("#service").hide();
	$("#device").hide();
	$("#all_report_today").show();
	$("#all_report_yesterday").hide();
	$("#all_report_week").hide();
	$("#all_report_month").hide();
	$("#all_report_last").hide();
	$('.print').prop("disabled",true).addClass("disabled");
	$('.downloadbtn').prop("disabled",true).addClass("disabled");
	gateway.page.init("Parental Control > Reports", "nav-parental-reports");
    $('.print').click(function() {
    	window.print();
    });
    $("#generate-report").click(function() {
		var mode=$("select#report_type").val();
		var timef=$("select#time_frame").val();
		jConfirm(
		'<?php echo _("This action may take more than one minute. Do you want to continue?")?>'
		,'<?php echo _("Are You Sure?")?>'
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
		$('.print:visible').prop("disabled",true).addClass("disabled");
		$('.downloadbtn:visible').prop("disabled",true).addClass("disabled");
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
	jProgress('<?php echo _("This may take several seconds.")?>',120);
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
				$("#"+mode+"_report_"+timef2+" > tbody").append("<tr class='"+trClass+"'><td>"+value.Des+", "+value.Count+" Attempts, "+value.time+"</td><td>"+value.Type+"</td></tr>");
				trClass=((trClass=="")?"odd":"");
				length++;
			});
			if(length>0){
				$('.print:visible').prop("disabled",false).removeClass("disabled");
				$('.downloadbtn:visible').prop("disabled",false).removeClass("disabled");
			}
			if(length>20){
				$('#'+mode+'_report_'+timef2).comcastPaginator();
				
			}
			//adjust current data table
			adjust_acs_tb("This is "+mode+" logs for "+timef, Array("log-details", "log-type"));
		},
		error: function(){
			jHide();
			jAlert("<?php echo _("Something is wrong, please try later.")?>");
		}
	});
}; //end of ajaxDo
</script>
<?php
	$partnerId = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
?>
<div id="content">
	<h1><?php echo _("Parental Control > Reports")?></h1>
	<div id="educational-tip" class="noprint">
		<p class="tip"><?php echo _("Generate, download, and print reports based on your parental controls.")?> </p>
	</div>
	<div class="module noprint">
		<h2><?php echo _("Report Filters")?></h2>
<form action="parental_reports_download.php" method="post">
			<label for="report_type" class="readonlyLabel"><?php echo _("Report Type:")?></label>
			<select id="report_type" name="report_type">
				<option value="all" selected="selected"><?php echo _("All")?></option>
				<?php
					if($partnerId!="cox"){
				?>
				<option value="site"><?php echo _("Managed Sites")?></option>
				<?php
					}
				?>
				<option value="service"><?php echo _("Managed Services")?></option>
				<option value="device"><?php echo _("Managed Devices")?></option>
			</select>
			<label for="time_frame" class="readonlyLabel"><?php echo _("Time Frame:")?></label>
			<select id="time_frame" name="time_frame">
				<option selected="selected" value="Today"><?php echo _("Today")?></option>
				<option value="Yesterday"><?php echo _("Yesterday")?></option>
				<option value="Last week"><?php echo _("Last week")?></option>
				<option value="Last month"><?php echo _("Last month")?></option>
				<option value="Last 90 days"><?php echo _("Last 90 days")?></option>
			</select>
			<input  id="generate-report" type="button" value="<?php echo _("GENERATE REPORT")?>" class="btn"/>
	</div>
	<div class="module forms data" id="site">
		<h2><?php echo _("Managed Sites Reports")?></h2>
		<table id="site_report_today" cellpadding="0" cellspacing="0" class="data">
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3"><?php echo _("Reports for Today")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="site_report_yesterday" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3"><?php echo _("Reports from Yesterday")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="site_report_week" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3"><?php echo _("Reports from Last week")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="site_report_month" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3"><?php echo _("Reports from Last month")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="site_report_last" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th" scope="col"  colspan="3"><?php echo _("Reports from Last 90 days")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table> <!-- or class-->
		<div class="btn-group">
			<input type="button" value="<?php echo _("Print")?>" class="btn alt print"/>
			<input type="submit" value="<?php echo _("Download")?>" class="btn alt downloadbtn"/>
		</div>
	</div> <!-- end .module -->
	<div class="module forms data" id="service" style="display:none">
		<h2><?php echo _("Managed Services Reports")?></h2>
		<table id="service_report_today" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports for Today")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="service_report_yesterday" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports from Yesterday")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="service_report_week" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports from Last week")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="service_report_month" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports from Last month")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="service_report_last" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports from Last 90 days")?></td>
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
	<div class="module forms data" id="all" style="display:none">
		<h2><?php echo _("All Reports")?></h2>
		<table id="all_report_today" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports for Today")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="all_report_yesterday" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports from Yesterday")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="all_report_week" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports from Last week")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="all_report_month" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports from Last month")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="all_report_last" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="" colspan="3"><?php echo _("Reports from Last 90 days")?></td>
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
	<div class="module forms data" id="device" style="display:none">
		<h2><?php echo _("Managed Devices Reports")?></h2>
		<table id="device_report_today" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="firewall_time_today" colspan="2"><?php echo _("Reports for Today")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="device_report_yesterday" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td  class="acs-th" scope="col" id="firewall_time_yesterday" colspan="2"><?php echo _("Reports from Yesterday")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="device_report_week" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th"  scope="col" id="firewall_time_lweek" colspan="2"><?php echo _("Reports from Last Week")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="device_report_month" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th"  scope="col" id="firewall_time_lmonth" colspan="2"><?php echo _("Reports from Last Month")?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<table id="device_report_last" cellpadding="0" cellspacing="0" class="data" style="display:none">
			<thead>
				<tr>
					<td class="acs-th"  scope="col" id="firewall_time_l90days" colspan="2"><?php echo _("Reports from Last 90 days")?></td>
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
