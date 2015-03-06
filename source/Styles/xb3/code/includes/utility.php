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
	//dump($key_ret);
	return $key_ret;
}

/**
 * Discription: 
 *     This function is used to display dump info in a beautiful manner
 */
function dump($vars, $label = '', $return = false)
{
    if (ini_get('html_errors')) {
        $content = "<pre>\n";
        if ($label != '') {
            $content .= "<strong>{$label} :</strong>\n";
        }
        $content .= htmlspecialchars(print_r($vars, true));
        $content .= "\n</pre>\n";
    } else {
        $content = $label . " :\n" . print_r($vars, true);
    }
    if ($return) { return $content; }
        echo $content;
    return null;
}

//show a PSM mode notification webpage, (then exit current script)
function init_psmMode($title, $navElementId)
{
	$msg = "";
	if ("Enabled"==$_SESSION["psmMode"])
	{
		$msg .= '<script type="text/javascript">';
		$msg .= '	$(document).ready(function(){comcast.page.init("'.$title.'", "'.$navElementId.'");});';
		$msg .= '</script>';
		$msg .= '<div id="content" class="main_content">';
		$msg .= '	<h1>'.$title.'</h1>';
		$msg .= '	<div class="module data">';
		$msg .= '		<h2>No information available</h2><br/>';
		$msg .= '		<strong>Gateway operating in battery mode.</strong>';
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

?>