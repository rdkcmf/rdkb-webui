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
<?php include('../includes/actionHandlerUtility.php') ?>
<?php

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
$blockedSiteInfo = json_decode($_POST['BlockInfo'], true);
$objPrefix = "Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.";
$rootObjName = $objPrefix;
$exist = false;
$block=$blockedSiteInfo['alwaysBlock'];
$startTime=isset($blockedSiteInfo['StartTime']) ? $blockedSiteInfo['StartTime'] : "";
$endTime=isset($blockedSiteInfo['EndTime']) ? $blockedSiteInfo['EndTime'] : "" ;
$blockDays=isset($blockedSiteInfo['blockedDays']) ? $blockedSiteInfo['blockedDays'] : "" ;
$result = "";
if( array_key_exists('URL', $blockedSiteInfo) ) {
	//this is to set blocked URL
	$validation = true;
	if($validation) $validation = validURL($blockedSiteInfo['URL']);
	if($validation) $validation = is_allowed_string($blockedSiteInfo['URL']);
	if($validation) $validation = isValInArray($blockedSiteInfo['alwaysBlock'], array('true', 'false'));
	if($validation && $blockedSiteInfo['alwaysBlock'] == 'false'){
		if($validation) $validation = validTime($blockedSiteInfo['StartTime'], $blockedSiteInfo['EndTime']);
		if($validation) $validation = validDays($blockedSiteInfo['blockedDays']);
	}
	$result = ($validation)?'':_('Invalid Inputs!');
	if ($result == ""){
	   	//firstly, check whether URL exist or not
		$url = $blockedSiteInfo['URL'];
		$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.";
		$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.");
		$mapping_array  = array("Site", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");
		$managedSitesValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
		if($UTC_local_Time_conversion) $managedSitesValues = days_time_conversion_get($managedSitesValues, 'Site');
    	foreach ($managedSitesValues as $key => $value) {
			$always_Block = $value["AlwaysBlock"];
			$start_Time = $value["StartTime"];
			$end_Time = $value["EndTime"];
			$block_Days = $value["BlockDays"];
			//Check for time and day conflicts
			$TD1=array($startTime, $endTime, $blockDays);
			$TD2=array($start_Time, $end_Time, $block_Days);
			if ((strcasecmp($url,$value["Site"])==0) && ((($always_Block == "true") || ($block == "true") || time_date_conflict($TD1, $TD2)))){
				$result .= _("Conflict with other blocked site rule. Please check your input!");
				break;
			}
		}
	}
	if ($result == ""){
		addTblObj("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.");
		$idArr = explode(",", getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite."));
		$index = array_pop($idArr);
		if ($blockedSiteInfo['alwaysBlock'] == 'true'){
			$paramArray = 
			array (
				array($objPrefix.$index.".Site", "string", $blockedSiteInfo['URL']),
				array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
				array($objPrefix.$index.".BlockMethod", "string", "URL"),
			);
			$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
			if (!$retStatus){
				$result=_("Success!");
			}	
			else {
				$result = _('Failed to add');
			}
			/*setStr($objPrefix.$index.".Site", $blockedSiteInfo['URL'], false);
			setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
			setStr($objPrefix.$index.".BlockMethod", "URL", true);*/
		}
		else {
			if ($UTC_local_Time_conversion) $timeData = days_time_conversion_set($blockedSiteInfo['StartTime'], $blockedSiteInfo['EndTime'], $blockedSiteInfo['blockedDays']);
			else $timeData = array($blockedSiteInfo['StartTime'], $blockedSiteInfo['EndTime'], $blockedSiteInfo['blockedDays'], false);
			$paramArray = 
				array (
					array($objPrefix.$index.".Site", "string", $blockedSiteInfo['URL']),
					array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
					array($objPrefix.$index.".BlockMethod", "string", "URL"),
					array($objPrefix.$index.".StartTime", "string", $timeData[0]),
					array($objPrefix.$index.".EndTime", "string", $timeData[1]),
					array($objPrefix.$index.".BlockDays", "string", $timeData[2]),
				);
			$retStatus1 = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);
			if($timeData[3]){
				addTblObj($rootObjName);
				$IDs=explode(",",getInstanceIDs($rootObjName));
				$index=$IDs[count($IDs)-1];
				$paramArray = 
					array (
						array($objPrefix.$index.".Site", "string", $blockedSiteInfo['URL']),
						array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
						array($objPrefix.$index.".BlockMethod", "string", "URL"),
						array($objPrefix.$index.".StartTime", "string", $timeData[4]),
						array($objPrefix.$index.".EndTime", "string", $timeData[5]),
						array($objPrefix.$index.".BlockDays", "string", $timeData[6]),
					);
				$retStatus2 = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);
			}
			if ((!$retStatus1 && !$timeData[3]) || (!$retStatus1 && $timeData[3] && !$retStatus2)){
				$result=_("Success!");
			}
			else {
				$result = _('Failed to add');
			}
/*
			setStr($objPrefix.$index.".Site", $blockedSiteInfo['URL'], false);
			setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
			setStr($objPrefix.$index.".BlockMethod", "URL", false);
			setStr($objPrefix.$index.".StartTime", $blockedSiteInfo['StartTime'], false);
			setStr($objPrefix.$index.".EndTime", $blockedSiteInfo['EndTime'], false);
			setStr($objPrefix.$index.".BlockDays", $blockedSiteInfo['blockedDays'], true);
*/		
		}
	}
}
else{
	//this is to set blocked Keyword
	$validation = true;
	if($validation) $validation = printableCharacters($blockedSiteInfo['Keyword']);
	if($validation) $validation = is_allowed_string($blockedSiteInfo['Keyword']);
	if($validation) $validation = isValInArray($blockedSiteInfo['alwaysBlock'], array('true', 'false'));
	if($validation && $blockedSiteInfo['alwaysBlock'] == 'false'){
		if($validation) $validation = validTime($blockedSiteInfo['StartTime'], $blockedSiteInfo['EndTime']);
		if($validation) $validation = validDays($blockedSiteInfo['blockedDays']);
	}
	$result = ($validation)?'':_('Invalid Inputs!');
	$keyword = $blockedSiteInfo['Keyword'];
		$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.";
		$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.");
		$mapping_array  = array("Site", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");
		$managedSitesValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
		if($UTC_local_Time_conversion) $managedSitesValues = days_time_conversion_get($managedSitesValues, 'Site');
    	foreach ($managedSitesValues as $key => $value) {
    	$always_Block = $value["AlwaysBlock"];
		$start_Time = $value["StartTime"];
		$end_Time = $value["EndTime"];
		$block_Days = $value["BlockDays"];
		//Check for time and day conflicts
		$TD1=array($startTime, $endTime, $blockDays);
		$TD2=array($start_Time, $end_Time, $block_Days);
		if ((strcasecmp($keyword,$value["Site"]) == 0) && ((($always_Block == "true") || ($block == "true") || time_date_conflict($TD1, $TD2)))){
			$result .= _("Conflict with other blocked Keyword rule. Please check your input!");
			break;
		}
	}
	if ($result == ""){
		addTblObj("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.");
		$idArr = explode(",", getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite."));
		$index = array_pop($idArr);
		if ($blockedSiteInfo['alwaysBlock'] == 'true'){
			$paramArray = 
			array (
				array($objPrefix.$index.".Site", "string", $blockedSiteInfo['Keyword']),
				array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
				array($objPrefix.$index.".BlockMethod", "string", "Keyword"),
			);
			$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
			if (!$retStatus){
				$result=_("Success!");
			}	
			else {
				$result = _('Failed to add');
			}
			/*setStr($objPrefix.$index.".Site", $blockedSiteInfo['Keyword'], false);
			setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
			setStr($objPrefix.$index.".BlockMethod", "Keyword", true);*/
		}
		else {
			if ($UTC_local_Time_conversion) $timeData = days_time_conversion_set($blockedSiteInfo['StartTime'], $blockedSiteInfo['EndTime'], $blockedSiteInfo['blockedDays']);
			else $timeData = array($blockedSiteInfo['StartTime'], $blockedSiteInfo['EndTime'], $blockedSiteInfo['blockedDays'], false);
			$paramArray = 
				array (
					array($objPrefix.$index.".Site", "string", $blockedSiteInfo['Keyword']),
					array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
					array($objPrefix.$index.".BlockMethod", "string", "Keyword"),
					array($objPrefix.$index.".StartTime", "string", $timeData[0]),
					array($objPrefix.$index.".EndTime", "string", $timeData[1]),
					array($objPrefix.$index.".BlockDays", "string", $timeData[2]),
				);
			$retStatus1 = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);
			if($timeData[3]){
				addTblObj($rootObjName);
				$IDs=explode(",",getInstanceIDs($rootObjName));
				$index=$IDs[count($IDs)-1];
				$paramArray = 
					array (
						array($objPrefix.$index.".Site", "string", $blockedSiteInfo['Keyword']),
						array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
						array($objPrefix.$index.".BlockMethod", "string", "Keyword"),
						array($objPrefix.$index.".StartTime", "string", $timeData[4]),
						array($objPrefix.$index.".EndTime", "string", $timeData[5]),
						array($objPrefix.$index.".BlockDays", "string", $timeData[6]),
					);
				$retStatus2 = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);
			}
			if ((!$retStatus1 && !$timeData[3]) || (!$retStatus1 && $timeData[3] && !$retStatus2)){
				$result=_("Success!");
			}
			else {
				$result = _('Failed to add');
			}
/*
			setStr($objPrefix.$index.".Site", $blockedSiteInfo['Keyword'], false);
			setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
			setStr($objPrefix.$index.".BlockMethod", "Keyword", false);
			setStr($objPrefix.$index.".StartTime", $blockedSiteInfo['StartTime'], false);
			setStr($objPrefix.$index.".EndTime", $blockedSiteInfo['EndTime'], false);
			setStr($objPrefix.$index.".BlockDays", $blockedSiteInfo['blockedDays'], true);
*/	
		}
	}
}
echo htmlspecialchars($result, ENT_NOQUOTES, 'UTF-8');
?>
