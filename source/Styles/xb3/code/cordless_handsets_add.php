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
<!-- $Id: cordless_handsets_add.php 3161 2010-01-13 00:22:07Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Connected Devices > Cordless Handsets > Register Handset", "nav-cordless-handsets");
    $('#btn-save').focus();
	if ("true" == "<?php echo getStr("Device.X_CISCO_COM_MTA.Dect.RegistrationMode"); ?>"){
		$("#turn_on").show();
		// $("#btn-save").attr("disabled", true);
	}
	else{
		$("#turn_off").show();
		// $("#btn-cancel").attr("disabled", true);
	}
	$(".btn").click(function(){		
		var reg_mode = "true";
		msg = '<b><?php echo _("Do you want to register handset?")?></b>';
		if ("Cancel"==$(this).val()){
			reg_mode = "false";
			msg = '<b><?php echo _("Do you want to abort registration?")?></b>';
		}
		jConfirm(
			msg
			, '<?php echo _("Are You Sure?")?>'
			, function(ret){
				if(ret){
					jProgress('<?php echo _("This may take several seconds...")?>',60);
					ajaxrequest=$.ajax({
						type:"POST",
						url:"actionHandler/ajaxSet_cordless_handsets.php",
						data:{
							target	:"register",				
							reg_mode:reg_mode
						},
						dataType:"JSON",
						success:function(result){
							jHide();
							if ("true"==result){
								// $("#turn_on").show();$("#turn_off").hide();
								location.reload();
							}
							else{
								location.href="cordless_handsets.php";
							}
						},
						error:function(){
							jHide();
							// jAlert("Sorry, please try again.");
						}
					});
				}
				else{
					// location.reload();
				}
			}
		);
	});
});
</script>
<form action="">
	<div id="content">
		<h1><?php echo _("Connected Devices > Cordless Handsets > Register Handset")?></h1>
		<div class="module">
			<h2><?php echo _("Register Handset")?></h2>
			<div id="turn_on" class="form-row" style="display:none;">
				<b><?php echo _("The Base is in Registration mode.")?></b>
				<br/>
				<p><?php echo _("On the handset, follow the registration flow to register with the CAT-iq Base.")?></p>
			</div>
			<div id="turn_off" class="form-row" style="display:none;">
				<b><?php echo _('Press "REGISTER" button to turn the base into registration mode.')?></b>
			</div>
			<div class="btn-group">
				<input type="button" id="btn-save" class="btn" value="Register" />
				<input type="button" id="btn-cancel" class="btn alt" value="Cancel" />
			</div>
		</div> <!-- end .module -->
	</div><!-- end #content -->
</form>
<?php include('includes/footer.php'); ?>
