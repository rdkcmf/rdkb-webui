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
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<?php
$enable = getStr("Device.X_CISCO_COM_DDNS.Enable");
?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Advanced > Dynamic DNS", "nav-Dynamic-dns");
	var jsEnable = <?php echo $enable === "true" ? "true" : "false"; ?>;
	$("#ddns_switch").radioswitch({
		id: "Dynamic-DNS-switch",
		radio_name: "DNS",
		id_on: "DNS_enabled",
		id_off: "DNS_disabled",
		title_on: "<?php echo _('Enable Dynamic DNS')?>",
		title_off: "<?php echo _('Disable Dynamic DNS')?>",
		state: jsEnable ? "on" : "off"
	});
	$("a.confirm").unbind('click');
	function setupDeleteConfirmDialogs() {
	/*
	 * Confirm dialog for delete action
	 */             
		$("a.confirm").click(function(e) {
			e.preventDefault();            
			var href = $(this).attr("href");
		    	var message = ($(this).attr("title").length > 0) ? "<?php echo _('Are you sure you want to')?> " + $(this).attr("title") + "?" : "<?php echo _('Are you sure?')?>";
			jConfirm(
				message
				,"<?php echo _('Are You Sure?')?>"
				,function(ret) {
				    if(ret) {
						delVal = href.substring(href.indexOf("=")+1);
						jProgress("<?php echo _('This may take several seconds.')?>",60);
						$.ajax({
							type:"POST",
							url:"actionHandler/ajax_ddns.php",
							data:{del:delVal},
							success:function(){
								jHide();
								window.location.reload();
							},
							error:function(){
								jHide();
								jAlert("<?php echo _('Error! Please try later!')?>");
							}
						});
				    }    
			});
		});
    	}
	function enableHandler() {
		var isUDNSDisabled = $("#ddns_switch").radioswitch("getState").on === false;
		if(isUDNSDisabled) {
			$("a.confirm").unbind('click');
			$('.module *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
			$("#DNS-items").prop("disabled",true).addClass("disabled");
			$("a.btn").addClass("disabled").click(function(e){e.preventDefault();});
		}
		else {
			setupDeleteConfirmDialogs();
		}
	}
	enableHandler();
	$("#ddns_switch").change(function() {
		var status = ($("#ddns_switch").radioswitch("getState").on ? "Enabled" : "Disabled");
		var isUDNSDisabled = $("#ddns_switch").radioswitch("getState").on === false;
		if(isUDNSDisabled){
			$("a.confirm").unbind('click');
			$('.module *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
			$("#DNS-items").prop("disabled",true).addClass("disabled");
			$("a.btn").addClass("disabled").click(function(e){e.preventDefault();});
		}else{
			$('.module *').not(".radioswitch_cont, .radioswitch_cont *").removeClass("disabled");
			$("#DNS-items").prop("disabled",false).removeClass("disabled");
			setupDeleteConfirmDialogs();
		}
		jProgress("<?php echo _('This may take several seconds')?>", 60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_ddns.php",
			data:{set:"true",status:status},
			success:function(results){
				//jAlert(results);
				jHide();
				if (status!=results){ 
					jAlert("<?php echo _('Could not do it!')?>");
					$("#ddns_switch").radioswitch("doSwitch", results === 'Enabled' ? 'on' : 'off');
				} else {
					window.location.href="dynamic_dns.php";
				}
			},
			error:function(){
				jHide();
				jAlert("<?php echo _('Failure, please try again.')?>");
			}
		});
	});
});
</script>
<script>
	//Don't allow user to add morethan 4 rules as only 4 Service Provider are allowed
	function add_service() {
		if ($('#add-service').hasClass('disabled'))
			return;
		else if ($('.edit').length > 3) {
			jAlert("<?php echo _('No more than 4 Dynamic DNS rules can be added!')?>");
			return;
		} else {
			location.href="dynamic_dns_add.php";
		}
	}
