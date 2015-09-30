<!--
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2015 RDK Management

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
-->
<?php 

$blockedSiteInfo = json_decode($_REQUEST['BlockInfo'], true);

$objPrefix = "Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.";
$rootObjName = $objPrefix;
$index = $blockedSiteInfo['InstanceID'];

if( array_key_exists('URL', $blockedSiteInfo) ) {
	//this is to edit blocked URL
	if ($blockedSiteInfo['alwaysBlock'] == "true"){
		$paramArray = 
			array (
				array($objPrefix.$index.".Site", "string", $blockedSiteInfo['URL']),
				array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
			);

		$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
		if (!$retStatus){
			$result="Success!";
		}	
		else {
			$result = 'Failed to add';
		}

		/*setStr($objPrefix.$index.".Site", $blockedSiteInfo['URL'], false);
		setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], true);*/
	}
	else{
		$paramArray = 
			array (
				array($objPrefix.$index.".Site", "string", $blockedSiteInfo['URL']),
				array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
				array($objPrefix.$index.".StartTime", "string", $blockedSiteInfo['StartTime']),
				array($objPrefix.$index.".EndTime", "string", $blockedSiteInfo['EndTime']),
				array($objPrefix.$index.".BlockDays", "string", $blockedSiteInfo['blockedDays']),
			);
	
		$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
		if (!$retStatus){
			$result="Success!";
		}	
		else {
			$result = 'Failed to add';
		}

/*
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".Site", $blockedSiteInfo['URL'], false);
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
		//setStr($objPrefix.$blockedSiteInfo['InstanceID'].".BlockMethod", "URL");
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".StartTime", $blockedSiteInfo['StartTime'], false);
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".EndTime", $blockedSiteInfo['EndTime'], false);
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".BlockDays", $blockedSiteInfo['blockedDays'], true);
*/	
	}
}
else{
	//this is to edit blocked Keyword
	if ($blockedSiteInfo['alwaysBlock'] == "true"){
		$paramArray = 
			array (
				array($objPrefix.$index.".Site", "string", $blockedSiteInfo['Keyword']),
				array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
			);

		$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
		if (!$retStatus){
			$result="Success!";
		}	
		else {
			$result = 'Failed to add';
		}

		/*setStr($objPrefix.$index.".Site", $blockedSiteInfo['Keyword'], false);
		setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], true);*/
	}
	else{

		$paramArray = 
			array (
				array($objPrefix.$index.".Site", "string", $blockedSiteInfo['Keyword']),
				array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
				array($objPrefix.$index.".StartTime", "string", $blockedSiteInfo['StartTime']),
				array($objPrefix.$index.".EndTime", "string", $blockedSiteInfo['EndTime']),
				array($objPrefix.$index.".BlockDays", "string", $blockedSiteInfo['blockedDays']),
			);
	
		$retStatus = DmExtSetStrsWithRootObj($rootObjName, TRUE, $paramArray);	
		if (!$retStatus){
			$result="Success!";
		}	
		else {
			$result = 'Failed to add';
		}

/*
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".Site", $blockedSiteInfo['Keyword'], false);
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
		//setStr($objPrefix.$blockedSiteInfo['InstanceID'].".BlockMethod", "Keyword");
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".StartTime", $blockedSiteInfo['StartTime'], false);
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".EndTime", $blockedSiteInfo['EndTime'], false);
		setStr($objPrefix.$blockedSiteInfo['InstanceID'].".BlockDays", $blockedSiteInfo['blockedDays'], true);
*/	
	}
}

?>
