 <?php include('includes/header.php'); ?>

<!-- $Id: digital_media_players.php 3103 2009-09-29 18:00:17Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > Media Sharing > DLNA > Digital Media Index", "nav-dlna-media-index");
});
</script>

<div id="content">
	<h1>Advanced > Media Sharing > DLNA > Digital Media Index</h1>

  <div id="educational-tip">
		<p class="tip">TIP: This page has the list of devices that are allowed to connect to the network as per the rules configured here.</p>
  </div>


<div class="module" id="media-library">
<h2>Media Library</h2>

<ul class="tabs">
	<li ><a href="digital_media_index_video.php">Video</a></li>
	<li ><a href="digital_media_index_tvshow.php">TV Shows</a></li>
	<li ><a href="digital_media_index.php">Music</a></li>
	<li class="selected"><a href="#">Pictures</a></li>


</ul>


<table class="sub-tabs">
<tr >
	<th width="25%">Name</th>
	<th width="25%" >Type</th>
<th width="25%">Folders</th>
	<th width="25%">Comments</th>

</tr>
</table>
<table class="data">
  <tr><td  width="15%">Friends</td><td  width="15%">jpeg </td><td  width="15%">USB1/folder1</td><td  width="15%">College farewell</td></tr>
  <tr><td  width="15%">Family</td><td  width="15%">png</td><td  width="15%">USB1/folder2</td><td  width="15%">Home and around the city</td></tr>
  <tr><td  width="15%">Cousins</td><td  width="15%">jpeg </td><td  width="15%">USB1/folder1</td><td  width="15%">Trip to disneyland</td></tr>


</table>

</div>

</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
