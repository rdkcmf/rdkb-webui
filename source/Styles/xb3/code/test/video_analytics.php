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