</script>
<div id="content">
   	<h1><?php echo _('Advanced > Dynamic DNS')?></h1>
    <div id="educational-tip">
        <p class="tip"><?php echo _('Configure the Gateway\'s router functionality as a Dynamic DNS client.')?></p>
        <p class="hidden"><?php echo _('<strong>Service Provider:</strong> Dynamic DNS Service Provider Domain name')?></p>
        <p class="hidden"><?php echo _('<strong>User Name:</strong> Name registered with the service provider')?></p>
        <p class="hidden"><?php echo _('<strong>Password:</strong> Password registered with the service provider')?></p>
        <p class="hidden"><?php echo _('<strong>Host Name:</strong> Host Name registered with the service provider')?></p>
    </div>
<form action="dynamic_dns.php" method="post">
	<div class="module">
		<div class="select-row">
    		<span class="readonlyLabel label"><?php echo _('Dynamic DNS:')?></span>
			<span id="ddns_switch"></span>
    	</div>
	</div>
	</form>
<div id="DNS-items">
<div class="module data">
		<h2><?php echo _('Dynamic DNS')?></h2>
		<p class="button"><a class="btn" id="add-service" onclick="add_service()"><?php echo _('+ ADD DDNS')?></a></p>
		<table class="data">
			<tr>
				<td class="acs-th"><?php echo _('Service Provider')?></td>
				<td class="acs-th"><?php echo _('User Name')?></td>
				<td class="acs-th"><?php echo _('Password')?></td>
				<td class="acs-th"><?php echo _('HostName(s)')?></td>
				<td class="acs-th" colspan="2">&nbsp;</td>
			</tr>
			<?php 
				$num=getStr("Device.X_CISCO_COM_DDNS.ServiceNumberOfEntries");
				if($num!=0) {
					$IDs=explode(",",getInstanceIDs("Device.X_CISCO_COM_DDNS.Service."));
					$iclass="";
					foreach ($IDs as $key=>$i) {
						$enableSrv = getStr("Device.X_CISCO_COM_DDNS.Service."."$i".".Enable");
						if($enableSrv == "true") {
							if ($iclass=="") {$iclass="odd";} else {$iclass="";}
							$name = getStr("Device.X_CISCO_COM_DDNS.Service."."$i".".ServiceName");
							$username = getStr("Device.X_CISCO_COM_DDNS.Service."."$i".".Username");
							$username = htmlspecialchars($username, ENT_NOQUOTES, 'UTF-8');
							$password = getStr("Device.X_CISCO_COM_DDNS.Service."."$i".".Password");
							$password = htmlspecialchars($password, ENT_NOQUOTES, 'UTF-8');
							$passwordStr = "";
							for($j=0;$j<strlen($password);$j++) {
								$passwordStr = $passwordStr . "*";
							}
							$hostname = getStr("Device.X_CISCO_COM_DDNS.Service."."$i".".Domain");
							$hostname = htmlspecialchars($hostname, ENT_NOQUOTES, 'UTF-8');
							$servicename = getStr("Device.X_CISCO_COM_DDNS.Service."."$i".".ServiceName");
							echo "
							<tr class=$iclass>
								<td>".$name."</td>
								<td>".$username."</td>
								<td >".$passwordStr."</td>
								<td>".$hostname."</td>
								<td class=\"edit\"><a  tabindex='0' href=\"dynamic_dns_edit.php?id=$i\" class=\"btn\" id=\"edit_$i\">"._('Edit')."</a></td>
								<td class=\"delete\"><a tabindex='0' href=\"actionHandler/ajax_ddns.php?del=$i\" class=\"btn confirm\" title=\"".sprintf(_('delete this service for %s'),$servicename)." \"id=\"delete_$i\">x</a></td>
							</tr>"; 
						}
					} 
				}
			?>
		</table>
		</div> <!-- end .module -->
</div>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
