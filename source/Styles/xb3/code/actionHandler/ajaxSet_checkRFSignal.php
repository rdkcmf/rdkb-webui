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
<?php
$enableRFCpativePortal = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_RFC.Feature.CaptivePortalForNoCableRF.Enable");
if($enableRFCpativePortal != "true") die();
$cableRFSignalStatus = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CableRfSignalStatus");
$jsConfig = "false";
if($cableRFSignalStatus=="true"){
	$jsConfig = "true";
}

echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>