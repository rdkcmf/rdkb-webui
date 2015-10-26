<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: managed_services.php 2943 2009-08-25 20:58:43Z slemoine $ -->

<div  id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php 
	$ret = init_psmMode("Parental Control > Managed Services", "nav-services");
	if ("" != $ret){echo $ret;	return;}
?>

<?php
$enableMS = getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.Enable");
if ($_DEBUG) {
	$enableMS = "true";
}
// $enableMS = "false";
//add by shunjie
("" == $enableMS) && ($enableMS = "false");

?>

<script  type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Parental Control > Managed Services", "nav-services");
	
	jsEnableMS = <?php echo $enableMS ?>;

	$("#managed_services_switch").radioswitch({
		id: "managed-services-switch",
		radio_name: "managed_services",
		id_on: "managed_services_enabled",
		id_off: "managed_services_disabled",
		title_on: "Enable managed services",
		title_off: "Disable managed services",
		state: jsEnableMS ? "on" : "off"
	});

	$("span[id^=trusted_user_]").each(function(){
		var $this = $(this);
		var idx = this.id.match(/trusted_user_(.+)$/)[1];
		var val = $this.attr("switch-val");

		$this.radioswitch({
			id: "trusted-user-"+idx,
			radio_name: "device_trusted-"+idx,
			id_on: "device_trusted-"+idx,
			id_off: "device_notrusted-"+idx,
			title_on: "Select trust",
			title_off: "Select untrust",
			size: "small",
			label_on: "Yes",
			label_off: "No",
			revertOrder: true,
			state: val
		}).change(function(){
    		var trustFlag = $(this).radioswitch("getState").on;
    		//alert(trustFlag);	
			jProgress('This may take several seconds', 60);
			$.ajax({                  
				type: "POST",
				url: "actionHandler/ajaxSet_trust_computer_service.php",
				data: { TrustFlag: '{"trustFlag": "'+trustFlag+'", "HostName": "'+hostNameArr[idx-1]+'", "IPAddress": "'+ipAddrArr[idx-1]+'"}' },
				success: function(){            
					jHide();
					//window.location.href = "managed_services.php";
				},
				error: function(){
					jHide();
					jAlert("Failure, please try again.");
				}
			});
    	});
	});

    $("a.confirm").unbind('click');
	
	$(".btn").click(function (e) {
		e.preventDefault();
		if ($(this).hasClass('disabled')) {
			return false; // Do something else in here if required
		}
		else
		{
			var btnHander = $(this);
			if (btnHander.attr("id").indexOf("delete")!=-1)	{
				jConfirm(
					"Are you sure you want to delete this service?"
					,"Are You Sure?"
					,function(ret) {
						if(ret) {
							window.location.href = btnHander.attr('href');
						}
					}
				);
			}
			else {
				window.location.href = $(this).attr('href');
			}
		}
	});
	
	// only run once on init
	if (false == jsEnableMS)
	{
		$('.main_content *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
		$(".main_content .radioswitch_cont:not(#managed_services_switch)").radioswitch("doEnable", false);
	}	
	
 // If Enable UPnP is not checked, disable the next two form fields
	$("#managed_services_switch").change(function() {
		var UMSStatus = $("#managed_services_switch").radioswitch("getState").on ? "Enabled" : "Disabled";
//		var UMSStatus = $("#managed_services_enabled").is(":checked");
		jProgress('This may take several seconds', 60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_managed_services.php",
			data:{set:"true",UMSStatus:UMSStatus},
			success:function(results){
				//jAlert(results);
				jHide();
				if (UMSStatus!=results){ 
					jAlert("Could not do it!");
					$("#managed_services_switch").radioswitch("doSwitch", results === 'Enabled' ? 'on' : 'off');
				}
				var isUMSDisabled = $("#managed_services_switch").radioswitch("getState").on === false;
				if(isUMSDisabled){
					// $("#managed-services-items").prop("disabled",true).addClass("disabled");
					$('.main_content *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
					$(".main_content .radioswitch_cont:not(#managed_services_switch)").radioswitch("doEnable", false);
				}else{
					// $("#managed-services-items").prop("disabled",false).removeClass("disabled");
					$('.main_content *').not(".radioswitch_cont, .radioswitch_cont *").removeClass("disabled");
					$(".main_content .radioswitch_cont:not(#managed_services_switch)").radioswitch("doEnable", true);
				}
			},
			error:function(){
				jHide();
				jAlert("Failure, please try again.");
			}
		});
	});
	
	$("ul[ name='trust-user-switch']").change(function(event, data) {
		var target = event.target; 
//		alert(target.getAttribute("id")+";"+data);
		var ID = target.getAttribute("id");
		var status;
		if(data == "yes")
			status = "true";
		else if(data == "no")
			status = "false";
//		alert(ID+";"+status);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_managed_services.php",
			data:{trust_not:"true",ID:ID,status:status},
			success:function(results){
				jAlert(results);
/*				if (UMSStatus!=results){ 
					jAlert("Could not do it!");
					$("input[ name='managed_services']").each(function(){
						if($(this).val()==results){$(this).parent().addClass("selected");$(this).prop("checked",true);}
						else{$(this).parent().removeClass("selected");$(this).prop("checked",false);}
					});
				}*/
				
			}
		});
	});
});



