<?php
function file_download($file_path, $file_name)
{
	//$file_path path to a file to output
	//$file_name filename that the browser will see

	if(!is_readable($file_path)) die('File not found or inaccessible!');

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
	} else die('Error - can not open file.');

	die();
}

set_time_limit(0);
$log=$_POST["log_type"]."_".$_POST["time_frame"];
$file_path="/var/tmp/troubleshooting_logs_".$log.".txt";
$file_name="troubleshooting_logs_".$log.".txt";

file_download($file_path, $file_name);
//file_download('troubleshooting_logs_sample.txt','troubleshooting_logs_sample.txt');
?>
