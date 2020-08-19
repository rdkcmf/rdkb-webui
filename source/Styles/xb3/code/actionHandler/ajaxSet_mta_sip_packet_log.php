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

if (!isset($_SESSION["loginuser"]) || $_SESSION['loginuser'] != 'mso') {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
exec("/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_MTA.MTALog. > /tmp/log_mta.txt");
$Log = array();
if (file_exists("/tmp/log_mta.txt"))
{
	$raw = file("/tmp/log_mta.txt");
	$len = count($raw)-1;
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
					$Des = $Des.'-'.htmlentities($raw[$i]);
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
echo htmlspecialchars(json_encode($Log), ENT_NOQUOTES, 'UTF-8');
?>
