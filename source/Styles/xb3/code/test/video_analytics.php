<?php
	//to download Test File for IP Video Analytics
	$filename="OddEvenPattern.test";
	$filesize=filesize("../../".$filename);
	header ("Content-Type: application/download"); 
	//header ("Content-Disposition: attachment; filename=backup_".date("YmdHis").".cfg");
	header ("Content-Disposition: attachment; filename=OddEvenPattern.test"); 
	header("Content-Length: ".$filesize); 
	$fp=fopen("../../".$filename,"r");
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