</script>

<div  id="content" class="main_content">


	<h1>Parental Control > Managed Services</h1>


	<div  id="educational-tip">
        <p class="tip">Manage access to specific services and applications by network devices.</p>
		<p class="hidden">Select <strong>Enable</strong> to manage services and applications, or <strong> Disable</strong>  to turn off.</p>
		<p class="hidden"><strong>+ADD:</strong> Add to block a new service or application.</p>
		<p class="hidden">The Gateway will block services and applications on all untrusted computers, based on the specified rules. If you don't want restrictions for a particular computer, select <strong>Yes</strong> under <strong>Trusted Computers</strong>.</p>
    </div>

	<div class="module">
		<div class="select-row">
		<span class="readonlyLabel label">Managed Services:</span>
		<span id="managed_services_switch"></span>
		</div>
	</div>

	<div  id="managed-services-items">
	<div class="module data">
		<h2>Blocked Services</h2>
		<p class="button"><a tabindex='0' href="managed_services_add.php"  id="add-blocked-services" class="btn">+ Add</a></p>
		<table  id="blocked-services" class="data" summary="This table lists available managed services">
	    <tr>
            <th id='service-number' class="number"></th>
            <th id='service-name' class="services">Services</th>
	    	<th id='protocol-type' class="type">TCP/UDP</th>
            <th id='start-port' class="port">Starting Port</th>
            <th id='end-port' class="port">Ending Port</th>
            <th id='effect-time' class="when">When</th>
            <th id='edit-button' class="edit">&nbsp;</th>
            <th id='delete-button' class="delete">&nbsp;</th>
	    </tr>
	    <?php 
             	$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedServices.Service.";
	          	$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedServices.Service.");
	           	$mapping_array  = array("Protocol", "AlwaysBlock", "Description", "StartPort", "EndPort","StartTime", "EndTime", "BlockDays");
		   		$blockedServicesInstance = array();
	           	$blockedServicesInstanceArr = getParaValues($rootObjName, $paramNameArray, $mapping_array);

				//TrustedUser
				$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.";
				$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedServices.TrustedUser.");
				$mapping_array  = array("IPAddress", "Trusted");

				$TrustedUser = getParaValues($rootObjName, $paramNameArray, $mapping_array);

				//Host
				$rootObjName    = "Device.Hosts.Host.";
				$paramNameArray = array("Device.Hosts.Host.");
				$mapping_array  = array("HostName", "PhysAddress", "IPAddress", "IPv6Address.1.IPAddress");

				$HostParam = getParaValues($rootObjName, $paramNameArray, $mapping_array);


				$num=getStr("Device.X_Comcast_com_ParentalControl.ManagedServices.ServiceNumberOfEntries");
				if($num!=0) {
					$MSIDs=explode(",",getInstanceIDs("Device.X_Comcast_com_ParentalControl.ManagedServices.Service."));
					$iclass="even";
					$j = 0;
					foreach ($MSIDs as $key=>$i) {
						$blockedServicesInstance["$i"] = $blockedServicesInstanceArr["$key"];
					}
					foreach ($MSIDs as $key=>$i) {
						$j += 1;
						if ($iclass=="even") {$iclass="odd";} else {$iclass="even";}
						$protocol = $blockedServicesInstance["$i"]["Protocol"];
						if($protocol == "BOTH")
							$protocol = "TCP/UDP";
						$blockStatus = $blockedServicesInstance["$i"]["AlwaysBlock"];
						if($blockStatus == "true")
							$blockStatus = "Always";
						else if($blockStatus == "false") {
							//$blockStatus = "Period";
							$stime = $blockedServicesInstance["$i"]["StartTime"];
							$etime = $blockedServicesInstance["$i"]["EndTime"];
							$bdays = $blockedServicesInstance["$i"]["BlockDays"];
					
					        $blockStatus = $stime."-".$etime.",".$bdays;
						}

						echo "
					<tr class=$iclass>
						<td headers='service-number' class=\"row-label alt number\">$j</td>
						<td headers='service-name'>".$blockedServicesInstance["$i"]["Description"]."</td>
						<td headers='protocol-type'>".$protocol."</td>
						<td headers='start-port'>".$blockedServicesInstance["$i"]["StartPort"]."</td>
						<td headers='end-port'>".$blockedServicesInstance["$i"]["EndPort"]."</td>
						<td headers='effect-time'>".$blockStatus."</td>
						<td headers='edit-button' class=\"edit\"><a tabindex='0' href=\"managed_services_edit.php?id=$i\" class=\"btn\"  id=\"edit_$i\">Edit</a></td>
						<td headers='delete-button' class=\"delete\"><a tabindex='0' href=\"actionHandler/ajax_managed_services.php?del=$i\" class=\"btn confirm\" title=\"delete this service for ".$blockedServicesInstance["$i"]["Description"]." \" id=\"delete_$i\">x</a></td>
					</tr>"; 
					} 
				}
		?>

	       <tfoot>
				<tr class="acs-hide">
					<td headers="service-number">null</td>
					<td headers="service-name">null</td>
					<td headers="protocol-type">null</td>
					<td headers="start-port">null</td>
					<td headers="end-port">null</td>
					<td headers="effect-time">null</td>
					<td headers="edit-button">null</td>
					<td headers="delete-button">null</td>
				</tr>
			</tfoot>

		</table>
	</div> <!-- end .module -->

	
		<form action="managed_services.php" method="post">
			<input  type="hidden"  name="update_trusted_computers"  value="true" />

		<?php
			$hostsInstance = getInstanceIds("Device.Hosts.Host.");
			$hostsInstanceArr = explode(",", $hostsInstance);

			$hostNums = getStr("Device.Hosts.HostNumberOfEntries");

			$ipAddrArr = array();
			$HostNameArr = array();

			for ($i=0; $i < $hostNums; $i++) { 
            
				$HostName = $HostParam[$i]["HostName"];
		        if (($HostName == "*") || (strlen($HostName) == 0)) {
		            $Host["$i"]['HostName'] = $HostParam[$i]["PhysAddress"];
		        }
		        else {
					$Host["$i"]['HostName'] = $HostName;
		        }

			$Host["$i"]['IPAddress'] = $HostParam[$i]["IPAddress"];
			$IPAddress = $HostParam["$i"]['IPAddress'];
			//$IPv4Address	= getStr("Device.Hosts.Host." .$hostsInstanceArr["$i"]. ".IPv4Address.1.IPAddress");
			$IPv6Address	= $HostParam[$i]["IPv6Address.1.IPAddress"];
			
			//for now as "Device.Hosts.Host.'$i'.IPv4Address.1.IPAddress" is not updating on GW_IP Change
			$IPv4Address = $IPAddress;

			//In IPv6 only mode, IPv4=NA
			if( strpos($IPv4Address, '.') === false ) $IPv4Address = 'NA';

			if (substr($IPv6Address, 0, 5) == "2001:") {
				$Host["$i"]['IPShow'] = $IPv4Address.'/'.$IPv6Address;
			}
			else {
				//If IPv6 is not global then IPv6=NA
				$Host["$i"]['IPShow'] = $IPv4Address.'/NA';
			}

				array_push($HostNameArr, $Host["$i"]['HostName']);
				array_push($ipAddrArr, $Host["$i"]['IPAddress']);
                $Host["$i"]['Trusted'] = false;
                foreach( $TrustedUser as $key => $value ){
                    if ( $value['IPAddress'] == $Host["$i"]['IPAddress']){

                        $Host["$i"]['Trusted'] = $value['Trusted']; 
                        break;
                    }
                }
			}
			if ($_DEBUG) {
				$hostNums = 2;
				$Host["0"] = array("HostName"=>"host1", "IPAddress"=>"1.1.1.1", "Trusted"=>false);
				$Host["1"] = array("HostName"=>"host2", "IPAddress"=>"2.2.2.2", "Trusted"=>true);
				$HostNameArr = array("host1", "host2");
				$ipAddrArr = array("1.1.1.1", "2.2.2.2");
			}
			
		 ?>

			<div class="module data">
				<h2>Trusted Computers</h2>
				<table  id="trusted_computers" class="data" summary="This table allows you to set trusted or untrusted devices for above managed services">
					<tr>
						<th id="number" class="number">&nbsp;</th>
						<th id="device-name" class="computer_name">Computer Name</th>
						<th id="IP" class="ip">IP</th>
						<th id="trusted-or-not" class="trusted">Trusted</th>
					</tr>

					<?php 
					for ($i=0,$k=1; $i < $hostNums; $i++,$k++) {

						if($k % 2) $odd = "class='odd'";
						else $odd = "";
						echo "<tr $odd>
						<td headers='number' class=\"row-label alt\">" .$k. "</td>
						<td headers='device-name'  id='HostName-" .$k. "' >" .$Host["$i"]['HostName']. "</td>
						<td headers='IP'  id='IPAddress-" .$k. "' >" .$Host["$i"]['IPShow']. "</td>
						<td headers='trusted-or-not' style='min-width: 92px;'>
							<span id=\"trusted_user_".$k."\" switch-val=\"".( $Host["$i"]['Trusted'] == 'true' ? "on" : "off" )."\"></span>
						</td>
						</tr>
						";
					}
					?>

				<tfoot>
					<tr class="acs-hide">
						<td headers="number">null</td>
						<td headers="device-name">null</td>
						<td headers="IP">null</td>
						<td headers="trusted-or-not">null</td>						
					</tr>
				</tfoot>

				</table>
			</div> <!-- end .module -->
		</form><!--end trusted computers -->
	
	</div>


</div><!-- end #content -->

<script  type="text/javascript">
	var hostNums = '<?php echo $hostNums; ?>';
	var hostNameArr = <?php echo json_encode($HostNameArr); ?>;
	var ipAddrArr = <?php echo json_encode($ipAddrArr); ?>;
</script>

<?php include('includes/footer.php'); ?>
