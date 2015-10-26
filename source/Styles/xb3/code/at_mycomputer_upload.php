<?php

ini_set('upload_tmp_dir','/var/tmp/');
$target = "/var/tmp/";
$target = $target.basename($_FILES['file']['name']);

if($_FILES["file"]["error"]>0){
	echo "Return Code: ".$_FILES["file"]["error"];
	exit;
} else {

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
		if(move_uploaded_file($_FILES['file']['tmp_name'], $target)){
			exec('confPhp restore '.$target,$output,$return_restore);
			if ($return_restore==-1) echo "Error when to restore configuraion!";
			else {
				sleep(1);
				do {
					sleep(1);
					exec('confPhp status',$output,$return_var);
				} while ($return_var==1);
			}
		}
		else { echo "Error when to restore configuraion!"; }
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- $Id: header.php 3167 2010-03-03 18:11:27Z slemoine $ -->

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>XFINITY</title>
</head>
<body>
    <!--Main Container - Centers Everything-->
	<div id="container">
		<div id="main-content">
		<?php
		echo "<h3>target $target</h3>";
		switch ($return_var) {
		case -1:
			echo "<h3>Error, get restore status failure</h3>";
			break;
		case 2:
			echo "<h3>Need Reboot to restore the saved configuration.</h3>";
			setStr("Device.X_CISCO_COM_DeviceControl.RebootDevice","Router,Wifi,VoIP,Dect,MoCA",true);
			break;
		case 3:
			echo "<h3>Error, restore configuration failure!</h3>";
			break;
		default:
			echo "<h3>Restore configuration Failure! Please try later. </h3>";
			break;
		}
		?>
		</div>
	</div>
</body>
</html>