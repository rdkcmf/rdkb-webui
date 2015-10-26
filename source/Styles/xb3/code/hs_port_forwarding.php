<?php include('includes/header.php'); ?>

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
	
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
<?php include('includes/utility.php'); ?>
<?php 
	$HPEnable = getStr("Device.NAT.X_Comcast_com_EnableHSPortMapping");
	if ($_DEBUG) {
		$HPEnable = 'true';
	}
?>


<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > HS Port Forwarding", "nav-HS-port-forwarding");

	$("#hspf_switch").radioswitch({
		id: "hs-forwarding-switch",
		radio_name: "hs-forwarding",
		id_on: "hs-forwarding_enabled",
		id_off: "hs-forwarding_disabled",
		title_on: "Enable HS port forwarding",
		title_off: "Disable HS port forwarding",
		state: <?php echo ($HPEnable === "true" ? "true" : "false"); ?> ? "on" : "off"
	});

	$("a.confirm").unbind('click');

	function setupDeleteConfirmDialogs() {
        /*
         * Confirm dialog for delete action
         */             
        	$("a.confirm").click(function(e) {
		    e.preventDefault();            
		    var href = $(this).attr("href");
		    var message = ($(this).attr("title").length > 0) ? "Are you sure you want to " + $(this).attr("title") + "?" : "Are you sure?";
		   
		    jConfirm(
		        message
		        ,"Are You Sure?"
		        ,function(ret) {
		            if(ret) {
		                window.location = href;
		            }    
		     });
        	});
	}

	var isUHSPDisabled = $("#hspf_switch").radioswitch("getState").on === false;
	if(isUHSPDisabled) {
		$("#hs-port-forwarding-items").prop("disabled",true).addClass("disabled");
		$("a.btn").addClass("disabled").click(function(e){e.preventDefault();});
		$("input[name='PortActive']").prop("disabled",true);
	}
	else{
		setupDeleteConfirmDialogs();
	}

	$("#hspf_switch").change(function() {
		var UHSPStatus = $("#hspf_switch").radioswitch("getState").on ? "Enabled" : "Disabled";
		var isUHSPDisabled = $("#hspf_switch").radioswitch("getState").on === false;
		jProgress('This may take several seconds.',60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_hs_port_forwarding.php",
			data:{set:"true",UHSPStatus:UHSPStatus},
			success:function(){
				jHide();
				/*results=eval("("+result+")");
				if (UHSPStatus!=results) {
					jAlert("Backend Error!");
					$("input[name='forwarding']").each(function(){
						if($(this).val()==results){$(this).parent().addClass("selected");$(this).prop("checked",true);}
						else{$(this).parent().removeClass("selected");$(this).prop("checked",false);}
					});
				}*/
				var isUHSPDisabled = $("#hspf_switch").radioswitch("getState").on === false;
				if(isUHSPDisabled) {
					$("#hs-port-forwarding-items").prop("disabled",true).addClass("disabled");
					$("a.btn").addClass("disabled").click(function(e){e.preventDefault();});
					$("input[name='PortActive']").prop("disabled",true);
					$("a.confirm").unbind('click');
				} else {
					$("#hs-port-forwarding-items").prop("disabled",false).removeClass("disabled");
					$("a.btn").removeClass("disabled").unbind('click');
					$("input[name='PortActive']").prop("disabled",false);
					setupDeleteConfirmDialogs();
					// window.location.href="hs_port_forwarding.php";
				}
			},
			error:function(){
				jHide();
				jAlert("Error! Please try later!");
			}
		});
	});
	
	$("input[name='PortActive']").change(function(){
		var isChecked=$(this).is(":checked");
		var id=$(this).prop("id").split("_");
		id=id[1];
		jProgress('This may take several seconds',60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_hs_port_forwarding.php",
			data:{active:"true",isChecked:isChecked,id:id},
			success:function(){
				jHide();
			},
			error:function(){
				jHide();
				jAlert("Error! Please try later!");
			}
		});
	});
});
</script>

