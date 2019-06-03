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
 $productLink = getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.CloudUI.link");
?>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1.0, user-scalable=yes">
  <link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/lib/progressBar.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="./cmn/css/common-min.css" />
   <!--[if IE 6]>
  <link rel="stylesheet" type="text/css" href="./cmn/css/ie6-min.css" />
  <![endif]-->
  <!--[if IE 7]>
  <link rel="stylesheet" type="text/css" href="./cmn/css/ie7-min.css" />
  <![endif]-->
  <!--Character Encoding-->
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta name="robots" content="noindex,nofollow">
  <script type="text/javascript" src="./cmn/js/lib/jquery-1.9.1.js"></script>
 <script type="text/javascript" src="./cmn/js/lib/jquery.validate.js"></script>
     <script type="text/javascript" src="./cmn/js/lib/jquery-migrate-1.2.1.js"></script>
  <script type="text/javascript" src="./cmn/js/lib/jquery.alerts.js"></script>
  <script type="text/javascript" src="./cmn/js/lib/jquery.ciscoExt.js"></script>
  <script type="text/javascript" src="./cmn/js/lib/jquery.highContrastDetect.js"></script>
  <script type="text/javascript" src="./cmn/js/lib/jquery.radioswitch.js"></script>
  <script type="text/javascript" src="./cmn/js/lib/jquery.virtualDialog.js"></script>
  <script type="text/javascript" src="./cmn/js/utilityFunctions.js"></script>
  <script type="text/javascript" src="./cmn/js/gateway.js"></script>
  <script type="text/javascript" src="./cmn/js/lib/bootstrap.min.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/bootstrap-waitingfor.js"></script>
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
  .si-twilight-background{
    background-color: #fff;
  }

    .dp-white-text {
      color: #000000;
    }
    .dp-gray-text {
      color: #000000;
      opacity: 0.7;
    }
    .dp-header {
      font-size: 24px;
      margin-top: 100px;
      line-height: 1.3;
    }
    .dp-text {
      font-size: 16px;
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
      

@media only screen 
 and (max-device-width: 600px){
#topbar,#set_up > p,#set_up{
  width: 100% !important;
}
} 
@media only screen 
 and (max-device-width: 420px){
  .rightbar{
     right: 0px !important;
  }
}

button {
    font-family: xfinSansLt;
    font-size: 18px;
    -webkit-font-smoothing: antialiased;
    width: 215px;
    height: 40px;
    background-color: rgb(46, 160, 221);
    color: rgb(255, 255, 255);
    cursor: pointer;
    border-radius: 20px;
    border-width: initial;
    border-style: none;
    border-color: initial;
    border-image: initial;
}
.rightbar {
    position: absolute;
    top: 35px;
    right: 20px;
    font-family: 'XfinitySansLgt';
}
#dropdown_initial_state {
    font-size: 16px;
    border-radius: 0px;
    outline: none;
    width: 110px;
    height: 40px;
    font-family: inherit;
    margin: 0;
    overflow: hidden;
}
#dropdown_active_state {
    font-size: 16px;
    border-radius: 0px;
    outline: none;
    width: 150px;
    height: 81px;
    color: white;
    font-family: inherit;
    margin: 0;
    overflow: hidden;
    background-color: #eee;
    -webkit-padding-start: 0px;
    text-align: right;
}
ele{
    color: black;
    text-decoration: none;
    display: block;
    -webkit-font-smoothing: initial;
    padding: 0px 20px;
    line-height: 40px;
    border-bottom: 1px solid black;
    text-align: left;
}
ili{
  position: absolute;
  top: 10;
  right: 20;
  border: solid black;
  border-width: 0 3px 3px 0;
  display: inline-block;
  padding: 3px;
}
.down {
    transform: rotate(45deg);
    -webkit-transform: rotate(45deg);
}
.up {
    transform: rotate(-135deg);
    -webkit-transform: rotate(-135deg);
    top: 15px;
    right: 20px;
}
  </style>
