<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Folder browser</title>
<style type="text/css">
body {
    color: #666666;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 12px;
    line-height: 1.5;
}
</style>

<link rel="stylesheet" type="text/css" media="screen" href="cmn/css/lib/jquery.foldertree.css" />
<script type="text/javascript" src="cmn/js/lib/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="cmn/js/lib/jquery.foldertree.js"></script>

<script type="text/javascript">

var home_dir = "USB1";

$(document).ready(function(){
	home_dir = $(window.parent.document).find("#Device").val();
	
	$("#smallbrowser").folderTree({
		root: '/mnt/'+home_dir.toLowerCase()+'/',
		script: 'actionHandler/ajaxSet_samba_server_foldertree.php?id='+'1',
		loadMessage: 'Retrieving directory tree, please wait...'
	});
	
	home_dir_name();
});

function home_dir_name()
{
	if ($(".home").length > 0)
	{
		$(".home").html(home_dir);
		return;
	}
	setTimeout("home_dir_name()", 100);
}

</script>
</head>

<body>
<!--h3>Please select a folder to share:</h3>
<!--p>Tips: Click "+" to explore sub directory</p-->
<div id="smallbrowser"></div>
<!--p>© 2011 Copyright <a href="http://www.htmlab.gr/">&lt;htmlab<sup>®</sup>&gt;</a>. All Rights Reserved. Valid XHTML and CSS.</p-->
</body>

</html>
