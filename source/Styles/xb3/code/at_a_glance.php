<?php include('includes/header.php'); ?>
<!-- $Id: at_a_glance.dory.php 2943 2009-08-25 20:58:43Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
<?php include('includes/utility.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
	comcast.page.init("Gateway > At a Glance", "nav-at-a-glance");
	
	/*
	** view management: if admin login, remove brige mdoe part
	*/
	/* New requirements: Enable/Disable should be available for all users
	var login_user = "<?php echo $_SESSION["loginuser"]; ?>";

    if(login_user == "admin") {
    	$('.div-bridge').remove();
    }*/

	<?php $bridge_mode = getStr("Device.X_CISCO_COM_DeviceControl.LanManagementEntry.1.LanMode"); ?>

	$("#bridge_switch").radioswitch({
		id: "at-a-glance-switch",
		radio_name: "at_a_glance",
		id_on: "at_a_glance_enabled",
		id_off: "at_a_glance_disabled",
		title_on: "Enable bridge mode",
		title_off: "Disable bridge mode",
		state: "<?php echo ($bridge_mode != 'router' ? "on" : "off"); ?>"
	});
	
	function changeBridge(isBridgeModelEnable) {
		var cnt = 90;
		jProgress('Waiting for fully applied in <b id="cnt">' + cnt + '</b> seconds, please be patient...', 600);
		$.ajax({
		type:"POST",
		url:"actionHandler/ajax_at_a_glance.php",
		data:{Bridge:"true",isBridgeModel:isBridgeModelEnable},
		success:function(result){
				// don't do jHide, with latest firmware, switch bridge will return before httpd restart, GUI reload at return will cause 500 error. So wait until http restored. 
				var hCnt = setInterval(function(){
					$("#cnt").text(cnt--);
					if (cnt < 0) {clearInterval(hCnt); location.reload();}
				}, 1000);					
			}
		});
	}

	$("#bridge_switch").change(function(){
		var isBridgeModelEnable = $("#bridge_switch").radioswitch("getState").on ? "Enabled" : "Disabled";
		//the 200ms timer is only used to fix confirm dialogue not shown issue on IE
		if ('Enabled' == isBridgeModelEnable) {
			setTimeout(function(){
				jConfirm(
				"Enabling Bridge Mode will disable Router functionality of Gateway and turn off the private Wi-Fi network. Are you sure you want to Continue?"
				,"WARNING:"
				,function(ret) {
					if(ret) {
						changeBridge(isBridgeModelEnable);
					} //end of if ret
					else {
						$("#bridge_switch").radioswitch("doSwitch", "off");
					}
				});//end of jConfirm

			}, 200);
		} //end of if Enabled
		else {
			changeBridge(isBridgeModelEnable);
		}
	});
		
	$("#IGMP_snooping_switch").change(function(){
		var IGMPEnable=$("input[name='IGMP_snooping']:radio:checked").val();
		jProgress('This may take several seconds', 60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_at_a_glance.php",
			data:{IGMP:"true",IGMPEnable:IGMPEnable},
			success:function(results){
				jHide();
				if(IGMPEnable!=results){
					jAlert("Could not do it!");
					$("input[name='IGMP_snooping']").each(function(){
						//alert($(this).val());alert(result);
						if($(this).val()==results){$(this).parent().addClass("selected");$(this).prop("checked",true);}
						else{$(this).parent().removeClass("selected");$(this).prop("checked",false);}
					});
				}
			}
		});
	});
});

function popUp(URL) {
	day = new Date();
	id = day.getTime();
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=700,height=400,left = 320.5,top = 105');");
}

