
<!-- $Id: home_loggedout.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<!-- do nothing, just clean php session, log this logout !!!user!!!, then redirect to login page -->

<?php
	session_start();
	$cur_user = $_SESSION['loginuser'];
	
	exec("/usr/bin/logger -t GUI -p local5.notice \"User:$cur_user logout\" ");
	
	session_unset();
	session_destroy();
	
	header("location: index.php");
?>
