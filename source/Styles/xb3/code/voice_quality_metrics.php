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
<?php include('includes/header.php'); 
if(PREPAID == TRUE){
	echo '<script type="text/javascript">alert("'._("No MTA support for this device").'"); window.history.back(); </script>';
	exit(0);
}
?>
<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php 
$line	= "1";
$call	= "table";
$action	= "display";
if (isset($_GET['line']))
{
	//line only 1, 2 are allowed
	//call only 1, 50 are allowed
	//action only display, clear_line, clear_all are allowed
	if	(($_GET['line'] == '1' || $_GET['line'] == '2') &&
			((preg_match('/^(50|[1234][0-9]|[1-9])$/', $_GET['call'])) || $_GET['call'] == 'all' || $_GET['call'] == 'table') &&
				($_GET['action'] == 'display' || $_GET['action'] == 'clear_line' || $_GET['action'] == 'clear_all')
	){
		$line	= $_GET['line'];
		$call	= $_GET['call'];
		$action	= $_GET['action'];
	}
	else die();
}
?>
<div id="pop_mask" style="z-index:100; position: fixed; height: 100%; width: 100%;">
<img style="position: fixed; left: 50%; top: 16%;" src="./cmn/img/loading.gif"  alt="Loading..."/><br>
</div>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Gateway > Connection > Voice Quality Metrics", "nav-gateway-voice");
	var line	= "<?php echo $line;?>";
	var call	= "<?php echo $call;?>";
	var action	= "<?php echo $action;?>";
	$("#line_number").val(line);
	$("#call_number").val(call);
	$("#action").val(action);
	$("[name='show_hide']:visible").hide();	
	switch(action)
	{
		case "display":
		{
			switch(call)
			{
				case "table":
				{
					$("#table"+line).show();
				}break;
				case "all":
				{
					$('div[id*="line'+line+'"]').show();
				}break;	
				default:
				{
					$("#line"+line+"call"+call).show();
				}break;
			}
			if (!$('[name="show_hide"]:visible').length)
			{
				$("#msg_no_data"+line).show();
			}
		}break;
		case "clear_line":
		{
		}break;
		case "clear_all":
		{
		}break;
		default:break;
	}
	$("#pop_mask").hide();
	$("#submit").click(function() {
		jProgress('This may take several seconds', 60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajaxSet_voice_quality_metrics.php",
			data:{line:$("#line_number").val(),call:$("#call_number").val(),action:$("#action").val()},
			success:function(results){
				//jAlert(results);
				jHide();
				window.location.href = 'voice_quality_metrics.php'
					+ '?line=' + $("#line_number").val() 
					+ '&call=' + $("#call_number").val()
					+ '&action=' + $("#action").val();
			},
			error:function(){
				jHide();
				jAlert("Failure, please try again.");
			}
		});
	});
});
</script>
<div id="content">
	<h1>Gateway > Connection > Voice Quality Metrics</h1>
	<div id="educational-tip">
		<p class="tip">This page displays the voice quality metrics of your Telephone lines.</p>
	</div>
	<div class="module data" style="margin-left:px">
		<table  id="radius_tab" style="font-size:11px;">
		<tr>
		<td>Line Number</td>
		<td>
			<select id="line_number">
				<option value="1" selected="selected">1</option>
				<option value="2">2</option>
			</select>
		</td>
		<td>Call Number</td>
		<td>
			<select id="call_number">
				<option value="table" selected="selected">table</option>
				<option value="all">All</option>
				<?php for ($k=1; $k<51; $k++) echo '<option value="'.$k.'">'.$k.'</option>'; ?>
			</select>
		</td>
		<td>Action</td>
		<td>
			<select id="action">
				<option value="display" selected="selected">Display Stats</option>
				<option value="clear_line" >Clear Line Stats</option>
				<option value="clear_all"  >Clear All Stats</option>
			</select>
		</td>
		<td>
			<input type="button" name="submit" id="submit" value="Submit"/>
		</td>
		</tr>
		</table>
		<div name="show_hide" id="msg_no_data1" style="display:none"><br/>There is no data to display for Line 1.</div>
		<div name="show_hide" id="msg_no_data2" style="display:none"><br/>There is no data to display for Line 2.</div>
	</div>
