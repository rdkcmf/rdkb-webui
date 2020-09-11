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
	$response_message = array();
    $jsConfig = $_POST['configInfo'];
    $wan_OE_setval = json_decode($jsConfig, true);
    $port_entries = getStr('Device.X_RDK-Central_COM_WanAgent.InterfaceNumberOfEntries');
    if(array_key_exists('router_reboot',$wan_OE_setval)){
        $response_message='success';
        setStr('Device.X_CISCO_COM_DeviceControl.RebootDevice', "Router,Wifi,VoIP,Dect,MoCA", true);
    }else{
    for ($i=1;$i<=$port_entries; $i++){
    	if("eth3" ==getStr('Device.X_RDK-Central_COM_WanAgent.Interface.'.$i.'.Name')){
            if($wan_OE_setval['value_set'] == 'true'){
                setStr('Device.X_RDK-Central_COM_WanAgent.Interface.'.$i.'.Wan.Enable', "true", true);
            }
            else{
                setStr('Device.X_RDK-Central_COM_WanAgent.Interface.'.$i.'.Wan.Enable', "false", true);
            }
        }
        $response_message='success';
    }
  }
    if($response_message!=''){
    	$response->error_message = $response_message;
    	echo htmlspecialchars(json_encode($response), ENT_NOQUOTES, 'UTF-8');
    }
    else echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>
