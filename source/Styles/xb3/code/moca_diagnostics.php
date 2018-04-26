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
<?php include('includes/utility.php'); ?>
<!-- $Id: wireless_network_configuration.usg.php 3159 2010-01-11 20:10:58Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<script type="text/javascript" src="cmn/js/lib/vis-network.min.js"></script>
<link href="cmn/css/lib/vis-network.min.css" rel="stylesheet" type="text/css" />
<?php include('includes/nav.php'); ?>
<?php 
	$ret = init_psmMode("Troubleshooting > MoCA Diagnostics", "nav-moca-diagnostics");
	if ("" != $ret){echo $ret;	return;}
	function div_mod($n, $m)
	{
		if (!is_numeric($n) || !is_numeric($m) || (0==$m)){
			return array(0, 0);
		}	
		for($i=0; $n >= $m; $i++){
			$n = $n - $m;
		}	
		return array($i, $n);
	}
	$MoCA_param = array(
		"moca_enable"		=> "Device.MoCA.Interface.1.Enable",
		"MACAddress" 		=> "Device.MoCA.Interface.1.MACAddress",
		"CurrentVersion" 	=> "Device.MoCA.Interface.1.CurrentVersion",
		"HighestVersion" 	=> "Device.MoCA.Interface.1.HighestVersion",
		"CurrentOperFreq" 	=> "Device.MoCA.Interface.1.CurrentOperFreq",
		"moca_NodeID" 		=> "Device.MoCA.Interface.1.NodeID",
		"BeaconPowerLimit" 	=> "Device.MoCA.Interface.1.BeaconPowerLimit",
		"LinkUpTime" 		=> "Device.MoCA.Interface.1.LinkUpTime",
		"FreqCurrentMask" 	=> "Device.MoCA.Interface.1.FreqCurrentMask",
		"TxPowerLimit" 		=> "Device.MoCA.Interface.1.TxPowerLimit",
		"Privacy_Setting" 	=> "Device.MoCA.Interface.1.PrivacyEnabledSetting",
		"BackupNC"			=> "Device.MoCA.Interface.1.BackupNC",
		"PreferredNC"		=> "Device.MoCA.Interface.1.PreferredNC",
		"ErrorsReceived" 	=> "Device.MoCA.Interface.1.Stats.ErrorsReceived",
		"Discard_Received" 	=> "Device.MoCA.Interface.1.Stats.DiscardPacketsReceived",
		"PacketsSent" 		=> "Device.MoCA.Interface.1.Stats.PacketsSent",
		"PacketsReceived" 	=> "Device.MoCA.Interface.1.Stats.PacketsReceived",
	);
	$MoCA_value = KeyExtGet("Device.MoCA.Interface.1.", $MoCA_param);
	$moca_enable		= $MoCA_value['moca_enable'];
	$MACAddress			= $MoCA_value['MACAddress'];
	$CurrentVersion		= $MoCA_value['CurrentVersion'];
	$HighestVersion		= $MoCA_value['HighestVersion'];
	$moca_NodeID		= $MoCA_value['moca_NodeID'];
	$CurrentOperFreq	= $MoCA_value['CurrentOperFreq'];
	$BeaconPowerLimit	= $MoCA_value['BeaconPowerLimit'];
	$LinkUpTime			= $MoCA_value['LinkUpTime'];
	$ErrorsReceived		= $MoCA_value['ErrorsReceived'];
	$Discard_Received	= $MoCA_value['Discard_Received'];
	$FreqCurrentMask	= $MoCA_value['FreqCurrentMask'];
	$TxPowerLimit		= $MoCA_value['TxPowerLimit'];
	$Privacy_Setting	= $MoCA_value['Privacy_Setting'];
	$BackupNC			= $MoCA_value['BackupNC'];
	$PacketsSent		= $MoCA_value['PacketsSent'];
	$PacketsReceived	= $MoCA_value['PacketsReceived'];
	$PreferredNC		= ($MoCA_value['PreferredNC'] == 'true') ? 'Yes' : 'No';
	$rootObjName	= "Device.MoCA.Interface.1.AssociatedDevice.";
	$paramNameArray	= array("Device.MoCA.Interface.1.AssociatedDevice.");
	$mapping_array	= array("MACAddress", "NodeID", "PreferredNC", "TxPowerControlReduction", "RxPowerLevel");
	$moca_AssocDev	= getParaValues($rootObjName, $paramNameArray, $mapping_array);
	$PreferredNC_data = 'NA';
	$BackupNC_data = 'NA';
	$moca_AssocDev['GW']['MACAddress'] 	= $MACAddress;
	$moca_AssocDev['GW']['NodeID'] 		= $moca_NodeID;
	$moca_AssocDev['GW']['PreferredNC'] = $MoCA_value['PreferredNC'];
	foreach ($moca_AssocDev as $key => $value) {
		//MoCA MAC address and Node ID of the Active Network Controller
		if($value['PreferredNC'] == 'true' && $BackupNC != $value['NodeID'])
			$PreferredNC_data = 'MAC: '.strtoupper($value['MACAddress']).', Node ID: '.$value['NodeID'];
		//MoCA MAC address and Node ID of the Backup Network Controller
		if($BackupNC == $value['NodeID'])
			$BackupNC_data = 'MAC: '.strtoupper($value['MACAddress']).', Node ID: '.$value['NodeID'];
	}
	if($MoCA_value['PreferredNC'] == 'true' && sizeof($moca_AssocDev) ==1){
		$PreferredNC_data = 'MAC: '.strtoupper($MACAddress).', Node ID: '.$moca_NodeID;
		$BackupNC_data = 'NA';
	}
