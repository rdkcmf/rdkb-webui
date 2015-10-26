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
		//zqiu: last 90 days include today
		//$maxtime=strtotime("today");
		$maxtime=strtotime("now");
		$mintime=strtotime("-90 days");
	break;
}

switch($mode){
	case "site":
		$type="Site Blocked";
		break;
	case "service":
		$type="Service Blocked";
		break;
	case "device":
		$type="Device Blocked";
		break;
	default:
		$type="all";
}

exec("/fss/gw/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_Security.InternetAccess.LogEntry. | grep 'type:' > /var/log_parental.txt");
$file= fopen("/var/log_parental.txt", "r");
$pos = 50;		//global file pointer where to read the value in a line
$Log = array();

for ($i=0; !feof($file); ) {
	$Count 	    = substr(fgets($file),$pos);
	$SourceIP 	= substr(fgets($file),$pos);	//don't need, but have to read
	$User 	    = substr(fgets($file),$pos);
	$TargetIP 	= substr(fgets($file),$pos);
	$Type 	    = rtrim(substr(fgets($file),$pos)); //need to trim the blank char in string end, otherwise $type never equal to $Type
	$time 	    = substr(fgets($file),$pos);
	$Des 	    = substr(fgets($file),$pos);

	if (feof($file)) break;					//PHP read last line will return false, not EOF!
	
	$timeU = strtotime($time);
	// dump('timeU = '. $timeU);
	// dump('mintime = '. $mintime);
	// dump('maxtime = '. $maxtime);

	if ($timeU > $maxtime || $timeU < $mintime) continue;	//only store the needed line

	if ($type == $Type) {
		$Log[$i++] = array("time"=>$time, "Des"=>$Des, "Count"=>$Count, "Target"=>$TargetIP,"Source"=>$SourceIP,"Type"=>$Type);
    }
    elseif ($type == "all") {
    	if (in_array($Type, array("Site Blocked", "Service Blocked", "Device Blocked"))) {
			$Log[$i++] = array("time"=>$time, "Des"=>$Des, "Count"=>$Count, "Target"=>$TargetIP,"Source"=>$SourceIP,"Type"=>$Type);
    	}
    }
	
}

fclose($file);

$firewall_log = array_reverse($Log);
// echo "firewall log ...: \n";
// var_dump($firewall_log);
	
$fh=fopen("/var/tmp/parental_reports_".$mode."_".$timef.".txt","w");
foreach ($firewall_log as $key=>$value){
	fwrite($fh, $value["Des"].", ".$value["Count"]." Attemps, ".$value["time"]."\t".$value["Type"]."\r\n");
}
fclose($fh);

header("Content-Type: application/json");
echo json_encode($firewall_log);
	
?>
