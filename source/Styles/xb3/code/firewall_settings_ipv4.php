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
<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
var o_disableFwForTSI = <?php echo (getStr('Device.X_CISCO_COM_Security.Firewall.TrueStaticIpEnable') === 'true') ? 'true' : 'false';?>;
$(document).ready(function() {
    gateway.page.init("Gateway > Firewall > IPv4", "nav-firewall-ipv4");
    function keyboard_toggle(){
    	//var $link = $("#security-level label");
    	var $link = $("input[name='firewall_level']");
		var $div = $("#security-level .hide");
		// toggle slide		
		$($link).keypress(function(ev) {
	    	var keycode = (ev.keyCode ? ev.keyCode : ev.which);
	        if (keycode == '13') {
	        	//e.preventDefault();
				$(this).siblings('.hide').slideToggle();
	        }
    	});
    }
    keyboard_toggle();	
    /*
     * Toggles Custom Security Checkboxes based on if the Custom Security is selected or not
     */
    $("input[name='firewall_level']").change(function() {
        if($("input[name='firewall_level']:checked").val() == 'custom') {
            $("#custom .target").removeClass("disabled").prop("disabled", false);
        } else {
            $("#custom .target").addClass("disabled").prop("disabled", true);
        }
    }).trigger("change");
	$("#disable_firewall").change(function(){
		if($("#disable_firewall").prop("checked")) {
			var message = "<?php echo _('You are trying to disable the firewall. It is a security risk. Are you sure you want to continue?')?>";
            jConfirm(
                message
                ,"<?php echo _('Are you sure?')?>"
                ,function(ret) {
                    if(ret) {
						$("#block_http").prop("disabled",true).attr('checked', false);
						$("#block_icmp").prop("disabled",true).attr('checked', false);
						$("#block_multicast").prop("disabled",true).attr('checked', false);
						$("#block_peer").prop("disabled",true).attr('checked', false);
						$("#block_ident").prop("disabled",true).attr('checked', false);
                    }  
                    else
                    {
                    	$("#disable_firewall").prop('checked', false);
                    }  
                });
		}
		else {
			$("#block_http").prop("disabled",false);
			$("#block_icmp").prop("disabled",false);
			$("#block_multicast").prop("disabled",false);
			$("#block_peer").prop("disabled",false);
			$("#block_ident").prop("disabled",false);
		}
	});
	if($("#disable_firewall").prop("checked")) {
		$("#block_http").prop("disabled",true).attr('checked', false);
		$("#block_icmp").prop("disabled",true).attr('checked', false);
		$("#block_multicast").prop("disabled",true).attr('checked', false);
		$("#block_peer").prop("disabled",true).attr('checked', false);
		$("#block_ident").prop("disabled",true).attr('checked', false);
	}
	else {
		$("#block_http").prop("disabled",false);
		$("#block_icmp").prop("disabled",false);
		$("#block_multicast").prop("disabled",false);
		$("#block_peer").prop("disabled",false);
		$("#block_ident").prop("disabled",false);
	}
    /*
     * Confirm dialog for restore to factory settings. If confirmed, the hiddin field (restore_factory_settings) is set to true
     */
    $("#restore-default-settings").click(function(e) {
        e.preventDefault();
        var currentSetting = $("input[name=firewall_level]:checked").parent().find("label:first").text();
        jConfirm(
            "<?php echo _('The firewall security level is currently set to')?> " +  currentSetting + ". <?php echo _('Are you sure you want to change to default settings?')?>"
            ,"<?php echo _('Reset Default Firewall Settings')?>"
            ,function(ret) {
                if(ret) {
                	$("#firewall_level_maximum").prop("checked",false);
                    $("#firewall_level_minimum").prop("checked",true);
					var firewallLevel = "Low";
					var firewallCfg = '{"firewallLevel": "' + firewallLevel + '"}';
				   // alert(firewallCfg);
					setFirewall(firewallCfg);
                }
            });
    });
    $('#submit_firewall').click(function(){
        var firewallLevel = "None";        
        var level1 = document.getElementById('firewall_level_maximum');
        if (level1.checked) { 
            firewallLevel = "High";
        }
        var level2 = document.getElementById('firewall_level_typical');
        if (level2.checked) { 
            firewallLevel = "Medium";
        }
        var level3 = document.getElementById('firewall_level_minimum');
        if (level3.checked) { 
            firewallLevel = "Low";
        }
        var level4 = document.getElementById('firewall_level_custom');
        if (level4.checked) { 
            firewallLevel = "Custom";
        }
        var blockHttp = "Disabled"; 
        var blockIcmp = "Disabled"; 
        var blockMulticast = "Disabled"; 
        var blockPeer  = "Disabled"; 
        var blockIdent = "Disabled"; 
        var obj1 = document.getElementById('block_http');
        if (obj1.checked) { 
            blockHttp = "Enabled";
        }
        var obj2 = document.getElementById('block_icmp');
        if (obj2.checked) { 
            blockIcmp = "Enabled";
        }
        var obj3 = document.getElementById('block_multicast');
        if (obj3.checked) { 
            blockMulticast = "Enabled";
        }
        var obj4 = document.getElementById('block_peer');
        if (obj4.checked) { 
            blockPeer = "Enabled";
        }
        var obj5 = document.getElementById('block_ident');
        if (obj5.checked) { 
            blockIdent = "Enabled";
        }
        var obj6 = document.getElementById('disable_firewall');
        if (obj6.checked) { 
            if (firewallLevel == "Custom") {
                firewallLevel = "None";
            }
        }
        var firewallCfg = '{"firewallLevel": "' + firewallLevel + '", "block_http": "' + blockHttp + '", "block_icmp": "' + blockIcmp +
                                 '", "block_multicast": "' + blockMulticast + '", "block_peer": "' + blockPeer + '", "block_ident": "' + blockIdent + '"} ';
       // alert(firewallCfg);
        setFirewall(firewallCfg);
    });
    function setFirewall(configuration){
		jProgress('<?php echo _('This may take several seconds...')?>', 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_firewall_config.php",
			data: { configInfo: configuration },
			success: function(){            
				jHide();	
				location.reload();
			},
			error: function(){            
				jHide();
				jAlert("<?php echo _('Failure, please try again.')?>");
			}
		});
    }
});
</script>
<div id="content">
    <h1><?php echo _('Gateway > Firewall > IPv4')?></h1>
	<div id="educational-tip">
		<p class="tip"><?php echo _('Manage your firewall settings.')?></p>
		<p class="hidden"><?php echo _('Select a security level for details. If you\'re unfamiliar with firewall settings, keep the default security level, Minimum Security (Low).')?></p>
		<p class="hidden"><?php echo _('<strong>Maxium Security (High):</strong> Blocks all applications, including voice applications (such as Gtalk, Skype) and P2P applications, but allows Internet, email, VPN, DNS, and iTunes services.')?></p>
		<p class="hidden"><?php echo _('<strong>Typical Security (Medium):</strong> Blocks P2P applications and pings to the Gateway, but allows all other traffic.')?></p>
		<p class="hidden"><?php echo _('<strong>Minimum Security (Low):</strong> No application or traffic is blocked. (Default setting)')?></p>
		<p class="hidden"><?php echo _('<strong>Custom security:</strong> Block specific services.')?></p>
	</div>
    <div class="module">
		<form id="pageForm">
		<input type="hidden" name="restore_factory_settings" id="restore_factory_settings" value="false" />
		<h2><?php echo _('Firewall Security Level')?></h2>
		<?php 
            $firewall_param = array(
                "SecurityLevel"     => "Device.X_CISCO_COM_Security.Firewall.FirewallLevel",
                "block_http"        => "Device.X_CISCO_COM_Security.Firewall.FilterHTTP",
                "block_icmp"        => "Device.X_CISCO_COM_Security.Firewall.FilterAnonymousInternetRequests",
                "block_multicast"   => "Device.X_CISCO_COM_Security.Firewall.FilterMulticast",
                "block_peer"        => "Device.X_CISCO_COM_Security.Firewall.FilterP2P",
                "block_ident"       => "Device.X_CISCO_COM_Security.Firewall.FilterIdent",
            );
            $firewall_value = KeyExtGet("Device.X_CISCO_COM_Security.Firewall.", $firewall_param);
			$SecurityLevel = $firewall_value["SecurityLevel"]; //getStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevel");	
            $block_http = $firewall_value["block_http"];
            $block_icmp = $firewall_value["block_icmp"];
            $block_multicast = $firewall_value["block_multicast"];
            $block_peer = $firewall_value["block_peer"];
            $block_ident = $firewall_value["block_ident"];
            $videoServiceEnable = getStr("Device.X_RDKCENTRAL-COM_VideoService.Enabled");
            $videoDisable = ($videoServiceEnable=="true") ? "disabled" : "";
		?>
		<ul class="combo-group" id="security-level">
			<li id="max">
				<input type="radio" name="firewall_level" value="high" id="firewall_level_maximum" <?php if ( !strcasecmp("High", $SecurityLevel)) echo "checked"; echo $videoDisable; ?>/>
				<label for="firewall_level_maximum" class="label"><?php echo _('Maximum Security (High)')?>
				</label>
				<?php
					if($videoServiceEnable=="true")
						echo '<p class="error"> '._("Video Service only works in this setting.").' </p>';
				?>
				<div class="hide">
					<p><?php echo _('<strong>LAN-to-WAN:</strong> Allow as per below.')?></p>
					<dl>
					<dd><?php echo _('HTTP and HTTPS (TCP port 80, 443)')?></dd>
					<dd><?php echo _('DNS (TCP/UDP port 53)')?></dd>
					<dd><?php echo _('NTP (TCP port 119, 123)')?></dd>
					<dd><?php echo _('email (TCP port 25, 110, 143, 465, 587, 993, 995)')?></dd>
					<dd><?php echo _('VPN (GRE, UDP 500, 4500, 62515, TCP 1723)')?></dd>
					<dd><?php echo _('iTunes (TCP port 3689)')?></dd>
					</dl>
					<p><?php echo _('<strong>WAN-to-LAN:</strong> Block all unrelated traffic and enable IDS.')?></p>
				</div>
			</li>
			<li id="medium">
				<input type="radio" name="firewall_level" value="medium" id="firewall_level_typical" <?php if ( !strcasecmp("Medium", $SecurityLevel)) echo "checked"; ?> />
				<label for="firewall_level_typical" class="label"><?php echo _('Typical Security (Medium)')?></label>
				<div class="hide">
					<p><?php echo _('<strong>LAN-to-WAN:</strong> Allow all.')?></p>
					<p><?php echo _('<strong>WAN-to-LAN:</strong> Block as per below and enable IDS.')?></p>
					<dl>
					<dd><?php echo _('IDENT (port 113)')?></dd>
					<dd><?php echo _('ICMP request')?></dd>
					<dd>
					<dl>
					<dt><?php echo _('Peer-to-peer apps:')?></dt>
					<dd><?php echo _('kazaa - (TCP/UDP port 1214)')?></dd>
					<dd><?php echo _('bittorrent - (TCP port 6881-6999)')?></dd>
					<dd><?php echo _('gnutella - (TCP/UDP port 6346)')?></dd>
					<dd><?php echo _('vuze - (TCP port 49152-65534)')?></dd>
					</dl>
					</dd>
					</dl>
				</div>
			</li>
			<li id="low">
				<input type="radio" name="firewall_level" value="low" id="firewall_level_minimum" <?php if ( !strcasecmp("Low", $SecurityLevel)) echo "checked"; ?>  />
				<label for="firewall_level_minimum" class="label"><?php echo _('Minimum Security (Low)')?></label>
				<div class="hide">
					<p><?php echo _('<strong>LAN-to-WAN:</strong> Allow all.')?></p>
					<p><?php echo _('<strong>WAN-to-LAN:</strong> Block as per below and enable IDS')?></p>
					<dl>
					<dd><?php echo _('IDENT (port 113)')?></dd>
					</dl>
				</div>
			</li>
			<li id="custom">
				<input class="trigger" type="radio" name="firewall_level" value="custom" id="firewall_level_custom" 
				<?php if (( !strcasecmp("Custom", $SecurityLevel)) || ( !strcasecmp("None", $SecurityLevel))) echo "checked"; ?> />
				<label for="firewall_level_custom" class="label"><?php echo _('Custom Security')?></label>
				<div class="hide">
				<p><?php echo _('<strong>LAN-to-WAN :</strong> Allow all.')?></p>
				<p><?php echo _('<strong>WAN-to-LAN :</strong> IDS Enabled and block as per selections below.')?></p>
				<p class="target disabled">
				<input class="target disabled"  type="checkbox" id="block_http" name="block_http" 
				<?php if ( !strcasecmp("true",  $block_http)) echo "checked"; ?> /> 
				<label for="block_http"><?php echo _('Block http (TCP port 80, 443)')?></label><br />
				<input class="target disabled"  type="checkbox" id="block_icmp" name="block_icmp"
				<?php if ( !strcasecmp("true", $block_icmp )) echo "checked"; ?> />
				<label for="block_icmp"><?php echo _('Block ICMP')?></label><br />
				<input class="target disabled"  type="checkbox" id="block_multicast" name="block_multicast"
				<?php if ( !strcasecmp("true", $block_multicast)) echo "checked"; ?> /> 
				<label for="block_multicast"><?php echo _('Block Multicast')?></label><br />
				<input class="target disabled"  type="checkbox" id="block_peer" name="block_peer" 
				<?php if ( !strcasecmp("true", $block_peer)) echo "checked"; ?>  /> 
				<label for="block_peer"><?php echo _('Block Peer-to-peer applications')?></label><br />
				<input class="target disabled" type="checkbox" id="block_ident" name="block_ident" 
				<?php if ( !strcasecmp("true", $block_ident)) echo "checked"; ?>  /> 
				<label for="block_ident"><?php echo _('Block IDENT (port 113)')?></label><br />
				<input class="target disabled" type="checkbox" id="disable_firewall" name="disable_firewall" 
				<?php if ( !strcasecmp("None", $SecurityLevel)) echo "checked"; ?>   />
				<label for="disable_firewall"><?php echo _('Disable entire firewall')?></label>
				</p>
				</div>
			</li>
		</ul>
		<div class="form-btn"> 
			<input id="submit_firewall"  type="button" value="<?php echo _('Save Settings')?>" class="btn" />
			<input id="restore-default-settings" type="button" value="<?php echo _('Restore Default Settings')?>" class="btn alt" />
		</div>
		</form>
    </div> <!-- end .module -->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
