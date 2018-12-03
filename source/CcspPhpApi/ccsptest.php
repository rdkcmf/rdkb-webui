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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- $Id: header.php 3167 2010-03-03 18:11:27Z slemoine $ -->

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>CCSP test</title>
</head>

<body>
<?php 

include('ccspdm.php');

    /* DmGetStrsWithRootObj */
    $rootObjName = "Device.NAT.";
    $paramNameArray =
        array(
            "Device.NAT.PortMapping.",
            'DmzEnable' => "Device.NAT.X_CISCO_COM_DMZ.Enable",
            'DmzIntIp'  => "Device.NAT.X_CISCO_COM_DMZ.InternalIP",
            'NumEntries'=> "Device.NAT.PortMappingNumberOfEntries"
        );

    $retArray = DmGetStrsWithRootObj($rootObjName, $paramNameArray);

    print "<p>DmGetStrsWithRootObj: return status = $retArray[0]</p>";
    print "<pre>Test direct access of parameter name/value: ";
    print $retArray[1][0];
    print " - ";
    print $retArray[1][1];
    print "</pre>";
    print "<pre>";
    var_dump($retArray);
    print "</pre>";

    /* DmSetStrsWithRootObj */
    $paramArray = 
        array (
            array("Device.NAT.X_CISCO_COM_DMZ.Enable", "bool", "false"),
            array("Device.NAT.X_CISCO_COM_ICMPTimeout", "uint", "120"),
            array("Device.NAT.X_CISCO_COM_DMZ.InternalIP", "string", "192.168.1.123"),
            array("Device.NAT.X_CISCO_COM_DMZ.InternalMAC", "string", "01:EE:DD:23:45:67")
        );

    print "<p>DmSetStrsWithRootObj:</p>";
    print "<pre>";
    var_dump($paramArray);
    print "</pre>";

    $retStatus = DmSetStrsWithRootObj($rootObjName, TRUE, $paramArray);
    
    print "<p>Return status = $retStatus</p>";

    /* DmGetInstanceIds */
    $rootObjName = "Device.NAT.PortMapping.";
    $retArray = DmGetInstanceIds($rootObjName);

    print "<p>DmGetInstanceIds: return status = $retArray[0]</p>";
    print "<pre>";
    var_dump($retArray);
    print "</pre>";
    
    /* DmAddObj */
    $ObjTableName = "Device.NAT.PortMapping.";
    $newPmId = DmAddObj($ObjTableName);

    print "<p>DmAddObj: add $ObjTableName, return = $newPmId</p>";

    /* DmDelObj */
    if ( $newPmId != 0 )
    {
        $ObjName = $ObjTableName . $newPmId . ".";

        $retStatus = DmDelObj($ObjName);

        print "<p>DmDelObj: del $ObjName, return = $retStatus</p>";
    }

