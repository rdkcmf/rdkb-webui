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
<?php
    //echo "Request URI    : ".$_SERVER['REQUEST_URI']."\n";      
    try{
        if ("true" == getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_RFC.Feature.EasyConnect.Enable")) {
            switch ($_GET["REQUEST_METHOD"]) {
                case 'POST':
                    $data=processPostRequest();
                    break;

                case 'GET':
                case 'PUT':	
                case 'DELETE':
                default:
                    $data=array("status"=>"405","message"=>"Please use proper request method !!");
            }
        }
        else {
            $data=array("status"=>"503","message"=>"Easy Connect feature is not enabled");
        }        
        echo json_encode($data);
    }
    catch(Exception $e) {
        echo json_encode(array("status"=>"500","message"=>'Caught exception: '. $e->getMessage()));
    }
    
    function processPostRequest() {
        $body = json_decode($_GET["REQUEST_BODY"]);
        $status = "400";
        $message = "Missing or invalid POST body";
        
        if (isset($body->dpp_uri)) {
            //echo "***Body:".$body->dpp_uri."\n";
            $list = explode(';', $body->dpp_uri);
            
            if (($pos = strpos($list[0], "DPP:C:")) !== FALSE) {
                $channel = substr($list[0], $pos+6);
                $channel = substr($channel, strpos($channel, '/') + 1);
            }
            
            if (($pos = strpos($list[1], "M:")) !== FALSE) {
                $mac = substr($list[1], $pos+2); 
            }
            
            if (($pos = strpos($body->dpp_uri, "K:")) !== FALSE) {
                $key = substr($body->dpp_uri, $pos+2, -2); 
            }
            
            if (isset($channel) && isset($mac) && isset($key)) {
                //echo "***chan:".$channel."\n";
                //echo "***mac:".$mac."\n";
                //echo "***key:".$key."\n";
                
                $status = "500";
                $message = "Failed to process POST Request";
                $sta="Device.WiFi.AccessPoint.1.X_RDKCENTRAL-COM_DPP.STA.1.";
                $ikey="MDkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDIgACiLN+2Rk4tRlwl4CKYkSEdheJIEbZO5UBr9SPoPFI394=";
                    
                if(setStr($sta."ClientMac",$mac,true) == true) {
                    if(setStr($sta."InitiatorBootstrapSubjectPublicKeyInfo",$ikey,true) == true) {
                        if(setStr($sta."ResponderBootstrapSubjectPublicKeyInfo",$key,true) == true) {
                            if(setStr($sta."Channels",$channel,true) == true) {
                                if(setStr($sta."MaxRetryCount",5,true) == true) {
                                    if(setStr($sta."Activate","true",true) == true) {
                                        $status = "200";
                                        $message = "Success";
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else {
                $status = "400";
                $message = "Invalid POST body";
            }
        }
        
        return array("status"=>$status,"message"=>$message);
    }
?>
