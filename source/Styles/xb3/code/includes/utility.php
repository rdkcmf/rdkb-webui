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
// enable _SESSION var
// session_start();		// already start in head.php
//wrap for PSM mode
function php_getstr($str)
{
	if ("Enabled" == $_SESSION["psmMode"])
	{
		if (strstr($str, "WiFi")){
			return "";
		}
		if (strstr($str, "MoCA")){
			return "";
		}
	}
	return getStr($str);
}
//wrap for PSM mode
function php_getinstanceids($str)
{
	if ("Enabled" == $_SESSION["psmMode"])
	{
		if (strstr($str, "WiFi")){
			return "";
		}
		if (strstr($str, "MoCA")){
			return "";
		}
	}
	return getInstanceIds($str);
}
//now you can use key to index the array
function KeyExtGet($root, $param)
{
	$raw_ret = DmExtGetStrsWithRootObj($root, $param);
	$key_ret = array();
	for ($i=1; $i<count($raw_ret); $i++)
	{
		$tmp = array_keys($param, $raw_ret[$i][0]);
		$key = $tmp[0];
		$val = $raw_ret[$i][1];
		$key_ret[$key] = $val;
	}
	return $key_ret;
}
//return a string of encryption type
function encrypt_map($mode, $method)
{
	$method = str_replace("AES+TKIP", "TKIP/AES", $method);
	switch ($mode)
	{
	case "None":
		return "Open (risky)";
	case "WEP-64":
		return "WEP 64 (risky)";
	case "WEP-128":
		return "WEP 128 (risky)";
	case "WPA-Personal":
		return "WPA-PSK (".$method.")"; 
	case "WPA2-Personal":
		return "WPA2-PSK (".$method.")"; 
	case "WPA-WPA2-Personal":
		return "WPAWPA2-PSK (".$method.")"; 
	case "WPA-Enterprise":
		return "WPA (".$method.")"; 
	case "WPA2-Enterprise":
		return "WPA2 (".$method.")"; 
	case "WPA-WPA2-Enterprise":
		return "WPAWPA2 (".$method.")"; 
	default:
		return "WPAWPA2-PSK (TKIP/AES)";
	}
}
/**
 * Discription: 
 *     This function is used to get the corresponding leaf name called by getParaValues
 *              
 * argument:
 *     $root: name of the common root object name for all paramters,
 *      e.g. $root   = "Device.Hosts.Host.";
 *
 *     $str: the returned dm parameters name	
 *      e.g. Device.Hosts.Host.{i}.Active
 *
 * return: The expected leaf name of dm parameters name
 *      e.g. Active is the returned string in above case 
 *
 * author: yaowu@cisco.com
 */
function getLeafName($str, $root){
	if (!empty($str)){	
		$str = str_replace($root, "", $str);
		$pos = strpos($str, '.');
		if ($pos === false) {
			/* if no further {i}. can be found, the leaf is just the remaining string */
			return $str;
		}
		return substr($str, $pos+1);
	}
} 
/**
 * Extract the id ({i}) portion in a DM path given the root obj path.
 * e.g. $str = Device.Hosts.Host.{i}.Active, $root = Device.Hosts.Host.,
 * then return id = {i}
 *
 */
function getObjIdInPath($str, $root) {
	if (!empty($str)) {
		$str = str_replace($root, "", $str);
		$pos = strpos($str, '.');
		if ($pos === false) {
			return NULL;
		}
		return substr($str, 0, $pos);
	}
}
/**
 * Discription: 
 *     This function is a wrapper for Dinghua's group get api call, enabling caller
 *     access returned parameter values via key name of PHP array
 *              
 * argument:
 *     $root: name of the common root object name for all paramters,
 *      e.g. $root   = "Device.Hosts.Host.";
 *
 *     $paramArray: usually = array($root);	
 *      e.g. $paramNameArray = array("Device.Hosts.Host.");
 *
 *     $mapping_array: the specific parameters you want to obtain,
 *      e.g. $mapping_array  = array("IPAddress", "HostName", "Active");
 *
 *     $includeId: optional, if true to specify the object id via '__id' attribute.
 *
 * return: The expected multiple-dimension PHP array
 *      e.g.  $key_ret[$i]['IPAddress'], $key_ret[$i]['HostName'], $key_ret[$i]['Active']
 *
 * author: yaowu@cisco.com
 */