/*
 *  Test result
 *
DmGetStrsWithRootObj: return status = 0

Test direct access of parameter name/value: Device.NAT.PortMapping.1.Enable - true

array(56) {
  [0]=>
  int(0)
  [1]=>
  array(2) {
    [0]=>
    string(31) "Device.NAT.PortMapping.1.Enable"
    [1]=>
    string(4) "true"
  }
  [2]=>
  array(2) {
    [0]=>
    string(31) "Device.NAT.PortMapping.1.Status"
    [1]=>
    string(7) "Enabled"
  }
  [3]=>
  array(2) {
    [0]=>
    string(30) "Device.NAT.PortMapping.1.Alias"
    [1]=>
    string(12) "PortMapping1"
  }
  [4]=>
  array(2) {
    [0]=>
    string(38) "Device.NAT.PortMapping.1.AllInterfaces"
    [1]=>
    string(4) "true"
  }
  [5]=>
  array(2) {
    [0]=>
    string(38) "Device.NAT.PortMapping.1.LeaseDuration"
    [1]=>
    string(6) "232332"
  }
  [6]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.PortMapping.1.ExternalPort"
    [1]=>
    string(2) "80"
  }
  [7]=>
  array(2) {
    [0]=>
    string(45) "Device.NAT.PortMapping.1.ExternalPortEndRange"
    [1]=>
    string(2) "80"
  }
  [8]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.PortMapping.1.InternalPort"
    [1]=>
    string(5) "22222"
  }
  [9]=>
  array(2) {
    [0]=>
    string(33) "Device.NAT.PortMapping.1.Protocol"
    [1]=>
    string(3) "UDP"
  }
  [10]=>
  array(2) {
    [0]=>
    string(39) "Device.NAT.PortMapping.1.InternalClient"
    [1]=>
    string(11) "192.168.1.1"
  }
  [11]=>
  array(2) {
    [0]=>
    string(35) "Device.NAT.PortMapping.1.RemoteHost"
    [1]=>
    string(10) "64.64.64.1"
  }
  [12]=>
  array(2) {
    [0]=>
    string(36) "Device.NAT.PortMapping.1.Description"
    [1]=>
    string(15) "this is for vod"
  }
  [13]=>
  array(2) {
    [0]=>
    string(34) "Device.NAT.PortMapping.1.Interface"
    [1]=>
    string(22) "Device.IP.Interface.1."
  }
  [14]=>
  array(2) {
    [0]=>
    string(31) "Device.NAT.PortMapping.2.Enable"
    [1]=>
    string(4) "true"
  }
  [15]=>
  array(2) {
    [0]=>
    string(31) "Device.NAT.PortMapping.2.Status"
    [1]=>
    string(7) "Enabled"
  }
  [16]=>
  array(2) {
    [0]=>
    string(30) "Device.NAT.PortMapping.2.Alias"
    [1]=>
    string(12) "PortMapping2"
  }
  [17]=>
  array(2) {
    [0]=>
    string(38) "Device.NAT.PortMapping.2.AllInterfaces"
    [1]=>
    string(4) "true"
  }
  [18]=>
  array(2) {
    [0]=>
    string(38) "Device.NAT.PortMapping.2.LeaseDuration"
    [1]=>
    string(5) "21222"
  }
  [19]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.PortMapping.2.ExternalPort"
    [1]=>
    string(2) "21"
  }
  [20]=>
  array(2) {
    [0]=>
    string(45) "Device.NAT.PortMapping.2.ExternalPortEndRange"
    [1]=>
    string(2) "21"
  }
  [21]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.PortMapping.2.InternalPort"
    [1]=>
    string(5) "22222"
  }
  [22]=>
  array(2) {
    [0]=>
    string(33) "Device.NAT.PortMapping.2.Protocol"
    [1]=>
    string(3) "TCP"
  }
  [23]=>
  array(2) {
    [0]=>
    string(39) "Device.NAT.PortMapping.2.InternalClient"
    [1]=>
    string(11) "192.168.1.2"
  }
  [24]=>
  array(2) {
    [0]=>
    string(35) "Device.NAT.PortMapping.2.RemoteHost"
    [1]=>
    string(10) "64.64.64.2"
  }
  [25]=>
  array(2) {
    [0]=>
    string(36) "Device.NAT.PortMapping.2.Description"
    [1]=>
    string(15) "this is for ftp"
  }
  [26]=>
  array(2) {
    [0]=>
    string(34) "Device.NAT.PortMapping.2.Interface"
    [1]=>
    string(22) "Device.IP.Interface.1."
  }
  [27]=>
  array(2) {
    [0]=>
    string(31) "Device.NAT.PortMapping.8.Enable"
    [1]=>
    string(5) "false"
  }
  [28]=>
  array(2) {
    [0]=>
    string(31) "Device.NAT.PortMapping.8.Status"
    [1]=>
    string(0) ""
  }
  [29]=>
  array(2) {
    [0]=>
    string(30) "Device.NAT.PortMapping.8.Alias"
    [1]=>
    string(12) "PortMapping8"
  }
  [30]=>
  array(2) {
    [0]=>
    string(38) "Device.NAT.PortMapping.8.AllInterfaces"
    [1]=>
    string(5) "false"
  }
  [31]=>
  array(2) {
    [0]=>
    string(38) "Device.NAT.PortMapping.8.LeaseDuration"
    [1]=>
    string(1) "0"
  }
  [32]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.PortMapping.8.ExternalPort"
    [1]=>
    string(1) "0"
  }
  [33]=>
  array(2) {
    [0]=>
    string(45) "Device.NAT.PortMapping.8.ExternalPortEndRange"
    [1]=>
    string(1) "0"
  }
  [34]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.PortMapping.8.InternalPort"
    [1]=>
    string(1) "0"
  }
  [35]=>
  array(2) {
    [0]=>
    string(33) "Device.NAT.PortMapping.8.Protocol"
    [1]=>
    string(0) ""
  }
  [36]=>
  array(2) {
    [0]=>
    string(39) "Device.NAT.PortMapping.8.InternalClient"
    [1]=>
    string(7) "0.0.0.0"
  }
  [37]=>
  array(2) {
    [0]=>
    string(35) "Device.NAT.PortMapping.8.RemoteHost"
    [1]=>
    string(7) "0.0.0.0"
  }
  [38]=>
  array(2) {
    [0]=>
    string(36) "Device.NAT.PortMapping.8.Description"
    [1]=>
    string(0) ""
  }
  [39]=>
  array(2) {
    [0]=>
    string(34) "Device.NAT.PortMapping.8.Interface"
    [1]=>
    string(0) ""
  }
  [40]=>
  array(2) {
    [0]=>
    string(31) "Device.NAT.PortMapping.9.Enable"
    [1]=>
    string(5) "false"
  }
  [41]=>
  array(2) {
    [0]=>
    string(31) "Device.NAT.PortMapping.9.Status"
    [1]=>
    string(0) ""
  }
  [42]=>
  array(2) {
    [0]=>
    string(30) "Device.NAT.PortMapping.9.Alias"
    [1]=>
    string(12) "PortMapping9"
  }
  [43]=>
  array(2) {
    [0]=>
    string(38) "Device.NAT.PortMapping.9.AllInterfaces"
    [1]=>
    string(5) "false"
  }
  [44]=>
  array(2) {
    [0]=>
    string(38) "Device.NAT.PortMapping.9.LeaseDuration"
    [1]=>
    string(1) "0"
  }
  [45]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.PortMapping.9.ExternalPort"
    [1]=>
    string(1) "0"
  }
  [46]=>
  array(2) {
    [0]=>
    string(45) "Device.NAT.PortMapping.9.ExternalPortEndRange"
    [1]=>
    string(1) "0"
  }
  [47]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.PortMapping.9.InternalPort"
    [1]=>
    string(1) "0"
  }
  [48]=>
  array(2) {
    [0]=>
    string(33) "Device.NAT.PortMapping.9.Protocol"
    [1]=>
    string(0) ""
  }
  [49]=>
  array(2) {
    [0]=>
    string(39) "Device.NAT.PortMapping.9.InternalClient"
    [1]=>
    string(7) "0.0.0.0"
  }
  [50]=>
  array(2) {
    [0]=>
    string(35) "Device.NAT.PortMapping.9.RemoteHost"
    [1]=>
    string(7) "0.0.0.0"
  }
  [51]=>
  array(2) {
    [0]=>
    string(36) "Device.NAT.PortMapping.9.Description"
    [1]=>
    string(0) ""
  }
  [52]=>
  array(2) {
    [0]=>
    string(34) "Device.NAT.PortMapping.9.Interface"
    [1]=>
    string(0) ""
  }
  [53]=>
  array(2) {
    [0]=>
    string(33) "Device.NAT.X_CISCO_COM_DMZ.Enable"
    [1]=>
    string(5) "false"
  }
  [54]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.X_CISCO_COM_DMZ.InternalIP"
    [1]=>
    string(13) "192.168.1.123"
  }
  [55]=>
  array(2) {
    [0]=>
    string(37) "Device.NAT.PortMappingNumberOfEntries"
    [1]=>
    string(1) "4"
  }
}

DmSetStrsWithRootObj:

array(4) {
  [0]=>
  array(3) {
    [0]=>
    string(33) "Device.NAT.X_CISCO_COM_DMZ.Enable"
    [1]=>
    string(4) "bool"
    [2]=>
    string(5) "false"
  }
  [1]=>
  array(3) {
    [0]=>
    string(34) "Device.NAT.X_CISCO_COM_ICMPTimeout"
    [1]=>
    string(4) "uint"
    [2]=>
    string(3) "120"
  }
  [2]=>
  array(3) {
    [0]=>
    string(37) "Device.NAT.X_CISCO_COM_DMZ.InternalIP"
    [1]=>
    string(6) "string"
    [2]=>
    string(13) "192.168.1.123"
  }
  [3]=>
  array(3) {
    [0]=>
    string(38) "Device.NAT.X_CISCO_COM_DMZ.InternalMAC"
    [1]=>
    string(6) "string"
    [2]=>
    string(17) "01:EE:DD:23:45:67"
  }
}

Return status = 0

DmGetInstanceIds: return status = 0

array(5) {
  [0]=>
  int(0)
  [1]=>
  string(1) "1"
  [2]=>
  string(1) "2"
  [3]=>
  string(1) "8"
  [4]=>
  string(1) "9"
}

DmAddObj: add Device.NAT.PortMapping., return = 10

DmDelObj: del Device.NAT.PortMapping.10., return = 0
 */
 
?>
</body>
</html>
