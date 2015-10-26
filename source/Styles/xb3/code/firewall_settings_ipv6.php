<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: firewall_settings.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
 
<script type="text/javascript" src="./cmn/js/lib/jquery.alerts.progress.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Gateway > Firewall > IPv6", "nav-firewall-ipv6");
    /*
     * Toggles Custom Security Checkboxes based on if the Custom Security is selected or not
     */
	
	//for IPv6
	$("#max, #medium, #low").hide(); 
	$("#default").show();

    $("input[name='firewall_level']").change(function() {
        if($("input[name='firewall_level']:checked").val() == 'Custom') {
            $("#custom .target").removeClass("disabled").prop("disabled", false);
        } else {
            $("#custom .target").addClass("disabled").prop("disabled", true);
        }
    }).trigger("change");
	
	$("#disable_firewall").change(function(){
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
	}).trigger("change");

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
     * Confirm dialog for restore to factory settings. If confirmed, the hiddin field (restore_factory_settings) is set to true
     */

    $("#restore-default-settings").click(function(e) {
        e.preventDefault();

        var currentSetting = $("input[name=firewall_level]:checked").parent().find("label:first").text();

        jConfirm(
            "The firewall security level is currently set to " + currentSetting + ". Are you sure you want the change to default settings?"
            ,"Reset Default Firewall Settings"
            ,function(ret) {
                if(ret) {
                    $("#firewall_level_default").prop("checked",true);
					$('#submit_firewall').click();
                }
            });
    });

    
    $('#submit_firewall').click(function(){
		var firewallLevel	= $("input[name='firewall_level']:checked").val();
        var blockHttp		= $("#block_http").prop("checked"); 
        var blockIcmp		= $("#block_icmp").prop("checked"); 
        var blockMulticast	= $("#block_multicast").prop("checked"); 
        var blockPeer		= $("#block_peer").prop("checked"); 
        var blockIdent		= $("#block_ident").prop("checked"); 

		if ("Custom" == firewallLevel && $("#disable_firewall").prop("checked")){
			firewallLevel = "None";
		}
        
        var firewallCfg = '{"firewallLevel": "' + firewallLevel + '", "block_http": "' + blockHttp + '", "block_icmp": "' + blockIcmp +
                                 '", "block_multicast": "' + blockMulticast + '", "block_peer": "' + blockPeer + '", "block_ident": "' + blockIdent + '"} ';
            
       // alert(firewallCfg);
        setFirewall(firewallCfg);

    });

    function setFirewall(configuration){
		jProgress('This may take several seconds...', 60);
		$.ajax({
			type: "POST",
			url: "actionHandler/ajaxSet_firewall_config_v6.php",
			data: { configInfo: configuration },
			success: function(){            
				jHide();	
				location.reload();
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
    <h1>Gateway > Firewall > IPv6</h1>
	<div id="educational-tip">
		<p class="tip">Manage your firewall settings.</p>
		<p class="hidden">Select a security level for details. If you're unfamiliar with firewall settings, keep the default security level, Minimum Security (Low).</p>
		<p class="hidden"><strong>Typical Security (Default):</strong> Allows all traffic from home network to internet and blocks all unrelated traffic from internet to home network.</p>
		<p class="hidden"><strong>Custom security:</strong> Block specific services as per selection.</p>
	</div>
    <div class="module">
		<form id="pageForm">
		
		<input type="hidden" name="restore_factory_settings" id="restore_factory_settings" value="false" />
		<h2>Firewall Security Level</h2>
		<?php 
            $firewall_param = array(
                "SecurityLevel"     => "Device.X_CISCO_COM_Security.Firewall.FirewallLevelV6",
                "block_http"        => "Device.X_CISCO_COM_Security.Firewall.FilterHTTPV6",
                "block_icmp"        => "Device.X_CISCO_COM_Security.Firewall.FilterAnonymousInternetRequestsV6",
                "block_multicast"   => "Device.X_CISCO_COM_Security.Firewall.FilterMulticastV6",
                "block_peer"        => "Device.X_CISCO_COM_Security.Firewall.FilterP2PV6",
                "block_ident"       => "Device.X_CISCO_COM_Security.Firewall.FilterIdentV6",
            );
            $firewall_value = KeyExtGet("Device.X_CISCO_COM_Security.Firewall.", $firewall_param);
			$SecurityLevel = $firewall_value["SecurityLevel"]; //getStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevel");	
            $block_http = $firewall_value["block_http"];
            $block_icmp = $firewall_value["block_icmp"];
            $block_multicast = $firewall_value["block_multicast"];
            $block_peer = $firewall_value["block_peer"];
            $block_ident = $firewall_value["block_ident"];

			//$SecurityLevel = getStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevelV6");	
		?>
		<ul class="combo-group" id="security-level">
			<li id="max">
				<input type="radio" name="firewall_level" value="High" id="firewall_level_maximum" <?php if ( !strcasecmp("High", $SecurityLevel)) echo "checked"; ?> />
				<label for="firewall_level_maximum" class="label">Maximum Security (High)</label>
				<div class="hide">
					<p><strong>LAN-to-WAN:</strong> Allow as per below.</p>
					<dl>
					<dd>HTTP and HTTPS (TCP port 80, 443)</dd>
					<dd>DNS (TCP/UDP port 53)</dd>
					<dd>NTP (TCP port 119, 123)</dd>
					<dd>email (TCP port 25, 110, 143, 465, 587, 993, 995)</dd>
					<dd>VPN (GRE, UDP 500, TCP 1723)</dd>
					<dd>iTunes (TCP port 3689)</dd>
					</dl>
					<p><strong>WAN-to-LAN:</strong> Block all unrelated traffic and enable IDS.</p>
				</div>
			</li>
			<li id="medium">
				<input type="radio" name="firewall_level" value="Medium" id="firewall_level_typical" <?php if ( !strcasecmp("Medium", $SecurityLevel)) echo "checked"; ?> />
				<label for="firewall_level_typical" class="label">Typical Security (Medium)</label>
				<div class="hide">
					<p><strong>LAN-to-WAN:</strong> Allow all.</p>
					<p><strong>WAN-to-LAN:</strong> Block as per below and enable IDS.</p>
					<dl>
					<dd>IDENT (port 113)</dd>
					<dd>ICMP request</dd>
					<dd>
					<dl>
					<dt>Peer-to-peer apps:</dt>
					<dd>kazaa - (TCP/UDP port 1214)</dd>
					<dd>bittorrent - (TCP port 6881-6999)</dd>
					<dd>gnutella- (TCP/UDP port 6346)</dd>
					<dd>vuze - (TCP port 49152-65534)</dd>
					</dl>
					</dd>
					</dl>
				</div>
			</li>
			<li id="low">
				<input type="radio" name="firewall_level" value="Low" id="firewall_level_minimum" <?php if ( !strcasecmp("Low", $SecurityLevel)) echo "checked"; ?>  />
				<label for="firewall_level_minimum" class="label">Minimum Security (Low)</label>
				<div class="hide">
					<p><strong>LAN-to-WAN:</strong> Allow all.</p>
					<p><strong>WAN-to-LAN:</strong> Block as per below and enable IDS</p>
					<dl>
					<dd>IDENT (port 113)</dd>
					</dl>
				</div>
			</li>
			<!--###### for IPv6 ######-->
			<li id="default">
				<input type="radio" name="firewall_level" value="Default" id="firewall_level_default" <?php if ( !strcasecmp("Default", $SecurityLevel)) echo "checked"; ?>  />
				<label for="firewall_level_default" class="label">Typical Security (Default)</label>
				<div class="hide">
					<p><strong>LAN-to-WAN:</strong> Allow all.</p>
					<p><strong>WAN-to-LAN:</strong> Block all unrelated traffic and enable IDS.</p>
				</div>
			</li>
			<li id="custom">
				<input class="trigger" type="radio" name="firewall_level" value="Custom" id="firewall_level_custom" 
				<?php if (( !strcasecmp("Custom", $SecurityLevel)) || ( !strcasecmp("None", $SecurityLevel))) echo "checked"; ?> />
				<label for="firewall_level_custom" class="label">Custom Security</label>
				<div class="hide">
				<p><strong>LAN-to-WAN :</strong> Allow all.</p>
				<p><strong>WAN-to-LAN :</strong> IDS Enabled and block as per selections below.</p>

				<p class="target disabled">
				<input class="target disabled"  type="checkbox" id="block_http" name="block_http" 
				<?php if ( !strcasecmp("true", $block_http)) echo "checked"; ?> /> 
				<label for="block_http">Block http (TCP port 80, 443)</label><br />

				<input class="target disabled"  type="checkbox" id="block_icmp" name="block_icmp"
				<?php if ( !strcasecmp("true", $block_icmp)) echo "checked"; ?> />
				<label for="block_icmp">Block ICMP</label><br />

				<input class="target disabled"  type="checkbox" id="block_multicast" name="block_multicast"
				<?php if ( !strcasecmp("true", $block_multicast)) echo "checked"; ?> /> 
				<label for="block_multicast">Block Multicast</label><br />

				<input class="target disabled"  type="checkbox" id="block_peer" name="block_peer" 
				<?php if ( !strcasecmp("true", $block_peer)) echo "checked"; ?>  /> 
				<label for="block_peer">Block Peer-to-peer applications</label><br />

				<input class="target disabled" type="checkbox" id="block_ident" name="block_ident" 
				<?php if ( !strcasecmp("true", $block_ident)) echo "checked"; ?>  /> 
				<label for="block_ident">Block IDENT (port 113)</label><br />

				<input class="target disabled" type="checkbox" id="disable_firewall" name="disable_firewall" 
				<?php if ( !strcasecmp("None", $SecurityLevel)) echo "checked"; ?>   />
				<label for="disable_firewall">Disable entire firewall</label>
				</p>
				</div>
			</li>
		</ul>

		<div class="form-btn"> 
			<input id="submit_firewall"  type="button" value="Save Settings" class="btn" />
			<input id="restore-default-settings" type="button" value="Restore Default Settings" class="btn alt" />
		</div>
		</form>

    </div> <!-- end .module -->
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