function getParaValues($root, $paramArray, $mapping_array, $includeId=false) {
	$key_ret = array();
	$i = 0;
	$cId = NULL;
	$pId = NULL;
	$mapping_array_size = count($mapping_array);
	$raw_ret = DmExtGetStrsWithRootObj($root, $paramArray);
	if(isset($raw_ret)){
		foreach ($raw_ret as $key => $value) {
			$leafValueName = getLeafName($value[0], $root);  //value[0] is like Device.Hosts.Host.MACAddress
			if(in_array($leafValueName, $mapping_array)){
				$pId = getObjIdInPath($value[0], $root);
				if (!isset($cId)) $cId = $pId;
				if ($cId !== $pId) {
					$cId = $pId;
					$i++;
				}
				$key_ret[$i][$leafValueName] = $value[1];
				if ($includeId && !isset($key_ret[$i]['__id'])) {
					$key_ret[$i]['__id'] = $pId;
				}
			}
		}
	}
	return $key_ret;
}
//show a PSM mode notification webpage, (then exit current script)
function init_psmMode($title, $navElementId)
{
	$msg = "";
	if ("Enabled"==$_SESSION["psmMode"])
	{
		$msg .= '<script type="text/javascript">';
		$msg .= '	$(document).ready(function(){gateway.page.init("'.$title.'", "'.$navElementId.'");});';
		$msg .= '</script>';
		$msg .= '<div id="content" class="main_content">';
		$msg .= '	<h1>'.$title.'</h1>';
		$msg .= '	<div class="module data">';
		$msg .= '		<h2>'._("No information available").'</h2><br/>';
		$msg .= '		<strong>'._("Gateway operating in battery mode.").'</strong>';
		$msg .= '	</div>';
		$msg .= '</div>';
		$msg .= file_get_contents("./includes/footer.php");
	}
	return $msg;
}
//bit-and limited to 32, I have to write this
function php_str_and($a, $b)
{
	$c = "";
	for ($i=0; $i<16; $i++)
	{
		$c = $c.dechex((hexdec(substr($a,$i,1)) & hexdec(substr($b,$i,1))));
	}
	return $c;
}
//delete front-tail blank of element, and delete empty element
function array_trim($arr){
	$ret = array();
	foreach($arr as $v){
		$v = trim($v);
		if ("" != $v){
			array_push($ret, $v);
		}
	}
	return $ret;
}
// get array of default value from DB file, do not use simpleXML method!
function getDefault($xmlFile, $arrName)
{
	$key_ret = array();
	if (file_exists($xmlFile))
	{
		$arrLine = file($xmlFile);
		foreach($arrLine as $line)
		{
			foreach($arrName as $name)
			{
				if (strpos($line, $name)) //search name can not be the start of the line
				{
					$tmp = array_keys($arrName, $name);
					$key = $tmp[0];
					$key_ret[$key] = trim(strip_tags($line));
					break;
				}
			}
		}
	}
	return $key_ret;
}
//div_sub($n, $m) is for division by subtraction
function div_sub($n, $m)
{
	if (!is_numeric($n) || !is_numeric($m) || (0==$m)){
		return array(0, 0);
	}
	for($i=0; $n >= $m; $i++){
		$n = $n - $m;
	}
	return array($i, $n);
}
//hm_to_sec($time) converts H:M to sec
function hm_to_sec($time){
	$newTime = explode(":",$time);
	$timeSec = $newTime[0]*60*60 + $newTime[1]*60;
	return $timeSec;
}
//sec_to_hm($time) converts sec to H:M
function sec_to_hm($time){
	$tmp = div_sub($time, 60*60);
	$hor = $tmp[0];
	$tmp = div_sub($tmp[1], 60);
	$min = $tmp[0];
	$min = ($min < 10)?('0'.$min):$min;
	$hor = ($hor>=24)?($hor - 24):$hor;
	$hor = ($hor < 10)?('0'.$hor):$hor;
	return "$hor:$min";
}
//$blockedDays are the days picked by user
//$shift true is to shift by +1Day
//$shift false is to shift by -1Day
function shift_blockedDays($blockedDays, $shift){
	$blockedDays = explode(',', $blockedDays);
	$week=array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
	if($shift){
		for($i=0;$i<sizeof($blockedDays);$i++){
			for($j=0;$j<sizeof($week);$j++){
				if($week[$j]==$blockedDays[$i]){
					$index=($j+1)%sizeof($week);
					$blockedDays[$i]=$week[$index];
					break;
				}
			}
		}
	}
	else {
		for($i=0;$i<sizeof($blockedDays);$i++){
			for($j=0;$j<sizeof($week);$j++){
				if($week[$j]==$blockedDays[$i]){
					if($j==0) $j=sizeof($week);
					$index=($j-1)%sizeof($week);
					$blockedDays[$i]=$week[$index];
					break;
				}
			}
		}
	}
	if($blockedDays[0]=='Sun') {
		array_shift($blockedDays);
		array_push($blockedDays,'Sun');
	}
	if($blockedDays[sizeof($blockedDays)-1]=='Mon') {
		array_pop($blockedDays);
		array_unshift($blockedDays,'Mon');
	}
	return implode(',', $blockedDays);
}
function group_2D_array($data, $fields) {
	if(empty($fields) || !is_array($fields)) {
		return $data;
	}
	$tempArray = array();
	$field = array_shift($fields);
	foreach($data as $val) {
		$tempArray[$val[$field]][] = $val;
	}
	foreach(array_keys($tempArray) as $key) {
		$tempArray[$key] = group_2D_array($tempArray[$key], $fields);
	}
	return $tempArray;
}
$UTC_Enable = getStr("Device.Time.UTC_Enable");
$UTC_local_Time_conversion = ($UTC_Enable === 'true') ? true : false ;
$timeOffset = getStr("Device.Time.TimeOffset");
//$timeOffset = '-25200'; //Eastern Standard Time (EST) = UTC-5
//$timeOffset = '-18000'; //Mountain Standard Time (MST) = UTC-7
//$timeOffset = '+25200'; //Indonesia Western Time = UTC+7
//local_to_UTC_Time($localTime) is for converting $localTime to $utcTime for SET
function local_to_UTC_Time($localTime, $blockedDays){
	global $timeOffset;
	if($localTime=='') return array('', '', false, false);
	$utcTime = hm_to_sec($localTime) - $timeOffset;
	$timeChangePos = ($utcTime > (24*60*60));
	$timeChangeNeg = ($utcTime < 0);
	$timeChangeEqu = ($utcTime == (24*60*60));
	$utcTime = ($timeChangePos)?($utcTime - (24*60*60)):$utcTime;
	$utcTime = ($timeChangeNeg)?($utcTime + (24*60*60)):$utcTime;
	$utcTime = ($timeChangeEqu)?($utcTime - 1):$utcTime;	
	if($timeChangePos)	$blockedDays = shift_blockedDays($blockedDays, true);
	if($timeChangeNeg)	$blockedDays = shift_blockedDays($blockedDays, false);
	return array(sec_to_hm($utcTime), $blockedDays, ($timeChangePos || $timeChangeNeg));
}
//UTC_to_local_Time($utcTime) is for converting $utcTime to $localTime for GET
function UTC_to_local_Time($utcTime, $blockedDays){
	global $timeOffset;
	if($utcTime=='') return array('', '');
	$localTime = hm_to_sec($utcTime) + $timeOffset;
	$timeChangePos = ($localTime > (24*60*60));
	$timeChangeNeg = ($localTime < 0);
	$localTime = ($timeChangePos)?($localTime - (24*60*60)):$localTime;
	$localTime = ($timeChangeNeg)?($localTime + (24*60*60)):$localTime;
	if($timeChangePos)	$blockedDays = shift_blockedDays($blockedDays, true);
	if($timeChangeNeg)	$blockedDays = shift_blockedDays($blockedDays, false);
	return array(sec_to_hm($localTime), $blockedDays);
}
function UTC_to_local_date($utcTime){
        global $timeOffset;
        $localTime = strtotime($utcTime) + $timeOffset;
        return gmdate("Y-m-d H:i:s", $localTime);
}
function UTC_to_local_date_logs($utcTime){
        global $timeOffset;
        $localTime = strtotime($utcTime) + $timeOffset;
        return gmdate("M d H:i:s Y", $localTime);
}
//
function time_in_min($time){
	$min = explode(':', $time);
	if(count($min)>1)
		return (($min[0]*60)+$min[1]);
	else
		return 0;
}
function cmp($a, $b) {
	if ($a["StartTime"]==$b["StartTime"]) return 0;
	return ($a["StartTime"]<$b["StartTime"])?-1:1;
}
function merge_days($data){
	usort($data, "cmp");
	for ($i=0; $i < sizeof($data); $i++) {
		if((isset($data[$i]['EndTime']) && time_in_min($data[$i]['EndTime'])+1) == (isset($data[$i+1]['StartTime']) && time_in_min($data[$i+1]['StartTime']) )) {
			$data[$i]['__id'] = $data[$i]['__id'].'_'.$data[$i+1]['__id'];
			$data[$i]['EndTime'] = $data[$i+1]['EndTime'];
			unset($data[$i+1]);
			$i++;
		}
	}
	return $data;
}
function days_time_conversion_get($data, $type){
	$returnData = array();
	foreach ($data as $key => &$value) {
		$startArr	= UTC_to_local_Time($value['StartTime'], $value['BlockDays']);
		$endArr		= UTC_to_local_Time($value['EndTime'], $value['BlockDays']);
		$value['StartTime']	= $startArr[0];
		$value['EndTime']	= $endArr[0];
		$value['BlockDays']	= $endArr[1];
	}
	unset($value);
	if(is_array($type)){
		//for "Managed Devices"
		$tempArray = group_2D_array($data, array($type[0], $type[1], 'BlockDays'));
		foreach ($tempArray as $key => $value) {
			foreach ($value as $key2 => $value2) {
				foreach ($value2 as $k => &$val) {
					$val = merge_days($val);
					foreach ($val as $v) {
						$returnData[] = $v;
					}
				}
				unset($val);
			}
		}
	}
	else {
		//for "Blocked Sites​", "Blocked Keywords​", "Managed Services​"
		$tempArray = group_2D_array($data, array($type, 'BlockDays'));
		foreach ($tempArray as $key => $value) {
			foreach ($value as $k => &$val) {
				$val = merge_days($val);
				foreach ($val as $v) {
					$returnData[] = $v;
				}
			}
			unset($val);
		}
	}
	return $returnData;
}
//for $startTime, $endTime, $blockedDays parameters do local_to_UTC_Time($localTime)
//$timeOffset can move this $blockedDays to Tomorrow or Yesterday
function days_time_conversion_set($startTime, $endTime, $blockedDays){
	$day_change = false;
	$startData 	= local_to_UTC_Time($startTime, 	$blockedDays);
	$endData 	= local_to_UTC_Time($endTime, 	$blockedDays);
	if(($startData[2] && $endData[2]) || (!$startData[2] && !$endData[2])){
		//start and end time in same day
		return array($startData[0], $endData[0], $startData[1], $day_change);
	}
	else {
		$day_change = true;
		return array($startData[0], '23:59', $startData[1], $day_change, '00:00', $endData[0], $endData[1]);
	}
}
//Check for time and day conflicts before ADD/EDIT of table rules
function time_date_conflict($TD1, $TD2) {
	$ret = false;
	$days1 = explode(",", $TD1[2]);
	$days2 = explode(",", $TD2[2]);
	foreach ($days1 as &$value) {
		if (in_array($value, $days2)) {
			//deMorgan's law - to find if ranges are overlapping
			//(StartA <= EndB)  and  (EndA >= StartB)
			if((strtotime($TD1[0]) < strtotime($TD2[1])) and (strtotime($TD1[1]) > strtotime($TD2[0]))){
				$ret = true;
				break;
			}
		}
	}
	return $ret;
}
// resolve IPV6 global address
function resolve_IPV6_global_address($address1, $address3){
	// IPV6 address can be "global address", "EMPTY" [from STACK] or NOT-SET [from STACK]
	$IPV6_Addresses = '';
	if(isset($address1) || isset($address3) ){
		if(trim($address1) != '') $IPV6_Addresses = $address1;
		else if (trim($address3) != '') $IPV6_Addresses = $address3;
	}
	return $IPV6_Addresses;
}
/**
 * Discription: 
 *     This function determines what the WAN type is
 *              
 * return: On a DOCSIS device returns "DOCSIS", on a EPON device retuns "EPON"
 */
