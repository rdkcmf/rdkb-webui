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
<?php include('../includes/utility.php'); ?>
<?php
function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) 
    {
        $colarr[$col] = array();
        foreach ($array as $k => $row) 
        { 
            $colarr[$col]['_'.$k] = strtolower($row[$col]); 
        }
    }
    $params = array();
    foreach ($cols as $col => $order) 
    {
    
        $params[] =&$colarr[$col];
        $order=(array)$order;
        foreach($order as $order_element)
        {
            //pass by reference, as required by php 5.3
            $params[]=&$order_element;
        }
    }
    call_user_func_array('array_multisort', $params);
    $ret = array();
    $keys = array();
    $first = true;
    $idx = 0;
    foreach ($colarr as $col => $arr) 
    {
        foreach ($arr as $k => $v) 
        {
            if ($first) 
            { 
                $keys[$k] = substr($k,1); 
            }
            $k = $keys[$k];
            
            if (!isset($ret[$k]))
            {
                $ret[$k] = $array[$k];
            }
            $ret[$k][$col] = $array[$k][$col];
	        $ret[$k]['idx'] = $idx;
	        $idx++;
        }
        $first = false;
    }
    return $ret;
}
function mac_translate($mac){
	//to change mac from 16cfe213c610 to 16:CF:E2:13:C6:10	
	$mac = strtoupper($mac);
	$mac_new = substr($mac, 0, 2).':'.substr($mac, 2, 2).':'.substr($mac, 4, 2).':'.substr($mac, 6, 2).':'.substr($mac, 8, 2).':'.substr($mac, 10, 2);
	return $mac_new;
}
function rearrange_SDTR($val){
	//add new line char after 4th','
	$variable = explode(',', $val);
	$count = 0;
	$val = '';
	foreach ($variable as $key => $value) {
		if($count == 2)	{ $val = $val.$value.',<br>'; $count = 0; }
		else { $val = $val.$value.','; $count = $count+1; };
	}
	return rtrim($val, ',');
}
function get_results()
{
	//sleep(5);
	$rootObjName    = "Device.WiFi.NeighboringWiFiDiagnostic.Result.";
	$paramNameArray = array("Device.WiFi.NeighboringWiFiDiagnostic.Result.");
	$mapping_array  = array('Radio', 'Channel', 'SSID', 'BSSID', 'SignalStrength', 'SupportedStandards', 'SecurityModeEnabled', 'SupportedDataTransferRates');
	$wifi_spec_values  = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
	if(empty($wifi_spec_values))
	{
		//get_results();
		return array("status" => "In progress");
	}
	else
	{
		$wifi_spec_values = array_msort($wifi_spec_values, array('Channel' => SORT_ASC));
		$new_array = array();
		foreach ($wifi_spec_values as $i => $spec_values) {
			$wifi_spec_values[$i]["BSSID"] = mac_translate($spec_values["BSSID"]);
			$wifi_spec_values[$i]["SupportedDataTransferRates"] = rearrange_SDTR($spec_values["SupportedDataTransferRates"]);
			array_push($new_array, $wifi_spec_values[$i]);
		}
		//var_dump($wifi_spec_values);
		return array("status"=> "success", "data"=> $new_array);
	}
}
$WiFiDiagnostic_enable = getStr("Device.WiFi.NeighboringWiFiDiagnostic.Enable");
if($WiFiDiagnostic_enable == false)
{
	setStr("Device.WiFi.NeighboringWiFiDiagnostic.Enable", true, true);
	setStr("Device.WiFi.NeighboringWiFiDiagnostic.DiagnosticsState", "Requested", true);
}
else
{
	setStr("Device.WiFi.NeighboringWiFiDiagnostic.DiagnosticsState", "Requested", true);	
}
$result = get_results();
echo json_encode($result);
?>
