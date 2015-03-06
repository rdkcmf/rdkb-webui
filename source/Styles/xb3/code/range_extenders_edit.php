<?php include('includes/header.php'); ?>

<!-- $Id: connected_devices_computers.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Connected Devices - Range Extenders - Edit Range Extenders ", "nav-range-extenders");
        $("#security-mode").change(function() {
            var $security_select = $(this);
            var $network_password = $("#network_password");

            if ($security_select.find("option:selected").val() != "NONE") {
                $network_password.val("");
                $network_password.prop("disabled", false);
            } else {
                $network_password.val("");
                $network_password.prop("disabled", true);
            }
    }).trigger("change");

    $('#submit_config').click(function(){
    var wifiSsid = $('#ssid').val();
    var wifiChannel = $('#channel').val();
    var secMode = $('#security-mode').val();
    var pwd = $('#network_password').val();

    var wifiCfg = '{"SSID": "' + wifiSsid + '", "Channel": "' + wifiChannel + '", "SecurityMode": "' + secMode + '", "Password": "' + pwd + '"} ';
  
   
//   alert(wifiCfg);
    setWifi24gCfg(wifiCfg);

});

function setWifi24gCfg(configuration){
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_rangeExtenders_config.php",
		data: { configInfo: configuration },
		success: function(){            
			alert("successful submit");
		},
		error: function(){            
			jHide();
			jAlert("Failure, please try again.");
		}
	});
}
});

</script>

<div id="content">
    <h1>Connected Devices > Range Extender >Edit Range Extenders</h1>

    <div class="module forms">
    <h2> Edit Range Extenders</h2>
    <div class="form-row odd">
        <label for="ssid">SSID:</label> <input type="text"  size="25"  name="ssid" id="ssid" value="<?php echo getStr("Device.WiFi.SSID.1.SSID"); ?>"/>
    </div>
    <div class="form-row">
            <span class="readonlyLabel">MAC:</span> <span class="value"><?php echo getStr("Device.WiFi.SSID.1.MACAddress"); ?></span>
            </div>

    <div class="form-row odd">
            <label for="channel" class="readonlyLabel">Channel:</label>
            <?php
                $channel = getStr("Device.WiFi.Radio.1.Channel");
            ?>
            
            <select id="channel">
            <option <?php if ($channel  == 1) echo "selected=\"selected\""; ?> >1</option>
            <option <?php if ($channel  == 2) echo "selected=\"selected\""; ?>>2</option>
            <option <?php if ($channel  == 3) echo "selected=\"selected\""; ?>>3</option>
            <option <?php if ($channel  == 4) echo "selected=\"selected\""; ?>>4</option>
            <option <?php if ($channel  == 5) echo "selected=\"selected\""; ?>>5</option>
            <option <?php if ($channel  == 6) echo "selected=\"selected\""; ?>>6</option>
            <option <?php if ($channel  == 7) echo "selected=\"selected\""; ?>>7</option>
            <option <?php if ($channel  == 8) echo "selected=\"selected\""; ?>>8</option>
            <option <?php if ($channel  == 9) echo "selected=\"selected\""; ?>>9</option>
            <option <?php if ($channel  == 10) echo "selected=\"selected\""; ?>>10</option>
            <option <?php if ($channel  == 11) echo "selected=\"selected\""; ?>>11</option>

            </select>
            </div>
            <div class="form-row">
                    <label for="security-mode" class="readonlyLabel">Security Mode:</label>
                    <?php
                        $secMode = getStr("Device.WiFi.AccessPoint.1.Security.ModeEnabled");
                    ?>
            
                    <select id="security-mode">
                        <option <?php if ( !strcasecmp("NONE", $secMode)) echo "selected=\"selected\""; ?>>NONE</option>
                        <option <?php if ( !strcasecmp("WEP 64", $secMode)) echo "selected=\"selected\""; ?>>WEP 64 (risky)</option>
                        <option <?php if ( !strcasecmp("WEP 128", $secMode)) echo "selected=\"selected\""; ?>>WEP 128 (risky)</option>
                        <option <?php if ( !strcasecmp("WPA-PSK (TKIP)", $secMode)) echo "selected=\"selected\""; ?>>WPA-PSK (TKIP)</option>
                        <option <?php if ( !strcasecmp("WPA-PSK (AES)", $secMode)) echo "selected=\"selected\""; ?>>WPA-PSK (AES)</option>
                        <option <?php if ( !strcasecmp("WPA2-PSK (TKIP)", $secMode)) echo "selected=\"selected\""; ?>>WPA2-PSK (TKIP) </option>
                        <option <?php if ( !strcasecmp("WPA2-PSK (AES)", $secMode)) echo "selected=\"selected\""; ?>>WPA2-PSK (AES)</option>
                        <option <?php if ( !strcasecmp("WPA2-PSK (TKIP/AES)", $secMode)) echo "selected=\"selected\""; ?>>WPA2-PSK (TKIP/AES)</option>
                        <option <?php if ( !strcasecmp("WPAWPA2-PSK (TKIP/AES)", $secMode)) echo "selected=\"selected\""; ?>>WPAWPA2-PSK (TKIP/AES)(recommended)</option>
                    </select>
            </div>
            <div class="form-row odd">
                    <label for="network_password">Network Password:</label>
                    <input type="password" size="23" value="<?php echo getStr("Device.WiFi.AccessPoint.1.Security.KeyPassphrase"); ?>" id="network_password" name="network_password" />

            </div>
            <div class="form-row form-btn">
                           <input id="submit_config" type="button" value="Save" class="btn" />
<!--                     <a href="range_extenders.php" class="btn" title="">Edit</a> -->
                            <a href="range_extenders.php" class="btn alt" title="">Cancel</a>
                    </div>
    </div>



</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