function get_wan_type()
{
    static $type = null;
    
    if ($type != null) {
        return $type;
    }
    
    // DSL Should be based on interface rather than model name.
    $modelName = getStr("Device.DeviceInfo.ModelName");
    if (getStr("Device.DPoE.Mac_address")) {
        $type = "EPON";
    } elseif (strpos($modelName, "SR20") !== false) {
        $type = "DSL";
    } else {
        $type = "DOCSIS";
    }

    return $type;
}

/**
 * Description:
 *     This function will add session cookies for translation strings required from the javascript code. This is
 *     required since the translation code is processed server side in php requests whereas the javascript code is 
 *     processed client side int the user's browser. 
 */
function fetch_js_translation_strings()
{
    // Well set these up as objects based on which js code requires which translation.
    // gateway.js sets the "more"/"less" info expansion labels
    if (isset($_COOKIE['more_less_label'])) 
    {
        setcookie ('more_less_label', '', 1);
    }
    
    // set more_less_label cookie
    {
        $more_less_label = array (
            "more" => _('more'),
            "less" => _('less')
        );
        setcookie ('more_less_label', json_encode($more_less_label));
    }
    
    // jquery.radioswitch.js sets the "Enable"/"Disable" button labels
    if (isset($_COOKIE['enable_disable_label'])) 
    {
        setcookie ('enable_disable_label', '', 1);
    }
    
    // if (!isset($_COOKIE['enable_disable_label'])) 
    {
        $enable_disable_label = array(
            "enable" => _('Enable'),
            "disable" => _('Disable')
        );
        setcookie ('enable_disable_label', json_encode($enable_disable_label));
    }
    
    // jquery.radioswitch.js sets the userbar tool tips labels
    if (isset($_COOKIE['tip_labels'])) 
    {
        setcookie ('tip_labels', '', 1);
    }
    
    //if (!isset($_COOKIE['tip_labels'])) 
    {
        $tip_labels = array(
            "none" => _("No Tips!"), 
            "devices" => _('Status: Connected-%s devices connected'), 
            "no_devices" => _('Status: Unconnected-no devices'), 
            "firewall" => _('Firewall is set to %s')
        );
        setcookie ('tip_labels', json_encode($tip_labels));
    }
}

// call to set translation strings for javascript.
fetch_js_translation_strings();
?>
