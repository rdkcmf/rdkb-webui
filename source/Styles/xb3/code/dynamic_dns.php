<?php include('includes/header.php'); ?>


<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
$enable = getStr("Device.X_CISCO_COM_DDNS.Enable");

("" == $enableMS) && ($enableMS = "false");

?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > Dynamic DNS", "nav-Dynamic-dns");

	var jsEnable = <?php echo $enable === "true" ? "true" : "false"; ?>;
	
	$("#ddns_switch").radioswitch({
		id: "Dynamic-DNS-switch",
		radio_name: "DNS",
		id_on: "DNS_enabled",
		id_off: "DNS_disabled",
		title_on: "Enable Dynamic DNS",
		title_off: "Disable Dynamic DNS",
		state: jsEnable ? "on" : "off"
	});

	function enableHandler() {
		var isUDNSDisabled = $("#ddns_switch").radioswitch("getState").on === false;

		if(isUDNSDisabled) {

		$("#DNS-items *").prop("disabled",true).addClass("disabled");

/*		document.getElementById('add-service').disabled = true;
		document.getElementById('edit1').disabled = true;
		document.getElementById('edit2').disabled = true;
		document.getElementById('delete1').disabled = true;
		document.getElementById('delete2').disabled = true;*/

		}
		else {
		$("#DNS-items *").prop("disabled",false).removeClass("disabled");

		$("btn").removeClass("disabled");
/*		document.getElementById('add-service').disabled = false;
		 document.getElementById('edit1').disabled = false;
		 document.getElementById('edit2').disabled = false;
		 document.getElementById('delete1').disabled = false;
		 document.getElementById('delete2').disabled = false;*/
		}
	}
	
	enableHandler();
	
	$("#ddns_switch").change(function() {

		var status = ($("#ddns_switch").radioswitch("getState").on ? "Enabled" : "Disabled");
//		var status = $("#DNS_disabled").is(":checked");
		jProgress('This may take several seconds', 60);
		$.ajax({
			type:"POST",
			url:"actionHandler/ajax_ddns.php",
			data:{set:"true",status:status},
			success:function(results){
				//jAlert(results);
				jHide();
				if (status!=results){ 
					jAlert("Could not do it!");
					$("#ddns_switch").radioswitch("doSwitch", results === 'Enabled' ? 'on' : 'off');
				}
				var isUDNSDisabled = $("#ddns_switch").radioswitch("getState").on === false;
				if(isUDNSDisabled){
					$("#DNS-items *").prop("disabled",true).addClass("disabled");
				}else{
					$("#DNS-items *").prop("disabled",false).removeClass("disabled");
				}
			},
			error:function(){
				jHide();
				jAlert("Failure, please try again.");
			}
		});
	});

});
</script>

<div id="content">
   	<h1>Advanced > Dynamic DNS</h1>

    <div id="educational-tip">
        <p class="tip">Configure the Gateway's router functionality as a Dynamic DNS client. </p>
        <p class="hidden"><strong>Service Provider:</strong> Dynamic DNS Service Provider Domain name</p>
        <p class="hidden"><strong>User Name:</strong> Name registered with the service provider</p>
        <p class="hidden"><strong>Password:</strong> Password registered with the service provider</p>
        <p class="hidden"><strong>Host Name:</strong> Host Name registered with the service provider</p>
    </div>

<form action="dynamic_dns.php" method="post">
	<div class="module">

		<div class="select-row">
    		<span class="readonlyLabel label">Dynamic DNS:</span>
			<span id="ddns_switch"></span>
    	</div>

	</div>
	</form>

<div id="DNS-items">
<div class="module data">
		<h2>Dynamic DNS</h2>
		<p class="button"><a href="dynamic_dns_add.php" class="btn" id="add-service">+ ADD DDNS</a></p>
		<table class="data">
			<tr>
				<td class="acs-th">Service Provider</td>
				<td class="acs-th">User Name</td>
				<td class="acs-th">Password</td>
				<td class="acs-th">HostName(s)</td>
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
							$password = getStr("Device.X_CISCO_COM_DDNS.Service."."$i".".Password");
							$passwordStr = "";
							for($j=0;$j<strlen($password);$j++) {
								$passwordStr = $passwordStr . "*";
							}
							$hostname = getStr("Device.X_CISCO_COM_DDNS.Service."."$i".".Domain");
							echo "
							<tr class=$iclass>
								<td>".$name."</td>
								<td>".$username."</td>
								<td >".$passwordStr."</td>
								<td>".$hostname."</td>
								<td class=\"edit\"><a  tabindex='0' href=\"dynamic_dns_edit.php?id=$i\" class=\"btn\" id=\"edit_$i\">Edit</a></td>
								<td class=\"delete\"><a tabindex='0' href=\"actionHandler/ajax_ddns.php?del=$i\" class=\"btn confirm\" title=\"delete this service for ".getStr("Device.X_CISCO_COM_DDNS.Service."."$i".".ServiceName")." \"id=\"delete_$i\">x</a></td>
							</tr>"; 
						}
					} 
				}
			?>
<!--				    <tr>
				        <td>dyndns.org</td>
					<td>USG</td>
								<td>**********</td>
								<td>App1, App2, Host1, Host2</td>

		            	<td class="edit"><a href="dynamic_dns_edit1.php" class="btn" id="edit1">Edit</a></td>
				        <td class="delete"><a href="#" class="btn confirm" title="delete this Port Forwading service for FTP" id="delete1">x</a>

				        </td>
				    </tr>
				    <tr class="odd">
				        <td>changeip.com</td>
					<td>USG1</td>
								<td>**********</td>
								<td>App3, App4, Host3, Host4</td>

		            	<td class="edit"><a href="dynamic_dns_edit2.php" class="btn" id="edit2">Edit</a></td>
				        <td class="delete"><a href="#" class="btn confirm" title="delete this Port Forwading service for HTTP" id="delete2">x</a></td>
				    </tr>-->
		</table>
		</div> <!-- end .module -->

</div>
</div><!-- end #content -->



<?php include('includes/footer.php'); ?>
