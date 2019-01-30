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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
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
		<!-- XFINITY logo placement -->
		<?xml version="1.0" encoding="UTF-8" standalone="no"?>
		<svg viewBox="0 0 92 36"  height="60" >
			<use xlink:href="#logo"  transform="translate(0.000000, 8.000000)"/>
		</svg>		
		<!-- XFINITY logo placement end -->

		<!-- XFINITY logo svg code -->
		<svg class="defs-only" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
			<!-- Generator: Sketch 42 (36781) - http://www.bohemiancoding.com/sketch -->
			<desc>Created with Sketch.</desc>
			<defs></defs>
			<symbol id="logo">
				<path d="M84.958105,2.5074325 L84.958105,0.1842344 L76.488791,0.1842344 L76.488791,15.3852709 L78.90745,15.3852709 L78.90745,8.5739385 L82.90896,8.5739385 L82.90896,6.2930973 L78.90745,6.2930973 L78.90745,2.5074325 L84.958105,2.5074325 Z M90.906273,13.9719577 L91.266922,15.0250551 L91.278944,15.0250551 L91.641415,13.9719577 L92,13.9719577 L92,15.3852709 L91.769646,15.3852709 L91.769646,14.5364334 L91.779724,14.2421196 L91.759688,14.2421196 L91.361031,15.3852709 L91.172813,15.3852709 L90.774156,14.2421196 L90.75412,14.2421196 L90.764077,14.5364334 L90.764077,15.3852709 L90.53178,15.3852709 L90.53178,13.9719577 L90.906273,13.9719577 Z M90.209017,13.9719577 L90.209017,14.1661441 L89.806474,14.1661441 L89.806474,15.3852709 L89.566041,15.3852709 L89.566041,14.1661441 L89.161433,14.1661441 L89.161433,13.9719577 L90.209017,13.9719577 Z M85.487543,15.3852709 L87.905595,15.3852709 L87.905595,4.2729514 L85.487543,4.2729514 L85.487543,15.3852709 Z M70.586038,9.6375948 L74.368001,4.2730728 L71.594157,4.2730728 L69.256492,7.6943953 L66.919799,4.2730728 L64.144133,4.2730728 L67.922939,9.6375948 L63.878685,15.3867273 L66.654351,15.3867273 L69.256492,11.5799447 L74.662835,19.3038321 L77.325085,19.3038321 L70.586038,9.6375948 Z M6.707353,9.6375948 L10.489316,4.2730728 L7.715472,4.2730728 L5.377807,7.6943953 L3.040992,4.2730728 L0.265448,4.2730728 L4.044254,9.6375948 L0,15.3867273 L2.775787,15.3867273 L5.377807,11.5799447 L10.78415,19.3038321 L13.446521,19.3038321 L6.707353,9.6375948 Z M59.145919,4.2728301 L56.535156,4.2728301 L52.654956,11.3540816 L50.001692,4.2729514 L47.494388,4.2729514 L51.2211,13.9627338 L48.337847,19.3038321 L50.891416,19.3038321 L59.145919,4.2728301 Z M12.861832,4.2730728 L11.720382,4.2730728 L10.398,6.1623856 L12.861832,6.1623856 L12.861832,15.3867273 L15.280491,15.3867273 L15.280491,6.1623856 L17.747601,6.1622642 L17.747601,4.2729514 L15.280491,4.2729514 L15.280491,3.1972799 C15.280491,2.3844882 15.61989,2.0534003 16.441855,2.0534003 C16.912521,2.0534003 17.402251,2.1461244 17.747601,2.2401834 L17.747601,0.2935856 C17.252284,0.1160264 16.564257,0 15.817701,0 C13.965273,0 12.861953,1.2117234 12.861953,3.2325975 L12.861832,4.2730728 Z M41.9756,6.1622642 L41.9756,12.3747741 C41.9756,14.4013525 43.078192,15.6031238 44.926734,15.6031238 C45.540324,15.6031238 46.149663,15.5294544 46.611101,15.386606 L46.902535,13.4571209 C46.763982,13.4930453 46.25798,13.6140478 45.8099,13.6140478 C44.803603,13.6140478 44.389645,13.21014 44.389645,12.2091088 L44.389645,6.1622642 L47.170168,6.1622642 L46.446926,4.2729514 L44.389645,4.2729514 L44.389645,0.2594816 L41.9756,1.4860118 L41.9756,4.2729514 L40.215461,4.2729514 L40.215461,6.1622642 L41.9756,6.1622642 Z M29.545455,4.0508507 C28.407163,4.0508507 27.322543,4.5283066 26.759225,5.2759244 L26.759225,4.2729514 L24.341659,4.2729514 L24.341659,15.386606 L26.759225,15.386606 L26.759225,8.8996863 C26.759225,7.0981215 27.538447,6.1036442 28.955909,6.1035228 C30.495166,6.1035228 31.153321,7.0032129 31.153321,9.1111068 L31.153321,15.386606 L33.566516,15.386606 L33.566637,9.0262716 C33.566637,5.7696435 32.179533,4.0508507 29.545455,4.0508507 Z M21.756032,4.2729514 L19.337859,4.2729514 L19.337737,15.3867273 L21.755911,15.3867273 L21.756032,4.2729514 Z M36.039333,15.386606 L38.468192,15.386606 L38.468192,4.2729514 L36.039333,4.2729514 L36.039333,15.386606 Z" fill="#FFFFFF"id="xfinity_xFi_logo_wht_RGB"></path>
			</symbol>
		</svg>		
		<!-- XFINITY logo svg code end -->
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
		<p class="dp-header dp-white-text">This device is paused or in Bedtime Mode.</p>
		<div class="dp-space-filler-30"></div>
		<p class="dp-text dp-gray-text">To resume access to the Internet on your home network, open the XFINITY xFi app or visit xfinity.com/myxfi using a different connection or device.</p>
	</div>
</body>
</html>
