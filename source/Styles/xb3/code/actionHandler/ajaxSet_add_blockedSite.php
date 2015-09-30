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
$exist = false;
$idArr = explode(",", getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite."));

if( array_key_exists('URL', $blockedSiteInfo) ) {
	//this is to set blocked URL
    //firstly, check whether URL exist or not
	$url = $blockedSiteInfo['URL'];
    foreach ($idArr as $key => $value) {
	    if ( $url == getStr($objPrefix.$value.".Site")){
	        $exist = true;
	        break;
	    }
	}

	if (! $exist){

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
				$result="Success!";
			}	
			else {
				$result = 'Failed to add';
			}

			/*setStr($objPrefix.$index.".Site", $blockedSiteInfo['URL'], false);
			setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
			setStr($objPrefix.$index.".BlockMethod", "URL", true);*/
		}
		else{

			$paramArray = 
				array (
					array($objPrefix.$index.".Site", "string", $blockedSiteInfo['URL']),
					array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
					array($objPrefix.$index.".BlockMethod", "string", "URL"),
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
			setStr($objPrefix.$index.".Site", $blockedSiteInfo['URL'], false);
			setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
			setStr($objPrefix.$index.".BlockMethod", "URL", false);
			setStr($objPrefix.$index.".StartTime", $blockedSiteInfo['StartTime'], false);
			setStr($objPrefix.$index.".EndTime", $blockedSiteInfo['EndTime'], false);
			setStr($objPrefix.$index.".BlockDays", $blockedSiteInfo['blockedDays'], true);
*/		
		}

		echo "0";
	}
	else{
		echo "1";
	}
}
else{
	//this is to set blocked Keyword
	$keyword = $blockedSiteInfo['Keyword'];
    foreach ($idArr as $key => $value) {
	    if ( $keyword == getStr($objPrefix.$value.".Site")){
	        $exist = true;
	        break;
	    }
	}

	if (! $exist){

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
				$result="Success!";
			}	
			else {
				$result = 'Failed to add';
			}

			/*setStr($objPrefix.$index.".Site", $blockedSiteInfo['Keyword'], false);
			setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
			setStr($objPrefix.$index.".BlockMethod", "Keyword", true);*/
		}
		else{

			$paramArray = 
				array (
					array($objPrefix.$index.".Site", "string", $blockedSiteInfo['Keyword']),
					array($objPrefix.$index.".AlwaysBlock", "bool", $blockedSiteInfo['alwaysBlock']),
					array($objPrefix.$index.".BlockMethod", "string", "Keyword"),
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
			setStr($objPrefix.$index.".Site", $blockedSiteInfo['Keyword'], false);
			setStr($objPrefix.$index.".AlwaysBlock", $blockedSiteInfo['alwaysBlock'], false);
			setStr($objPrefix.$index.".BlockMethod", "Keyword", false);
			setStr($objPrefix.$index.".StartTime", $blockedSiteInfo['StartTime'], false);
			setStr($objPrefix.$index.".EndTime", $blockedSiteInfo['EndTime'], false);
			setStr($objPrefix.$index.".BlockDays", $blockedSiteInfo['blockedDays'], true);
*/	
		}
		echo "0";
	}
	else{
		echo "1";
	}
}

?>
