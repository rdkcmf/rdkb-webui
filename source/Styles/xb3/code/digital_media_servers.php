<?php include('includes/header.php'); ?>

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php 

	$dms = array(
		'AllowedTypes' => getStr("Device.DLNA.X_CISCO_COM_DMS.AllowedMediaTypes"),
		'AllowedSource' => getStr("Device.DLNA.X_CISCO_COM_DMS.AllowMediaSource"),
		'MediaIndexPath' => getStr("Device.DLNA.X_CISCO_COM_DMS.MediaIndexPath"),

	);

	$js_dms = json_encode($dms);

 ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > Media Sharing > DLNA > Digital Media Servers", "nav-dlna-media-servers");

    var js_dms = <?php echo $js_dms; ?>;
    //judge if allowed_types is a substr of js_dms.AllowedTypes
    var allowed_types = [];
    

    $("input[name='path']").click(function() {
    	var mode = $(this).val();
    	if(mode == "USB1") {
    		$("#index_path").val("USB1");
    	} else if(mode == "USB2") {
    		$("#index_path").val("USB2");
    	}
    	else if(mode == "Internal") {
    		$("#index_path").val("Internal");
    	}
    });

});

</script>

<div id="content">
    <h1>Advanced > Media Sharing > DLNA > Digital Media Servers</h1>

    <div id="educational-tip">
    	<p class="tip">Configure the DLNA Media Server.</p>
    	<p class="hidden">Select the media types that the Gateway should serve over DLNA. </p>
    	<p class="hidden">Choose the media sources the Gateway should accept (USB, shared network drives, or both).  </p>
    	<p class="hidden">For a large collection of media (pictures, video, music), use the connected USB drive as the media index path.</p>

    </div>

    <form  method="post" id="pageForm">
    	<div class="module data data">
    		<h2>Digital Media Server</h2>
    		<div class="form-row">
    			<label for="Media_Types_Video">Select Allowed Media Types:</label>
    			<input type="checkbox"  id="Media_Types_Video" checked="checked"><b> Video </b></input>
    			<input type="checkbox"  id="Media_Types_Audio" checked="checked"><b>Audio</b></input>
    			<input type="checkbox"  id="Media_Types_Pictures" checked="checked"><b>Pictures</b></input>
    			<input type="checkbox"  id="Media_Types_Recorded TV " checked="checked"><b>Recorded TV </b></input>

    		</div>
    		<div class="form-row odd">
    			<label for="Media_Sources_USB">Select Allowed Media Sources:</label>
    			<input type="checkbox"   id="Media_Sources_USB " checked="checked"><b> USB  </b></input>
    			<input type="checkbox"   id="Media_Sources_Shared Network Drives" ><b>Shared Network Drives</b></input>
    		</div>
    		<div class="form-row " id="hid1">
    			<label for="Media_Index">Select the Media Index path:</label>
    			<input type="text" id="index_path" value="Internal" size="8" disabled="disabled"> </input>

    		</div>
    		<div id="hid" >
    			<input type="radio"  name="path" id="USB1" value="USB1" ><b> USB1 </b></input></br>
    			<input type="radio"  name="path" id="USB2"  value="USB2"><b> USB2</b></input></br>
    			<input type="radio"  name="path" id="Internal" value="Internal" checked="checked"  ><b>Internal</b></input></br>

    			<div class="form-btn">
    				<input  type="button" value="Save Setting" id="save" name="save" class="btn" />
    			</div>
    		</div>

    	</form>
</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
