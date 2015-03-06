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
	<li class="selected"><a href="#">Video</a></li>
	<li><a href="digital_media_index_tvshow.php">TV Shows</a></li>
	<li ><a href="digital_media_index.php">Music</a></li>
		<li ><a href="digital_media_index_picture.php">Pictures</a></li>

</ul>


<table class="sub-tabs">
<tr >
	<th width="15%">Name</th>
	<th width="15%" >Genre</th>

	<th width="15%">Artist</th>
	<th width="15%">Duration</th>
	<th width="15%">Folders</th>
	<th width="10%">Rating</th>
</tr>
</table>
<table class="data">
  <tr><td  width="15%">Inception</td><td  width="15%">Fiction</td><td  width="15%">Leonardo</td><td  width="15%" >150min</td><td  width="15%" >USB1/folder1</td><td  width="10%">G</td></tr>
  <tr><td  width="15%">Avatar</td><td  width="15%">Action </td><td  width="15%"> Sam Worthington</td><td  width="15%">180min</td><td  width="15%" >USB1/folder2</td><td  width="10%">PG</td></tr>
  <tr><td  width="15%">Titanic</td><td  width="15%">Drama </td><td  width="15%">Kate Winslet </td><td  width="15%" >120min</td><td  width="15%" >Networksharename/</br>folder4</td><td  width="10%">R</td></tr>


</table>

</div>

</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