<?php
$locale = array();
$remote = array();
$metric = array(
 "Call End Time"
,"Call Start Time"
,"Call Duration"
,"Line Number"
,"Remote IP Address"
,"Codec"
,"CW Errors"
,"CW Error Rate"
,"SNR"
,"Micro Reflections"
,"Downstream Power"
,"Upstream Power"
,"EQI Average"
,"EQI Minimum"
,"EQI Maximum"
,"EQI Instantaneous"
,"MOS-LQ"
,"MOS-CQ"
,"Echo Return Loss"
,"Signal Level"
,"Noise Level"
,"Loss Rate"
,"Pkt Loss Concealment"
,"Discard Rate"
,"Burst Density"
,"Gap Density"
,"Burst Duration"
,"Gap Duration"
,"Round Trip Delay"
,"Remote Signal Level"
,"Gmin"
,"R Factor"
,"External R Factor"
,"Jitter Buf Adaptive"
,"Jitter Buf Rate"
,"JB Nominal Delay"
,"JB Max Delay"
,"JB Abs.Max Delay"
,"Tx Packets"
,"Tx Octets"
,"Rx Packets"
,"Rx Octets"
,"Packet Loss"
,"Interval Jitter"
,"Originator"
,"Remote Interval Jitter"
);
$mmmmmm = array(
 "--"
,"--"
,"--"
,"--"
,"--"
,"Remote Codec"
,"--"
,"--"
,"--"
,"--"
,"--"
,"--"
,"--"
,"--"
,"--"
,"--"
,"Remote MOS-LQ"
,"Remote MOS-CQ"
,"Remote Echo Return Loss"
,"Remote Signal Level"
,"Remote Noise Level"
,"Remote Loss Rate"
,"Remote Pkt Loss Concealment"
,"Remote Discard Rate"
,"Remote Burst Density"
,"Remote Gap Density"
,"Remote Burst Duration"
,"Remote Gap Duration"
,"Remote Round Trip Delay"
,"Remote Signal Level"
,"Remote Gmin"
,"Remote R Factor"
,"Remote External R Factor"
,"Remote Jitter Buf Adaptive"
,"Remote Jitter Buf Rate"
,"Remote JB Nominal Delay"
,"Remote JB Max Delay"
,"Remote JB Abs.Max Delay"
,"--"
,"--"
,"--"
,"--"
,"--"
,"--"
,"--"
,"--"
);
// $line = array_filter(explode(",", getInstanceIds("Device.X_CISCO_COM_MTA.LineTable.")));
// $line = array(1,2);
// for ($i=0; $i<count($line); $i++)
if ("display" == $action)
{
	$dmroot	= "Device.X_CISCO_COM_MTA.LineTable.$line.VQM.Calls.";
	$dmval	= DmExtGetStrsWithRootObj($dmroot, array($dmroot));
	$call	= array_filter(explode(",", getInstanceIds($dmroot)));
	// sleep(3);
	$t		= 66;					//total parameters number in a call
	for ($j=0; $j<count($call); $j++)
	{
		//locale metric for line 1 of this call number
		$locale[$j] = array(
		 $dmval[$j*$t + 3][1]		//("LineTable.$line.VQM.Calls.$call[$j].CallEndTime")
		,$dmval[$j*$t + 4][1]		//("LineTable.$line.VQM.Calls.$call[$j].CallStartTime")
		,$dmval[$j*$t + 10][1]		//("LineTable.$line.VQM.Calls.$call[$j].CallDuration")
		,$line						//("LineTable.$line.LineNumber")
		,$dmval[$j*$t + 9][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteIPAddress")
		,$dmval[$j*$t + 1][1]		//("LineTable.$line.VQM.Calls.$call[$j].Codec")
		,$dmval[$j*$t + 11][1]		//("LineTable.$line.VQM.Calls.$call[$j].CWErrors")
		,$dmval[$j*$t + 5][1]		//("LineTable.$line.VQM.Calls.$call[$j].CWErrorRate")
		,$dmval[$j*$t + 12][1]		//("LineTable.$line.VQM.Calls.$call[$j].SNR")
		,$dmval[$j*$t + 13][1]		//("LineTable.$line.VQM.Calls.$call[$j].MicroReflections")
		,$dmval[$j*$t + 14][1]		//("LineTable.$line.VQM.Calls.$call[$j].DownstreamPower")
		,$dmval[$j*$t + 15][1]		//("LineTable.$line.VQM.Calls.$call[$j].UpstreamPower")
		,$dmval[$j*$t + 16][1]		//("LineTable.$line.VQM.Calls.$call[$j].EQIAverage")
		,$dmval[$j*$t + 17][1]		//("LineTable.$line.VQM.Calls.$call[$j].EQIMinimum")
		,$dmval[$j*$t + 18][1]		//("LineTable.$line.VQM.Calls.$call[$j].EQIMaximum")
		,$dmval[$j*$t + 19][1]		//("LineTable.$line.VQM.Calls.$call[$j].EQIInstantaneous")
		,$dmval[$j*$t + 20][1]		//("LineTable.$line.VQM.Calls.$call[$j].MOS-LQ")
		,$dmval[$j*$t + 21][1]		//("LineTable.$line.VQM.Calls.$call[$j].MOS-CQ")
		,$dmval[$j*$t + 22][1]		//("LineTable.$line.VQM.Calls.$call[$j].EchoReturnLoss")
		,$dmval[$j*$t + 23][1]		//("LineTable.$line.VQM.Calls.$call[$j].SignalLevel")
		,$dmval[$j*$t + 24][1]		//("LineTable.$line.VQM.Calls.$call[$j].NoiseLevel")
		,$dmval[$j*$t + 25][1]		//("LineTable.$line.VQM.Calls.$call[$j].LossRate")
		,$dmval[$j*$t + 6][1]		//("LineTable.$line.VQM.Calls.$call[$j].PktLossConcealment")
		,$dmval[$j*$t + 26][1]		//("LineTable.$line.VQM.Calls.$call[$j].DiscardRate")
		,$dmval[$j*$t + 27][1]		//("LineTable.$line.VQM.Calls.$call[$j].BurstDensity")
		,$dmval[$j*$t + 28][1]		//("LineTable.$line.VQM.Calls.$call[$j].GapDensity")
		,$dmval[$j*$t + 29][1]		//("LineTable.$line.VQM.Calls.$call[$j].BurstDuration")
		,$dmval[$j*$t + 30][1]		//("LineTable.$line.VQM.Calls.$call[$j].GapDuration")
		,$dmval[$j*$t + 31][1]		//("LineTable.$line.VQM.Calls.$call[$j].RoundTripDelay")
		,$dmval[$j*$t + 42][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteSignalLevel")
		,$dmval[$j*$t + 32][1]		//("LineTable.$line.VQM.Calls.$call[$j].Gmin")
		,$dmval[$j*$t + 33][1]		//("LineTable.$line.VQM.Calls.$call[$j].RFactor")
		,$dmval[$j*$t + 34][1]		//("LineTable.$line.VQM.Calls.$call[$j].ExternalRFactor")
		,$dmval[$j*$t + 7][1]		//("LineTable.$line.VQM.Calls.$call[$j].JitterBufferAdaptive")
		,$dmval[$j*$t + 35][1]		//("LineTable.$line.VQM.Calls.$call[$j].JitterBufRate")
		,$dmval[$j*$t + 36][1]		//("LineTable.$line.VQM.Calls.$call[$j].JBNominalDelay")
		,$dmval[$j*$t + 37][1]		//("LineTable.$line.VQM.Calls.$call[$j].JBMaxDelay")
		,$dmval[$j*$t + 38][1]		//("LineTable.$line.VQM.Calls.$call[$j].JBAbsMaxDelay")
		,$dmval[$j*$t + 60][1]		//("LineTable.$line.VQM.Calls.$call[$j].TxPackets")
		,$dmval[$j*$t + 61][1]		//("LineTable.$line.VQM.Calls.$call[$j].TxOctets")
		,$dmval[$j*$t + 62][1]		//("LineTable.$line.VQM.Calls.$call[$j].RxPackets")
		,$dmval[$j*$t + 63][1]		//("LineTable.$line.VQM.Calls.$call[$j].RxOctets")
		,$dmval[$j*$t + 64][1]		//("LineTable.$line.VQM.Calls.$call[$j].PacketLoss")
		,$dmval[$j*$t + 65][1]		//("LineTable.$line.VQM.Calls.$call[$j].IntervalJitter")
		,$dmval[$j*$t + 8][1]		//("LineTable.$line.VQM.Calls.$call[$j].Originator")
		,$dmval[$j*$t + 66][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteIntervalJitter")
		);
		//remote metric for line 1 of this call number
		$remote[$j] = array(
		 "--"
		,"--"
		,"--"
		,"--"
		,"--"
		,$dmval[$j*$t + 2][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteCodec")
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		,$dmval[$j*$t + 39][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteMOS-LQ")
		,$dmval[$j*$t + 40][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteMOS-CQ")
		,$dmval[$j*$t + 41][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteEchoReturnLoss")
		,$dmval[$j*$t + 42][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteSignalLevel")
		,$dmval[$j*$t + 43][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteNoiseLevel")
		,$dmval[$j*$t + 44][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteLossRate")
		,$dmval[$j*$t + 45][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemotePktLossConcealment")
		,$dmval[$j*$t + 46][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteDiscardRate")
		,$dmval[$j*$t + 47][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteBurstDensity")
		,$dmval[$j*$t + 48][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteGapDensity")
		,$dmval[$j*$t + 49][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteBurstDuration")
		,$dmval[$j*$t + 50][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteGapDuration")
		,$dmval[$j*$t + 51][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteRoundTripDelay")
		,$dmval[$j*$t + 42][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteSignalLevel")
		,$dmval[$j*$t + 52][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteGmin")
		,$dmval[$j*$t + 53][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteRFactor")
		,$dmval[$j*$t + 54][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteExternalRFactor")
		,$dmval[$j*$t + 55][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteJitterBufferAdaptive")
		,$dmval[$j*$t + 56][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteJitterBufRate")
		,$dmval[$j*$t + 57][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteJBNominalDelay")
		,$dmval[$j*$t + 58][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteJBMaxDelay")
		,$dmval[$j*$t + 59][1]		//("LineTable.$line.VQM.Calls.$call[$j].RemoteJBAbsMaxDelay")
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		,"--"
		);
	}
}
?>
<?php
// for ($n=0; $n<count($line); $n++)
if (count($call))
{
	echo '<div name="show_hide" class="module data" style="display:none;overflow:auto" id="table'.$line.'">';
	echo '<table class="data">';
		echo '<tr>';
		echo '<th><div style="width: 150px">Call Number</div></th>';
		for ($i=0; $i<count($call); $i++)
		{
			echo '<th><div style="width: 150px">'.($i+1).'(Local)</div></th>';
			echo '<th><div style="width: 150px">'.($i+1).'(Remote)</div></th>';
		}
		echo '</tr>';
		for ($i=0; $i<count($metric); $i++)
		{
			echo '<tr>';
			echo '<td>'.$metric[$i].':</td>';
			for ($j=0; $j<count($call); $j++)
			{
				echo '<td class="odd"><div style="width: 150px">'.$locale[$j][$i].'</div></td>';
				echo '<td class="odd"><div style="width: 150px">'.$remote[$j][$i].'</div></td>';
			}
			echo '</tr>';
		}			
	echo '</table>';
	echo '</div>';
	for ($i=0; $i<count($call); $i++)
	{
		echo '<div name="show_hide" class="module forms" id="line'.$line.'call'.($i+1).'" style="display:none">';
		echo '<h2>Call '.($i+1).': Call End Time: '.$locale[$i][0].'</h2>';
		$odd = true;
		for ($j=1; $j<=37; $j++)
		{
			echo '<div class="form-row '.($odd=!$odd?"odd":"").'">';
			echo '<span class="readonlyLabel">'.$metric[$j].': </span> <span class="value">'.$locale[$i][$j].'</span>';
			echo '</div>';
		}
		for ($j=5; $j<=37; $j = (5==$j?$j+11:$j+1))
		{
			echo '<div class="form-row '.($odd=!$odd?"odd":"").'">';
			echo '<span class="readonlyLabel">'.$mmmmmm[$j].': </span> <span class="value">'.$remote[$i][$j].'</span>';
			echo '</div>';
		}
		for ($j=38; $j<=45; $j++)
		{
			echo '<div class="form-row '.($odd=!$odd?"odd":"").'">';
			echo '<span class="readonlyLabel">'.$metric[$j].': </span> <span class="value">'.$locale[$i][$j].'</span>';
			echo '</div>';
		}
		echo '</div>';	
	}
}
?>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