</script>
<div id="content">
	<h1>Gateway > At a Glance</h1>

	<div id="educational-tip">
		<p class="tip">Summary of your network and connected devices.</p>
		<p class="hidden"> Select <strong>VIEW CONNECTED DEVICES </strong>to manage devices connected to your network.</p>
	</div>
	
	<div class="module div-bridge">
		<div class="select-row">
			<span class="readonlyLabel label">Bridge Mode:</span>
			<span id="bridge_switch"></span>
		</div>
	</div>

	<div class="module forms">
		<input type="button" onClick="javascript:popUp('at_downloading.php')" class="btn" value="Save Current Configuration"/>
		<input type="button" onClick="javascript:popUp('at_mycomputer.php')" class="btn" value="Restore Saved Configuration"/>
	</div>
	<div class="module block" id="home-network">
		<div>
			<h2>Home Network</h2>
			<?php
			if ("Disabled"==$_SESSION["psmMode"]) {
				/*
				$InterfaceNumber=getStr("Device.Ethernet.InterfaceNumberOfEntries");$InterfaceEnable=0;
				for($i=1;$i<=$InterfaceNumber;$i++){
					$EthernetEnable=getStr("Device.Ethernet.Interface.".$i.".Enable");
					$InterfaceEnable+=($EthernetEnable=="true"?1:0);
				}
				if ($InterfaceEnable==$InterfaceNumber) {
					echo "<div class=\"form-row\"><span class=\"on-off\">On</span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				} else {
					echo "<div class=\"form-row off\"><span class=\"on-off\">Off</span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				}*/
				
				$ids = explode(",", getInstanceIds("Device.Ethernet.Interface."));
				$ethEnable = false;

				foreach ($ids as $i){
					if ("true" == getStr("Device.Ethernet.Interface.".$i.".Enable")){
						$ethEnable = true;
						break;
					}
				}

				if ($ethEnable) {
					echo "<div class=\"form-row\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Ethernet On' /></span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				} else {
					echo "<div class=\"form-row off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Ethernet Off' /></span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				}

				// if (getStr("Device.WiFi.SSID.1.Enable")=="true" || getStr("Device.WiFi.SSID.2.Enable")=="true") {
				if ("true" == $sta_wifi) {		// define in userhar, should have defined every componet status in userbar
					echo "<div class=\"form-row odd\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='WiFi On' /></span> <span class=\"readonlyLabel\">Wi-Fi</span></div>";
				} else {
					echo "<div class=\"form-row odd off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='WiFi Off' /></span> <span class=\"readonlyLabel\">Wi-Fi</span></div>";
				}
		
				if (getStr("Device.MoCA.Interface.1.Enable")=="true") {
					echo "<div class=\"form-row\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='MoCA On' /></span> <span class=\"readonlyLabel\">MoCA</span></div>";
				} else {
					echo "<div class=\"form-row off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='MoCA Off' /></span> <span class=\"readonlyLabel\">MoCA</span></div>";
				}
			}
			else {				
				echo "<div class=\"form-row off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Ethernet Off' /></span> <span class=\"readonlyLabel\">Ethernet</span></div>";
				echo "<div class=\"form-row odd off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='WiFi Off' /></span> <span class=\"readonlyLabel\">Wi-Fi</span></div>";
				echo "<div class=\"form-row off\"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='MoCA Off' /></span> <span class=\"readonlyLabel\">MoCA</span></div>";
			}
			?>
			<div class="form-row odd">
				<span class="readonlyLabel">Firewall Security Level:</span> <span class="value"><?php echo getStr("Device.X_CISCO_COM_Security.Firewall.FirewallLevel")?></span>
			</div>
		</div>
	</div> <!-- end .module -->
	
	<div id="internet-usage" class="module block">
		<h2>Connected Devices</h2>
		<?php
		if ("Disabled"==$_SESSION["psmMode"]) {

			$rootObjName    = "Device.Hosts.Host.";
			$paramNameArray = array("Device.Hosts.Host.");
			$mapping_array  = array("PhysAddress", "HostName", "Active");

			$HostIndexArr = DmExtGetInstanceIds("Device.Hosts.Host.");
			if(0 == $HostIndexArr[0]){  
				// status code 0 = success   
				$HostNum = count($HostIndexArr) - 1;
			}

			if(!empty($HostNum)){

				$Host = getParaValues($rootObjName, $paramNameArray, $mapping_array);
				//this is to construct host info array
				
				$j = 1;
				if(!empty($Host)){

					foreach ($Host as $key => $value) {
						if (!strcasecmp("true", $value['Active'])) {
							$HostInfo[$j]['HostName']   = $value['HostName'];
							$HostInfo[$j]['Active']     = $value['Active'];
							$HostInfo[$j]['PhysAddress']  = $value['PhysAddress'];
							$j += 1;
						}
					}// end of foreach

					for($i=1; $i<$j; $i++) { 
						
						if( $i%2 ) {$divClass="form-row ";}
							else {$divClass="form-row odd";}

						$HostName = $HostInfo[$i]['HostName'];

						if (($HostName == "*") || (strlen($HostName) == 0)) {
							$HostName = strtoupper($HostInfo[$i]['PhysAddress']);
						}

						echo " 
						   <div class=\" $divClass \"><span class=\"on-off sprite_cont\"><img src=\"./cmn/img/icn_on_off.png\" alt='Host On' /></span> <span class=\"readonlyLabel\">$HostName</span></div>
						";

					}//end of for
				}//end of empty $host
			}//end of if empty $hostnum
			
			echo '<div class="btn-group"><a href="connected_devices_computers.php" class="btn">View Connected Devices</a></div>';
		
		}//end of psmMode condition
		?>
	</div> <!-- end .module -->
	
	<!--div class="module">
		<div class="select-row">
			<span class="readonlyLabel label">IGMP Snooping:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
			<?php
			//$IGMP_mode=getStr("Device.X_CISCO_COM_DeviceControl.IGMPSnoopingEnable");
			$IGMP_mode = "false";
			if ($IGMP_mode=="true") { //or Enabled
			?>
			<ul id="IGMP_snooping_switch" class="radio-btns enable">
				<li>
					<input id="IGMP_snooping_enabled" name="IGMP_snooping" type="radio"  value="Enabled" checked="checked" />
					<label for="IGMP_snooping_enabled" >Enable </label>
				</li>
				<li class="radio-off">
					<input id="IGMP_snooping_disabled" name="IGMP_snooping" type="radio"  value="Disabled" />
					<label for="IGMP_snooping_disabled" >Disable </label>
				</li>
			</ul>
			<?php }else{?>
			<ul id="IGMP_snooping_switch" class="radio-btns enable">
				<li>
					<input id="IGMP_snooping_enabled" name="IGMP_snooping" type="radio"  value="Enabled"/>
					<label for="IGMP_snooping_enabled" >Enable </label>
				</li>
				<li class="radio-off">
					<input id="IGMP_snooping_disabled" name="IGMP_snooping" type="radio"  value="Disabled" checked="checked"/>
					<label for="IGMP_snooping_disabled" >Disable </label>
				</li>
			</ul>
			<?php } ?>
		</div>
	</div-->
	
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
