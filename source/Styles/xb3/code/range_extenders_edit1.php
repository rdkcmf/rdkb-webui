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

            if($security_select.find("option:selected").val() != "NONE") {
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
    setWifi5gCfg(wifiCfg);

});

function setWifi5gCfg(configuration){
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_rangeExtenders1_config.php",
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
        <label for="ssid">SSID:</label> <input type="text"  size="25"  name="ssid" id="ssid" value="<?php echo getStr("Device.WiFi.SSID.2.SSID"); ?>"/>
    </div>
    <div class="form-row">
                <span class="readonlyLabel">MAC:</span> <span class="value"><?php echo getStr("Device.WiFi.SSID.2.MACAddress"); ?></span>
            </div>
    <div class="form-row odd">
        <label for="channel" class="readonlyLabel">Channel:</label>
            <?php
                $channel = getStr("Device.WiFi.Radio.2.Channel");
            ?>
            
        <select id="channel">
            <option value="36" <?php if ($channel == 36) echo "selected=\"selected\""; ?>>36</option>
            <option value="40" <?php if ($channel == 40) echo "selected=\"selected\""; ?>>40</option>
            <option value="44" <?php if ($channel == 44) echo "selected=\"selected\""; ?>>44</option>
            <option value="48" <?php if ($channel == 48) echo "selected=\"selected\""; ?>>48</option>
            <option value="52" <?php if ($channel == 52) echo "selected=\"selected\""; ?>>52</option>
            <option value="56" <?php if ($channel == 56) echo "selected=\"selected\""; ?>>56</option>
            <option value="60" <?php if ($channel == 60) echo "selected=\"selected\""; ?>>60</option>
            <option value="64" <?php if ($channel == 64) echo "selected=\"selected\""; ?>>64</option>
            <option value="100" <?php if ($channel == 100) echo "selected=\"selected\""; ?>>100</option>                  
            <option value="104" <?php if ($channel == 104) echo "selected=\"selected\""; ?>>104</option>                  
            <option value="108" <?php if ($channel == 108) echo "selected=\"selected\""; ?>>108</option>
            <option value="112" <?php if ($channel == 112) echo "selected=\"selected\""; ?>>112</option>
            <option value="116" <?php if ($channel == 116) echo "selected=\"selected\""; ?>>116</option>
            <option value="132" <?php if ($channel == 132) echo "selected=\"selected\""; ?>>132</option>
            <option value="136" <?php if ($channel == 136) echo "selected=\"selected\""; ?>>136</option>
            <option value="140" <?php if ($channel == 140) echo "selected=\"selected\""; ?>>140</option>
            <option value="149" <?php if ($channel == 149) echo "selected=\"selected\""; ?>>149</option>
            <option value="153" <?php if ($channel == 153) echo "selected=\"selected\""; ?>>153</option>
            <option value="157" <?php if ($channel == 157) echo "selected=\"selected\""; ?>>157</option>
            <option value="161" <?php if ($channel == 161) echo "selected=\"selected\""; ?>>161</option>
            <option value="165" <?php if ($channel == 165) echo "selected=\"selected\""; ?>>165</option>  
        </select>
        </div>
        
        <div class="form-row">
            <label for="security-mode" class="readonlyLabel">Security Mode:</label>
              <?php
                $secMode = getStr("Device.WiFi.AccessPoint.2.Security.ModeEnabled");
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
            <label for="netPassword">Network Password:</label>
            <input type="password" size="23" value="<?php echo getStr("Device.WiFi.AccessPoint.2.Security.KeyPassphrase"); ?>" id="network_password" name="network_password" />
        </div>
            
        <div class="form-row form-btn">
            <input id="submit_config" type="button" value="Save" class="btn" />
<!--      <a href="range_extenders.php" class="btn" title="">edit</a> -->
            <a href="range_extenders.php" class="btn alt" title="">Cancel</a>
        </div>
    </div>

</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
