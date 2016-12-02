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
function printableCharacters($input){
	//check only if range is set
	if(is_array($input)){
		$regEx = '/^[ -~]{'.$input[1].','.$input[2].'}$/';
		if(preg_match($regEx, $input[0])) return true;
		else return false;
	}
	//if range is not set then match for *
	else if (preg_match("/^[ -~]*$/", $input)) return true;
	else return false;
}
//check if the $IPAddr is a valid IP address[checks for both IPv4 & IPv6]
function validIPAddr($IPAddr){
	if(inet_pton($IPAddr) !== false) return true;
	else return false;
}
//check if the $link is a valid valid URL per the URL spec
function validLink($link){
	if (preg_match("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/",$link)) return true;
	else return false;
}
//check if the $port is a valid port number 1 - 65535
function validPort($port){
	if (preg_match("/^[1-9][0-9]{0,3}$|^[1-5][0-9]{4}$|^6[0-4][0-9]{3}$|^65[0-4][0-9]{2}$|^655[0-2][0-9]$|^6553[0-5]$/", $port)) return true;
	else return false;
}
//check if the $id is in range 1 - 256
function validId($id){
	if (preg_match("/^[1-9][0-9]{0,1}$|^1[0-9]{2}$|^2[0-4][0-9]$|^25[0-6]$/", $id)) return true;
	else return false;
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
?>