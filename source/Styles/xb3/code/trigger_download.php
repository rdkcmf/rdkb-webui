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
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="index.php";</script>';
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