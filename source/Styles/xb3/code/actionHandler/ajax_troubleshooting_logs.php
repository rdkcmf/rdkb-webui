<!--
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2015 RDK Management

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
-->
<?php

// the log type of GUI wanted
$mode	= $_POST['mode'];
$timef	= $_POST['timef'];

// get Unix time of the target time point
switch($timef) {
	case "Today":
		$maxtime=strtotime("now");
		$mintime=strtotime("today");
	break;
	case "Yesterday":
		$maxtime=strtotime("today");
		$mintime=strtotime("yesterday");
	break;
	case "Last week":
		$maxtime=strtotime("this Monday");
		$mintime=strtotime("last Monday");
	break;
	case "Last month":
		$maxtime=strtotime("this month");
		$mintime=strtotime("last month");
	break;
	case "Last 90 days":
		$maxtime=strtotime("today");
		$mintime=strtotime("-90 days");
	break;
}

// find each log entry
if ("system" == $mode) {
	$allDM = "Device.X_CISCO_COM_Diagnostics.DumpAllSyslog";
}
else if ("event" == $mode) {
	$allDM = "Device.X_CISCO_COM_Diagnostics.DumpAllEventlog";
}
else if ("firewall" == $mode) {
	$allDM = "Device.X_CISCO_COM_Security.InternetAccess.DumpAllFWlog";
}

$MONTH = array("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
$logs  = array();

exec("ccsp_bus_client_tool eRT getv $allDM ", $raws);

for ($i=6; $i<count($raws);)
{
	if ("firewall" == $mode) {
		$Count		= $raws[$i++];	//attempts, count
		$SourceIP	= $raws[$i++];	//source IP, not used
		$User		= $raws[$i++];	//user, not used
		$TargetIP	= $raws[$i++];	//target IP, not used
		$Level		= $raws[$i++];	//action proceed by firewall
		$time		= $raws[$i++];	//trigger time
		$Des		= $raws[$i++].", $Count Attempts.";	//description
	}
	else {
		$time 		= $raws[$i++];	//system or event time
		$Tag		= $raws[$i++];	//localxxx, not used
		$Level 		= $raws[$i++];	//info, warning, error
		$Des 		= $raws[$i++];	//description
	}		
	
	$timeT = explode(' ', $time);	//Oct 10 17:09:17 2014
	$timeU = mktime(0, 0, 0, $MONTH[$timeT[0]], $timeT[1], $timeT[3]);
	if ($timeU > $maxtime || $timeU < $mintime) continue;	//only store the needed line
	
	array_push($logs, array("time"=>$timeT[3].'/'.$MONTH[$timeT[0]].'/'.$timeT[1].' '.$timeT[2], "Level"=>$Level, "Des"=>$Des));
}

// last log shows first
$logs = array_reverse($logs);

// logic of download log file from GUI
$fh=fopen("/var/tmp/troubleshooting_logs_".$mode."_".$timef.".txt","w");
foreach ($logs as $key=>$value) {
	fwrite($fh, $value["time"]."\t".$value["Level"]."\t".$value["Des"]."\r\n");
}
fclose($fh);

// return results to GUI in json format
header("Content-Type: application/json");
echo json_encode($logs);	

// exec("/fss/gw/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_Diagnostics.Syslog.Entry. | grep 'type:' > /var/log_system.txt");
// exec("/fss/gw/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_Diagnostics.Eventlog.Entry. | grep 'type:' > /var/log_event.txt");
// exec("/fss/gw/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_Security.InternetAccess.LogEntry. | grep 'type:' > /var/log_firewall.txt");

?>
