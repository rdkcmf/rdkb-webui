<?php include('includes/header.php'); ?>

<!-- $Id: connected_devices_computers.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Connected Devices - Range Extenders - add Range Extenders ", "nav-range-extenders");
	$("#security-mode").change(function() {
	        var $security_select = $(this);

			var $network_password = $("#network_password");

	        if($security_select.find("option:selected").val() != "NONE") {

				$network_password.val("");
				$network_password.prop("disabled", false);

	        } else {

	            $network_password.val("");
	            $network_password.prop("disabled", true);
	        }
    }).trigger("change");
});

</script>

<div id="content">
	<h1>Connected Devices > Range Extender >Add Range Extenders</h1>

    <div class="module forms">
    <h2> Add Range Extenders</h2>
	<div class="form-row odd">
				<label for="ssid">SSID:</label> <input type="text"  size="25"  name="ssid" id="ssid" />
		</div>
		<div class="form-row ">
						<label for="mac">MAC:</label> <input type="text"  size="25"  name="mac" id="mac" />
		</div>
		<div class="form-row odd">
					<label for="channel" class="readonlyLabel">Channel:</label>
					<select id="channel">
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
						<option>5</option>
						<option>6</option>
						<option>7</option>
						<option>8</option>
						<option>9</option>
						<option>10</option>
						<option>11</option>

					</select>
					</div>
					<div class="form-row">
										<label for="security-mode" class="readonlyLabel">Security Mode:</label>
										<select id="security-mode">
											<option>NONE</option>
											<option>WEP Open</option>
											<option>WEP Shared</option>
											<option>WPA-TKIP</option>
											<option>WPA-AES</option>
											<option>WPA2-TKIP</option>
											<option>WPA2-AES</option>


										</select>
					</div>
					<div class="form-row odd">
									<label for="network_password">Network Password:</label> <input type="text"  size="25"  name="network_password" id="network_password" />
					</div>
					<div class="form-row form-btn">
					            	<a href="range_extenders.php" class="btn" title="">Add</a>
					            	<input type="button" id="btn-cancel" class="btn alt reset" value="Cancel"/>
            			</div>




		</div>



</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
