<?php include('includes/header.php'); ?>

<!-- $Id: qos.php 3159 2010-01-11 20:10:58Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
//Start by licha
$enableWMM = "false";
$enableMoCA = "false";

$APIDs=explode(",",getInstanceIDs("Device.WiFi.AccessPoint."));
for($i=0;$i<count($APIDs);$i++) {
	$enableWMM = getStr("Device.WiFi.AccessPoint.".$APIDs[$i].".WMMEnable"); 
	if($enableWMM == "true") {
		$enableWMM = "true";
		break;
	}
}	
	
$MoCAIDs=explode(",",getInstanceIDs("Device.MoCA.Interface."));
for($i=0;$i<count($MoCAIDs);$i++) {
	$enableMoCA = getStr("Device.MoCA.Interface.".$MoCAIDs[$i].".QoS.X_CISCO_COM_Enabled"); 
	if($enableMoCA == "true") {
		$enableMoCA = "true";
		break;
	}
}
//$enableLAN = getStr("Device.X_CISCO_COM_DDNS.Enable"); //? R3
//$enableUPnP = getStr("Device.X_CISCO_COM_DDNS.Enable"); //? R3

//end by licha

//add by shunjie
("" == $enableWMM)  && ($enableWMM = "false");
("" == $enableMoCA) && ($enableMoCA = "false");

?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > QoS", "nav-qos1");

//start by Licha

var jsEnableWMM = <?php echo $enableWMM ?>;
var jsEnableMoCA = <?php echo $enableMoCA ?>;

//alert(jsEnableMoCA);

function init() {
	if(jsEnableWMM == true)
		$("#multimedia_enabled").prop("checked", true);
	else if(jsEnableWMM == false)
		$("#multimedia_disabled").prop("checked", true);
	if(jsEnableMoCA == true)
		$("#moca_enabled").prop("checked", true);
	else if(jsEnableMoCA == false)
		$("#moca_disabled").prop("checked", true);
/*	if(jsEnableLAN == true)
		$("#lan_enabled").prop("checked", true);
	else if(jsEnableLAN == false)
		$("#lan_disabled").prop("checked", true);
	if(jsEnableUPnP == true)
		$("#upnp_enabled").prop("checked", true);
	else if(jsEnableUPnP == false)
		$("#upnp_disabled").prop("checked", true);*/
}

init();

$('#save_setting').click(function() {
	var isEnabledWMM = $("#multimedia_enabled").is(":checked");
	var isEnabledMoCA = $("#moca_enabled").is(":checked");
//	var isEnabledLAN = $("#lan_enabled").is(":checked");
//	var isEnabledUPnP = $("#upnp_enabled").is(":checked");
	
	var qosInfo;
	qosInfo = '{"IsEnabledWMM":"'+isEnabledWMM+'", "IsEnabledMoCA":"'+isEnabledMoCA+'"}';
//	qosInfo = '{"IsEnabledWMM":"'+isEnabledWMM+'", "IsEnabledMoCA":"'+isEnabledMoCA+'", "IsEnabledLAN":"'+isEnabledLAN+'", "IsEnabledUPnP":"'+isEnabledUPnP+'"}';
//	alert(qosInfo);
	saveQoS(qosInfo);
});

function saveQoS(information) {
//alert(information);
	jProgress('This may take several seconds', 60);
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_QoS1_configuration.php",
		data: { qosInfo: information },
		success: function(){            
			jHide();
			//               alert("successful submit");
		},
		error: function(){            
			jHide();
			jAlert("Failure, please try again.");
		}
	});
}


	$(".radio-btns").radioToButton();
});
</script>

<div id="content">
   	<h1>Advanced > QoS</h1>
    <div id="educational-tip">
        <p class="tip">Manage network Quality of Service (QoS).</p>
        <p class="hidden">
Enabling QoS would provide better performance for latency sensitive communications, such as voice or video, especially during high traffic.</p>
    </div>

	<form action="#TBD" method="post" id="pageForm">
		<div class="module forms enable" id="enable-qos">
            <div class="form-row">
                <label for="multimedia_enabled">QoS for Wi-Fi Multimedia (WMM):</label>
                <ul class="radio-btns">
                    <li>
                        <input id="multimedia_enabled" name="multimedia" type="radio" checked="checked" value="Enabled"/>
                        <label for="multimedia_enabled">Enable</label>
                    </li>
                    <li id="off">
                        <input id="multimedia_disabled" name="multimedia" type="radio" value="Disabled"/>
                        <label for="multimedia_disabled">Disable</label>
                    </li>
                </ul>
            </div>
            <div class="form-row odd">
                <label for="moca_enabled">QoS for MoCA:</label>
                <ul class="radio-btns">
                    <li>
                        <input id="moca_enabled" name="moca" type="radio" value="Enabled"/>
                        <label for="moca_enabled">Enable</label>
                    </li>
                    <li id="off">
                        <input id="moca_disabled" name="moca" type="radio" checked="checked" value="Disabled"/>
                        <label for="moca_disabled">Disable</label>
                    </li>
                </ul>
            </div>
<!--            <div class="form-row">
                <label for="lan_enabled">QoS for LAN:</label>
                <ul class="radio-btns">
                    <li>
                        <input id="lan_enabled" name="lan" type="radio" checked="checked" value="Enabled"/>
                        <label for="lan_enabled">Enabled</label>
                    </li>
                    <li id="off">
                        <input id="lan_disabled" name="lan" type="radio" value="Disabled"/>
                        <label for="lan_disabled">Disabled</label>
                    </li>
                </ul>
            </div>
            <div class="form-row odd">
                <label for="upnp_enabled">QoS for UPnP:</label>
                <ul class="radio-btns">
                    <li>
                        <input id="upnp_enabled" name="upnp" type="radio" value="Enabled"/>
                        <label for="upnp_enabled">Enabled</label>
                    </li>
                    <li id="off">
                        <input id="upnp_disabled" name="upnp" type="radio" checked="checked" value="Disabled"/>
                        <label for="upnp_disabled">Disabled</label>
                    </li>
                </ul>
            </div>-->
            <div class="form-btn">
				<input id="save_setting" type="button" value="Save" class="btn" /> <!--//licha if type="submit", then an error occured in POST-->
				<!-- <input type="reset" value="Cancel"class="btn alt" /> -->
			</div>
			


		</div> <!-- end .module -->
	</form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
