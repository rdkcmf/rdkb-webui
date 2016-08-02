<?php include('includes/header.php'); ?>
<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<style>
 td {
    border: 1px solid white;
}
</style>
<script>
$(document).ready(function(){
	comcast.page.init("Troubleshooting > Wi-Fi Spectrum Analyzer", "nav-wifi-spectrum-analyzer");
	$("#save_result").hide();
	function spanTable($rows, startIdx, endIdx){
		if (endIdx === 0){
			return;
		}
		var i , currentIndex = startIdx, count=1, lst=[];
		var tds = $rows.find('td:eq('+ currentIndex +')');
		var ctrl = $(tds[0]);
		lst.push($rows[0]);
		for (i=1;i<=tds.length;i++){
			if (ctrl.text() ==  $(tds[i]).text()){
				count++;
				$(tds[i]).addClass('deleted');
				lst.push($rows[i]);
			}
			else{
				if (count>1){
					ctrl.attr('rowspan',count);
					spanTable($(lst),startIdx+1,endIdx-1)
				}
				count=1;
				lst = [];
				ctrl=$(tds[i]);
				lst.push($rows[i]);
			}
		}
	}
	function ajax_spec_analyzer()
	{
		$("#save_result").hide();
		$("#scan_status").html("Scanning...").show();
		$("#start_scan").hide();
		$("#spec_capture_table tr").slice(1).remove()
		$.ajax({
			type: "POST",
			url: "actionHandler/ajax_wifi_spectrum_analyser.php",
			success: function(result) {
				result = JSON.parse(result);
				if(result.status == "success")
				{
					$("#scan_status").hide();
					var wifi_spec_values = result["data"];
					function compareChannel(a,b) {
						return a['Channel']-b['Channel'];
					}
					wifi_spec_values.sort(compareChannel);
					var table = $('#spec_capture_table tbody');
					for (var index in wifi_spec_values)
					{
						var spec_values = wifi_spec_values[index];
						var tr = $('<tr/>').appendTo(table);
						var Radio_val = (spec_values['Radio'] == 'Device.WiFi.Radio.1') ? "2.4GHz": "5GHz";
				        tr.append("<td headers='band_id' class='band_id'>" + Radio_val+ "</td>");
				        tr.append("<td headers='channel_number' class='channel_number'>" + spec_values["Channel"]+ "</td>");
				        tr.append("<td headers='mac_id' class='mac_id'>" + spec_values["BSSID"]+ "</td>");
						tr.append("<td headers='ssid_name' class='ssid_name' style='white-space: pre;'>" + spec_values["SSID"]+ "</td>");
				        tr.append("<td headers='Signal_level' class='Signal_level'>" + spec_values["SignalStrength"]+" dBm"+ "</td>");
				        tr.append("<td headers='mode' class='mode'>" + spec_values["SupportedStandards"]+ "</td>");
				        tr.append("<td headers='security' class='security'>" + spec_values["SecurityModeEnabled"]+ "</td>");
				        tr.append("<td headers='max_rate' class='max_rate'>" + spec_values["SupportedDataTransferRates"]+ "</td>");
					}
					spanTable($('#spec_capture_table tr:has(td)'),0,2);
					$('#spec_capture_table .deleted').remove();
					$("#start_scan").show();
					$("#save_result").show();
				}
				else
				{
					setTimeout(function(){
						ajax_spec_analyzer();
					}, 5000);
				}
			},
			failure: function()
			{
				//failure: function(){}
			}
		});
	}
	function popUp(URL) {
	day = new Date();
	id = day.getTime();
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=700,height=400,left = 320.5,top = 105');");
	}
	$("#start_scan").click(function() {
		ajax_spec_analyzer();
	});
	$("#save_result").click(function(){
		table_data = $("#table_WSA").html();
		jProgress('This may take several seconds', 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajax_at_saving.php",
			data: { configInfo: table_data },
			success: function(result) {
				if(result == "success")
				{
					popUp("at_saving.php");
					jHide();
				}
			}
		});
	});
	ajax_spec_analyzer();
});
</script>
<div id="content" class="main_content">
<h1>Troubleshooting > Wi-Fi Spectrum Analyzer</h1>
	<div class="module">
		<div>
			<input type="button" class="btn" value="Start Scan" id = "start_scan"/>
			<span id="scan_status"></span>
			<input type="button" class="btn button" value="Save Result" id = "save_result" style="top: 8px;"/>
		</div>
	</div>
	<div id="table_WSA" class="module data">
		<h2>Wi-Fi Spectrum Analyzer Data</h2>
		<div style="overflow: auto;">
			<table class="data" summary="Wi-Fi Spectrum Analyzer" id="spec_capture_table">
				<tr>
					<th id="band_id">Band</th>
					<th id="channel_number" >Channel</th>
					<th id="mac_id">MAC</th>
					<th id="ssid_name">SSID</th>
					<th id="Signal_level">SignalLevel</th>
					<th id="mode">Mode</th>
					<th id="security">Security</th>
					<th id="max_rate">MaxRate</th>
				</tr>
			</table>
		</div>			
	</div>
</div>
<?php include('includes/footer.php'); ?>
