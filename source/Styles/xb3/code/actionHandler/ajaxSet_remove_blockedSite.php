<?php 

$removeInfo = json_decode($_REQUEST['removeBlockInfo'], true);

delTblObj("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite." .$removeInfo['InstanceID']. ".");

?>