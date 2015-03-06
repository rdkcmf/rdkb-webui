<?php include('includes/header.php'); ?>

<!-- $Id: managed_apps.php 2943 2009-08-25 20:58:43Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Parental Control > Managed Applications", "nav-applications");
    
    $(".radio-btns").radioToButton({ autoSubmitForm:true });

});
</script>

<div id="content">
	<h1>Parental Control > Managed Applications</h1>
	<div id="educational-tip">
		<p class="tip"> Some useful help text needed here.</p>
		<p class="hidden">Some more useful text might be needed here.</p>
	</div>
	<div class="module data">
		<h2>Blocked Applications</h2>
		<p class="button"><a href="managed_apps_add.php" class="btn">+ Add</a></p>
		<table id="blocked-apps" class="data">
	    <tr>
            <th class="number"></th>
            <th class="apps">Applications</th>
            <th class="port">Starting Port</th>
            <th class="port">Ending Port</th>
            <th class="when">When</th>
            <th class="edit">&nbsp;</th>
            <th class="delete">&nbsp;</th>
	    </tr>
	    <tr class="odd">
                <th class="row-label alt number">1</th>
				<td>Second Life</td>
				<td>13000</td>
				<td>13050</td>
				<td>Always</td>
                <td class="edit"><a href="managed_apps_add.php" class="btn">edit</a></td>
                <td class="delete"><a href="#" class="btn confirm" title="delete blocked application Second Life">x</a></td>
	       </tr>
		</table>
	</div> <!-- end .module -->

	<form action="managed_apps.php" method="post">
	<input type="hidden" name="update_trusted_computers" value="true" />
	<div class="module data">
		<h2>Trusted Computers</h2>
		<table id="trusted_computers" class="data">
	    	<tr>
	    		<th class="number">&nbsp;</th>
		        <th class="computer_name">Computer Name</th>
		        <th class="ip">IP</th>
		        <th class="trusted">Trusted</th>
		    </tr>
		    <tr class="odd">
		    	<th class="row-label alt">1</th>
		    	<td>seth's computer</td>
				<td>10.0.0.0</td>
				<td>
					<ul class="radio-btns">
						<li class="radio-off"><input id="comp1_not_trusted" value="not_trusted" type="radio" name="comp1_trusted" /> <label for="comp1_not_trusted">No</label></li>
						<li><input id="comp1_trusted" value="yes" type="radio" checked="checked" name="comp1_trusted" /> <label for="comp1_trusted">Yes</label></li>
					</ul>
				</td>
		  	</tr>
		  	<tr>
			  	<th class="row-label alt">2</th>
		    	<td>carlos' linux machine</td>
				<td>10.0.0.0</td>
				<td>
					<ul class="radio-btns">
						<li class="radio-off"><input id="comp1_not_trusted" value="not_trusted" checked="checked" type="radio" name="comp2_trusted" /> <label for="comp1_not_trusted">No</label></li>
						<li><input id="comp1_trusted" value="yes" type="radio"  name="comp2_trusted" /> <label for="comp1_trusted">Yes</label></li>
					</ul>
				</td>
			</tr>
		</table>
	</div> <!-- end .module -->
	</form>

</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
