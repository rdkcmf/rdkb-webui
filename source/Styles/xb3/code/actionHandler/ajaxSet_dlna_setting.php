<?php 

if (isset($_POST['set'])){
	setStr("Device.DLNA.X_CISCO_COM_DMS.Enable", $_POST['dlna_enabled'], true);
}

?>