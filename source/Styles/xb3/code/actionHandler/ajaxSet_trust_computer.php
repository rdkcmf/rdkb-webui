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
$ipAddr= explode("/",$flag['IPAddress']);
$validation = true;
if($validation) $validation = isValInArray($flag['trustFlag'], array('true', 'false'));
if($validation) $validation = printableCharacters($flag['HostName']);
if($validation) $validation = (validIPAddr($ipAddr[0])||$ipAddr[0]=="");
if($validation){
    if( $flag['trustFlag'] == "true" ){
        // "no" => "yes"
        //if device not in trusted user table, add this device to Trusted user table, set the trusted flag == true
        //if already exist, just set the trusted flag  == true
       /* $IDs  = getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.");
        $idArr = explode(",", $IDs);*/
        $deviceExist = false;
        $rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.";
        $paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.");
        $mapping_array  = array("IPAddress", "HostDescription");
        $TrustedUserValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
        foreach ($TrustedUserValues as $key => $value) {
            if ( $flag['HostName'] == $value["HostDescription"]) {
               $deviceExist = true;
               $id = $value["__id"];
               $ipStatus= getStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$id.IPAddressType");
                if($ipStatus=="IPv4"){
                    setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$id.IPAddress", $ipAddr[0], false);
                }else if($ipStatus=="IPv6"){
                    setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$id.IPAddress", $ipAddr[1], false);
                }
               setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$id.Trusted", $flag['trustFlag'], true);
               //break; 
            }
        }
        if (!$deviceExist)
        {
            if($ipAddr[1]!="NA"){
                addTblObj("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser."); 
                $IDs  = getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.");
                $idArr = explode(",", $IDs);
                $instanceid = array_pop($idArr);
                setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$instanceid.HostDescription", $flag['HostName'], false);
                setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$instanceid.IPAddress", $ipAddr[1], false);
                setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$instanceid.IPAddressType", "IPv6", false);
                setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$instanceid.Trusted", $flag['trustFlag'], true);
            }
            if($ipAddr[0]=="NA")
                $ipAddr[0]="";
            addTblObj("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser."); 
            $IDs  = getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.");
            $idArr = explode(",", $IDs);
            $instanceid = array_pop($idArr);
            setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$instanceid.HostDescription", $flag['HostName'], false);
            setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$instanceid.IPAddress", $ipAddr[0], false);
            setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$instanceid.IPAddressType", "IPv4", false);
            setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$instanceid.Trusted", $flag['trustFlag'], true);
        }
    }
    else{
        // "yes" => "no" not trusted
       /* $IDs  = getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.");
        $idArr = explode(",", $IDs);*/
        $rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.";
        $paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.");
        $mapping_array  = array("IPAddress", "HostDescription");
        $TrustedUserValues = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);
        foreach ($TrustedUserValues as $key => $value) {
             if ($flag['HostName'] == $value["HostDescription"]) {
                $index = $value["__id"];
                setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$index.Trusted", 'false', true);
            }
        }
        //delTblObj("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.$index.");
    }
}
?>
