<?php 

$flag = json_decode($_REQUEST['Enable'], true);

setStr("Device.X_Comcast_com_ParentalControl.ManagedSites.Enable", $flag['Enable'], true);

?>