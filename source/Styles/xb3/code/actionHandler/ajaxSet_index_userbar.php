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
/*
 * Set the Locale for the Web UI based on the LANG setting or current linux locale
 */
$locale = getenv("LANG");
if(isset($locale)) {
    if(!isset($_SESSION['language']) || setlocale(LC_MESSAGES, 0) != $locale){
        //putenv("LANG=" . $locale);
        setlocale(LC_MESSAGES, $locale);
        setlocale(LC_TIME, $locale);
        
        $domain = "rdkb";
        bindtextdomain($domain, 'locales');
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
        $_SESSION['language'] = $locale; // set the default locale for future pages
    }
}

$jsConfig = $_POST['configInfo'];
//$jsConfig = '{"status":"true", "target":"sta_inet"}';
$arConfig = json_decode($jsConfig, true);
//print_r($arConfig);
function get_tips($target, $status, $user_type)
{
	 $tip = _("No Tips!");
            
	switch($target)
	{
		case "sta_inet":{
		    if ("true"==$status){
		        // _('Status: Connected-%s devices connected')
		        $tip = sprintf(_("Status: Connected-%s devices connected"),getStr("Device.Hosts.X_CISCO_COM_ConnectedDeviceNumber"));
		    }
		    else{
		        // _('Status: Unconnected-no devices');
		        $tip = _("Status: Unconnected-no devices");
		    }
		}break;
		case "sta_wifi":{
			if ("true"==$status){
				$sum = 0;
				$ids = explode(",", getInstanceIds("Device.WiFi.AccessPoint."));
				if($user_type == 'admin') $ids = array(1,2);
				foreach($ids as $i){
					$sum += getStr("Device.WiFi.AccessPoint.$i.AssociatedDeviceNumberOfEntries");
				}
				// _('Status: Connected-%s devices connected')
				$tip = sprintf(_("Status: Connected-%s devices connected"),$sum);
			}
			else{
			    // _('Status: Unconnected-no devices')
			    $tip = _("Status: Unconnected-no devices");
			}
		}break;
		case "sta_moca":{
		    if ("true"==$status ){
		        // _('Status: Connected-%s devices connected')
		        $tip = sprintf(_("Status: Connected-%s devices connected"),getStr("Device.MoCA.Interface.1.X_CISCO_COM_NumberOfConnectedClients"));
		    }
		    else{
		        // _('Status: Unconnected-no devices')
		        $tip = _("Status: Unconnected-no devices");
		    }	
		}break;
		/*case "sta_dect":{
			if ("true"==$status){
				$tip = getStr("Device.X_CISCO_COM_MTA.Dect.HandsetsNumberOfEntries").' Handsets connected';
			}
			else{
				$tip = 'no Handsets';
			}
		}break;*/
		case "sta_fire":{
		    // _('Firewall is set to %s')
		    $tip = sprintf(_("Firewall is set to %s"),_($status));
		}break;
		default:{
		    // _("No Tips!")
		    $tip = _("No Tips!");
		}break;
	}
	return $tip;
}
$tags = explode(',', $arConfig['target']);
$stas = explode(',', $arConfig['status']);
$user_type = $arConfig['user_type'];
$tips = array();
for ($i=0; $i<count($tags); $i++) {
	array_push($tips, get_tips($tags[$i], $stas[$i], $user_type));
}
$arConfig = array('tags'=>$tags, 'tips'=>$tips);
$jsConfig = json_encode($arConfig);
echo htmlspecialchars($jsConfig, ENT_NOQUOTES, 'UTF-8');
?>
