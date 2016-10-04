<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:
 Copyright 2016 RDK Management
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
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="../index.php";</script>';
	exit(0);
}
$myfile = fopen("/var/tmp/Wifi_Spectrum_Analyzer_Table.html", "w");
fwrite($myfile, "<style>table th tr {}</style>");
fwrite($myfile, "<style>");
fwrite($myfile, "h2 { background: #39baf1; color: #fff; padding: 10px; font-size: 1.1em; font-weight: bold; margin-bottom: 0; }");
fwrite($myfile, "table { border-collapse: collapse; clear: both; width: 100%; background-color: #ededed; }");
fwrite($myfile, "th { background: #39baf1; color: #fff; }");
fwrite($myfile, "td { border: 1px solid white; }");
fwrite($myfile, "</style>");
fwrite($myfile, $_POST['configInfo']);
fclose($myfile);
echo "success";
?>
