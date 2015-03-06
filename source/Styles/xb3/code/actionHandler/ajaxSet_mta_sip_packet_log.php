<?php

exec("/fss/gw/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_CableModem.MTALog. > /var/log_mta.txt");

$Log = array();

if (file_exists("/var/log_mta.txt"))
{
	$raw = file("/var/log_mta.txt");
	$len = count($raw);
	$pos = 50;		//global file pointer where to read the value in a line
	for ($i=0; $i<$len; $i++) 
	{
		if (strstr($raw[$i], "Time")) 
		{
			$time  = substr($raw[$i+1],$pos);
			$Level = substr($raw[$i+7],$pos);
			$Des   = htmlentities(substr($raw[$i+9], $pos));
			
			for ($i+=10; $i<$len; $i++)
			{
				if (!strstr($raw[$i], "Time"))
				{
					$Des = $Des.'<br/>'.htmlentities($raw[$i]);
				}
				else
				{
					$i--; break;	//back to the time tag
				}
			}
			array_push($Log, array("time"=>$time, "Level"=>$Level, "Des"=>$Des));	
		}
	}
}
header("Content-Type: application/json");
echo json_encode($Log);	

?>

