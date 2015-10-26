<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: port_triggering.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
<?php
$PTEnable=getStr("Device.NAT.X_CISCO_COM_PortTriggers.Enable");
if ($_DEBUG) {
	$PTEnable = "true";
}
?>

<style>
	td:not(.edit) {word-break: break-all;}
</style>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > Port Triggering", "nav-port-triggering");

	$("#pt_switch").radioswitch({
		id: "port-triggering-switch",
		radio_name: "triggering",
		id_on: "triggering_enabled",
		id_off: "triggering_disabled",
		title_on: "Enable port triggering",
		title_off: "Disable port triggering",
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
	
		jProgress('This may take several seconds.',60);
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
				jAlert("Error! Please try later!");
			}
		});
	});
	
	$("input[name='PortActive']").change(function(){
		var isChecked=$(this).is(":checked");
		var id=$(this).attr("id").split("_");
		id=id[1];
		jProgress('This may take several seconds.',60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_port_triggering.php",
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
	<h1>Advanced > Port Triggering</h1>

	<div id="educational-tip">
		<p class="tip">Manage external access to specific ports on your network.</p>
		<p class="hidden">Port triggering monitors outbound traffic on your network. When traffic is detected on a particular outbound port, the Gateway remembers that computer's IP address, triggers the inbound port to accept the incoming traffic, and directs the communications to the same computer.</p>
		<p class="hidden">Select <strong>Enable</strong> to manage external access to specific ports on your network.</p>
		<p class="hidden">Click <strong>+ADD PORT TRIGGER</strong> to add new port triggering rules.</p>
		<p class="hidden">Port triggering settings can affect the Gateway's performance.</p>
	</div>

	<form action="port_triggering.php" method="post">
	<div class="module">
		<div class="select-row">
    		<span class="readonlyLabel label">Port Triggering:</span>	
			<span id="pt_switch"></span>
    	</div>
		<!--div class="select-row" style="color:red">Switch this button will make all service Active/Inactive</div-->
	</div>
	</form>


	<div id="port-triggering-items">
	<div class="module data">
		<h2>Port Triggering</h2>
		<p class="button"><a tabindex='0' href="port_triggering_add.php" class="btn" id="add-port-trigger">+ Add Port Trigger</a></p>
		<table class="data" summary="This table lists available port triggering entries">
		    <tr>
		        <th id="service-name">Service Name</td>
				<th id="service-type">Service Type</td>
				<th id="trigger-port">Trigger Port(s)</td>
				<th id="target-port">Target port(s)</td>
     			<th id="active">Active</td>
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
			//var_dump(count($PTIDs));
			$iclass="even";
			foreach ($PTIDs as $key=>$i) {
				$portTriggerValues["$i"] = $portTriggerValuesArr["$key"];
			}
			foreach ($PTIDs as $key=>$i) {
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
	            <td headers='edit-button' class=\"edit\"><a tabindex='0' href=\"port_triggering_edit.php?id=$i\" class=\"btn\" id=\"edit_$i\">Edit</a></td>
		        <td headers='delete-button' class=\"delete\"><a tabindex='0' href=\"actionHandler/ajax_port_triggering.php?del=$i\" class=\"btn confirm\" title=\"delete port Triggering for ".$portTriggerValues["$i"]["Description"]." \" id=\"delete_$i\">x</a></td>
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
