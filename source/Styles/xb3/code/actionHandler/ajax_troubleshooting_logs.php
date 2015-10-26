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
﻿<?php

function str2time($str){

	$MONTH = array("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
	// $timeT = preg_replace('/\s(?=\s)/', '', $str);
	// $timeTmp = explode(" ", $timeT);
	$timeTmp = preg_split("/[\s,]+/", $str);
	$time  = array();

	if (! empty($timeTmp[3])) {
		//log time contains 'year'
		$time['formatted_time'] = $timeTmp[3] .'/'. $MONTH[$timeTmp[0]] .'/'. $timeTmp[1] .' '. $timeTmp[2]; 
		$time['firewall_time']  = $timeTmp[3] .'/'. $MONTH[$timeTmp[0]] .'/'. $timeTmp[1] .' '. $timeTmp[2]; 
		$time['timeU'] = mktime(0, 0, 0, $MONTH[$timeTmp[0]], $timeTmp[1], $timeTmp[3]);
	}
	else {
		if ($MONTH[$timeTmp[0]] <= $MONTH[date("M")]) {
			$time['timeU'] = mktime(0, 0, 0, $MONTH[$timeTmp[0]], $timeTmp[1], date("Y"));
			$time['formatted_time'] = date("Y") .'/'. $MONTH[$timeTmp[0]] .'/'. $timeTmp[1] .' '. $timeTmp[2]; 
			$time['firewall_time']  = date("Y") .'/'. $MONTH[$timeTmp[0]] .'/'. $timeTmp[1] .' '. $timeTmp[2]; 
		} 
		else {
			$time['timeU'] = mktime(0, 0, 0, $MONTH[$timeTmp[0]], $timeTmp[1], date("Y")-1);
			$time['formatted_time'] = date("Y")-1 .'/'. $MONTH[$timeTmp[0]] .'/'. $timeTmp[1] .' '. $timeTmp[2]; 
			$time['firewall_time']  = date("Y")-1 .'/'. $MONTH[$timeTmp[0]] .'/'. $timeTmp[1] .' '. $timeTmp[2]; 
		}
	}
	//var_dump($time);
	return $time;
}


$mode=$_POST['mode'];
$timef=$_POST['timef'];
switch($timef){			//	[$mintime, $maxtime)
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

$pos = 50;		//global file pointer where to read the value in a line

if ($mode=="system"){

	exec("/fss/gw/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_Diagnostics.Syslog.Entry. | grep 'type:' > /var/log_system.txt");
	$file= fopen("/var/log_system.txt", "r");
	$Log = array();
	// for($i=0; !feof($file); $i++)
	for($i=0; !feof($file); )
	{
		$time 	= substr(fgets($file),$pos);
		$Tag	= substr(fgets($file),$pos);	//don't need, but have to read
		$Level 	= substr(fgets($file),$pos);
		$Des 	= substr(fgets($file),$pos);

		// $Log[$i] =	array("time"=>$time, "Level"=>$Level, "Des"=>$Des);

		if (feof($file)) break;					//PHP read last line will return false, not EOF!
		
		$timeArr = str2time(trim($time));
		$timeU = $timeArr['timeU'];

		if ($timeU > $maxtime || $timeU < $mintime) continue;	//only store the needed line
		
		$Log[$i++] = array("time"=>$timeArr['formatted_time'], "Level"=>$Level, "Des"=>$Des);
	}
	fclose($file);
	// array_pop($Log);	
	
	$sysLog = $Log;

	//dump($sysLog);
	
	$fh=fopen("/var/tmp/troubleshooting_logs_".$mode."_".$timef.".txt","w+");
	foreach ($sysLog as $key=>$value){
		fwrite($fh, $value["Des"]."\t".$value["time"]."\t".$value["Level"]."\r\n");
	}
	fclose($fh);

	header("Content-Type: application/json");
	echo json_encode($sysLog);	

}
else if ($mode=="event") {
	
	exec("/fss/gw/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_Diagnostics.Eventlog.Entry. | grep 'type:' > /var/log_event.txt");
	$file= fopen("/var/log_event.txt", "r");
	$Log = array();
	// for($i=0; !feof($file); $i++)
	for($i=0; !feof($file); )
	{
		$time 	= substr(fgets($file),$pos);
		$ID 	= substr(fgets($file),$pos);	//don't need, but have to read
		$Level 	= substr(fgets($file),$pos);
		$Des 	= substr(fgets($file),$pos);

		// $Log[$i] =	array("time"=>$time, "Level"=>$Level, "Des"=>$Des);

		if (feof($file)) break;					//PHP read last line will return false, not EOF!
		
		$timeArr = str2time(trim($time));
		$timeU = $timeArr['timeU'];
		if ($timeU > $maxtime || $timeU < $mintime) continue;	//only store the needed line
		
		$Log[$i++] = array("time"=>$timeArr['formatted_time'], "Level"=>$Level, "Des"=>$Des);
	}
	fclose($file);
	// array_pop($Log);	
	
	$docLog = $Log;
		
	$fh=fopen("/var/tmp/troubleshooting_logs_".$mode."_".$timef.".txt","w+");
	foreach ($docLog as $key=>$value){
		fwrite($fh, $value["Des"]."\t".$value["time"]."\t".$value["Level"]."\r\n");
	}
	fclose($fh);
	
	header("Content-Type: application/json");
	echo json_encode($docLog);
	
}
else {	

	exec("/fss/gw/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_Security.InternetAccess.LogEntry. | grep 'type:' > /var/log_firewall.txt");
	$file= fopen("/var/log_firewall.txt", "r");
	$Log = array();
	// for($i=0; !feof($file); $i++)
	for($i=0; !feof($file); )
	{
		$Count		= substr(fgets($file),$pos);
		$SourceIP	= substr(fgets($file),$pos);	//don't need, but have to read
		$User		= substr(fgets($file),$pos);
		$TargetIP	= substr(fgets($file),$pos);
		$Type		= substr(fgets($file),$pos);
		$time		= substr(fgets($file),$pos);
		$Des		= substr(fgets($file),$pos);

		// $Log[$i] =	array("time"=>$time, "Des"=>$Des, "Count"=>$Count, "Target"=>$TargetIP,"Source"=>$SourceIP,"Type"=>$Type);

		if (feof($file)) break;						//PHP read last line will return false, not EOF!

		$timeArr = str2time(trim($time));
		$timeU = $timeArr['timeU'];
		if ($timeU > $maxtime || $timeU < $mintime) continue;	//only store the needed line
		
		$Log[$i++] = array("time"=>$timeArr['firewall_time'], "Des"=>$Des, "Count"=>$Count, "Target"=>$TargetIP,"Source"=>$SourceIP,"Type"=>$Type);
	}
	fclose($file);
	
	$firewallLog = $Log;	
	$fh=fopen("/var/tmp/troubleshooting_logs_".$mode."_".$timef.".txt","w+");

	foreach ($firewallLog as $key=>$value){
		fwrite($fh, $value["Des"].", ".$value["Count"]." Attemps, ".$value["time"]."\t".$value["Type"]."\r\n");
	}
	fclose($fh);
	
	header("Content-Type: application/json");
	echo json_encode($firewallLog);
}


?>
