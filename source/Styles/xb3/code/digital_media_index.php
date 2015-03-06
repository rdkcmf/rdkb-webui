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
		<p class="tip">View your DLNA media library, sorted by type, hosted on USB storage or a shared network drive.</p>
  </div>


<div class="module" id="media-library">
<h2>Media Library</h2>

<ul class="tabs">
	<li  ><a href="digital_media_index_video.php">Video</a></li>
	<li><a href="digital_media_index_tvshow.php">TV Shows</a></li>
	<li class="selected"><a href="#">Music</a></li>
		<li ><a href="digital_media_index_picture.php">Pictures</a></li>

</ul>


<table class="sub-tabs">
<tr >
	<th width="15%">All Music</th>
	<th width="15%" >Genre</th>
	<th width="15%">Album</th>
	<th width="15%">Artist</th>
	<th width="15%">Folders</th>
	<th width="10%">Duration</th>
</tr>
</table>
<table class="data">
  <tr><td  width="15%">The Doors</td><td  width="15%">Classical </td><td  width="15%">The End</td><td  width="15%">Jim Morrison</td><td  width="15%">USB1/folder1</td><td  width="10%">4</td></tr>
  <tr><td  width="15%">The High</td><td  width="15%">Rock </td><td  width="15%">The Thriller</td><td  width="15%">Michael Jackson</td><td  width="15%">USB1/folder2</td><td  width="10%">3.5</td></tr>
  <tr><td  width="15%">Money</td><td  width="15%">Rock </td><td  width="15%">The Wall</td><td  width="15%">Pink Floyd</td><td  width="15%">Networksharename/</br>folder1</td><td  width="10%">3</td></tr>
  <tr><td  width="15%">Back B</td><td  width="15%">Fusion </td><td  width="15%">Millennium</td><td  width="15%">Backstreet Boys</td><td  width="15%">USB1/folder1</td><td  width="10%">4</td></tr>
  <tr><td  width="15%">Spirit</td><td  width="15%">Fusion </td><td  width="15%">Collection</td><td  width="15%">Madonna</td><td  width="15%">USB1/folder1</td><td  width="10%">4.5</td></tr>
  <tr><td  width="15%">Unforgiven</td><td  width="15%">Metal </td><td  width="15%">Real Metal</td><td  width="15%">Metallica</td><td  width="15%">USB1/folder4</td><td  width="10%">5</td></tr>

</table>

</div>

</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
