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
<!-- $Id: network_storage_edit.php 3158 2010-01-08 23:32:05Z slemoine $ -->
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Connected Devices - Edit Network Storage", "nav-network-storage");
    $("#pageForm").validate({
        rules: {
            storage_name: {
                required: true
            }
		}
    });
});
</script>
<div id="content">
	<h1>Connected Devices > Network Storage > Edit Network Storage</h1>
	<form id="pageForm" action="network_storage.php">
		<div class="module forms">
			<h2>Edit Network Storage</h2>
			<div class="form-row odd">
				<label for="storage_name">Storage Name</label> <input type="text" name="storage_name" id="storage_name" class="text" maxlength="40" value="" /> 
			</div>
			<div class="form-row">
				<label for="file_system" class="readonlyLabel">File System</label> <span id="file_system" class="value">FAT 32</span>
			</div> 
			<div class="form-row odd">
				<label for="space" class="readonlyLabel">Space Available</label> <span id="space" class="value">500 GB</span>
			</div>
			<div class="form-row">
				<label for="space" class="readonlyLabel">Total Space</label> <span id="space" class="value">500 GB</span>
			</div>			
			<div class="form-row odd">
				<label for="location" class="readonlyLabel">Location</label> <span id="location" class="value">USB Port 4</span>
			</div>
	<div class="form-btn">
		<input type="button" class="btn submit" value="Save"/>
		<input type="reset" class="btn alt reset" value="Cancel"/>
	</div>
		</div> <!-- end .module -->
	</form>
</div><!-- end #content -->
<?php include('includes/footer.php'); ?>
