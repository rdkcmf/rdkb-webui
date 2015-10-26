<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<!--link rel="stylesheet" type="text/css" href="../cmn/css/lib/smartpaginator.css"/-->
<!--script type="text/javascript" src="../cmn/js/lib/smartpaginator.js"></script-->
<!--script type="text/javascript" src="../cmn/js/lib/jquery.alerts.progress.js"></script-->

<script type="text/javascript">

$(document).ready(function() {
    comcast.page.init("Gateway > Connection > QoS", "nav-qos");
});

</script>


<div id="content">
	<h1>Gateway > Connection > QoS > DSX logs</h1>
	<div class="module forms data" id="event">
	<?php
		function del_blank($v){return (""==$v?false:true);}
		$ids = array_filter(explode(",", getInstanceIds("Device.X_CISCO_COM_MTA.DSXLog.")));
		if (1 == count($ids))
		{
			echo '<h3 id="log_summary">There are currently no DSX Logs</h3>';
		}
		else
		{
			echo '<h2>MTA SIP Packet Log</h2><table class="data" summary="This table shows DSX logs"><thead><tr><th id="dsx_metrics">Metrics</th><th id="dsx_ds">Downstream</th><th id="dsx_us">Upstream</th></tr></thead>';
			$rootObjName    = "Device.X_CISCO_COM_MTA.DSXLog.";
			$paramNameArray = array("Device.X_CISCO_COM_MTA.DSXLog.");
			$mapping_array  = array("Description");
	
			$dsxLogsInstance = getParaValues($rootObjName, $paramNameArray, $mapping_array);
			for ($i=1; $i<count($ids); $i++)
			{
				$item = array_values(array_filter(explode(" ", $dsxLogsInstance[$i]["Description"]), "del_blank"));
				echo '<tr class="'.(($i%2)?'odd':'').'" >';
				echo '<td headers="dsx_metrics">'.$item[0].' '.$item[1].'</td>';
				echo '<td headers="dsx_ds">'.$item[2].'</td>';
				echo '<td headers="dsx_us">'.$item[3].'</td>';
				echo '</tr>';
			}
			echo '<tfoot><tr class="acs-hide"><td headers="dsx_metrics">null</td><td headers="dsx_ds">null</td><td headers="dsx_us">null</td></tr></tfoot>';
			echo '</table>';
		}
	?>
	</div>
</div> <!-- end #container -->

<?php include('includes/footer.php'); ?>
