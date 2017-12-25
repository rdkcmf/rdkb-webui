<?php
include_once __DIR__ .'/CSRF-Protector-PHP/libs/csrf/csrfprotector_rdkb.php';
//Initialise CSRFGuard library
csrfprotector_rdkb::init();
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="index.php";</script>';
	exit(0);
}
	$filename="backup.cfg";
	$filesize=filesize("/var/tmp/".$filename);
	header ("Content-Type: application/download"); 
	//header ("Content-Disposition: attachment; filename=backup_".date("YmdHis").".cfg");
	header ("Content-Disposition: attachment; filename=backup_latest.cfg"); 
	header("Content-Length: ".$filesize); 
	$fp=fopen("/var/tmp/".$filename,"r");
	$buffersize=1024*1024;
	$curpos=0;
	while(!feof($fp) && $filesize-$curpos>$buffersize){
		$buffer=fread($fp,$buffersize);
		echo $buffer;
		$curpos+=$buffersize;
	}
	$buffer=fread($fp,$filesize-$curpos);
	echo $buffer;
	fclose($fp);
?>