<?php include('includes/header.php'); ?>

<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<link rel="stylesheet" type="text/css" href="./cmn/css/lib/smartpaginator.css"/>
<script type="text/javascript" src="./cmn/js/lib/smartpaginator.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Connection > QoS", "nav-qos");
	
	// $("#showlogs").click(function() {
		// jConfirm("This action may take more than one minute. Do you want to continue?", "Are You Sure?", function(ret){
			// if(ret){
				jProgress('This may take several seconds...', 180);
				$.ajax({
					type:"POST",
					url:"actionHandler/ajaxSet_callsignallog.php",
					data:{},
					dataType:"json",
					success:function(results){
						var length=0;		
						var trClass="odd";	
						
						// $("#event_logs_today > tbody").empty();
						if (""==results) {
							document.getElementById('log_summary').innerHTML='<b>There are currently no Call Signal Logs</b>';
							jHide();
							return;
						}
						else {
							document.getElementById('event').innerHTML='<h2>Call Signal logs</h2><table summary="This table shows Call Signal logs" id="event_logs_today" class="data" style="word-break:break-all"><thead><th id="sip_value">Description</th><th width="111" id="sip_time">Time</th></thead><tbody></tbody><tfoot><tr class="acs-hide"><td headers="sip_value">null</td><td headers="sip_time">null</td></tr></tfoot></table><div class="smart_paginator"></div>';
						}
						
						$.each(results,function(key,value) {
							$("#event_logs_today > tbody").append('<tr class="'+trClass+'"><td headers="sip_value">'+value.Des+'</td><td headers="sip_time">'+value.time+'</td></tr>');
							trClass=((trClass=="")?"odd":"");
							length++;
						});
						
						if (length>10) {
							$(".smart_paginator:visible").smartpaginator({
								totalrecords:length,
								recordsperpage:10,
								datacontainer:'event_logs_today',
								dataelement:'tr',
								theme:'blue'
							});
						}
						else {
							$(".smart_paginator:visible").hide();
						}
						
						jHide();
					}
				});
			// }
		// });
	// });
});

</script>

<div id="content">
	<h1>Gateway > Connection > CallP/QoS > Call Signal logs</h1>
	<div class="module forms data" id="event">
		<h3 id="log_summary">There are currently no Call Signal Logs</h3>
	</div>
</div> <!-- end #container -->

<?php include('includes/footer.php'); ?>
