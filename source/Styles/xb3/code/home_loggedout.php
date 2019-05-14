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
<!-- $Id: home_loggedout.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<!-- do nothing, just clean php session, log this logout !!!user!!!, then redirect to login page -->
<?php
include_once __DIR__ .'/CSRF-Protector-PHP/libs/csrf/csrfprotector_rdkb.php';
//Initialise CSRFGuard library
csrfprotector_rdkb::init();
	session_start();
	$cur_user = $_SESSION['loginuser'];
	exec("/usr/bin/logger -t GUI -p local5.notice \"User:$cur_user logout\" ");
	session_unset();
	session_destroy();
	header("location: index.php");
?>
