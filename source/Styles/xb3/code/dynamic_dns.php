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
				} else {
					window.location.href="dynamic_dns.php";
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

<script>
	//Don't allow user to add morethan 4 rules as only 4 Service Provider are allowed
	function add_service() {
		if ($('.edit').length > 3) {
			jAlert("No more than 4 Dynamic DNS rules can be added!");
			return;
		} else {
			location.href="dynamic_dns_add.php";
		}
	}
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
		<p class="button"><a class="btn" id="add-service" onclick="add_service()">+ ADD DDNS</a></p>
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
		</table>
		</div> <!-- end .module -->

</div>
</div><!-- end #content -->



<?php include('includes/footer.php'); ?>
