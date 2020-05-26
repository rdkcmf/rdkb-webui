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
<?php include('../includes/actionHandlerUtility.php') ?>
<?php 

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("'._("Please Login First!").'"); location.href="../index.php";</script>';
	exit(0);
}
/**
 *  Description: Judge whether the user input ip valid or not (based on current gw ip and subnetmask)
 *  parameter  : input IP address
 *  return     : bool(TRUE/FALSE), string(message)
 */
function isIPValid($IP, $MAC){
    $ret        = TRUE;
    $msg 		= '';
    $LanSubMask = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanSubnetMask");
    $LanGwIP    = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanIPAddress");
    $gwIP       = explode('.', $LanGwIP);
    $hostIP     = explode('.', $IP); 
    if ($LanGwIP == $IP) {
        $msg = _("This IP is reserved for Lan Gateway!");
        $ret = FALSE;
    }
    elseif (strstr($IP, '172.16.12')) {
        $msg = _("This IP is reserved for Home Security!");
        $ret = FALSE;
    }     
    elseif (strstr($LanSubMask, '255.255.255')) {
        //the first three field should be equal to gw ip field
        if (($gwIP[0] != $hostIP[0]) || ($gwIP[1] != $hostIP[1]) || ($gwIP[2] != $hostIP[2])) {
           $msg = _("Input IP is not in valid range:\n") . "$gwIP[0].$gwIP[1].$gwIP[2].[2~253]!";
           $ret = FALSE;
        }      
    }
    elseif ($LanSubMask == '255.255.0.0') {
        if (($gwIP[0] != $hostIP[0]) || ($gwIP[1] != $hostIP[1])) {
           $msg = _("Input IP is not in valid range:\n") . "$gwIP[0].$gwIP[1].0.1 - $gwIP[0].$gwIP[1].255.254 !";
           $ret = FALSE;
        }      
    } 
    else {
        if ($gwIP[0] != $hostIP[0]) {
           $msg = _("Input IP is not in valid range:\n [10.0.0.2 ~ 10.255.255.254]!");
           $ret = FALSE;
        } 
    } 
    if ($ret) {
		
		$MDIDs=explode(",",getInstanceIDs("Device.Hosts.Host."));
        $arrayDHCPMAC=array();
        $arrayDHCPIPs=array();
        $arrayStaticMAC=array();
        $arrayStaticIPs=array();
        $MDStaticIDs = explode(",", getInstanceIds("Device.DHCPv4.Server.Pool.1.StaticAddress."));

        foreach ($MDStaticIDs as $key=>$i) {
                array_push($arrayStaticMAC, strtoupper(getStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$i.Chaddr")));
                array_push($arrayStaticIPs, strtoupper(getStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$i.Yiaddr")));	
        }
        
		foreach ($MDIDs as $key=>$i) {
			$type = getStr("Device.Hosts.Host.".$i.".AddressSource");
			if($type == "DHCP") {
                array_push($arrayDHCPMAC, strtoupper(getStr("Device.Hosts.Host.".$i.".PhysAddress")));
                array_push($arrayDHCPIPs, strtoupper(getStr("Device.Hosts.Host.".$i.".IPv4Address.1.IPAddress")));
			}
        }
        //if DHCP ==> ReservedIP
		if(in_array(strtoupper($MAC), $arrayDHCPMAC)){
            
            //checking if IP is same as before
             foreach ($MDIDs as $key => $value) {
                if ( !strcasecmp(getStr("Device.Hosts.Host.$value.PhysAddress"), $MAC) ){   
                    if ( !strcasecmp(getStr("Device.Hosts.Host.$value.IPv4Address.1.IPAddress"), $IP) ) {
                        
				        return array(TRUE, $msg);
			            break;
                    } 
                    //if IP is not same, then checking whether it has been assigned to any DHCP or Static client
                    elseif(in_array(strtoupper($IP), $arrayDHCPIPs) || in_array(strtoupper($IP), $arrayStaticIPs))
                    {
                        $msg = _("IP has already been reserved for another device.\nPlease try using another IP address!");
                        $ret = FALSE;
                        break;
                    }
                }

            }   
		}
		//if ReservedIP => ReservedIP
        elseif(in_array(strtoupper($MAC), $arrayStaticMAC)) {
		    foreach ($MDStaticIDs as $key => $value) {
                //checking if IP is same as before
		    	if ( !strcasecmp(getStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$value.Chaddr"), $MAC) ) {
				    if ( !strcasecmp(getStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$value.Yiaddr"), $IP) ) {
					    //if device is there with same mac and ip then its an EDIT of comments of ReservedIP
					    return array(TRUE, $msg);
					    break;
                    }
                    //if IP is not same, then checking whether it has been assigned to any DHCP or Static client
                    elseif(in_array(strtoupper($IP), $arrayDHCPIPs) || in_array(strtoupper($IP), $arrayStaticIPs))
                    {
					    $msg = _("IP has already been reserved for another device.\nPlease try using another IP address!");
                        $ret = FALSE;
					    break;
				    }
		    	}
            }
        } 
        //adding new static device
        else {
             //checking whether it has been assigned to any DHCP or Static client
            if(in_array(strtoupper($IP), $arrayDHCPIPs) || in_array(strtoupper($IP), $arrayStaticIPs)) 
                {
                    $msg = _("IP has already been reserved for another device.\nPlease try using another IP address!");
                    $ret = FALSE; 
                }
        }   
    }
    return array($ret, $msg);
}
$deviceInfo = json_decode($_POST['DeviceInfo'], true);
$result     = "";
$validation = true;
if($validation) $validation = printableCharacters($deviceInfo['hostName']);
$new_hostName = str_replace(str_split('<>&"\'|'), '', $deviceInfo['hostName']);
if($validation) $validation = validMAC($deviceInfo['macAddress']);
if($validation) $validation = validIPAddr($deviceInfo['reseverd_ipAddr']);
if($validation) $validation = printableCharacters($deviceInfo['Comments']);
if($validation) $validation = is_allowed_string($deviceInfo['Comments']);
$result = ($validation)?'':_('Invalid Inputs!');
if( !array_key_exists('delFlag', $deviceInfo) ) {
    //key kelFlag is not exist, so this is to reserve a ip addr for host 
    //firstly check whether this device is already in the reserved ip list
    $exist   = false;
    $macAddr = $deviceInfo['macAddress'];
    $ipAddr  = $deviceInfo['reseverd_ipAddr'];
    $resp = isIPValid($ipAddr, $macAddr);
    if (array_key_exists('UpdateComments', $deviceInfo)){
        //from edit device page scenario: DHCP ==> DHCP
        //only update comments for this device connected via DHCP
        $idArr = explode(",", getInstanceIds("Device.Hosts.Host."));
        foreach ($idArr as $key => $value) {
            $macArr["$value"] =  getStr("Device.Hosts.Host.$value.PhysAddress");
        }
        foreach ($macArr as $key => $value) {
            if ( !strcasecmp($value, $macAddr) ) {
              $index = $key;  
              break;
            }
        }
        if( isSet($index) ){
           setStr("Device.Hosts.Host.$index.Comments", $deviceInfo['Comments'], true);
        }    
        $result = _("success");        
    }//end of array_key_exist updateComments
    //First of all, check whether the user post IP address available or not
    elseif ($resp[0] == FALSE) {
        $result = $resp[1];
    }
    else{
        $idArr = explode(",", getInstanceIds("Device.DHCPv4.Server.Pool.1.StaticAddress."));
        foreach ($idArr as $key => $value) {
            if ( !strcasecmp(getStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$value.Chaddr"), $macAddr) ) {
                $exist = true;
                $existIndex = $value;
                break;
            }
        }
        if( ! $exist ){
            /*
            * there are two scenarios: 
            *  1. DHCP ==> ReservedIP, add entry, update host comments
            *  2. ReservedIP ==> ReservedIP, mac address changed, modify this static entry, update host comments meanwhile
            */
            addTblObj("Device.DHCPv4.Server.Pool.1.StaticAddress.");
            $IDs  = getInstanceIds("Device.DHCPv4.Server.Pool.1.StaticAddress.");
            $idArr = explode(",", $IDs);
            $instanceid = array_pop($idArr);
            setStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$instanceid.X_CISCO_COM_DeviceName", $new_hostName, false);
            setStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$instanceid.Chaddr", $deviceInfo['macAddress'], false);
            setStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$instanceid.Yiaddr", $deviceInfo['reseverd_ipAddr'], false);
            if(setStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$instanceid.X_CISCO_COM_Comment", $deviceInfo['Comments'], true)){
                $result = _("success");
            }
            if (array_key_exists('addResvIP', $deviceInfo)){
                //this post is from add device page, only set staticAddress table, do nothing any more
            }
            else{
                //this post is from edit device page, set Host talbe comments as well.
                $idArr = explode(",", getInstanceIds("Device.Hosts.Host."));
                $macArr = array();
                foreach ($idArr as $key => $value) {
                    $macArr["$value"] =  getStr("Device.Hosts.Host.$value.PhysAddress");
                }
                foreach ($macArr as $key => $value) {
                    if ( !strcasecmp($value, $macAddr) ) {
                      $index = $key;  
                      break;
                    }
                }
                if( isSet($index) ){
                   setStr("Device.Hosts.Host.$index.Comments", $deviceInfo['Comments'], true);
		setStr("Device.Hosts.Host.$index.AddressSource", "Static", true);
                }
            }//end of else
        } //end of exist
        else{
            if ( array_key_exists('addResvIP', $deviceInfo) ) {
                $result = _("Conflicting MAC address, please input again.");
            }
            else {
                /* 
                * From edit device scenario: ReservedIP  ==> ReservedIP, only update static table entry, and host comments
                */
                setStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$existIndex.Chaddr", $deviceInfo['macAddress'], false);
                setStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$existIndex.Yiaddr", $deviceInfo['reseverd_ipAddr'], false);
                setStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$instanceid.X_CISCO_COM_DeviceName", $new_hostName, false);
                if(setStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$existIndex.X_CISCO_COM_Comment", $deviceInfo['Comments'], true)){
                    $result = _("success");
                }
                $idArr = explode(",", getInstanceIds("Device.Hosts.Host."));
                $macArr = array();
                foreach ($idArr as $key => $value) {
                    $macArr["$value"] =  getStr("Device.Hosts.Host.$value.PhysAddress");
                }
                foreach ($macArr as $key => $value) {
                    if ( !strcasecmp($value, $macAddr) ) {
                      $index = $key;  
                      break;
                    }
                }
                if( isSet($index) ){
                   setStr("Device.Hosts.Host.$index.Comments", $deviceInfo['Comments'], true);
                }
            }// end of else
        }
    }//end of else isIPValid
}
else{
    //from edit page scenario: Reserved IP => DHCP
    //this is going to remove the corresponding reserved ip in static address table 
    $macAddr = $deviceInfo['macAddress'];
    $idArr = explode(",", getInstanceIds("Device.DHCPv4.Server.Pool.1.StaticAddress."));
    foreach ($idArr as $key => $value) {
        $macArr["$value"] =  getStr("Device.DHCPv4.Server.Pool.1.StaticAddress.$value.Chaddr");
    }
    foreach ($macArr as $key => $value) {
        if ( !strcasecmp($value, $macAddr) ) {
          $index = $key;  
          break;
        }
    }
    if( isSet($index) ){
       delTblObj("Device.DHCPv4.Server.Pool.1.StaticAddress.$index.");    
    }
    $idArr = explode(",", getInstanceIds("Device.Hosts.Host."));
    unset($macArr); // this is very important 
    foreach ($idArr as $key => $value) {
        $macArr["$value"] =  getStr("Device.Hosts.Host.$value.PhysAddress");
    }
    foreach ($macArr as $key => $value) {
        if ( !strcasecmp($value, $macAddr) ) {
          $i = $key;  
          break;
        }
    }
    if( isSet($i) ){
       setStr("Device.Hosts.Host.$i.Comments", $deviceInfo['Comments'], true);
       setStr("Device.Hosts.Host.$i.AddressSource", "DHCP", true);
    }
    $result = _("success");
}
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES, 'UTF-8');
?>
