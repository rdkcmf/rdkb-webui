<?php




/*********************************************************************/
/*      this file is replaced by ajaxSet_mta_sip_packet_log.php      */	
/*********************************************************************/





	exec("/fss/gw/usr/ccsp/ccsp_bus_client_tool eRT getv Device.X_CISCO_COM_MTA.DSXLog. | grep 'type:' > /var/log_dsx.txt");

	$file= fopen("/var/log_dsx.txt", "r");

	$pos = 50;		//global file pointer where to read the value in a line
	
	$Log = array();

	for($i=0; !feof($file) && $i<600; $i++)
	{
		$time 	= substr(fgets($file),$pos);
		$ID		= substr(fgets($file),$pos);	//don't need, but have to read
		$Level 	= substr(fgets($file),$pos);
		$Des 	= substr(fgets($file),$pos);

		$Log[$i] =	array("time"=>$time, "Level"=>$Level, "Des"=>$Des);
	}

	fclose($file);
	array_pop($Log);
	
	header("Content-Type: application/json");
	echo json_encode($Log);	
?>
