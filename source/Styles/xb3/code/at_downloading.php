<?php die(); ?>
<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2018 RDK Management

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
<?php
include_once __DIR__ .'/CSRF-Protector-PHP/libs/csrf/csrfprotector_rdkb.php';
//Initialise CSRFGuard library
csrfprotector_rdkb::init();
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="index.php";</script>';
	exit(0);
}
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
			echo '<meta http-equiv=REFRESH CONTENT="0.1; url=trigger_download.php">';
			echo "<h3>The current configuration has been saved as a backup file in your local machine.</h3>";
		}
	}
}
?>
