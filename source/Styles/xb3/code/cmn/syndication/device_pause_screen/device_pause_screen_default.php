<!doctype html>
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

$partner_url=getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.CloudUI.link");
$partner_logo_file=getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.WiFiPersonalization.MSOLogo");
$partner_brandname=getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.CloudUI.brandname");
$partner_productname=getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.CloudUI.productname");
$default_lang=getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.DefaultLanguage");
if ($default_lang == "fre") {
$STRING1='Cet appareil est en pause ou en mode Sommeil';
$STRING2="Pour réactiver l'accès à Internet sur votre réseau domestique, lancez l'application Helix Fi ou rendez-vous sur helixfi.videotron.com à l'aide d'une autre connexion ou d'un autre appareil.";
}
else {
$STRING1='This device is paused or in Bedtime Mode.';
$STRING2="To resume access to the Internet on your home network, open the ".$partner_brandname.$partner_productname." app or visit ".$partner_url." using a different connection or device.";
}
?>
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
		width: 500px;
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
   		color: #fff;
   		opacity: 0.7;
   	}
   	.dp-header {
   		font-size: 28px;
   		line-height: 1.3;
   	}
   	.dp-text {
   		font-size: 18px;
   		line-height: 1.3;
   	}
   	.dp-space-filler-30{
   		height: 30px;
   	}
   	.dp-space-filler-42{
   		height: 42px;
   	}
   	.dp-space-filler-40{
   		height: 40px;
   	}
   	.dp-space-filler-48{
   		height: 48px;
   	}
   	.dp-space-filler-top{
   		height: 54px;
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
   	svg.defs-only {
		display: block;
		position: absolute;
		height: 0;
		width: 0;
		margin: 0;
		padding: 0;
		border: none;
		overflow: hidden;
	}
   	@media(max-width: 420px){
   		.dp-content {
			width: 296px !important;
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
		<img src="cmn/syndication/img/<?php echo $partner_logo_file; ?>" style="margin: 15px;" />
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
		<div class="dp-space-filler-30"></div>
		<p class="dp-header dp-white-text"><?php echo $STRING1 ?></p>
		<div class="dp-space-filler-30"></div>
		<p class="dp-text dp-gray-text"><?php echo $STRING2 ?></p>
	</div>
</body>

</html>