$channel_array = array(
	"1150" => "D1(1150 MHz)",
	"1200" => "D2(1200 MHz)",
	"1250" => "D3(1250 MHz)",
	"1300" => "D4(1300 MHz)",
	"1350" => "D5(1350 MHz)",
	"1400" => "D6(1400 MHz)",
	"1450" => "D7(1450 MHz)",
	"1500" => "D8(1500 MHz)",
	"1175" => "D1a(1175 MHz)",
	"1225" => "D2a(1225 MHz)",
	"1275" => "D3a(1275 MHz)",
	"1325" => "D4a(1325 MHz)",
	"1375" => "D5a(1375 MHz)",
	"1425" => "D6a(1425 MHz)",
	"1475" => "D7a(1475 MHz)",
	"1525" => "D8a(1525 MHz)",
	"1550" => "D9(1550 MHz)",
	"1575" => "D9a(1575 MHz)",
	"1600" => "D10(1600 MHz)",
	"1625" => "D10a(1625 MHz)",
);
?>
<script type="text/javascript">
var network = null;
function ajax_moca_diagnostics() {
		jProgress('This may take several seconds', 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajax_moca_diagnostics.php",
			data: { moca_diagnostics: 'diagnostics' },
			success: function(result) {
				jHide();
				if(result['MeshTableEntries']=='0') jAlert("Currently MoCA devices are not connected to the Gateway.");
				else draw(result);
			},
			failure: function() {
				jHide();
				jAlert("Failure, please try again.");
			},
			error: function(){
				jHide();
				jAlert("Failure, please try again.");
			}
		});
	}
