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
/*
	utility functions for files in "actionHandler" folder
*/
//input can only be printable characters, the printable ASCII characters start at the space and end at the tilde
//check if $input[0] is in range $input[1] - $input[2]
?>
<?php
include_once __DIR__ .'/../CSRF-Protector-PHP/libs/csrf/csrfprotector_rdkb.php';
//Initialise CSRFGuard library
csrfprotector_rdkb::init();
?>
<?php
function printableCharacters($input){
	//check only if range is set
	if(is_array($input)){
		$regEx = '/^[ -~]{'.$input[1].','.$input[2].'}$/';
		return (preg_match($regEx, $input[0])==1);
	}
	//if range is not set then match for *
	else return (preg_match("/^[ -~]*$/", $input)==1);
}
//check for valid Input
function noSpace($input){
	return (preg_match('/\S/',$input)==1);
}
//check if the $IPAddr is a valid IP address[checks for both IPv4 & IPv6]
function validIPAddr($IPAddr){
	if(inet_pton($IPAddr) !== false) return true;
	else return false;
}
//check if the $link is a valid valid URL per the URL spec
function validLink($link){
	return (preg_match("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/",$link)==1);
}
//check if the $mac is a valid MAC Address
//Note that the first byte of the source address is always even (since the least significant bit, or first bit on the wire indicates that the address is a group address ).
function validMAC($mac){
	//return (preg_match('/^([a-f0-9]{2}:){5}[a-f0-9]{2}$/i', $mac)==1);
	if(preg_match('/^([a-f0-9]{2}:){5}[a-f0-9]{2}$/i', $mac)!=1 || "00:00:00:00:00:00"==$mac || hexdec(substr($mac, 0, 2))%2 != 0) return false;
	else return true;
}
//check if the $port is a valid port number 1 - 65535
function validPort($port){
	return (preg_match("/^[1-9][0-9]{0,3}$|^[1-5][0-9]{4}$|^6[0-4][0-9]{3}$|^65[0-4][0-9]{2}$|^655[0-2][0-9]$|^6553[0-5]$/", $port)==1);
}
//check if the $id is in range 1 - 256
function validId($id){
	return (preg_match("/^[1-9][0-9]{0,1}$|^1[0-9]{2}$|^2[0-4][0-9]$|^25[0-6]$/", $id)==1);
}
//for Parental Control $id can be 10_12 or 10
function validId_PC($id){
	$idRegEx = "/^[1-9][0-9]{0,1}$|^1[0-9]{2}$|^2[0-4][0-9]$|^25[0-6]$/";
	$ids = explode('_', $id);
	if (array_key_exists('1', $ids)) return (preg_match($idRegEx, $ids[0])==1 && preg_match($idRegEx, $ids[1])==1);
	else return (preg_match($idRegEx, $ids[0])==1);
}
//check if the parameter is in array
function isValInArray($val, $valArray){
	if(in_array($val, $valArray, true)) return true;
	else return false;
}
//check if the $val is in range $min - $max
function isValInRange($val, $min, $max){
	if($val >= $min && $val <= $max) return true;
	else return false;
}
//check if the $url is valid
function validURL($url){
	$urlRegEx01 = "/^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(([a-z]|\d|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])*([a-z]|\d|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])))\.)+(([a-z]|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(([a-z]|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])*([a-z]|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\x{E000}-\x{F8FF}]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\x{00A0}-\x{D7FF}\x{F900}-\x{FDCF}\x{FDF0}-\x{FFEF}])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/iu";
    $urlRegEx02 = "/^(https?|s?ftp):\/\/\[((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))]?(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$/iu";
	if (preg_match($urlRegEx01, $url)==1 || preg_match($urlRegEx02, $url)==1) return true;
	else return false;
}
//check if the $startTime is less than $endTime and Time is in range 00:00-23:59
function validTime($startTime, $endTime){
	//range 00:00-23:59
	$start_hm 	= explode(':', $startTime);
	$end_hm 	= explode(':', $endTime);
	//hours
	$hourRegEX 		= '/^(0)?\d$|^([1]\d)$|^(2[0-3])$/';
	//start min can only be	00 15 30 45
	$startMinRegEX	= '/^00$|^15$|^30$|^45$/';
	//end min can only be 00 15 30 45 59
	$endMinRegEX	= '/^00$|^15$|^30$|^45$|^59$/';
	$start_min 	= ($start_hm[0]*60)+$start_hm[1];
	$end_min 	= ($end_hm[0]*60)+$end_hm[1];
	if(preg_match($hourRegEX, $start_hm[0])==1 && preg_match($startMinRegEX, $start_hm[1])==1 && preg_match($hourRegEX, $end_hm[0])==1 && preg_match($endMinRegEX, $end_hm[1])==1 && ($start_min < $end_min)) return true;
	else  return false;
}
//check if the $day is of array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun") only
function validDays($day){
	$validation = true;
	$day = explode(",",$day);
	$allDays = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
	foreach ($day as $value) {
		if (!in_array($value, $allDays)){
			$validation = false;
			break;
		}
	}
	return $validation;
}
//check if $ssid_name is as per specs
function valid_ssid_name($ssid_name){
	$ssid_name = strtolower($ssid_name);
	//1 to 32 ASCII characters
	$ssid_name_check = (preg_match('/^[ -~]{1,32}$/', $ssid_name) == 1);
	//SSID name cannot contain only spaces
	$not_only_spaces_check = (preg_match('/^\s+$/', $ssid_name) != 1);
	//SSID Starting with "XHS-" and "XH-" are reserved
	$not_hhs_check  = (preg_match('/^xhs-|^xh-/', $ssid_name) != 1);
	//SSID containing "optimumwifi", "TWCWiFi", "cablewifi" and "xfinitywifi" are reserved
	$ssid_name = preg_replace('/[\.,-\/#@!$%\^&\*;:{}=+?\-_`~()"\'\\|<>\[\]\s]/', '', $ssid_name);
	$not_hhs2_check = !((strpos($ssid_name, 'cablewifi') !== false) || (strpos($ssid_name, 'twcwifi') !== false) || (strpos($ssid_name, 'optimumwifi') !== false) || (strpos($ssid_name, 'xfinitywifi') !== false) || (strpos($ssid_name, 'xfinity') !== false) || (strpos($ssid_name, 'coxwifi') !== false) || (strpos($ssid_name, 'spectrumwifi') !== false)  || (strpos($ssid_name, 'shawopen') !== false)  || (strpos($ssid_name, 'shawpasspoint') !== false) || (strpos($ssid_name, 'shawguest') !== false) || (strpos($ssid_name, 'shawmobilehotspot') !== false) || (strpos($ssid_name, 'shawgo') !== false) );
	return $ssid_name_check && $not_only_spaces_check && $not_hhs_check && $not_hhs2_check;
}
//check if $name has any Invalid characters
//Invalid characters are Less than (<), Greater than (>), Ampersand (&), Double quote ("), Single quote ('), Pipe (|).
function is_allowed_string($name){
	return (preg_match('/[<>&"\'|]/', $name)!=1);
}
function remove_special_char($name){
        return (preg_match('/[!@#$%^&*(),?":{}|<>]/', $name)!=1);
}

?>
<?php
/*
 * Start the session
 */
session_start();
/*
 * Set the Locale for the Web UI based on the LANG setting or current linux locale
 */
$locale = getenv("LANG");
if(isset($locale)) {
    if(!isset($_SESSION['language']) || setlocale(LC_MESSAGES, 0) != $locale){
        //putenv("LANG=" . $locale);
        setlocale(LC_MESSAGES, $locale);
        setlocale(LC_TIME, $locale);
        
        $domain = "rdkb";
        bindtextdomain($domain, 'locales');
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
        $_SESSION['language'] = $locale; // set the default locale for future pages
    }
}
?>