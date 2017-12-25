<?php
include_once __DIR__ .'/CSRF-Protector-PHP/libs/csrf/csrfprotector_rdkb.php';
//Initialise CSRFGuard library
csrfprotector_rdkb::init();
?>
<html>
<head>
	<title>Access blocked</title>
</head>
<body>
<h2>Access blocked! Please contact with Administrator!</h2>
</body>
</html>
<script>
	alert('Access blocked! Please contact with Administrator!');
</script>
