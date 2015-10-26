<?php
exec('confPhp status',$output,$return_status);
switch ($return_status)
{
case 1:
	echo "Last save or restore has not finished!";
	break;
case 2:
	echo "Need to reboot after last restore!";
	break;
default:
	$filename="backup.cfg";
	exec('confPhp get /var/tmp/'.$filename,$output,$return_save);
	if ($return_save==-1) echo "Error, get current configuration failure! Please try again";
	else {
		do {
			sleep(1);
			exec('confPhp status',$output,$return_var);
		} while ($return_var==1);
		if ($return_var==-1) echo "<h3>Error, get save status failure! </h3>";
		else if ($return_var==3) echo "<h3>Error, get current configuration failure! </h3>";
		else {				//no case 2 when getting
		
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
		}
	}
}
?>
