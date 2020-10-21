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
$flag = json_decode($_POST['TrustFlag'], true);
$validation = true;
if($validation) $validation = isValInArray($flag['trustFlag'], array('true', 'false'));
if($validation) $validation = printableCharacters($flag['HostName']);
if($validation) $validation = (validIPAddr($flag['IPAddress'])||$flag['IPAddress']=='');
if($validation){
    if( $flag['trustFlag'] == "true" ){
        // "no" => "yes"
        //if device not in trusted user table, add this device to Trusted user table, set the trusted flag == true
        //if already exist, just set the trusted flag  == true
        /*$IDs  = getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.");
        $idArr = explode(",", $IDs);*/
        $rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.";
        $paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.");
        $mapping_array  = array("IPAddress", "HostDescription");
        $TrustedUserValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
        $deviceExist = false;
        foreach ($TrustedUserValues as $value) {
            if ($flag['IPAddress'] == $value["IPAddress"] && $flag['HostName'] == $value["HostDescription"]) {
               $deviceExist = true;
               $id = $value["__id"];
               setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$id.Trusted", $flag['trustFlag'], true);
               break; 
            }
        }
        if (!$deviceExist)
        {
            addTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser."); 
            $IDs  = getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.");
            $idArr = explode(",", $IDs);
            $instanceid = array_pop($idArr);
            setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$instanceid.HostDescription", $flag['HostName'], false);
            if ( strpbrk($flag['IPAddress'], ':') != FALSE ){
                setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$instanceid.IPAddressType", "IPv6", false);
            }
            else{
                setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$instanceid.IPAddressType", "IPv4", false);
            }
            setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$instanceid.IPAddress", $flag['IPAddress'], false);
            setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$instanceid.Trusted", $flag['trustFlag'], true);
        }
        else{
            setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$instanceid.IPAddressType", "IPv4", false);
        }
        setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$instanceid.Trusted", $flag['trustFlag'], true);
    }
    else{
        // "yes" => "no" not trusted
    /*    $IDs  = getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.");
        $idArr = explode(",", $IDs);*/
        $rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.";
        $paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.");
        $mapping_array  = array("IPAddress", "HostDescription");
        $TrustedUserValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
        foreach ($TrustedUserValues as $value) {
            if ($flag['IPAddress'] == $value["IPAddress"] && $flag['HostName'] == $value["HostDescription"]) {
               $index = $value["__id"];
               break; 
            }
        }
        setStr("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$index.Trusted", 'false', true);
        //delTblObj("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.$index.");
    }
}
?>
