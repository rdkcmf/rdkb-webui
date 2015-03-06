<?php include('includes/header.php'); ?>

<!-- $Id: addins.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Add-ins > Overview", "nav-config-backup");
});
function f(){
return openWindow(this, {width:790,height:450,center:true});

}

function openWindow(anchor, options) {

	var args = '';

	if (typeof(options) == 'undefined') { var options = new Object(); }
	if (typeof(options.name) == 'undefined') { options.name = 'win' + Math.round(Math.random()*100000); }

	if (typeof(options.height) != 'undefined' && typeof(options.fullscreen) == 'undefined') {
		args += "height=" + options.height + ",";
	}

	if (typeof(options.width) != 'undefined' && typeof(options.fullscreen) == 'undefined') {
		args += "width=" + options.width + ",";
	}

	if (typeof(options.fullscreen) != 'undefined') {
		args += "width=" + screen.availWidth + ",";
		args += "height=" + screen.availHeight + ",";
	}

	if (typeof(options.center) == 'undefined') {
		options.x = 0;
		options.y = 0;
		args += "screenx=" + options.x + ",";
		args += "screeny=" + options.y + ",";
		args += "left=" + options.x + ",";
		args += "top=" + options.y + ",";
	}

	if (typeof(options.center) != 'undefined' && typeof(options.fullscreen) == 'undefined') {
		options.y=Math.floor((screen.availHeight-(options.height || screen.height))/2)-(screen.height-screen.availHeight);
		options.x=Math.floor((screen.availWidth-(options.width || screen.width))/2)-(screen.width-screen.availWidth);
		args += "screenx=" + options.x + ",";
		args += "screeny=" + options.y + ",";
		args += "left=" + options.x + ",";
		args += "top=" + options.y + ",";
	}

	if (typeof(options.scrollbars) != 'undefined') { args += "scrollbars=1,"; }
	if (typeof(options.menubar) != 'undefined') { args += "menubar=1,"; }
	if (typeof(options.locationbar) != 'undefined') { args += "location=1,"; }
	if (typeof(options.resizable) != 'undefined') { args += "resizable=1,"; }

	var win = window.open(anchor, options.name, args);
	return false;

}

</script>

<div id="content">
	<h1>Add-ins > Overview</h1>

    <div id="educational-tip">
		<p class="tip">TIP: Click on the configure button to configure the Addin device.</p>
    </div>

	<div class="module data">
		<h2>Add-ins</h2>

					<input type="button" onclick="f()"> Configure</td>
				</div>


	</div> <!-- end .module -->
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
