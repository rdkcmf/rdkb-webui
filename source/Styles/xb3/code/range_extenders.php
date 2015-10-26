<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: connected_devices_computers.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php 
	$ret = init_psmMode("Connected Devices - Range Extenders", "nav-range-extenders");
	if ("" != $ret){echo $ret;	return;}
?>

<?php
/*
//Fetch enabled SSID (no more than 4) per radio
$radio_2G = array();
$radio_5G = array();
$ids = explode(",", getInstanceIds("Device.WiFi.SSID."));
foreach ($ids as $i)
{
	if ("true" == getStr("Device.WiFi.SSID.$i.Enable"))
	{
		if ("Device.WiFi.Radio.1." == getStr("Device.WiFi.SSID.$i.LowerLayers"))
		{
			if (count($radio_2G) < 5)
			{
				array_push($radio_2G, $i);
			}
		}
		else
		{
			if (count($radio_5G) < 5)
			{
				array_push($radio_5G, $i);
			}
		}
	}
}

//Fetch all online device (include disconnect device), MAC of comma separated string
$online_client		= array_trim(explode(",", getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.X_CISCO_COM_ONLINE_CLIENT")));
$disconnect_client	= array_trim(explode(",", getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.X_CISCO_COM_DISCONNECT_CLIENT")));
// $online_client		= array_trim(explode(",", "  00:11:22:33:44:11  ,  00:11:22:33:44:99  ,  00:11:22:33:44:bb    "));
// $disconnect_client	= array_trim(explode(",", "  00:11:22:33:44:11  ,  00:11:22:33:44:22  ,  00:11:22:33:44:33, 00:11:22:33:44:44, 00:11:22:33:44:55, 00:11:22:33:44:66, 00:11:22:33:44:77, 00:11:22:33:44:88 "));
// var_dump($disconnect_client);

//Establish data structure
$dat = array();
for ($i=0; $i<count($online_client); $i++)
{
	$dat[$i]['ext_name']	= "Range Extender ".($i+1);
	$dat[$i]['ext_mac']		= $online_client[$i];
	$dat[$i]['ssid_info']	= array();
	
	if (0 == $i)
	{
		for ($j=0; $j<count($radio_2G); $j++)
		{
			array_push($dat[$i]['ssid_info'], array(
				// 'id'		=> $radio_2G[$j],
				'ssid'		=> getStr("Device.WiFi.SSID.$radio_2G[$j].SSID"),
				'bssid'		=> $online_client[$i],
				'freq'		=> "2.4 GHz",
				'channel'	=> getStr("Device.WiFi.Radio.1.Channel"),
				'secur'		=> encrypt_map(getStr("Device.WiFi.AccessPoint.$radio_2G[$j].Security.ModeEnabled"), getStr("Device.WiFi.AccessPoint.$radio_2G[$j].Security.X_CISCO_COM_EncryptionMethod"))			
				));
		}
		
		for ($j=0; $j<count($radio_5G); $j++) 
		{
			array_push($dat[$i]['ssid_info'], array(
				// 'id'		=> $radio_5G[$j],
				'ssid'		=> getStr("Device.WiFi.SSID.$radio_5G[$j].SSID"),
				'bssid'		=> substr($online_client[$i], 0, 16).dechex(hexdec(substr($online_client[$i], -1, 1)) + 1),
				'freq'		=> "5 GHz",
				'channel'	=> getStr("Device.WiFi.Radio.2.Channel"),
				'secur'		=> encrypt_map(getStr("Device.WiFi.AccessPoint.$radio_5G[$j].Security.ModeEnabled"), getStr("Device.WiFi.AccessPoint.$radio_5G[$j].Security.X_CISCO_COM_EncryptionMethod"))			
				));
		}
	}
	else
	{
		$dat[$i]['ssid_info']	= $dat[0]['ssid_info'];		//other extender share the same SSID info, but different extender mac
		
		for ($j=0; $j<(count($radio_2G)+count($radio_5G)); $j++)
		{
			if ("2.4 GHz" == $dat[$i]['ssid_info'][$j]['freq'])
			{
				$dat[$i]['ssid_info'][$j]['bssid']	= $online_client[$i];	
			}
			else
			{
				$dat[$i]['ssid_info'][$j]['bssid']	= substr($online_client[$i], 0, 16).dechex(hexdec(substr($online_client[$i], -1, 1)) + 1);
			}
		}
	}
	
	if (in_array($online_client[$i], $disconnect_client))
	{
		$dat[$i]['con_action']	= "Connect";
	}
	else
	{
		$dat[$i]['con_action']	= "Disconnect";
	}
}

$arConfig = array('dat'=>$dat, 'online_client'=>$online_client, 'disconnect_client'=>$disconnect_client);
$jsConfig = json_encode($arConfig);
*/

