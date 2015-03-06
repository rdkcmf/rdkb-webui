<?php include('includes/header.php'); ?>

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php 
    $dlnaEnable = getStr("Device.DLNA.X_CISCO_COM_DMS.Enable");
    if ($_DEBUG) {
        $dlnaEnable = 'true';
    }
?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > Media Sharing > DLNA > DLNA Settings", "nav-dlna-settings");

    $("#dlna_switch").radioswitch({
        id: "dlna-switch",
        radio_name: "DLNA",
        id_on: "dlna_enabled",
        id_off: "dlna_disabled",
        title_on: "Enable DLNA",
        title_off: "Disable DLNA",
        state: <?php echo ($dlnaEnable === "true" ? "true" : "false"); ?> ? "on" : "off"
    });

    $('#save-dlna').click(function(event) {
        event.preventDefault();

        var dlna_enabled = $("#dlna_switch").radioswitch("getState").on ? "true" : "false";

        jProgress("This may take several seconds.",60);
        $.ajax({
            type:"POST",
            url:"actionHandler/ajaxSet_dlna_setting.php",
            data:{set:"true", dlna_enabled:dlna_enabled},
            dataType: "json",
            success:function(){
                jHide();
                window.location.reload();
            },
            error: function(){
                jHide();
                jAlert("Error! Please try later!");
            }
        }); //end of ajax

    });


});
</script>

<div id="content">
	<h1>Advanced > Media Sharing > DLNA > DLNA Settings</h1>

     <div id="educational-tip">
	        <p class="tip">Manage DLNA settings.</p>
	         <p class="hidden">DLNA allows the sharing of digital media between devices such as computers, laptops, printers, cameras, tablets, cell phones and other multimedia products.</p>
	         <p class="hidden">DLNA can be enabled or disabled. When enabled, the Gateway will act as a Digital Media Server.</p>

    </div>

	<form  id="pageForm">
        <div class="module">
            <h2>DLNA Settings</h2>
        
            <div class="select-row odd">
                <span class="readonlyLabel ">DLNA:</span>
                <span id="dlna_switch"></span>
            </div>
               
			<div class="btn-group">
				<input type="button" id="save-dlna" name="save-dlna" value="Save" class="btn" />
			</div>
		</form>
</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
