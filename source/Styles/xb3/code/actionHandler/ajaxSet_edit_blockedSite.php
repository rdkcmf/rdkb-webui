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
