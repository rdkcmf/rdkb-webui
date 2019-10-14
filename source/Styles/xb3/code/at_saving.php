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
include_once __DIR__ .'/CSRF-Protector-PHP/libs/csrf/csrfprotector_rdkb.php';
//Initialise CSRFGuard library
csrfprotector_rdkb::init();
session_start();

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

if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert(\"'._("Please Login First!").'\"); location.href="index.php";</script>';
	exit(0);
}
	echo '<meta http-equiv=REFRESH CONTENT="0.1; url=trigger_spectrum_download.php">';
	echo "<h3>"._('Wi-Fi spectrum analyzer results has been saved to your local machine.')."</h3>";
?>