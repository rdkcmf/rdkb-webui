<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>XFINITY</title>

	<!--CSS-->
	<link rel="stylesheet" type="text/css" media="screen" href="cmn/css/common-min.css" />
	<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="cmn/css/ie6-min.css" />
	<![endif]-->
	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="cmn/css/ie7-min.css" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" media="print" href="cmn/css/print.css" />

	<!--Character Encoding-->
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

    <script type="text/javascript" src="./cmn/js/lib/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery-migrate-1.2.1.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.validate.js"></script>
    <script type="text/javascript" src="./cmn/js/lib/jquery.alerts.js"></script>
	<script type="text/javascript" src="./cmn/js/lib/jquery.alerts.progress.js"></script>

	<script type="text/javascript" src="./cmn/js/utilityFunctions.js"></script>
    <script type="text/javascript" src="./cmn/js/comcast.js"></script>
	<script type="text/javascript">

     $(document).ready(function() { 

	 	$('#restoreBtn').click(function(e){

		e.preventDefault();

		jConfirm(
		"Alert: Click 'OK' would lost your current configuration ! \nAre you sure you want to restore saved configuration?"
		,"Restore Saved Configuration"
		,function(ret) {
		if(ret) { 

			var path=document.getElementById('id1').value;
			if((path==null || path=="")){
				alert("Please Select a file to Restore the Configuration!");
			}
			else{
				$('form').submit();
			}
		} } );

	 });

	 $("#id1").focus();

	 });

	</script>
</head>

<body style="background-color: #ffffff;">
	<form enctype="multipart/form-data" action="at_mycomputer_upload.php" method="post">
		<input id="id1" name="file" type="file" style="border: solid 1px;">   </input>
		</br>
		</br>
		<input id="restoreBtn" type="button" value="Restore"> </input>
	</form>
</body>
</html>

