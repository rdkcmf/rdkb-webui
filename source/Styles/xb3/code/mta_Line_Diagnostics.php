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
<?php include('includes/header.php');
if(PREPAID == TRUE){
	echo '<script type="text/javascript">alert("'._("No MTA support for this device").'"); window.history.back(); </script>';
	exit(0);
}
?>
<!-- $Id: managed_services_add.php 2943 2009-08-25 20:58:43Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
gateway.page.init("Gateway > Connection > MTA > Line Diagnostics", "nav-mta-line-diagnostics");
	$("#start_diagnostics1").click(function(){
		var isTest1	= true;
		document.getElementById('line1hp').value	= "InProgress";
		document.getElementById('line1femf').value	= "InProgress";
		document.getElementById('line1rf').value	= "InProgress";
		document.getElementById('line1roh').value	= "InProgress";
		document.getElementById('line1re').value	= "InProgress";
		jProgress('<?php echo _("Check telephony line status, please wait...")?>', 60);
		$.post(
			"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
			{"get_status1":"true"},
			function(msg)
			{
				jHide();
				if ("Off-Hook" == msg.line1hook){
					jConfirm(
					'<?php echo _("Phone is Off-Hook, do you want to start the test anyway?")?>'
					, '<?php echo _("Are You Sure?")?>'
					, function(ret){
						if(ret){
							jProgress('<?php echo _("This may take several seconds...")?>', 60);
							$.post(
								"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
								{"start_diagnostics1":"true"},
								function(msg)
								{            
									document.getElementById('line1hp').value	= msg.line1hp;
									document.getElementById('line1femf').value	= msg.line1femf;
									document.getElementById('line1rf').value	= msg.line1rf;
									document.getElementById('line1roh').value	= msg.line1roh;
									document.getElementById('line1re').value	= msg.line1re;
									jHide();
								},
								"json"     
							);
						}
					});
				}
				else{
					jProgress('<?php echo _("This may take several seconds...")?>', 60);
					$.post(
						"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
						{"start_diagnostics1":"true"},
						function(msg)
						{            
							document.getElementById('line1hp').value	= msg.line1hp;
							document.getElementById('line1femf').value	= msg.line1femf;
							document.getElementById('line1rf').value	= msg.line1rf;
							document.getElementById('line1roh').value	= msg.line1roh;
							document.getElementById('line1re').value	= msg.line1re;
							jHide();
						},
						"json"     
					);
				}
			},
			"json"     
		);
	});
	$("#start_diagnostics2").click(function(){
		document.getElementById('line2hp').value	= "InProgress";
		document.getElementById('line2femf').value	= "InProgress";
		document.getElementById('line2rf').value	= "InProgress";
		document.getElementById('line2roh').value	= "InProgress";
		document.getElementById('line2re').value	= "InProgress";
		jProgress('<?php echo _("Check telephony line status, please wait...")?>', 60);
		$.post(
			"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
			{"get_status2":"true"},
			function(msg)
			{
				jHide();
				if ("Off-Hook" == msg.line2hook){
					jConfirm(
					'<?php echo _("Phone is Off-Hook, do you want to start the test anyway?")?>'
					, '<?php echo _("Are You Sure?")?>'
					, function(ret){
						if(ret){
							jProgress('<?php echo _("This may take several seconds...")?>', 60);
							$.post(
								"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
								{"start_diagnostics2":"true"},
								function(msg)
								{            
									document.getElementById('line2hp').value	= msg.line2hp;
									document.getElementById('line2femf').value	= msg.line2femf;
									document.getElementById('line2rf').value	= msg.line2rf;
									document.getElementById('line2roh').value	= msg.line2roh;
									document.getElementById('line2re').value	= msg.line2re;
									jHide();
								},
								"json"     
							);
						}
					});
				}
				else{
					jProgress('<?php echo _("This may take several seconds...")?>', 60);
					$.post(
						"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
						{"start_diagnostics2":"true"},
						function(msg)
						{            
							document.getElementById('line2hp').value	= msg.line2hp;
							document.getElementById('line2femf').value	= msg.line2femf;
							document.getElementById('line2rf').value	= msg.line2rf;
							document.getElementById('line2roh').value	= msg.line2roh;
							document.getElementById('line2re').value	= msg.line2re;
							jHide();
						},
						"json"     
					);
				}
			},
			"json"     
		);
	});
});
</script>
<div id="content">
<h1><?php echo _("Gateway > Connection > MTA > Line Diagnostics")?></h1>
<div id="educational-tip">
<p class="tip"><?php echo _("Information related to the MTA Line Diagnostics.")?></p>
</div>
<div class="module forms">
	<input type="hidden" value="mta_line_diagnostics" name="file"/>
	<input type="hidden" value="admin/" name="dir"/>
	<input type="hidden" name="line1" id="line1_name" value="1" />
	<h2><?php echo _("MTA Line 1 Diagnostics")?></h2>
	<div class="form-row">
		<!--div>Hazardous Potential:</div-->
<span class="valuemta"><?php echo _("Hazardous Potential:")?></span>
		<label for="line1hp" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1hp"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-row odd">
		<div><?php echo _("Foreign EMF:")?></div>
		<label for="line1femf" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1femf"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-row">
		<div><?php echo _("Resistive Faults:")?></div>
		<label for="line1rf" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1rf"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-row odd">
		<div><?php echo _("Receiver Off Hook:")?></div>
		<label for="line1roh" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1roh"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-row">
		<div><?php echo _("Ringer Equivalency:")?></div>
		<label for="line1re" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1re"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-btn odd">
		<input id="start_diagnostics1" type="button" value="<?php echo _("Start Diagnostics")?>" class="btn" />
	</div>
	<input type="hidden" name="webcheck">
</div> <!-- end .module -->
<div class="module forms">
	<input type="hidden" value="mta_line_diagnostics" name="file"/>
	<input type="hidden" value="admin/" name="dir"/>
	<input type="hidden" name="line2" id="line2_name" value="1" />
	<h2><?php echo _("MTA Line 2 Diagnostics")?></h2>
	<div class="form-row">
		<div><?php echo _("Hazardous Potential:")?></div>
		<label for="line2hp" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2hp"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-row odd">
		<div><?php echo _("Foreign EMF:")?></div>
		<label for="line2femf" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2femf"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-row">
		<div><?php echo _("Resistive Faults:")?></div>
		<label for="line2rf" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2rf"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-row odd">
		<div><?php echo _("Receiver Off Hook:")?></div>
		<label for="line2roh" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2roh"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-row">
		<div><?php echo _("Ringer Equivalency:")?></div>
		<label for="line2re" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2re"><?php echo _("Not Started")?></textarea>
	</div>
	<div class="form-btn odd">
		<input id="start_diagnostics2" type="button" value="<?php echo _("Start Diagnostics")?>" class="btn" />
	</div>
	<input type="hidden" name="webcheck">
</div> <!-- end .module -->
</div><!-- end #content -->
<!-- Page Specific Script -->
<?php include('includes/footer.php'); ?>
