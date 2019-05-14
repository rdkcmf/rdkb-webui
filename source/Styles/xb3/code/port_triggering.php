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
<!-- $Id: port_triggering.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php
$PTEnable=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Enable");
/*if ($_DEBUG) {
	$PTEnable = "true";
}*/
?>
<style>
	td:not(.edit) {word-break: break-all;}
</style>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Advanced > Port Triggering", "nav-port-triggering");
	$("#pt_switch").radioswitch({
		id: "port-triggering-switch",
		radio_name: "triggering",
		id_on: "triggering_enabled",
		id_off: "triggering_disabled",
		title_on: "<?php echo _("Enable port triggering")?>",
		title_off: "<?php echo _("Disable port triggering")?>",
		state: <?php echo ($PTEnable === "true" ? "true" : "false"); ?> ? "on" : "off"
	});
	$("a.confirm").unbind('click');
	function setupDeleteConfirmDialogs() {
        /*
         * Confirm dialog for delete action
         */             
        $("a.confirm").click(function(e) {
            e.preventDefault();            
            var href = $(this).attr("href");
            var message = ($(this).attr("title").length > 0) ? "<?php echo _("Are you sure you want to")?> " + $(this).attr("title") + "?" : "<?php echo _("Are you sure?")?>";
            jConfirm(
                message
                ,"<?php echo _("Are You Sure?")?>"
                ,function(ret) {
                    if(ret) {
						delVal = href.substring(href.indexOf("=")+1);
						jProgress('<?php echo _("This may take several seconds.")?>',60);
						$.ajax({
							type:"POST",
							url:"actionHandler/ajax_port_triggering.php",
							data:{del:delVal},
							success:function(){
								jHide();
								window.location.reload();
							},
							error:function(){
								jHide();
								jAlert("<?php echo _("Error! Please try later!")?>");
							}
						});
                    }    
                });
        });
    }
	var isUPTRDisabled = $("#pt_switch").radioswitch("getState").on === false;
	if(isUPTRDisabled) { 
		$("a.confirm").unbind('click');
		$('.module *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
		$("#port-triggering-items").prop("disabled",true).addClass("disabled");
		$("a.btn").addClass("disabled").click(function(e){e.preventDefault();});
		$("input[name='PortActive']").prop("disabled",true);
	}
	else {
		setupDeleteConfirmDialogs();
	}
	$("#pt_switch").change(function() {
		var UPTRStatus = $("#pt_switch").radioswitch("getState").on ? "Enabled" : "Disabled";
		var isUPTRDisabled = $("#pt_switch").radioswitch("getState").on === false;
		if(isUPTRDisabled) { 
			$("a.confirm").unbind('click');
			$('.module *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
			$("#port-triggering-items").prop("disabled",true).addClass("disabled");
			$("a.btn").addClass("disabled").click(function(e){e.preventDefault();});
			$("input[name='PortActive']").prop("disabled",true);
		}
		else{
			$('.module *').not(".radioswitch_cont, .radioswitch_cont *").removeClass("disabled");
			$("#port-triggering-items").prop("disabled",false).removeClass("disabled");
			$("input[name='PortActive']").prop("disabled",false);
			setupDeleteConfirmDialogs();
		}
		jProgress('<?php echo _("This may take several seconds.")?>',60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_port_triggering.php",
			data:{set:"true",UPTRStatus:UPTRStatus},
			success:function(){
				jHide();
				/*results=eval("("+result+")");
				if (UPTRStatus!=results){
					jAlert("Backend Error!");
					$("input[name='triggering']").each(function(){
						if($(this).val()==results){$(this).parent().addClass("selected");$(this).prop("checked",true);}
						else{$(this).parent().removeClass("selected");$(this).prop("checked",false);}
					});
				}*/
			/*	var isUPTRDisabled = $("#triggering_disabled").is(":checked");
				if(isUPTRDisabled) {
					$("#port-triggering-items").prop("disabled",true).addClass("disabled");
					$("a.btn").addClass("disabled").click(function(e){e.preventDefault();});
					$("input[name='PortActive']").prop("disabled",true);
				}
				else {
					//$("#port-triggering-items").prop("disabled",false).removeClass("disabled");
					//$("a.btn").removeClass("disabled").unbind('click');
					//$("input[name='PortActive']").prop("disabled",false);
					window.location.href="port_triggering.php";
				}*/
				window.location.href="port_triggering.php";
			},
			error: function(){
				jHide();
				jAlert("<?php echo _("Error! Please try later!")?>");
			}
		});
	});
	$("input[name='PortActive']").change(function(){
		var isChecked=$(this).is(":checked");
		var id=$(this).attr("id").split("_");
		id=id[1];
		jProgress('<?php echo _("This may take several seconds.")?>',60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_port_triggering.php",
			data:{active:"true",isChecked:isChecked,id:id},
			success:function(){
				jHide();
			},
			error:function(){
				jHide();
				jAlert("<?php echo _("Error! Please try later!")?>");
			}
		});
	});
});
</script>
<div id="content">
	<h1><?php echo _("Advanced > Port Triggering")?></h1>
	<div id="educational-tip">
		<p class="tip"><?php echo _("Manage external access to specific ports on your network.")?></p>
		<p class="hidden"><?php echo _("Port triggering monitors outbound traffic on your network. When traffic is detected on a particular outbound port, the Gateway remembers that computer's IP address, triggers the inbound port to accept the incoming traffic, and directs the communications to the same computer.")?></p>
		<p class="hidden"><?php echo _("Select <strong>Enable</strong> to manage external access to specific ports on your network.")?></p>
		<p class="hidden"><?php echo _("Click <strong>+ADD PORT TRIGGER</strong> to add new port triggering rules.")?></p>
		<p class="hidden"><?php echo _("Port triggering settings can affect the Gateway's performance.")?></p>
	</div>
	<form action="port_triggering.php" method="post">
	<div class="module">
		<div class="select-row">
    		<span class="readonlyLabel label"><?php echo _("Port Triggering:")?></span>	
			<span id="pt_switch"></span>
    	</div>
		<!--div class="select-row" style="color:red">Switch this button will make all service Active/Inactive</div-->
	</div>
	</form>
	<div id="port-triggering-items">
	<div class="module data">
		<h2><?php echo _("Port Triggering")?></h2>
		<p class="button"><a tabindex='0' href="port_triggering_add.php" class="btn" id="add-port-trigger"><?php echo _("+ Add Port Trigger")?></a></p>
		<table class="data" summary="<?php echo _("This table lists available port triggering entries")?>">
		    <tr>
		        <th id="service-name"><?php echo _("Service Name")?></td>
				<th id="service-type"><?php echo _("Service Type")?></td>
				<th id="trigger-port"><?php echo _("Trigger Port(s)")?></td>
				<th id="target-port"><?php echo _("Target port(s)")?></td>
     			<th id="active"><?php echo _("Active")?></td>
				<!-- <th id="edit-or-delete" colspan="2">&nbsp;</th> -->
				<th id="edit-button">&nbsp;</th>
				<th id="delete-button">&nbsp;</th>
		    </tr>
			<?php
			if (getStr("Device.NAT.X_CISCO_COM_PortTriggers.TriggerNumberOfEntries")==0) {}
			else{
                    $rootObjName    = "Device.NAT.X_CISCO_COM_PortTriggers.Trigger.";
                    $paramNameArray = array("Device.NAT.X_CISCO_COM_PortTriggers.Trigger.");
                    $mapping_array  = array("TriggerProtocol", "Description", "TriggerPortStart", "TriggerPortEnd", "ForwardPortStart", "ForwardPortEnd", "Enable");
		    		$portTriggerValues = array();
                    $portTriggerValuesArr = getParaValues($rootObjName, $paramNameArray, $mapping_array);
			$PTIDs=explode(",",getInstanceIDs("Device.NAT.X_CISCO_COM_PortTriggers.Trigger."));
			$iclass="even";
			foreach ($PTIDs as $key=>$i) {
				$portTriggerValues["$i"] = $portTriggerValuesArr["$key"];
			}
			foreach ($PTIDs as $key=>$i) {
				$portTriggerValues[$i]['Description'] = htmlspecialchars($portTriggerValues[$i]['Description'], ENT_NOQUOTES, 'UTF-8');
				if ($iclass=="even") {$iclass="odd";} else {$iclass="even";}
				$Protocol =  $portTriggerValues["$i"]["TriggerProtocol"];
				if ($Protocol=="BOTH") $Protocol="TCP/UDP";
				echo "
		    	<tr class=$iclass>
		        <td headers='service-name'>".$portTriggerValues["$i"]["Description"]."</td>
		        <td headers='service-type'>".$Protocol."</td>
				<td headers='trigger-port'>".$portTriggerValues["$i"]["TriggerPortStart"]."~".$portTriggerValues["$i"]["TriggerPortEnd"]."</td>
				<td headers='target-port'>".$portTriggerValues["$i"]["ForwardPortStart"]."~".$portTriggerValues["$i"]["ForwardPortEnd"]."</td>";
				if ($portTriggerValues["$i"]["Enable"]=="true") {
					echo "<td headers='active'><input tabindex='0' type=\"checkbox\" id=\"PortActive_$i\" name=\"PortActive\" checked=\"checked\" /><label for=\"PortActive_$i\"  class='acs-hide'></label></td>";
				} else {
					echo "<td headers='active'><input tabindex='0' type=\"checkbox\" id=\"PortActive_$i\" name=\"PortActive\" /><label for=\"PortActive_$i\"  class='acs-hide'></label></td>";
				}	
				echo "
	            <td headers='edit-button' class=\"edit\"><a tabindex='0' href=\"port_triggering_edit.php?id=$i\" class=\"btn\" id=\"edit_$i\">"._("Edit")."</a></td>
		        <td headers='delete-button' class=\"delete\"><a tabindex='0' href=\"actionHandler/ajax_port_triggering.php?del=$i\" class=\"btn confirm\" title=\"".sprintf(_("delete port Triggering for %s."),$portTriggerValues["$i"]["Description"])."\" id=\"delete_$i\">x</a></td>
		    </tr>";
			} }?>
			<tfoot>
				<tr class="acs-hide">
					<td headers="service-name">null</td>
					<td headers="service-type">null</td>
					<td headers="trigger-port">null</td>
					<td headers="target-port">null</td>
					<td headers="active">null</td>
					<td headers="edit-button">null</td>
					<td headers="delete-button">null</td>
				</tr>
			</tfoot>
		</table>
	</div> <!-- end .module -->
	</div>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