<div id="content">
	<h1>Advanced > HS Port Forwarding</h1>

	<div id="educational-tip">
		<p class="tip">Add port forwarding related to Home Security Device.</p>
   		<p class="hidden">Users can configure the RG to provide the port forwarding services which allow the Internet users to
		access local services such as the Web server or FTP server at your local site. This is done by
		redirecting the combination of the WAN IP address and the service port to the local private IP and its
        service port.</p>
	</div>

	<div class="module">

		<div class="select-row">
    		<span class="readonlyLabel label">HS Port Forwarding:</span>
    		<span id="hspf_switch"></span>
    	</div>
	</div>

	<div id="hs-port-forwarding-items">
		<div class="module data">
			<h2>HS Port Forwarding</h2>
			<p class="button"><a tabindex='0' href="hs_port_forwarding_add.php" class="btn" id="add-port-forward">+ Add Port Forward</a></p>
			<table class="data" summary="This table lists home security port forwarding entries">
				<tr>
					<th id="service-name">Service Name</th>
					<th id="service-type">Type</th>
					<th id="public-port">Public Port</th>
					<th id="private-port">Private Port</th>
					<th id="server-ip">Server IP</th>
					<th id="active">Active</th>
					<th id="edit-button">&nbsp;</th>
					<th id="delete-button">&nbsp;</th>
				</tr>
				<?php

				$rootObjName    = "Device.NAT.PortMapping.";
				$paramNameArray = array("Device.NAT.PortMapping.");
				$mapping_array  = array("LeaseDuration", "InternalPort", "Protocol", "Description",
					                    "ExternalPort", "ExternalPortEndRange", "InternalClient", "Enable");

				$resArray = getParaValues($rootObjName, $paramNameArray, $mapping_array, true);				
				if ($_DEBUG) {
					$resArray = array(
						array(
							'Protocol' => 'TCP',
							'Enable' => 'true',
							'Description' => 'services 1',
							'ExternalPort' => 666,
							'InternalPort' => 22,
							'InternalClient' => '172.16.12.2',
							'LeaseDuration' => 0,
							),
							array(
							'Protocol' => 'BOTH',
							'Enable' => 'true',
							'Description' => 'services 2',
							'ExternalPort' => 366,
							'InternalPort' => 25,
							'InternalClient' => '172.16.12.22',
							'LeaseDuration' => 0,
							),
					 );
				}
				//dump($resArray);

				if(!empty($resArray)){

					$iclass = ""; 
					foreach ($resArray as $hspf_entry) {
							//zqiu
							if (($hspf_entry['InternalPort'] === '0') || 
								($hspf_entry['InternalClient'] === '0.0.0.0') ||
								(strpos($hspf_entry['InternalClient'],'172.16.12.') === false)
								) {
								//filter out hs port forwarding entry whose internal port !== 0
								continue;
							}								

							$id = $hspf_entry['__id'];

							$iclass = ($iclass === "") ? "odd" : "";

							($hspf_entry['Protocol'] === "BOTH") && ($hspf_entry['Protocol'] = "TCP/UDP");

							$checked = $hspf_entry['Enable'] === "true" ? "checked" : "";

							echo "<tr class=$iclass>";
							echo "<td headers='service-name'>" .$hspf_entry['Description']. "</td>";
							echo "<td headers='service-type'>" .$hspf_entry['Protocol']. "</td>";
							echo "<td headers='public-port'>"  .$hspf_entry['ExternalPort']. "</td>";
							echo "<td headers='private-port'>" .$hspf_entry['InternalPort']. "</td>";
							echo "<td headers='server-ip'>"    .$hspf_entry['InternalClient']. "</td>";
							echo "<td headers='active'><input tabindex='0' type=\"checkbox\" id=\"PortActive_$id\" name=\"PortActive\" $checked/>
							<label for=\"PortActive_$id\"></label></td>";	
							echo "<td headers='edit-button'  class=\"edit\"><a tabindex='0' href=\"hs_port_forwarding_edit.php?id=$id\" class=\"btn\"  id=\"edit_$id\">Edit</a></td>
								<td headers='delete-button'  class=\"delete\"><a tabindex='0'  href=\"actionHandler/ajax_hs_port_forwarding.php?del=$id\" class=\"btn confirm\" 
									title=\"delete this HS Port Forwading service for " . $hspf_entry['Description'] . " \" id=\"delete_$id\">x</a></td>
								</tr>";
							echo "</tr>";
						
					}//end of foreach
				}//end of empty

				/*foreach($HPIDs as $key=>$i) {
					if (getStr("Device.NAT.PortMapping.".$i.".LeaseDuration")==0 && getStr("Device.NAT.PortMapping.".$i.".InternalPort")!=0){
						if ($iclass=="") {$iclass="odd";} else {$iclass="";}
						$Protocol=getStr("Device.NAT.PortMapping.".$i.".Protocol");
						if ($Protocol=="BOTH") $Protocol="TCP/UDP";
						echo "
						'<tr class=$iclass>';
						<td headers='service-name'>".getStr("Device.NAT.PortMapping.".$i.".Description")."</td>
						<td headers='service-type'>".$Protocol."</td>";
						if (($startPort=getStr("Device.NAT.PortMapping.".$i.".ExternalPort"))==($endPort=getStr("Device.NAT.PortMapping.".$i.".ExternalPortEndRange"))) {
							echo "
							<td headers='public-port'>".$startPort."</td>";
						} else {
							echo "
							<td headers='public-port'>".$startPort."~".$endPort."</td>";
						}
						echo "
						<td headers='private-port'>".getStr("Device.NAT.PortMapping.".$i.".InternalPort")."</td>
						<td headers='server-ip'>".getStr("Device.NAT.PortMapping.".$i.".InternalClient")."</td>";

						if (getStr("Device.NAT.PortMapping.".$i.".Enable")=="true") {
							echo "
							<td headers='active'><input tabindex='0' type=\"checkbox\" id=\"PortActive_$i\" name=\"PortActive\" checked=\"checked\" />
							<label for=\"PortActive_$i\"></label></td>";
						} else {
							echo "
							<td headers='active'><input tabindex='0' type=\"checkbox\" id=\"PortActive_$i\" name=\"PortActive\" />
							<label for=\"PortActive_$i\"></label></td>";
						}
						echo "
						</tr>";
					} 
				}*/

				?>

				<tfoot>
				<tr class="acs-hide">
					<td headers="service-name">null</td>
					<td headers="service-type">null</td>
					<td headers="public-port">null</td>
					<td headers="private-port">null</td>
					<td headers="server-ip">null</td>
					<td headers="active">null</td>
				</tr>
				</tfoot>

			</table>
		</div> <!-- end .module -->
	</div>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