function draw($data_MoCA) {
	update_NC_AssociatedDevices($data_MoCA);
	destroy();
	var data_network = create_nodes_edges($data_MoCA);
	var container = document.getElementById('network_diagram');
	var options = {
		"edges": {
			"smooth": {
				"type": "straightCross",
				"roundness": 0.5,
				"forceDirection": "none"
			},
			"font":{
				"align":'top',
			},
			"arrows": {
				"to": {
					"scaleFactor": 0.5
				},
			},
			'arrowStrikethrough': false,
		},
		"interaction": {
			"dragNodes": false
		},
		"physics": {
			"barnesHut": {
				"springLength": 300,
				"springConstant": 0.19,
				"damping": 0.50,
				"avoidOverlap": 1
			},
		"minVelocity": 0.75
		}
	};
	network = new vis.Network(container, data_network, options);
}
function update_NC_AssociatedDevices($data){
		$AssociatedDevices = $data['AssociatedDevices'];
		$MACAddress	= $AssociatedDevices['GW']['MACAddress'];
		$BackupNC	= $AssociatedDevices['GW']['BackupNC'];
		$('#moca_nodes').find("tr:gt(0)").remove();
		$class = false;
		$PreferredNC_data	= "NA";
		$BackupNC_data		= "NA";
		$.each($AssociatedDevices, function($key, $value) {
			//MoCA MAC address and Node ID of the Active Network Controller
			if($value['PreferredNC'] == 'true' && $BackupNC != $value['NodeID'])
				$PreferredNC_data = 'MAC: '+$value['MACAddress'].toUpperCase()+', Node ID: '+$value['NodeID'];
			//MoCA MAC address and Node ID of the Backup Network Controller
			if($BackupNC == $value['NodeID'])
				$BackupNC_data = 'MAC: '+$value['MACAddress'].toUpperCase()+', Node ID: '+$value['NodeID'];
			//MoCA Nodes Table
			$network_controller = ($value['PreferredNC']=='true')?'Yes':'No';
			$odd = ($class)?"":" class='odd'";
			$class = !$class;
			$transmit_power_level	= ($value['TxPowerControlReduction'] != null)?$value['TxPowerControlReduction']:"NA";
			$receive_power_level	= ($value['RxPowerLevel'] != null)?$value['RxPowerLevel']:"NA";
			//create row
			row = "<tr "+$odd+">";
				row += "<td headers='node-id'>"+$value['NodeID']+"</td>";
				row += "<td headers='mac-address'>"+$value['MACAddress'].toUpperCase()+"</td>";
				row += "<td headers='network-controller'>"+$network_controller+"</td>";
				row += "<td headers='transmit-power-level'>"+$transmit_power_level+"</td>";
				row += "<td headers='receive-power-level'>"+$receive_power_level+"</td>";
			row += "</tr>";
			$('#moca_nodes').append(row);
		});
		$('#active_NC').text($PreferredNC_data);
		$('#backup_NC').text($BackupNC_data);
	}
