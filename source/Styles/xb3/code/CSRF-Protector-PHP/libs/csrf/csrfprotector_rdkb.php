<?php
include_once __DIR__ ."/csrfprotector.php";
class csrfprotector_rdkb extends csrfprotector 
{
    public static function logCSRFattack() {
		/*--
			// logging functionality is commented out
			$logFile = fopen(self::$config['logDirectory']
			."/" .date("m-20y") .".log", "a+");
			
			//throw exception if above fopen fails
			if (!$logFile)
				throw new logFileWriteError("OWASP CSRFProtector: Unable to write to the log file");	

			//miniature version of the log
			$log = array();
			$log['timestamp'] = time();
			$log['HOST'] = $_SERVER['HTTP_HOST'];
			$log['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
			$log['requestType'] = self::$requestType;

			if (self::$requestType === "GET")
				$log['query'] = $_GET;
			else
				$log['query'] = $_POST;

			$log['cookie'] = $_COOKIE;

			//convert log array to JSON format to be logged
			$log = json_encode($log) .PHP_EOL;

			//append log to the file
			fwrite($logFile, $log);

			//close the file handler
			fclose($logFile);
		*/
    }
};
?>