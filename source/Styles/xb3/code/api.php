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
<?php include('includes/actionHandlerUtility.php') ?>
<?php

    //echo "Request URI    : ".$_SERVER['REQUEST_URI']."\n";      
    //Expiration Time for token 
    $tokenTtl = 7776000; // Expiration time configured to 90 days in seconds.(90 *(60*60*24))

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
    
    function getKey() {
        $obsKeyFile = "/nvram/.keys/vyinerkyo.wyr";
        $plainKeyFile = "/tmp/".bin2hex(random_bytes(5));
        $keyLen = 16;
        $key = 0;

        if (file_exists($obsKeyFile)) {
            system("configparamgen jx $obsKeyFile $plainKeyFile > /dev/null 2>&1",$retVal);
            if(0 == $retVal) {
                $file = fopen($plainKeyFile,"r");
                $key = fread($file,filesize($plainKeyFile));
                fclose($file);
                system("rm -f $plainKeyFile");
            }
        }
        else {
            $key=substr(bin2hex(openssl_random_pseudo_bytes(ceil($keyLen / 2))), 0, $keyLen);
            $dirname = dirname($obsKeyFile);
            if (!is_dir($dirname))
            {
                mkdir($dirname, 0666, true);
            }
            $file = fopen($plainKeyFile,"w");
            fwrite($file, $key);
            fclose($file);
            system("configparamgen mi $plainKeyFile $obsKeyFile > /dev/null 2>&1",$retVal);
            system("rm -f $plainKeyFile");
            if(0 != $retVal) {
                $key = 0;
            }
        }
        return $key;
    }

    function genToken() {
        $key=getKey();
        header('HTTP/1.1 500 Internal Server Error');
        $data = array("status"=>"500","message"=>"Internal Server Error");

        if (!empty($key)) {
            $iat= time();
            global $tokenTtl;
            $exp = $iat + $tokenTtl;
            $plaintext = $exp ;
            $cipher = "AES-128-CBC";
            $ivlen = 4;
            $iv = openssl_random_pseudo_bytes($ivlen); 
            $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv );
            $ciphertext = base64_encode($iv.$ciphertext_raw); 
            $data = array("status"=>"200","message"=>"Success","token"=>"$ciphertext");
        }
        return $data;
    }

    function validateToken($ciphertext) {
        header('HTTP/1.1 500 Internal Server Error');
        $data = array("status"=>"500","message"=>"Internal Server Error");
        $key=getKey();

        if (!empty($key)) {
            $cipher = "AES-128-CBC";
            $c = base64_decode($ciphertext);
            $ivlen = 4;
            $iv = substr($c, 0, $ivlen);
            $ciphertext_raw = substr($c, $ivlen);
            $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            //validate the expiration time
            $curtime = time();
            $tokenexp = intval( $original_plaintext );
            if( $curtime < $tokenexp ) {
                $data = array("status"=>"200","message"=>"Success");
            }
            else {
                header('HTTP/1.1 401 Unauthorized');
                $data = array("status"=>"401","message"=>"What is Wifi passphrase");
            }
        }
        return $data;
    }

    function processPostRequest() {
        $body = json_decode($_GET["REQUEST_BODY"]);
        $data = authenticateCaller();

        if ($data["status"] == "200") {
            $status = "400";
            $message = "Missing or invalid POST body";
            if (isset($body->dpp_uri)) {
                //echo "***Body PR:".$body->dpp_uri."\n";
                $list = explode(';', $body->dpp_uri);
                if (($pos = strpos($list[0], "DPP:C:")) !== FALSE) {
                    $channel = substr($list[0], $pos+6);
                    $channel = substr($channel, strpos($channel, '/') + 1);
                }
                if (($pos = strpos($list[1], "M:")) !== FALSE) {
                    $mac_str = substr($list[1], $pos+2);
                    if (($ch = strpos($mac_str, ":")) !== FALSE) {
                        $mac = substr($mac_str, 0);
                    } else {
                        // Append MacAddress with colons
                        $mac = substr($mac_str, $pos, 2).":".substr($mac_str, $pos+2, 2).":".substr($mac_str, $pos+4, 2).":".
                            substr($mac_str, $pos+6, 2).":".substr($mac_str, $pos+8, 2).":".substr($mac_str, $pos+10, 2);
                    }
                    // Validate MacAddress
                    if (!validMAC($mac)) {
                        unset($mac);
                    }
                }
                if (($pos = strpos($body->dpp_uri, "K:")) !== FALSE) {
                    $key = substr($body->dpp_uri, $pos+2, -2);
                }
                if (isset($channel) && isset($mac) && isset($key)) {
                    //echo "***chan:".$channel."\n";
                    //echo "***mac:".$mac."\n";
                    //echo "***key:".$key."\n";
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
                                            header('HTTP/1.1 200 OK');
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
                    header('HTTP/1.1 400 Bad Request');
                    $status = "400";
                    $message = "Invalid POST body";
                    unset($data["token"]);
                }
            }
            $data["status"] = $status;
            $data["message"] = $message;
        }
        return $data;
    }

    function getHeaderValue($name) {
        foreach (getallheaders() as $header => $value) {
            if ($header == $name) {
                if (!empty($header)) {
                        return $value;
                }
            }
        }
        return null;
    }

    function authenticateCaller() {

        header('HTTP/1.1 401 Unauthorized');
        $data = array("status"=>"401","message"=>"What is Wifi passphrase");

        if ("false" == getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_RFC.Feature.EasyConnect.EnableAPISecurity")) {
            $data["status"] = "200";
            $data["message"] = "Success";
        }
        else {
            $token = getHeaderValue("X-Authorization-Token");
            if (!empty($token) ) {
                $retdata=validateToken($token);
                $data["status"] = $retdata["status"];
                $data["message"] = $retdata["message"];
            }
            else {
                $wifipassphrase = getHeaderValue("X-Challenge-Response");
                if (!empty($wifipassphrase)) {
                    $Password_24G = getStr("Device.WiFi.AccessPoint.1.Security.X_COMCAST-COM_KeyPassphrase");
                    $Password_5G = getStr("Device.WiFi.AccessPoint.2.Security.X_COMCAST-COM_KeyPassphrase");
                    if (($wifipassphrase == $Password_24G) || ($wifipassphrase == $Password_5G)) {
                        $retdata=genToken();
                        $data["status"] = $retdata["status"];
                        $data["message"] = $retdata["message"];
                        if ($retdata["status"] == "200") {
                            $data["token"] = $retdata["token"];
                        }
                    }
                }
            }
        }
        return $data;
    }
?>