<script>
var check="false";
function checkRFSignalStatus(val){
      
      if(val=="button"){
        jProgress('Waiting for backend to be fully executed, please be patient...',60);
      }
      
      $.ajax({
      type: "POST",
      url: "actionHandler/ajaxSet_checkRFSignal.php",
      success: function (msg) {
      if(msg=="true"){
        check="true";
        jHide();
        //console.log("inside");
          window.location = "http://<?php echo $productLink;?>";
        
        }
        else{
         if(val=="button"){
             jHide();
            setTimeout(function(){
               jAlert("Please try again later");
             }, 2000);
           
          }
        }
      }
    });
      }
$(document).ready(function(){
  $("#get_set_up").click(function(){
     checkRFSignalStatus("button");
  });
  $("#dropdown_initial_state").mouseenter(function(){
      $("#dropdown_initial_state").hide();
      $("#dropdown_active_state").show();
  });
  $("#dropdown_active_state").mouseleave(function(){
      $("#dropdown_initial_state").show();
      $("#dropdown_active_state").hide();
  });
  $("#f_i_option1").click(function(){
      $("#dropdown_initial_state").show();
      $("#dropdown_active_state").hide();
  });
  $("#f_a_option2").click(function(){
    
  });
});
setInterval(function() {

  if(check=="false"){
      checkRFSignalStatus();
  }
  
}, 5000);
  </script>
</head>

<body class="si-twilight-background">
<?php
  session_start();
$defaultLanguage= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.RDKB_UIBranding.DefaultLanguage");
  $partnerId= getStr("Device.DeviceInfo.X_RDKCENTRAL-COM_Syndication.PartnerId");
  if($_GET['lang']!=""){
      $defaultLanguage= $_GET['lang'];
  }
  if($defaultLanguage=="fre"){
      $header="Aucun signal de câble détecté.";
      $content="Vérifiez que vos câbles coaxiaux sont bien connectés. S’ils sont connectés et que vous tentez d’activer votre borne, essayez d’utiliser une autre prise. Si le problème persiste, veuillez nous appeler.";
      $tryAgain="Réessayer";
  }else{
      $header="We can't detect a cable signal.";
      $content ="Please check that your coax cables are tightly secured. If the cables are secured, and you're trying to activate your Gateway, try another cable outlet in your house. If the problem persists, give us a call";
       if($partnerId=="comcast")
          $content = $content." at <font color='#3BB9FF'> 1-800-xfinity </font></p>";
        else
          $content = $content.".";
      $tryAgain="Try Again";  
  }
  
   if (!isset($_SESSION['lang']))
    $_SESSION['lang'] = $defaultLanguage;
  else if (isset($_SESSION['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])){
    if ($_GET['lang'] == "eng")
      $_SESSION['lang'] = "eng";
    else if ($_GET['lang'] == "fre")
      $_SESSION['lang'] = "fre";
  }
  require_once "includes/".$_SESSION['lang'].".php";
?>
  <div class="dp-content">
    <div class="dp-space-filler-top"></div>
    <?xml version="1.0" encoding="UTF-8" standalone="no"?>
  
    <div class="dp-space-filler-30"></div>
    <?php
      echo '<div class="rightbar">
                                 <ul id="dropdown_initial_state">
        <a href = "index.php?lang='; echo  $lang['lang']; echo '"> <ele id="f_i_option1">'; echo $lang["option1"]; echo '<ili class="down"></ili></ele></a>
                                </ul>
                                 <ul id="dropdown_active_state" style="display:none;">
                                <a href = "index.php?lang='; echo  $lang['lang']; echo '">  <ele id="f_a_option1">'; echo  $lang["option1"]; echo '<ili class="up"></ili></ele></a>
                                <a href = "index.php?lang='; echo  $lang['otherlang']; echo '">  <ele id="f_a_option2">'; echo $lang["option2"]; echo '</ele></a>
                                </ul>
                </div>';
      ?>
    <p class="dp-header dp-white-text"><?php echo $header ?></p>
    <div class="dp-space-filler-30"></div>
    <p class="dp-text dp-gray-text"><?php echo $content ?>
  <br><br>
  <div>
        <button id="get_set_up"><?php echo $tryAgain;?></button>
      </div>
</div>
</body>

</html>