/************************************!!!Backend Design Changed!!!**********************************************/
$online_client		= array();
$disconnect_client	= array_trim(explode(",", getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.X_CISCO_COM_DISCONNECT_CLIENT")));

// get all wired attached MoCA extender (including connected/disconnected(just disable radio))
$dat	= array();
$exts	= explode(",", getInstanceIds("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice."));
// $exts	= explode(",", "");
$exts	= array_trim($exts);

foreach ($exts as $i){
	$dat[$i]['ext_name']	= getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice.$i.DeviceName");
	$dat[$i]['ext_ip']		= trim(getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice.$i.IPAddress"));
	$dat[$i]['ext_action']	= in_array($dat[$i]['ext_ip'], $disconnect_client) ? "Connect" : "Disconnect";
	$dat[$i]['ssid_info']	= array();
	
	$ssids = explode(",", getInstanceIds("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice.$i.SSID."));
	foreach ($ssids as $j){
		array_push($dat[$i]['ssid_info'], array(
			'ssid'		=> getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice.$i.SSID.$j.SSID"),
			'bssid'		=> getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice.$i.SSID.$j.BSSID"),
			'freq'		=> getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice.$i.SSID.$j.Band"),
			'channel'	=> getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice.$i.SSID.$j.Channel"),
			'secur'		=> encrypt_map(getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice.$i.SSID.$j.SecurityMode"), getStr("Device.MoCA.X_CISCO_COM_WiFi_Extender.ExtenderDevice.$i.SSID.$j.Encryption"))			
		));
	}
	
	array_push($online_client, $dat[$i]['ext_ip']);
}

$arConfig = array('dat'=>$dat, 'online_client'=>$online_client, 'disconnect_client'=>$disconnect_client);
$jsConfig = json_encode($arConfig);


?>

<style>
#range_extender td
  {
  border: 1px solid #39BAF1;
  }

</style>

<script type="text/javascript">

    $(document).ready(function() {
        comcast.page.init("Connected Devices - Range Extenders", "nav-range-extenders");
		
		var obj					= eval('(' + '<?php echo $jsConfig;?>' + ')'); 
		var dat					= obj.dat;
		var online_client		= obj.online_client;
		var disconnect_client	= obj.disconnect_client;
		var odd					= false;
		// alert(dat[0].ssid_info[0].ssid);

		if (0 == online_client.length)
		{
			$("#no_client").show();
		}
		
/* 		for (var i=0; i<dat.length; i++)
		{
			for (var j=0; j<dat[i].ssid_info.length; j++)
			{
				$("#range_extender").append('\
					<tr id="'+ dat[i].ext_mac +'" class="'+ ((odd=!odd) ? "odd" : "") +'">\
						<td class="ext_name" rowspan="'+dat[i].ssid_info.length+'" style="display:'+ (j?"none":"") +'">'+ dat[i].ext_name +'</td>\
						<td class="ssid">'+ dat[i].ssid_info[j].ssid +'</td>\
						<td class="bssid">'+ dat[i].ssid_info[j].bssid +'</td>\
						<td class="freq">'+ dat[i].ssid_info[j].freq +'</td>\
						<td class="channel">'+ dat[i].ssid_info[j].channel +'</td>\
						<td class="secur">'+ dat[i].ssid_info[j].secur +'</td>\
						<td class="con_action" align="center" rowspan="'+dat[i].ssid_info.length+'" style="display:'+ (j?"none":"") +'"><a class="btn connect_switch">'+ dat[i].con_action +'</a></td>\
					</tr>');
			}
		} */
		
		$.each(dat, function(idx, val)		// the index may be 1,3,5... instead of 1,2,3...
		{
			var k=0;
			for (var j=0; j<val.ssid_info.length; j++)
			{
				if ("admin" == "<?php echo $_SESSION["loginuser"]; ?>"){
					if (!(val.ssid_info[j].ssid.toLowerCase().indexOf("xhs")!=-1 ||  val.ssid_info[j].ssid.toLowerCase().indexOf("xhh")!=-1)) {
						$("#range_extender").append('\
							<tr id="'+ val.ext_ip +'" class="'+ ((odd=!odd) ? "odd" : "") +'">\
								<td headers="ext-name" class="ext_name" rowspan="'+val.ssid_info.length+'" style="display:'+ (k?"none":"") +'">'+ val.ext_name +'</td>\
								<td headers="ssid" class="ssid">'+ val.ssid_info[j].ssid +'</td>\
								<td headers="bssid" class="bssid">'+ val.ssid_info[j].bssid +'</td>\
								<td headers="frequency-band" class="freq">'+ val.ssid_info[j].freq +'</td>\
								<td headers="channel" class="channel">'+ val.ssid_info[j].channel +'</td>\
								<td headers="security-mode" class="secur">'+ val.ssid_info[j].secur +'</td>\
								<td headers="disconnect-btn" class="ext_action" align="center" rowspan="'+val.ssid_info.length+'" style="display:'+ (k?"none":"") +'"><a href="javascript:void(0);" title="'+val.ext_name+'" id="del_'+idx+j+'" class="btn connect_switch" tabindex="0">'+ val.ext_action +'</a></td>\
							</tr>');
					}
				}
				else {
					$("#range_extender").append('\
						<tr id="'+ val.ext_ip +'" class="'+ ((odd=!odd) ? "odd" : "") +'">\
							<td headers="ext-name" class="ext_name" rowspan="'+val.ssid_info.length+'" style="display:'+ (k?"none":"") +'">'+ val.ext_name +'</td>\
							<td headers="ssid" class="ssid">'+ val.ssid_info[j].ssid +'</td>\
							<td headers="bssid" class="bssid">'+ val.ssid_info[j].bssid +'</td>\
							<td headers="frequency-band" class="freq">'+ val.ssid_info[j].freq +'</td>\
							<td headers="channel" class="channel">'+ val.ssid_info[j].channel +'</td>\
							<td headers="security-mode" class="secur">'+ val.ssid_info[j].secur +'</td>\
							<td headers="disconnect-btn" class="ext_action" align="center" rowspan="'+val.ssid_info.length+'" style="display:'+ (k?"none":"") +'"><a href="javascript:void(0);" title="'+val.ext_name+'" id="del_'+idx+j+'" class="btn connect_switch" tabindex="0">'+ val.ext_action +'</a></td>\
						</tr>');
				}
				k=k+1;
			}
			// console.log(idx);
		});
		
		$(".connect_switch").click(function(){
			var onl_mac		= online_client;
			var dis_mac		= disconnect_client;
			var ext_mac		= $(this).parents("tr:eq(0)").attr('id');
			var prev_action	= $(this).text();
			
			jConfirm(
			'Do you want to '+prev_action+' it?'
			, 'Are You Sure?'
			, function(ret){
				if(ret){
					if ("Disconnect" == prev_action){
						dis_mac.push(ext_mac);
						if (dis_mac.length > 8)		//delete (the older one) && (not show in online list), write no more than 8 into disconnect list
						{
							for (var i=0; i<dis_mac.length; i++)
							{
								if ($.inArray(dis_mac[i], onl_mac) == -1)
								{
									dis_mac.splice(i, 1);
									break;
								}
							}
						}
					}
					else{
						for (var i=0; i<dis_mac.length; i++)
						{
							if (ext_mac == dis_mac[i])
							{
								dis_mac.splice(i, 1);
								break;
							}
						}
					}
				
					var jsConfig 	=	'{"ext_mac":"'+ext_mac
						+'", "dis_mac":"'+dis_mac.join()
						+'"}';

					jProgress('This may take several seconds...', 60);
					
					$.ajax({
						type: "POST",
						url: "actionHandler/ajaxSet_range_extenders.php",
						data: { configInfo: jsConfig },
						success: function(msg) {
							jHide();
							location.reload();
						},
						error: function(){            
							jHide();
							jAlert("Failure, please try again.");
						}
					});
				}
			});
		});
	});
</script>

<style type="text/css">

.module.data{
	word-wrap:break-word;
	overflow:auto;
}

#range_extender td, #range_extender td{
	font-size: 90%;
	padding: 6px 3px 6px 3px;
	margin: 0px;
}

</style>

<div id="content">
    <h1>Connected Devices > Range Extenders</h1>
    <div id="educational-tip">
		<p class="tip">Manage the connected range extenders below, which were all auto-discovered by the Gateway.</p>
		<p class="hidden">You may choose to <strong>DISCONNECT, EDIT,</strong> or delete <strong>(X)</strong> the range extenders connected to your network.</p>
    </div>
    <div class="module data">
        <h2>Range Extenders</h2>
		<table class="data" id="range_extender" summary="This table lists connected range extenders">
			<tr>
				<th id="ext-name">Range Extender</td>
				<th id="ssid">SSID</td>
				<th id="bssid">BSSID</td>
				<th id="frequency-band">Frequency Band</td>
				<th id="channel">Channel</td>
				<th id="security-mode">Security Mode</td>
				<th id="disconnect-btn">&nbsp;</td>
			</tr>

			<tfoot>
				<tr class="acs-hide">
					<td headers="ext-name">null</td>
					<td headers="ssid">null</td>
					<td headers="bssid">null</td>
					<td headers="frequency-band">null</td>
					<td headers="channel">null</td>
					<td headers="security-mode">null</td>
					<td headers="disconnect-btn">null</td>
				</tr>
			</tfoot>

		</table>

		<div id="no_client" style="display: none;">
			<p>There are no valid extender found!</p>
		</div>
    </div> <!-- end .module -->

	<form name="h_form" method="post" action="range_extenders_edit.php">
		<input type="hidden" name="h_idex" value=""/>
		<input type="hidden" name="h_ssid" value=""/>
		<input type="hidden" name="h_mac" value=""/>
		<input type="hidden" name="h_channel" value=""/>
		<input type="hidden" name="h_security" value=""/>
		<input type="hidden" name="h_password" value=""/>
	</form>	
	
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
