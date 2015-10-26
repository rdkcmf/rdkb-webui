<?php include('includes/header.php'); ?>

<!-- $Id: managed_services_add.php 2943 2009-08-25 20:58:43Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
 
<script type="text/javascript">
$(document).ready(function() {
comcast.page.init("Gateway > Connection > MTA > Line Diagnostics", "nav-mta-line-diagnostics");

	$("#start_diagnostics1").click(function(){
	
		var isTest1	= true;
		document.getElementById('line1hp').value	= "InProgress";
		document.getElementById('line1femf').value	= "InProgress";
		document.getElementById('line1rf').value	= "InProgress";
		document.getElementById('line1roh').value	= "InProgress";
		document.getElementById('line1re').value	= "InProgress";
		
		jProgress('Check telephony line status, please wait...', 60);
		$.post(
			"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
			{"get_status1":"true"},
			function(msg)
			{
				jHide();
				if ("Off-Hook" == msg.line1hook){
					jConfirm(
					'Phone is Off-Hook, do you want to start the test anyway?'
					, 'Are You Sure?'
					, function(ret){
						if(ret){
							jProgress('This may take several seconds...', 60);
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
					jProgress('This may take several seconds...', 60);
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
		
		jProgress('Check telephony line status, please wait...', 60);
		$.post(
			"actionHandler/ajaxSet_mta_Line_Diagnostics.php",
			{"get_status2":"true"},
			function(msg)
			{
				jHide();
				if ("Off-Hook" == msg.line2hook){
					jConfirm(
					'Phone is Off-Hook, do you want to start the test anyway?'
					, 'Are You Sure?'
					, function(ret){
						if(ret){
							jProgress('This may take several seconds...', 60);
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
					jProgress('This may take several seconds...', 60);
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
<h1>Gateway > Connection > MTA > Line Diagnostics</h1>
<div id="educational-tip">
<p class="tip">Information related to the MTA Line Diagnostics.</p>
</div>

<div class="module forms">
	<input type="hidden" value="mta_line_diagnostics" name="file"/>
	<input type="hidden" value="admin/" name="dir"/>
	<input type="hidden" name="line1" id="line1_name" value="1" />

	<h2>MTA Line 1 Diagnostics</h2>
	<div class="form-row">
		<!--div>Hazardous Potential:</div-->
<span class="valuemta">Hazardous Potential:</span>
		<label for="line1hp" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1hp">Not Started</textarea>
	</div>
	<div class="form-row odd">
		<div>Foreign EMF:</div>
		<label for="line1femf" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1femf">Not Started</textarea>
	</div>
	<div class="form-row">
		<div>Resistive Faults:</div>
		<label for="line1rf" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1rf">Not Started</textarea>
	</div>
	<div class="form-row odd">
		<div>Receiver Off Hook:</div>
		<label for="line1roh" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1roh">Not Started</textarea>
	</div>
	<div class="form-row">
		<div>Ringer Equivalency:</div>
		<label for="line1re" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line1re">Not Started</textarea>
	</div>
	
	<div class="form-btn odd">
		<input id="start_diagnostics1" type="button" value="Start Diagnostics" class="btn" />
	</div>
	<input type="hidden" name="webcheck">
</div> <!-- end .module -->

<div class="module forms">
	<input type="hidden" value="mta_line_diagnostics" name="file"/>
	<input type="hidden" value="admin/" name="dir"/>
	<input type="hidden" name="line2" id="line2_name" value="1" />

	<h2>MTA Line 2 Diagnostics</h2>
	<div class="form-row">
		<div>Hazardous Potential:</div>
		<label for="line2hp" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2hp">Not Started</textarea>
	</div>
	<div class="form-row odd">
		<div>Foreign EMF:</div>
		<label for="line2femf" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2femf">Not Started</textarea>
	</div>
	<div class="form-row">
		<div>Resistive Faults:</div>
		<label for="line2rf" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2rf">Not Started</textarea>
	</div>
	<div class="form-row odd">
		<div>Receiver Off Hook:</div>
		<label for="line2roh" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2roh">Not Started</textarea>
	</div>
	<div class="form-row">
		<div>Ringer Equivalency:</div>
		<label for="line2re" class="acs-hide"></label>
		<textarea class="textAreaDiagnostic" cols="80" rows="3" value="" id="line2re">Not Started</textarea>
	</div>
	
	<div class="form-btn odd">
		<input id="start_diagnostics2" type="button" value="Start Diagnostics" class="btn" />
	</div>
	<input type="hidden" name="webcheck">
</div> <!-- end .module -->


</div><!-- end #content -->

<!-- Page Specific Script -->

<?php include('includes/footer.php'); ?>