function moca_bonded_paths($MeshPHYTxRate, $Tx_HighestVersion, $Rx_HighestVersion){
	$Tx_HighestVersion = $Tx_HighestVersion.replace('.','');
	$Rx_HighestVersion = $Rx_HighestVersion.replace('.','');
	if($Tx_HighestVersion == 20 && $Rx_HighestVersion == 20)
		$HighestVersion = 20;
	else if($Tx_HighestVersion == 11 || $Rx_HighestVersion == 11)
		$HighestVersion = 11;
	else $HighestVersion = '';
	//MoCA 1.1 Paths >=200mbps is marked in green, paths >=180mbps but <200mbps are marked in orange, and paths <180mbps are marked in red.
	//MoCA 2.0 paths >=370mbps are marked in green, paths >=330mbps but <370mbps are marked in orange, and paths < 330mbps are marked in red.
	//MoCA 2.0 bonded paths >= 740mbps are marked in green, paths >=660 but <740mbps are marked in orange, and paths < 660 are marked in red.
	// HighestVersion can be 20[MoCA 2.0] or 11[MoCA 1.1], MoCA 2.0 bonded is not supported
	/*if ($MeshPHYTxRate >= 740) return 'green';
	else if ($MeshPHYTxRate >= 660) return 'orange';
	else if ($MeshPHYTxRate >= 371) return 'red';*/
	if($HighestVersion == 20){
		if ($MeshPHYTxRate >= 370) $bonded_path = 'green';
		else if ($MeshPHYTxRate >= 330) $bonded_path = 'orange';
		else $bonded_path = 'red';
	}
	else if($HighestVersion == 11){
		if ($MeshPHYTxRate >= 200) $bonded_path = 'green';
		else if ($MeshPHYTxRate >= 180) $bonded_path = 'orange';
		else $bonded_path = 'red';
	}
	else $bonded_path = 'red';
	return $bonded_path;
}
function create_nodes_edges($data_MoCA){
	var nodes = new Array();
	var edges = new Array();
	// create an array with nodes & edges
	for (var $k in $data_MoCA['Mesh_HighestVersion']) {
		nodes.push({id: $k, label: "Node ID: "+$k, shape: 'box',});
	}
	for (var $key in $data_MoCA) {
		if($key != 'Mesh_HighestVersion' && $key != 'AssociatedDevices' && $key != 'MeshTableEntries') {
			$TxID = $data_MoCA[$key][0]['MeshTxNodeId'];
			$RxID = $data_MoCA[$key][0]['MeshRxNodeId'];
			var $path_color = moca_bonded_paths($data_MoCA[$key][0]['MeshPHYTxRate'], $data_MoCA['Mesh_HighestVersion'][$TxID], $data_MoCA['Mesh_HighestVersion'][$RxID]);
			edges.push({from: $TxID, to: $RxID, label: $data_MoCA[$key][0]['MeshPHYTxRate'], color:{color: $path_color, highlight: $path_color}});
		}
	}
	var data = {
		nodes: nodes,
		edges: edges
	};
	return data;
}
function destroy() {
	if (network !== null) {
		network.destroy();
		network = null;
	}
}
$(document).ready(function() {
	gateway.page.init("Troubleshooting > MoCA Diagnostics", "nav-moca-diagnostics");
	$('#refresh').click(function() {
		ajax_moca_diagnostics();
	});
});
$(window).load(function() {
	ajax_moca_diagnostics();
});
</script>
<div id="content" >
	<h1>Troubleshooting > MoCA Diagnostics</h1>
	<div id="educational-tip">
		<p class="tip">View information about devices currently connected to the Gateway's MoCA Network.</p>
		<p class="hidden"><strong>MoCA Privacy: </strong> If MoCA Privacy is enabled, all the devices connecting to the Gateway via MoCA will use the MoCA Network Password. </p>
	</div>
	<form id="pageForm">
		<legend class="acs-hide">MoCA Diagnostics</legend>
		<div class="module forms">
			<h2>MoCA Diagnostics</h2>
			<div class="form-row">
				<label>Status:</label>
				<span class="readonlyValue"><?php echo ($moca_enable == 'true')?'Enabled':'Disabled'; ?></span>
			</div>
			<div class="form-row odd">
				<label>MAC Address:</label>
				<span class="readonlyValue"><?php echo strtoupper($MACAddress); ?></span>
			</div>
			<div class="form-row">
				<label>Maximum Version Supported:</label>
				<span class="readonlyValue"><?php echo $HighestVersion; ?></span>
			</div>
			<div class="form-row odd">
				<label>Current Operational Capabilities:</label>
				<span class="readonlyValue"><?php echo $CurrentVersion; ?></span>
			</div>
			<div class="form-row">
				<label>Preferred Network Controller:</label>
				<span class="readonlyValue"><?php echo $PreferredNC; ?></span>
			</div>
			<div class="form-row odd">
				<label>Active Network Controller:</label>
				<span class="readonlyValue" id="active_NC"><?php echo $PreferredNC_data; ?></span>
			</div>
			<div class="form-row">
				<label>Backup Network Controller:</label>
				<span class="readonlyValue" id="backup_NC"><?php echo $BackupNC_data; ?></span>
			</div>
			<div class="form-row odd">
				<label>Beacon Frequency:</label>
				<span class="readonlyValue"><?php echo $channel_array[$CurrentOperFreq]; ?></span>
			</div>
			<div class="form-row">
				<label>Center frequency:</label>
				<span class="readonlyValue"><?php echo $channel_array[$CurrentOperFreq]; ?></span>
			</div>
			<div class="form-row odd">
				<label>Beacon Backoff Power Level:</label>
				<span class="readonlyValue"><?php echo $BeaconPowerLimit.'dB'; ?></span>
			</div>
			<div class="form-row">
				<label>Link Uptime:</label>
				<span class="readonlyValue">
					<?php
						$tmp = div_mod($LinkUpTime, 24*60*60);
						$day = $tmp[0];
						$tmp = div_mod($tmp[1], 60*60);
						$hor = $tmp[0];
						$tmp = div_mod($tmp[1],    60);
						$min = $tmp[0];
						echo $day."d:".$hor."h:".$min."m";
					?>
				</span>
			</div>
			<div class="form-row odd">
				<label>Number of Packets Transmitted:</label>
				<span class="readonlyValue"><?php echo $PacketsSent; ?></span>
			</div>
			<div class="form-row">
				<label>Number of Packets Received:</label>
				<span class="readonlyValue"><?php echo $PacketsReceived; ?></span>
			</div>
			<div class="form-row odd">
				<label>Number of Uncorrectable Error Packets Received:</label>
				<span class="readonlyValue"><?php echo $Discard_Received; ?></span>
			</div>
			<div class="form-row">
				<label>Number of Corrected Error Packets Received:</label>
				<span class="readonlyValue"><?php echo ($ErrorsReceived - $Discard_Received); ?></span>
			</div>
			<div class="form-row odd">
				<label>Channel Mask:</label>
				<span class="readonlyValue"><?php echo $FreqCurrentMask; ?></span>
			</div>
			<div class="form-row">
				<label for="Privacy">Privacy:</label>
				<span class="readonlyValue"><?php echo ($Privacy_Setting == 'true')?'Enabled':'Disabled'; ?></span>
			</div>
		</div> <!-- end .module -->
	</form>
	<div id='moca-online' class="module data">
		<h2>MoCA Nodes</h2>
		<table class="data" id="moca_nodes" summary="This table displays Online Devices connected to MoCA network">
			<tr>
				<th id="node-id">Node ID</th>
				<th id="mac-address">MoCA MAC Address</th>
				<th id="network-controller">Network Controller</th>
				<th id="transmit-power-level" width="20%">MoCA Transmit Power Reduction</th>
				<th id="receive-power-level" width="20%">MoCA Receive Power Level</th>
			</tr>
			<?php
				$class = false;
				foreach ($moca_AssocDev as $key => $value) {
					$network_controller = ($value['PreferredNC']=='true')?'Yes':'No';
					$odd = ($class)?"":" class='odd'";
					$class = !$class;
					$transmit_power_level	= (isset($value['TxPowerControlReduction']))?$value['TxPowerControlReduction']:"NA";
					$receive_power_level	= (isset($value['RxPowerLevel']))?$value['RxPowerLevel']:"NA";
					echo "
						<tr $odd>
							<td headers='node-id'>".$value['NodeID']."</td>
							<td headers='mac-address'>".strtoupper($value['MACAddress'])."</td>
							<td headers='network-controller'>".$network_controller."</td>
							<td headers='transmit-power-level'>".$transmit_power_level."</td>
							<td headers='receive-power-level'>".$receive_power_level."</td>
						</tr>
					";
				}
			?>
	     	<tfoot>
				<tr class="acs-hide">
					<td headers="node-id">null</td>
					<td headers="mac-address">null</td>
					<td headers="network-controller">null</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="module data">
		<h2>MoCA Network Diagram</h2>
		<input style="float: right; margin-top: 10px;" type="button" value="Refresh" id="refresh">
		<div id="network_diagram" style="height: 400px; border: 1px solid lightgray; margin-top: 8px; padding-bottom: 50px;"></div>
	</div>
</div><!-- end #content -->
<?php include('includes/footer.php');?>