<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:
 Copyright 2016 RDK Management
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
<?php include('../includes/actionHandlerUtility.php') ?>
<?php

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
//if responce is "", then CcspTandDSsp has crashed
$states=array("Complete","Error_CannotResolveHostName","Error_Internal","Error_Other");
$states_trace=array("Complete","Error_CannotResolveHostName","Error_MaxHopCountExceeded","Error_Internal");
if (isset($_POST['test_connectivity'])){
	$destination_address=$_POST['destination_address'];
	$count1=$_POST['count1'];
	$DiagnosticsState="Requested";
	if(validLink($destination_address) && preg_match('/^[1-4]$/', $count1)){
		// setStr("Device.IP.Diagnostics.IPPing.Interface","Device.IP.Interface.1");
		setStr("Device.IP.Diagnostics.IPPing.Host",$destination_address,true);
		setStr("Device.IP.Diagnostics.IPPing.NumberOfRepetitions",$count1,true);
		setStr("Device.IP.Diagnostics.IPPing.DiagnosticsState",$DiagnosticsState,true);
		do{
			sleep(1);
			$pingState=getStr("Device.IP.Diagnostics.IPPing.DiagnosticsState");
			if($pingState == "") $trace_ipv4_status = _("Error_Internal");
		}while(!in_array($pingState,$states));
		$success_received=getStr("Device.IP.Diagnostics.IPPing.SuccessCount");
		// $failure_received=getStr("Device.IP.Diagnostics.IPPing.FailureCount");
		if ($success_received==0) {$connectivity_internet=_("Inactive: ").$pingState;}
		else {$connectivity_internet=_("Active");}
		$result=array('connectivity_internet'=>$connectivity_internet,'success_received'=>$success_received);
	}
	else{
		$result=array('connectivity_internet'=>_("Error"),'success_received'=>"0");
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
}
else if (isset($_POST['destination_ipv4'])){
	$destination_ipv4=$_POST['destination_ipv4'];
	$count2=$_POST['count2'];
	$DiagnosticsState="Requested";
	if(validIPAddr($destination_ipv4) && preg_match('/^[1-4]$/', $count2)){
		setStr("Device.IP.Diagnostics.IPPing.Host",$destination_ipv4,true);
		setStr("Device.IP.Diagnostics.IPPing.NumberOfRepetitions",$count2,true);
		setStr("Device.IP.Diagnostics.IPPing.DiagnosticsState",$DiagnosticsState,true);
		do{
			sleep(1);
			$pingState=getStr("Device.IP.Diagnostics.IPPing.DiagnosticsState");
			if($pingState == "") $trace_ipv4_status = _("Error_Internal");
		}while(!in_array($pingState,$states));
		$success_received=getStr("Device.IP.Diagnostics.IPPing.SuccessCount");
		if($success_received>0) {$connectivity_ipv4="OK";}
		else {$connectivity_ipv4=_("Error");}
		$result=array('connectivity_ipv4'=>$connectivity_ipv4);
	}
	else{
		$result=array('connectivity_ipv4'=>_("Error"));
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
}
else if (isset($_POST['destination_ipv6'])){
	$destination_ipv6=$_POST['destination_ipv6'];
	$count3=$_POST['count3'];
	$DiagnosticsState="Requested";
	if(validIPAddr($destination_ipv6) && preg_match('/^[1-4]$/', $count3)){
		setStr("Device.IP.Diagnostics.IPPing.Host",$destination_ipv6,true);
		setStr("Device.IP.Diagnostics.IPPing.NumberOfRepetitions",$count3,true);
		setStr("Device.IP.Diagnostics.IPPing.DiagnosticsState",$DiagnosticsState,true);
		do{
			sleep(1);
			$pingState=getStr("Device.IP.Diagnostics.IPPing.DiagnosticsState");
			if($pingState == "") $trace_ipv4_status = _("Error_Internal");
		}while(!in_array($pingState,$states));
		$success_received=getStr("Device.IP.Diagnostics.IPPing.SuccessCount");
		if($success_received>0) {$connectivity_ipv6="OK";}
		else {$connectivity_ipv6=_("Error");}
		$result=array('connectivity_ipv6'=>$connectivity_ipv6);
	}
	else{
		$result=array('connectivity_ipv6'=>_("Error"));
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
}
else if (isset($_POST['trace_ipv4_dst'])){
	$trace_ipv4_dst	   = $_POST['trace_ipv4_dst'];
	$trace_ipv4_status = "Requested";
	$trace_ipv4_result = array(
		// "Tracing route to comcast.net [10.0.0.101] over a maximum of 30 hops:",
		// "1    <1 ms    <1 ms    <1 ms  10.87.12.1",
		// "2    <1 ms    <1 ms    <1 ms  10.19.1.24",
		// "3     2 ms     2 ms     2 ms  172.168.86.86",
		// "4     2 ms     2 ms     2 ms  172.25.24.13",
		// "5     2 ms     2 ms     2 ms  162.25.47.238",
		// "6    10 ms     9 ms    10 ms  192.10.96.97",
		// "7    10 ms    10 ms    10 ms  10.66.6.33",
		// "8    10 ms    10 ms    10 ms  comcast.net [10.0.0.101]",
		// "Trace complete."
	);
	if(validIPAddr($trace_ipv4_dst)){
		setStr("Device.IP.Diagnostics.TraceRoute.Host", $trace_ipv4_dst, true);
		setStr("Device.IP.Diagnostics.TraceRoute.DiagnosticsState", $trace_ipv4_status, true);
		do{
			sleep(3);
			$trace_ipv4_status = getStr("Device.IP.Diagnostics.TraceRoute.DiagnosticsState");
			if($trace_ipv4_status == "") $trace_ipv4_status = _("Error_Internal");
			// $trace_ipv4_status = "Complete";
		}while(!in_array($trace_ipv4_status, $states_trace));
		if ("Complete" == $trace_ipv4_status){
			$ids = explode(",", getInstanceIds("Device.IP.Diagnostics.TraceRoute.RouteHops."));
			foreach($ids as $i){
				$time = getStr("Device.IP.Diagnostics.TraceRoute.RouteHops.$i.RTTimes");
				$host = getStr("Device.IP.Diagnostics.TraceRoute.RouteHops.$i.Host");
				$addr = getStr("Device.IP.Diagnostics.TraceRoute.RouteHops.$i.HostAddress");
				array_push($trace_ipv4_result, $i.': '.$time.' '.$host.' '.$addr);
			}
		}
		$result=array('trace_ipv4_status'=>$trace_ipv4_status, 'trace_ipv4_result'=>$trace_ipv4_result);
	}
	else{
		$result=array('trace_ipv4_status'=>_("Error"), 'trace_ipv4_result'=>_("Error"));
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
}
else if (isset($_POST['trace_ipv6_dst'])){
	$trace_ipv6_dst	   = $_POST['trace_ipv6_dst'];
	$trace_ipv6_status = "Requested";
	$trace_ipv6_result = array();
	if(validIPAddr($trace_ipv6_dst)){
		setStr("Device.IP.Diagnostics.TraceRoute.Host", $trace_ipv6_dst, true);
		setStr("Device.IP.Diagnostics.TraceRoute.DiagnosticsState", $trace_ipv6_status, true);
		do{
			sleep(3);
			$trace_ipv6_status = getStr("Device.IP.Diagnostics.TraceRoute.DiagnosticsState");
			if($trace_ipv6_status == "") $trace_ipv4_status = _("Error_Internal");
			// $trace_ipv6_status = "Error_CannotResolveHostName";
		}while(!in_array($trace_ipv6_status, $states_trace));
		if ("Complete" == $trace_ipv6_status){
			$ids = explode(",", getInstanceIds("Device.IP.Diagnostics.TraceRoute.RouteHops."));
			foreach($ids as $i){
				$time = getStr("Device.IP.Diagnostics.TraceRoute.RouteHops.$i.RTTimes");
				$host = getStr("Device.IP.Diagnostics.TraceRoute.RouteHops.$i.Host");
				$addr = getStr("Device.IP.Diagnostics.TraceRoute.RouteHops.$i.HostAddress");
				array_push($trace_ipv6_result, $i.': '.$time.' '.$host.' '.$addr);
			}
		}
		$result=array('trace_ipv6_status'=>$trace_ipv6_status, 'trace_ipv6_result'=>$trace_ipv6_result);
	}
	else{
		$result=array('trace_ipv6_status'=>_("Error"), 'trace_ipv6_result'=>_("Error"));
	}
	header("Content-Type: application/json");
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
}
?>
