<?php
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="index.php";</script>';
	exit(0);
}
	$file = "/var/tmp/Wifi_Spectrum_Analyzer_Table.html";
	header("Content-type: text/html");
   	header("Content-Disposition: attachment; filename=Wifi_Spectrum_Analyzer_Table.html");
   	if(file_exists($file))
   	{
		readfile($file);
		unlink($file);
	}
?>