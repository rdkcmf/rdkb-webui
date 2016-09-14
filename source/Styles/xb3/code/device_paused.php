<?php
$CloudUIEnable = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_CloudUIEnable");
	if($CloudUIEnable == "false"){
		header('Location:at_a_glance.php');
		exit;
	}
session_start();
if (!isset($_SESSION["loginuser"])) {
	echo '<script type="text/javascript">alert("Please Login First!"); location.href="home_loggedout.php";</script>';
	exit(0);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
  <style type="text/css">
    @font-face {
      font-family: "XfinitySansLgt";
      src: url("cmn/fonts/XfinitySans-Light.woff2") format("woff2"), url("cmn/fonts/XfinitySans-Light.woff") format("woff"), url("cmn/fonts/Xfinity_Sans/XfinitySans-Light.ttf") format("truetype");
      font-weight: 300;
      font-style: normal;
    }
	html, body {
	    margin: 0;
	    height: 100%;
	    text-align: center;
	}
	p {
		margin: 0;
		font-family: "XfinitySansLgt";
		-webkit-font-smoothing: antialiased;
	}
	.si-twilight-background {
	    background: #142F66;
	    background-image:
      	linear-gradient(193deg, rgba(13,45,104,0.00) 20%, rgba(150,141,161,0.68) 100%),
      	linear-gradient(to right, rgba(22,37,66,1), rgba(6, 52, 75,1) 20%, rgba(19, 67, 103,1) 62%, rgba(34, 62, 109,1) 86%, rgba(20, 45, 101,1) 100%),
      	linear-gradient(144deg, rgba(14,20,77,0.00) 3%, rgba(54,69,137,0.60) 96%);
	}
	.dp-content {
		width: 330px;
		margin: auto;
	}
	#dp-nav {
	  	background-color: #000;
	  	z-index: 0;
	  	height: 60px;
	}
	#dp-xfinity-logo {
		display: block;
		height: 100%;
		margin: auto;
	}
   	.dp-white-text {
   		color: #fff;
   	}
   	.dp-gray-text {
   		color: #C7CDD2;
   	}
   	.dp-header {
   		font-size: 24px;
   		line-height: 1.3;
   	}
   	.dp-text {
   		font-size: 16px;
   		line-height: 1.2;
   	}
   	.dp-space-filler-30{
   		height: 30px;
   	}
   	.dp-space-filler-42{
   		height: 42px;
   	}
   	.dp-space-filler-48{
   		height: 48px;
   	}
   	.dp-space-filler-top{
   		height: 102px;
   	}
   	.dp-space-filler-below-header{
   		height: 24px;
   	}
   	.dp-border-top-splitter {
   		width: 60px;
   		margin: auto;
   		border-top: 1px solid #C7CDD2;
   		opacity: 0.7;
   	}
   	@media(max-width: 320px){
   		.dp-content {
			width: 296px;
		}
		.dp-space-filler-top {
			height: 42px;
		}
		.dp-space-filler-below-header {
			height: 18px;
		}
   	}
  </style>
</head>
<body class="si-twilight-background">
	<div id="dp-nav">
		<img src="cmn/img/logo.png" style="margin: 15px;" />
	</div>
	<div class="dp-content">
		<div class="dp-space-filler-top"></div>
		<?xml version="1.0" encoding="UTF-8" standalone="no"?>
		<svg width="100px" height="99px" viewBox="0 0 100 99" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
		    <!-- Generator: Sketch 39.1 (31720) - http://www.bohemiancoding.com/sketch -->
		    <title>pause_roadblock</title>
		    <desc>Created with Sketch.</desc>
		    <defs></defs>
		    <g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
		        <g id="pause_roadblock">
		            <ellipse id="Oval-8" stroke="#B1B9BF" stroke-width="1.5" stroke-dasharray="4.186046400735544" cx="49.9990322" cy="49.5" rx="48.5" ry="48.5"></ellipse>
		            <ellipse id="Oval-8" fill="#DDE2E6" opacity="0.15339319" cx="49.8298321" cy="49.5" rx="38.5008066" ry="38.6355932"></ellipse>
		            <rect id="Rectangle-12" fill="#FFFFFF" x="36.3182993" y="36.3764706" width="6.83579986" height="27.3882353"></rect>
		            <rect id="Rectangle-12" fill="#FFFFFF" x="56.8256988" y="36.3764706" width="6.83579986" height="27.3882353"></rect>
		        </g>
		    </g>
		</svg>
		<div class="dp-space-filler-48"></div>
		<p class="dp-header dp-white-text">Your device is currently paused.</p>
		<div class="dp-space-filler-below-header"></div>
		<p class="dp-text dp-gray-text">You are seeing this message, because this device has been paused in your <br> XFINITY Internet Settings.</p>
		<div class="dp-space-filler-30"></div>
		<div class="dp-space-filler-30 dp-border-top-splitter"></div>
		<p class="dp-text dp-gray-text">To un-pause, please use a different device to log in at internet.xfinity.com.</p>
	</div>
</body>
</html>