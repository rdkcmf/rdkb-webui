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
?>
<?php
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="index.php";</script>';
	exit(0);
}
function file_download($file_path, $file_name)
{
	//$file_path path to a file to output
	//$file_name filename that the browser will see
	if(!is_readable($file_path)) die(_('File not found or inaccessible!'));
	$file_size = filesize($file_path);
	$file_name = rawurldecode($file_name);
	@ob_end_clean(); //Clean (erase) the output buffer and turn off output buffering
	// required for IE, otherwise Content-Disposition may be ignored
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');
	//to make the download non-cacheable
	header("Cache-control: private");
	header('Pragma: private');
	header('Expires: 0');
	header('Content-Type: text/plain');
	header('Content-Disposition: attachment; filename="'.$file_name.'"');
	header("Content-Transfer-Encoding: binary");
	header('Accept-Ranges: bytes');
	// multipart-download and download resuming support
	if(isset($_SERVER['HTTP_RANGE']))
	{
		header("HTTP/1.1 206 Partial Content");
		$scope = explode("=",$_SERVER['HTTP_RANGE'],2);
		$scope = $scope[1];
		$scope = explode(",",$scope,2);
		$scope = $scope[0];
		$scope_array = explode("-", $scope);
		$scope = $scope_array[0];
		$scope_end = $scope_array[1];
		$scope=intval($scope);
		if(!$scope_end) $scope_end=$file_size-1;
		else $scope_end=intval($scope_end);
		$new_length = $scope_end-$scope+1;
		header("Content-Length: $new_length");
		header("Content-Range: bytes $scope-$scope_end/$file_size");
	} else {
		header("Content-Length: ".$file_size);
		$new_length=$file_size;
	}
	//code to output the file
	$chunksize = 1*(1024*1024);
	$bytesSent = 0;
	if ($file_path = fopen($file_path, 'r'))
	{
		if(isset($_SERVER['HTTP_RANGE'])) fseek($file_path, $scope);
		while(!feof($file_path) && (!connection_aborted()) && ($bytesSent<$new_length))
		{
			$buffer = fread($file_path, $chunksize);
			print($buffer); //echo($buffer); // is also possible
			flush();
			$bytesSent += strlen($buffer);
		}
		fclose($file_path);
	} else die(_('Error - can not open file.'));
	die();
}
set_time_limit(0);
$log=$_POST["log_type"]."_".$_POST["time_frame"];
$log_type_array = array("system", "event", "firewall");
$time_frame_array = array(_("Today"), _("Yesterday"), _("Last week"), _("Last month"), _("Last 90 days"));
if (!in_array($_POST["log_type"], $log_type_array))	die(_('Not allowed!'));
if (!in_array($_POST["time_frame"], $time_frame_array))	die(_('Not allowed!'));
$file_path="/tmp/troubleshooting_logs_{$log}.txt";
$file_name="troubleshooting_logs_{$log}.txt";
file_download($file_path, $file_name);
//file_download('troubleshooting_logs_sample.txt','troubleshooting_logs_sample.txt');
?>
