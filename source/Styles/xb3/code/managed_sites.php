<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>
<!-- $Id: managed_sites.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>
<?php
$isManageEnabled = getStr("Device.X_Comcast_com_ParentalControl.ManagedSites.Enable");
if ($_DEBUG) {
	$isManageEnabled = "true";
}
?>

<?php 
	$ret = init_psmMode("Parental Control > Managed Sites", "nav-sites");
	if ("" != $ret){echo $ret;	return;}
?>

<script  type="text/javascript">

$(document).ready(function() {
	comcast.page.init("Parental Control > Managed Sites", "nav-sites");
    
    jQuery.validator.addMethod("url2", function(value, element, param) {
       if (value.indexOf('//www.') > 0) {
            value = value.replace("//www.","//");
        }

        return this.optional(element) || (value.match(".$") != '.') && /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value)
        || /^(https?|s?ftp):\/\/\[((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))]?(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$/i.test(value);  
    }, jQuery.validator.messages.url);

    $(".edit-URL").hide();
    $(".edit-Keyword").hide();
    $("a.confirm").unbind('click');
   
	$("#managed_sites_switch").radioswitch({
		id: "managed-sites-switch",
		radio_name: "managed_sites",
		id_on: "managed_sites_enabled",
		id_off: "managed_sites_disabled",
		title_on: "Enable managed sites",
		title_off: "Disable managed sites",
		state: isManageEnabled === 'true' ? "on" : "off"
	});

	$("span[id^=trusted_user_]").each(function(){
		var $this = $(this);
		var idx = this.id.match(/trusted_user_(.+)$/)[1];
		var val = $this.attr("switch-val");

		$this.radioswitch({
			id: "trusted-user-"+idx,
			radio_name: "device_trusted-"+idx,
			id_on: "device_trusted-"+idx,
			id_off: "device_notrusted-"+idx,
			title_on: "Select trust",
			title_off: "Select untrust",
			size: "small",
			label_on: "Yes",
			label_off: "No",
			revertOrder: true,
			state: val
		}).change(function(){
    		var trustFlag = $(this).radioswitch("getState").on;
    		//alert(trustFlag);	
			jProgress('This may take several seconds', 60);
			$.ajax({                  
				type: "POST",
				url: "actionHandler/ajaxSet_trust_computer.php",
				data: { TrustFlag: '{"trustFlag": "'+trustFlag+'", "HostName": "'+hostNameArr[idx-1]+'", "IPAddress": "'+ipAddrArr[idx-1]+'"}' },
				success: function(){            
					jHide();
					//window.location.href = "managed_services.php";
				},
				error: function(){
					jHide();
					jAlert("Failure, please try again.");
				}
			});
    	});
	});

	$("span[id^=blockRadio_]").each(function(){
		var $this = $(this);
		var idx = this.id.match(/blockRadio_(.+)$/)[1];
		var val = $this.attr("switch-val");

		$this.radioswitch({
			id: "blockRadio-"+idx,
			radio_name: "block-"+idx,
			id_on: "yes-"+idx,
			id_off: "no-"+idx,
			title_on: "Select always block",
			title_off: "Unselect always block",
			size: "small",
			label_on: "Yes",
			label_off: "No",
			revertOrder: true,
			state: val
		}).change(function(){
			if ($(this).radioswitch("getState").on)
				$("#block-time-"+idx).find("*").prop("disabled", true).addClass("disabled");
			else
				$("#block-time-"+idx).find("*").prop("disabled", false).removeClass("disabled");
		});
	});

    $('.add-btn').click(function (e) {
    	e.preventDefault();
    	if ($(this).hasClass('disabled'))
    		return false; // Do something else in here if required
        else
	        window.location.href = $(this).attr('href');
	});

    $(".weekday_select_all").click(function() {
    	if(!$(this).is(".disabled")) {
    		$(".weekday input").prop("checked", true);
    	}
    });
    $(".weekday_select_none").click(function() {
    	if(!$(this).is(".disabled")) {
    		$(".weekday input").prop("checked", false);
    	}
    });

    $(".edit-cancel").click(function() {
    	window.location.href = "managed_sites.php";
    });

    $(".del-btn").unbind('click').click(function(e){

	e.preventDefault();
            
    var href = $(this).attr("href");
    var message = ($(this).attr("title").length > 0) ? "Are you sure you want to " + $(this).attr("title") + "?" : "Are you sure?";
    var InstanceID = $(this).attr("id");
    var removeBlockInfo = 
    jConfirm(
        message
        ,"Are You Sure?"
        ,function(ret) {
            if(ret) {

				jProgress('This may take several seconds', 60); 
				$.ajax({                   
					type: "POST",
					url: "actionHandler/ajaxSet_remove_blockedSite.php",
					data: { removeBlockInfo: '{"InstanceID": "'+InstanceID+'"}' },
					success: function(){ 
						jHide();           
						window.location.href = "managed_sites.php";
					},
					error: function(){
						jHide();
						jAlert("Failure, please try again.");
					}
				});
            }    
        });
});

if(isManageEnabled != 'true'){
	$('.main_content *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
	$(".main_content .radioswitch_cont:not(#managed_sites_switch)").radioswitch("doEnable", false);
	$(".btn").prop("disabled", true);
	$('.del-btn').unbind('click');
}

$("#managed_sites_switch").change(function() {

 	var isManageDisabled = $("#managed_sites_switch").radioswitch("getState").on === false;
	if(isManageDisabled) {
		$('.main_content *').not(".radioswitch_cont, .radioswitch_cont *").addClass("disabled");
		$(".main_content .radioswitch_cont:not(#managed_sites_switch)").radioswitch("doEnable", false);
		$(".btn").prop("disabled", true);
		$('.del-btn').unbind("click");
	}
 	else {
 		$(".main_content *").not(".radioswitch_cont, .radioswitch_cont *").removeClass("disabled");
		$(".main_content .radioswitch_cont:not(#managed_sites_switch)").radioswitch("doEnable", true);
 		$(".btn").prop("disabled", false);
		$("a.confirm").click(function(e) {
			e.preventDefault();

			var href = $(this).attr("href");
			var message = ($(this).attr("title").length > 0) ? "Are you sure you want to " + $(this).attr("title") + "?" : "Are you sure?";
			var InstanceID = $(this).attr("id");
			var removeBlockInfo = jConfirm(
				message
				,"Are You Sure?"
				,function(ret) {
					if(ret) {

						jProgress('This may take several seconds', 60); 
						$.ajax({
							type: "POST",
							url: "actionHandler/ajaxSet_remove_blockedSite.php",
							data: { removeBlockInfo: '{"InstanceID": "'+InstanceID+'"}' },
							success: function(){  
								jHide();          
								window.location.href = "managed_sites.php";
							},
							error: function(){
								jHide();
								jAlert("Failure, please try again.");
							}
						});
					}    
				});
		});
 	}//end of else

 	var enable = $("#managed_sites_switch").radioswitch("getState").on;
 	//alert(enable);

	jProgress('This may take several seconds', 60); 
	$.ajax({
		type: "POST",
		url: "actionHandler/ajaxSet_enable_manageSite.php",
		data: { Enable: '{"Enable": "'+enable+'"}' },
		success: function(){   
			jHide();          
			//window.location.href = "managed_sites.php";
		},
		error: function(){
			jHide();
			jAlert("Failure, please try again.");
		}
	});
});// end of function

//===========================================================

	for (var i=0, k=1; i<editNums; i++, k++) {  
		( 
		 function(x) {
			var btn = "#edit-btn-" + k;
			var edit = "#edit-" + k;
			var saveEdit = "#save-edit-" + k;
			var url     = "#url-" + k;
			var keyword = "#Keyword-" + k;
			var time_start_ampm = "#time_start_ampm-" + k;
			var time_start_hour = "#time_start_hour-" + k;
			var time_start_minute = "#time_start_minute-" + k;
			var time_end_ampm = "#time_end_ampm-" + k;
			var time_end_hour = "#time_end_hour-" + k;
			var time_end_minute = "#time_end_minute-" + k;
			var blockedDay = ".blockedDay-" + k;
			var pageFORM = "#pageForm-" + k;
			var blockRadio = "#blockRadio_" + k;
			//alert(butn);

			$(btn).click(function(){
				$('.main_content').hide();
				$(edit).show();
			
	 $(function() {
$.validator.addMethod("no_space", function(value, element, param) {
		return !param || /^[a-zA-Z0-9]*$/i.test(value);
	}, " No spaces. Case sensitive.");

				$(pageFORM).validate({
					rules: {
						url: {
							required: true,
							url2: true 
						},
						Keyword: {
							 required: true,
               						 no_space:true
						},
						day: {
							required: function() {
								return ($(blockRadio).radioswitch("getState").on === false);
							}
						}
					},
					messages:{
						url: "Please input a valid URL, start with 'http://' or 'https://'"
					}
				});
			});
});
			//=========================================================
			var blockTime = "#block-time-" + k;  

			if ($(blockRadio).radioswitch("getState").on)
				$(blockTime).find("*").prop("disabled", true).addClass("disabled");
			else
				$(blockTime).find("*").prop("disabled", false).removeClass("disabled");

			//========================================================

			//========================================================
			$(saveEdit).click(function(){ 
				var InstanceID = $(this).attr("name");
				//alert(InstanceID);

				if( $(edit).hasClass('edit-URL') ){
					//this is going to submit a edit URL form

					var URL = $(url).val();
					var alwaysBlock = $(blockRadio).radioswitch("getState").on; 
					//alert($(yes).prop("checked"));  true or false

					var startTime_unit = $(time_start_ampm).val();
					var endTime_unit   = $(time_end_ampm).val();
					var startHour = parseInt($(time_start_hour).val());
					var endHour   = parseInt($(time_end_hour).val());
					var sminute   = parseInt($(time_start_minute).val());
					var eminute   = parseInt($(time_end_minute).val());

					if (startTime_unit === "PM" && startHour !== 12) {      
						startHour += 12;
					}
					else if (startTime_unit === "AM" && startHour === 12) {
						startHour = 0;
					}

					if (endTime_unit === "PM" && endHour !== 12) {      
						endHour += 12;
					}
					else if (endTime_unit === "AM" && endHour === 12) {
						endHour = 0;
					}

					if(! alwaysBlock){
						if ((startHour>endHour) || ((startHour==endHour) && (sminute>=eminute))) {
							jAlert("Start time should be smaller than End time !");
							return;
						} 
					}

					(0 === startHour) && (startHour = '00');
					(0 === endHour)   && (endHour   = '00');
					(0 === sminute)   && (sminute   = '00');
					(0 === eminute)   && (eminute   = '00');

					var StartTime = startHour + ':' + sminute;
					var EndTime   = endHour   + ':' + eminute;

					var blockedDays="";
					$(blockedDay).each(function(){ if($(this).prop("checked") == true) blockedDays += $(this).val()+','; });
					blockedDays = blockedDays.slice(0, -1); //trim the last,
					//alert(blockedDays);
					//$(".blockedDay").each(function(){ alert($(this).val());});

					if( alwaysBlock )
						var blockInfo = '{"URL": "'+URL+'", "InstanceID": "'+InstanceID+'", "alwaysBlock": "'+alwaysBlock+'"}';
					else
						var blockInfo = '{"URL": "'+URL+'", "InstanceID": "'+InstanceID+'", "alwaysBlock": "'+alwaysBlock+'", "StartTime": "'+StartTime+'", "EndTime": "'+EndTime+'", "blockedDays": "'+blockedDays+'"}';
					//alert(blockInfo);

				} //end of if edit-url
				else{
					//this is going to submit a edit keyword form
					var Keyword = $(keyword).val();
					var alwaysBlock = $(blockRadio).radioswitch("getState").on; 
					//alert($(yes).prop("checked"));  true or false

					var startTime_unit = $(time_start_ampm).val();
					var endTime_unit   = $(time_end_ampm).val();
					var startHour = parseInt($(time_start_hour).val());
					var endHour   = parseInt($(time_end_hour).val());
					var sminute   = parseInt($(time_start_minute).val());
					var eminute   = parseInt($(time_end_minute).val());

					if (startTime_unit === "PM" && startHour !== 12) {      
						startHour += 12;
					}
					else if (startTime_unit === "AM" && startHour === 12) {
						startHour = 0;
					}

					if (endTime_unit === "PM" && endHour !== 12) {      
						endHour += 12;
					}
					else if (endTime_unit === "AM" && endHour === 12) {
						endHour = 0;
					}

					if(! alwaysBlock){
						if ((startHour>endHour) || ((startHour==endHour) && (sminute>=eminute))) {
							jAlert("Start time should be smaller than End time !");
							return;
						} 
					}

					(0 === startHour) && (startHour = '00');
					(0 === endHour)   && (endHour   = '00');
					(0 === sminute)   && (sminute   = '00');
					(0 === eminute)   && (eminute   = '00');

					var StartTime = startHour + ':' + sminute;
					var EndTime   = endHour   + ':' + eminute;

					var blockedDays="";
					$(blockedDay).each(function(){ if($(this).prop("checked") == true) blockedDays += $(this).val()+','; });
					blockedDays = blockedDays.slice(0, -1); //trim the last,
					//alert(blockedDays);
					//$(".blockedDay").each(function(){ alert($(this).val());});

					if( alwaysBlock )
						var blockInfo = '{"Keyword": "'+Keyword+'", "InstanceID": "'+InstanceID+'", "alwaysBlock": "'+alwaysBlock+'"}';
					else
						var blockInfo = '{"Keyword": "'+Keyword+'", "InstanceID": "'+InstanceID+'", "alwaysBlock": "'+alwaysBlock+'", "StartTime": "'+StartTime+'", "EndTime": "'+EndTime+'", "blockedDays": "'+blockedDays+'"}';
					//alert(blockInfo); 
	    		}//end of else

				if($(pageFORM).valid()){

					jProgress('This may take several seconds', 60);
					$.ajax({
						type: "POST",
						url: "actionHandler/ajaxSet_edit_blockedSite.php",
						data: { BlockInfo: blockInfo },
						success: function(){            
							jHide();
							window.location.href = "managed_sites.php";
						},
						error: function(){
							jHide();
							jAlert("Failure, please try again.");	          
						}
					});
				}

			});
		}) (i); 
	}; // end of for loop
});

</script>

<div id="content" class="main_content">

<h1>Parental Control > Managed Sites</h1>

	<div id="educational-tip">
		<p class="tip">Manage access to specific websites by network devices.</p>
    <p class="hidden">Select <strong>Enable</strong> to manage sites, or <strong>Disable</strong> to turn off.</p>
    <p class="hidden"><strong>+ADD:</strong> Add a new website or keyword.</p>
    <p class="hidden"><strong>Blocked Sites:</strong> Deny access to specific websites (URLs).</p>
    <p class="hidden"><strong>Blocked Keywords:</strong> Deny access to websites containing specific words.</p>
    <p class="hidden">The Gateway will block connections to websites on all untrusted computers, based on the specified rules. If you don't want restrictions for a particular computer, select <strong>Yes</strong> under <strong>Trusted Computers</strong>.</p>
	</div>

	<form action="managed_sites.php" method="post"  name="managed_sites">

	<div class="module">
		<div class="select-row">
 	    	<span class="readonlyLabel label">Managed Sites:</span>
			<span id="managed_sites_switch"></span>
		</div>
	</div>
	</form>

	<?php 
	$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.";
	$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite.");
	$mapping_array  = array("BlockMethod", "Site", "AlwaysBlock", "StartTime", "EndTime", "BlockDays");
	
	$blockedSitesInstance = getParaValues($rootObjName, $paramNameArray, $mapping_array);
	
	$blockedSitesInstanceArr = explode(",", getInstanceIds("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSite."));

	//TrustedUser
	$rootObjName    = "Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.";
	$paramNameArray = array("Device.X_Comcast_com_ParentalControl.ManagedSites.TrustedUser.");
	$mapping_array  = array("IPAddress", "Trusted");

	$TrustedUser = getParaValues($rootObjName, $paramNameArray, $mapping_array);

	//Host
	$rootObjName    = "Device.Hosts.Host.";
	$paramNameArray = array("Device.Hosts.Host.");
	$mapping_array  = array("HostName", "PhysAddress", "IPAddress", "IPv6Address.1.IPAddress");

	$Host = getParaValues($rootObjName, $paramNameArray, $mapping_array);

   	// $blockedSitesNums = sizeof($blockedSitesInstanceArr);
	$blockedSitesNums = getStr("Device.X_Comcast_com_ParentalControl.ManagedSites.BlockedSiteNumberOfEntries");
    	//dump($blockedSitesNums);
	$blockedSitesURL = array();
	$blockedSitesKeyWord = array();

	for ($i=0,$j=0,$k=0; $i < $blockedSitesNums; $i++) { 
    	// retrieve info from backend

		$blockedSites["$i"]['BlockMethod'] = $blockedSitesInstance["$i"]['BlockMethod'];
		$blockedSites["$i"]['Site'] = $blockedSitesInstance["$i"]['Site'];
		$blockedSites["$i"]['AlwaysBlock'] = $blockedSitesInstance["$i"]['AlwaysBlock'];
		$blockedSites["$i"]['StartTime'] = $blockedSitesInstance["$i"]['StartTime'];
		$blockedSites["$i"]['EndTime'] = $blockedSitesInstance["$i"]['EndTime'];
		$blockedSites["$i"]['BlockedDays'] = $blockedSitesInstance["$i"]['BlockDays'];

    	//process blockedSites info based on Blocked Method, URL/Keywords
		if( !strcasecmp("URL", $blockedSites["$i"]['BlockMethod'])){
			$blockedSitesURL["$j"]['InstanceID'] = $i + 1;
			$blockedSitesURL["$j"]['RealID'] = $blockedSitesInstanceArr["$i"];
			$blockedSitesURL["$j"]['Site'] = $blockedSites["$i"]['Site'];
			$blockedSitesURL["$j"]['AlwaysBlock'] = $blockedSites["$i"]['AlwaysBlock'];
			$blockedSitesURL["$j"]['StartTime'] = $blockedSites["$i"]['StartTime'];
			$blockedSitesURL["$j"]['EndTime'] = $blockedSites["$i"]['EndTime'];
			$blockedSitesURL["$j"]['BlockedDays'] = $blockedSites["$i"]['BlockedDays'];
			$j++;
		}
		else{
			$blockedSitesKeyWord["$k"]['InstanceID'] = $i + 1;
			$blockedSitesKeyWord["$k"]['RealID'] = $blockedSitesInstanceArr["$i"];
			$blockedSitesKeyWord["$k"]['Site'] = $blockedSites["$i"]['Site'];
			$blockedSitesKeyWord["$k"]['AlwaysBlock'] = $blockedSites["$i"]['AlwaysBlock'];
			$blockedSitesKeyWord["$k"]['StartTime'] = $blockedSites["$i"]['StartTime'];
			$blockedSitesKeyWord["$k"]['EndTime'] = $blockedSites["$i"]['EndTime'];
			$blockedSitesKeyWord["$k"]['BlockedDays'] = $blockedSites["$i"]['BlockedDays'];
			$k++;
		}
	}
	if ($_DEBUG) {
		$blockedSitesNums = 4;
		$blockedSitesURL = array(
			array("InstanceID"=>1, "RealID"=>"1", "Site"=>"www.baidu.com", "AlwaysBlock"=>"true", "StartTime"=>"8:00", "EndTime"=>"17:00", "BlockedDays"=>"Mon"),
			array("InstanceID"=>2, "RealID"=>"2", "Site"=>"www.sohu.com", "AlwaysBlock"=>"false", "StartTime"=>"8:00", "EndTime"=>"17:00", "BlockedDays"=>"Mon"),
			);
		$blockedSitesKeyWord = array(
			array("InstanceID"=>3, "RealID"=>"3", "Site"=>"keyword1", "AlwaysBlock"=>"true", "StartTime"=>"8:00", "EndTime"=>"15:00", "BlockedDays"=>"Mon"),
			array("InstanceID"=>4, "RealID"=>"4", "Site"=>"keyword2", "AlwaysBlock"=>"false", "StartTime"=>"8:00", "EndTime"=>"15:00", "BlockedDays"=>"Mon"),
			);
	}

	$URLNums = sizeof($blockedSitesURL);
	$KeywordNums = sizeof($blockedSitesKeyWord);
    //dump($URLNums);
    //dump($KeywordNums);
	?>

	<div id="managed-sites-items">
		<div class="module data">
			<h2>Blocked Sites</h2>
			<p class="button"><a tabindex='0' href="managed_sites_add_site.php" id="add_blocked_site" class="btn add-btn">+ Add</a></p>
			<table class="data" summary="This table lists blocked URLs">
				<tr>
					<th id="url-number" class="number">&nbsp;</th>
					<th id="url" class="url">URL</th>
					<th id="url-blocked-time" class="when">When</th>
					<th id="edit-url" class="edit">&nbsp;</th>
					<th id="delete-url" class="delete">&nbsp;</th>
				</tr>

			<?php 
				for ($i=0,$k=1; $i < $URLNums; $i++,$k++) { 
					$URLAlwaysBlock = $blockedSitesURL["$i"]['AlwaysBlock'];
					$URLBlockTime = $blockedSitesURL["$i"]['StartTime']. " - " .$blockedSitesURL["$i"]['EndTime']. ",  " .$blockedSitesURL["$i"]['BlockedDays'];
					
					if($k % 2) $odd = "class='odd'";
					else $odd = "";
					echo "<tr $odd>
					<td headers='url-number' class=\"row-label alt\">" .$k. "</td>
					<td headers='url'>" .$blockedSitesURL["$i"]['Site']. "</td>
					<td headers='url-blocked-time'>" . ( ($URLAlwaysBlock == 'true') ? "Always" : $URLBlockTime ) . "</td>
					<td headers='edit-url' class=\"edit\"><button tabindex='0' class=\"btn\" id=\"edit-btn-" .$blockedSitesURL["$i"]['InstanceID']. "\">edit</button></td>
					<td headers='delete-url' class=\"delete\"><a  tabindex='0' href=\"#\" class=\"btn confirm del-btn\" title=\"Delete Blocked Site\" id=\"" .$blockedSitesURL["$i"]['RealID']. "\">x</a></td>
					</tr>
					";
				}
			?>

      <tfoot>
          <tr class="acs-hide">
            <td headers="url-number">null</td>
            <td headers="url">null</td>
            <td headers="url-blocked-time">null</td>
            <td headers="edit-url">null</td>            
            <td headers="delete-url">null</td>            
          </tr>
        </tfoot>

			</table>
		</div> <!-- end blocked URL .module -->

		<div class="module data">
			<h2>Blocked Keywords</h2>
			<p class="button"><a tabindex='0' href="managed_sites_add_keyword.php" class="btn add-btn" id="add-blocked-keywords">+ Add</a></p>

			<table class="data" summary="This table lists blocked Keywords">
				<tr>
					<th id="keyword-number" class="number">&nbsp;</th>
					<th id="keyword" class="keyword">Keyword</th>
					<th id="keyword-blocked-time" class="when">When</th>
					<th id="edit-keyword" class="edit">&nbsp;</th>
					<th id="delete-keyword" class="delete">&nbsp;</th>
				</tr>

			<?php 

				for ($i=0,$k=1; $i < $KeywordNums; $i++,$k++) {
					$KeywordAlwaysBlock = $blockedSitesKeyWord["$i"]['AlwaysBlock'];
					$KeywordBlockTime = $blockedSitesKeyWord["$i"]['StartTime']. " - " .$blockedSitesKeyWord["$i"]['EndTime']. ",  " .$blockedSitesKeyWord["$i"]['BlockedDays'];
					
					if($k % 2) $odd = "class='odd'";
					else $odd = "";
					echo "<tr $odd>
					<td headers='keyword-number' class=\"row-label alt\">" .$k. "</td>
					<td headers='keyword'>" .$blockedSitesKeyWord["$i"]['Site']. "</td>
					<td headers='keyword-blocked-time'>" . ( ($KeywordAlwaysBlock == 'true') ? "Always" : $KeywordBlockTime ) . "</td>
					<td headers='edit-keyword' class=\"edit\"><button tabindex='0' class=\"btn\" id=\"edit-btn-" .$blockedSitesKeyWord["$i"]['InstanceID']. "\">edit</button></td>
					<td headers='delete-keyword' class=\"delete\"><a tabindex='0' href=\"#\" class=\"btn confirm del-btn\" title=\"Delete Blocked Keyword\" id=\"" .$blockedSitesKeyWord["$i"]['RealID']. "\">x</a></td>
					</tr>
					";
				}

			?>

        <tfoot>
          <tr class="acs-hide">
            <td headers="keyword-number">null</td>
            <td headers="keyword">null</td>
            <td headers="keyword-blocked-time">null</td>
            <td headers="edit-keyword">null</td>            
            <td headers="delete-keyword">null</td>            
          </tr>
        </tfoot>

			</table>
		</div> <!-- end blocked Keywords.module -->

		<form action="managed_sites.php" method="post">
      <fieldset>
      <legend class="acs-hide">Trusted devices management</legend>

			<input  type="hidden"  name="update_trusted_computers"  value="true" />

		<?php
		$hostsInstance = getInstanceIds("Device.Hosts.Host.");
		$hostsInstanceArr = explode(",", $hostsInstance);

		$hostNums = getStr("Device.Hosts.HostNumberOfEntries");

		$ipAddrArr = array();
		$HostNameArr = array();

		for ($i=0; $i < $hostNums; $i++) {

			$HostName = $Host[$i]["HostName"];
			if (($HostName == "*") || (strlen($HostName) == 0)) {
				$Host["$i"]['HostName'] = $Host[$i]["PhysAddress"];
			}
			else {
				$Host["$i"]['HostName'] = $HostName;
			}

			$Host["$i"]['IPAddress'] = $Host[$i]["IPAddress"];
			$IPAddress = $Host["$i"]['IPAddress'];
			//$IPv4Address	= getStr("Device.Hosts.Host." .$hostsInstanceArr["$i"]. ".IPv4Address.1.IPAddress");
			$IPv6Address	= $Host[$i]["IPv6Address.1.IPAddress"];
			
			//"Device.Hosts.Host.'$i'.IPv4Address.1.IPAddress" is not updating on GW_IP Change
			$IPv4Address = $IPAddress;

			//In IPv6 only mode, IPv4=NA
			if( strpos($IPv4Address, '.') === false ) $IPv4Address = 'NA';

			if (substr($IPv6Address, 0, 5) == "2001:") {
				$Host["$i"]['IPShow'] = $IPv4Address.'/'.$IPv6Address;
			}
			else {
				//If IPv6 is not global then IPv6=NA
				$Host["$i"]['IPShow'] = $IPv4Address.'/NA';
			}

			array_push($HostNameArr, $Host["$i"]['HostName']);
			array_push($ipAddrArr, $Host["$i"]['IPAddress']);
			$Host["$i"]['Trusted'] = false;
			foreach( $TrustedUser as $key => $value ){
				if ( $value['IPAddress'] == $Host["$i"]['IPAddress']){

					$Host["$i"]['Trusted'] = $value['Trusted']; 
					break;
				}
			}
		}
		if ($_DEBUG) {
			$hostNums = 2;
			$Host["0"] = array("HostName"=>"host1", "IPAddress"=>"1.1.1.1", "Trusted"=>false);
			$Host["1"] = array("HostName"=>"host2", "IPAddress"=>"2.2.2.2", "Trusted"=>true);
			$HostNameArr = array("host1", "host2");
			$ipAddrArr = array("1.1.1.1", "2.2.2.2");
		}

		 ?>

			<div class="module data">
				<h2>Trusted Computers</h2>		

        <table  id="trusted_computers" class="data" summary="This table allows you to set trusted or untrusted devices for above blocked URLs or Keywords">
          <tr>
            <th id="number" class="number">&nbsp;</th>
            <th id="device-name" class="computer_name">Computer Name</th>
            <th id="IP" class="ip">IP</th>
            <th id="trusted-or-not" class="trusted">Trusted</th>
          </tr>  

					<?php 
					for ($i=0,$k=1; $i < $hostNums; $i++,$k++) {

						if($k % 2) $odd = "class='odd'";
						else $odd = "";
						echo "<tr $odd>
						<td headers='number' class=\"row-label alt\">" .$k. "</td>
						<td headers='device-name' id='HostName-" .$k. "' >" .$Host["$i"]['HostName']. "</td>
						<td headers='IP' id='IPAddress-" .$k. "' >" .$Host["$i"]['IPShow']. "</td>
						<td headers='trusted-or-not' style='min-width: 92px;'>
						<span id=\"trusted_user_".$k."\" switch-val=\"".( $Host["$i"]['Trusted'] == 'true' ? "on" : "off" )."\"></span>
						</td>
						</tr>
						";
					}
					?>

          <tfoot>
          <tr class="acs-hide">
            <td headers="number">null</td>
            <td headers="device-name">null</td>
            <td headers="IP">null</td>
            <td headers="trusted-or-not">null</td>            
          </tr>
        </tfoot>

				</table>
			</div> <!-- end .module -->
    </fieldset>
		</form><!--end trusted computers -->
	</div><!-- end managed-sites -->

</div><!-- end #content -->

	<script  type="text/javascript">
	    var isManageEnabled = '<?php echo $isManageEnabled; ?>';
	    //alert(isManageEnabled);
		//var URLNums = '<?php echo $URLNums; ?>';
		//var KeywordNums = '<?php echo $KeywordNums; ?>';
		var editNums = '<?php echo $URLNums + $KeywordNums; ?>';
		var hostNums = '<?php echo $hostNums; ?>';
		var hostNameArr = <?php echo json_encode($HostNameArr); ?>;
		var ipAddrArr = <?php echo json_encode($ipAddrArr); ?>;
//console.log(ipAddrArr);
	</script>

<?php 

	function generateEditPart($blockedInfo){

    $ID = $blockedInfo['InstanceID'];
   
    $st_time = explode(":", $blockedInfo['StartTime']);	
    $st_hour = $st_time['0'];
    $st_min  = $st_time['1'];
    $end_time = explode(":", $blockedInfo['EndTime']);	
    $end_hour = $end_time['0'];
    $end_min  = $end_time['1'];
    $rel_st_hour =  ((int)$st_hour)>12 ? $st_hour-12 : $st_hour;
    $rel_end_hour =  ((int)$end_hour)>12 ? $end_hour-12 : $end_hour;
    $block_days = $blockedInfo['BlockedDays'];

    if((int)$rel_st_hour == 0) 
    	$rel_st_hour = 12;
    if((int)$rel_end_hour == 0) 
    	$rel_end_hour = 12;

        $str_edit = "<div class=\"form-row\">
				<label for=\"on\">Always Block?</label>
				<span id=\"blockRadio_".$ID."\" switch-val=\"".($blockedInfo['AlwaysBlock'] == 'true' ? "on" : "off")."\"></span>
			</div>

        	<div id=\"block-time-" .$ID. "\" class=\"block-time\">

        		<h3>Set Block Time</h3>
        		<div class=\"form-row\">
        			<label for=\"time_start_hour-" .$ID. "\">Start from:</label> 
        			<select id=\"time_start_hour-" .$ID. "\"  name=\"time_start_hour\">
                        <option ". ( (int)$rel_st_hour == 12 ? " selected='selected'" : "") ."  value=\"12\">12</option>
                        <option ". ( (int)$rel_st_hour == 1 ? " selected='selected'" : "") ."  value=\"1\">1</option>
                        <option ". ( (int)$rel_st_hour == 2 ? " selected='selected'" : "") ."  value=\"2\">2</option>
                        <option ". ( (int)$rel_st_hour == 3 ? " selected='selected'" : "") ."  value=\"3\">3</option>
                        <option ". ( (int)$rel_st_hour == 4 ? " selected='selected'" : "") ."  value=\"4\">4</option>
                        <option ". ( (int)$rel_st_hour == 5 ? " selected='selected'" : "") ."  value=\"5\">5</option>
                        <option ". ( (int)$rel_st_hour == 6 ? " selected='selected'" : "") ."  value=\"6\">6</option>
                        <option ". ( (int)$rel_st_hour == 7 ? " selected='selected'" : "") ."  value=\"7\">7</option>
                        <option ". ( (int)$rel_st_hour == 8 ? " selected='selected'" : "") ."  value=\"8\">8</option>
                        <option ". ( (int)$rel_st_hour == 9 ? " selected='selected'" : "") ."  value=\"9\">9</option>
                        <option ". ( (int)$rel_st_hour == 10 ? " selected='selected'" : "") ."  value=\"10\">10</option>
                        <option ". ( (int)$rel_st_hour == 11 ? " selected='selected'" : "") ."  value=\"11\">11</option>
        			</select>
              <label for=\"time_start_minute-" .$ID. "\" class='acs-hide'></label>
        			<select id=\"time_start_minute-" .$ID. "\"  name=\"time_start_minute\">
                        <option ". ( $st_min == '00' ? " selected='selected'" : "") ."  value=\"00\">00</option>
                        <option ". ( $st_min == '15' ? " selected='selected'" : "") ."   value=\"15\">15</option>
                        <option ". ( $st_min == '30' ? " selected='selected'" : "") ."   value=\"30\">30</option>
                        <option ". ( $st_min == '45' ? " selected='selected'" : "") ."   value=\"45\">45</option>
        			</select>
              <label for=\"time_start_ampm-" .$ID. "\" class='acs-hide'></label>
        			<select id=\"time_start_ampm-" .$ID. "\"  name=\"time_start_ampm\">
                        <option  value=\"AM\" " . ( $st_hour < 12 ? " selected='selected'" : "") . ">AM</option>
                        <option  value=\"PM\"" . ( $st_hour  < 12 ? "" : " selected='selected'"). ">PM</option>
        			</select>
                </div>
        		<div class=\"form-row\">
        			<label for=\"time_end_hour-" .$ID. "\">End on:</label>
                    <select id=\"time_end_hour-" .$ID. "\"  name=\"time_end_hour\">
                        <option ". ( (int)$rel_end_hour == 12 ? " selected='selected'" : "") ."  value=\"12\">12</option> 
                        <option ". ( (int)$rel_end_hour == 1 ? " selected='selected'" : "") ."  value=\"1\">1</option>
                        <option ". ( (int)$rel_end_hour == 2 ? " selected='selected'" : "") ."  value=\"2\">2</option>
                        <option ". ( (int)$rel_end_hour == 3 ? " selected='selected'" : "") ."  value=\"3\">3</option>
                        <option ". ( (int)$rel_end_hour == 4 ? " selected='selected'" : "") ."  value=\"4\">4</option>
                        <option ". ( (int)$rel_end_hour == 5 ? " selected='selected'" : "") ."  value=\"5\">5</option>
                        <option ". ( (int)$rel_end_hour == 6 ? " selected='selected'" : "") ."  value=\"6\">6</option>
                        <option ". ( (int)$rel_end_hour == 7 ? " selected='selected'" : "") ."  value=\"7\">7</option>
                        <option ". ( (int)$rel_end_hour == 8 ? " selected='selected'" : "") ."  value=\"8\">8</option>
                        <option ". ( (int)$rel_end_hour == 9 ? " selected='selected'" : "") ."  value=\"9\">9</option>
                        <option ". ( (int)$rel_end_hour == 10 ? " selected='selected'" : "") ."  value=\"10\">10</option>
                        <option ". ( (int)$rel_end_hour == 11 ? " selected='selected'" : "") ."  value=\"11\">11</option>
        			</select>
              <label for=\"time_end_minute-" .$ID. "\" class='acs-hide'></label>
        			<select id=\"time_end_minute-" .$ID. "\"  name=\"time_end_minute\">
                        <option ". ( $end_min == '00' ? " selected='selected'" : "") ."   value=\"00\">00</option>
                        <option ". ( $end_min == '15' ? " selected='selected'" : "") ."   value=\"15\">15</option>
                        <option ". ( $end_min == '30' ? " selected='selected'" : "") ."   value=\"30\">30</option>
                        <option ". ( $end_min == '45' ? " selected='selected'" : "") ."   value=\"45\">45</option>
                        <option ". ( $end_min == '59' ? " selected='selected'" : "") ."   value=\"59\">59</option>
        			</select>
              <label for=\"time_end_ampm-" .$ID. "\" class='acs-hide'></label>
        			<select id=\"time_end_ampm-" .$ID. "\"  name=\"time_end_ampm\">
                        <option  value=\"AM\" " . ( $end_hour < 12 ? " selected='selected'" : "") . ">AM</option>
                        <option  value=\"PM\"" . ( $end_hour < 12 ? "" : " selected='selected'"). ">PM</option>
        			</select>
        		</div>

        		<h3>Set Blocked Days</h3>
        		<div class=\"select_all_none\">
        			<a rel=\"weekday\" href=\"#select_all\"  class=\"weekday_select_all\">Select All</a> | <a rel=\"weekday\"  href=\"#select_none\" class=\"weekday_select_none\">Select None</a>
                </div>
        		<div class=\"form-row weekday\">
        			<input  name=\"day\" class=\"blockedDay-" .$ID. "\"  type=\"checkbox\" id=\"monday-" .$ID. "\"  value=\"Mon\"" . ( (stristr($block_days, "Mon") != false) ? " checked='checked'" : "" ). "/><label class=\"checkbox\" for=\"monday-" .$ID. "\">Monday</label><br />
        			<input  name=\"day\" class=\"blockedDay-" .$ID. "\"  type=\"checkbox\" id=\"tuesday-" .$ID. "\"  value=\"Tue\"" . ( (stristr($block_days, "Tue") != false) ? " checked='checked'" : "" ). " /><label class=\"checkbox\" for=\"tuesday-" .$ID. "\">Tuesday</label><br />
        			<input  name=\"day\" class=\"blockedDay-" .$ID. "\"  type=\"checkbox\" id=\"wednesday-" .$ID. "\"  value=\"Wed\"" . ((stristr($block_days, "Wed") != false) ? " checked='checked'" : "" ). " /><label class=\"checkbox\" for=\"wednesday-" .$ID. "\">Wednesday</label><br />
        			<input  name=\"day\" class=\"blockedDay-" .$ID. "\"  type=\"checkbox\" id=\"thursday-" .$ID. "\"  value=\"Thu\"" . ( (stristr($block_days, "Thu") != false) ? " checked='checked'" : "" ). " /><label class=\"checkbox\" for=\"thursday-" .$ID. "\">Thursday</label><br />
        			<input  name=\"day\" class=\"blockedDay-" .$ID. "\"  type=\"checkbox\" id=\"friday-" .$ID. "\"  value=\"Fri\"" . ( (stristr($block_days, "Fri") != false) ? " checked='checked'" : "" ). " /><label class=\"checkbox\" for=\"friday-" .$ID. "\">Friday</label><br />
        			<input  name=\"day\" class=\"blockedDay-" .$ID. "\"  type=\"checkbox\" id=\"saturday-" .$ID. "\"  value=\"Sat\"" . ((stristr($block_days, "Sat") != false) ? " checked='checked'" : "" ). " /><label class=\"checkbox\" for=\"saturday-" .$ID. "\">Saturday</label><br />
        			<input  name=\"day\" class=\"blockedDay-" .$ID. "\"  type=\"checkbox\" id=\"sunday-" .$ID. "\"  value=\"Sun\"" . ( (stristr($block_days, "Sun") != false) ? " checked='checked'" : "" ). " /><label class=\"checkbox\" for=\"sunday-" .$ID. "\">Sunday</label>
        		</div>
        	</div> <!-- end #block-time -->

            <div class=\"form-row form-btn\">
            	<input  type=\"button\"  name='" .$blockedInfo['RealID']. "' id=\"save-edit-" .$ID. "\" class=\"btn submit\"  value=\"Save\"/>
            	<input  type=\"button\"  class=\"btn alt reset edit-cancel\"  value=\"Cancel\"/>
            </div>";

            return $str_edit;
	}

    for($i=0; $i<$URLNums; $i++){
     //generate edit blocked URL part 
    	echo "<div id=\"edit-" . $blockedSitesURL["$i"]['InstanceID'] . "\" class=\"edit-URL content\" style='display:none'>
    	<h1>Parental Control > Managed Sites > Edit Blocked Site</h1>
      <form id=\"pageForm-" . $blockedSitesURL["$i"]['InstanceID'] . "\" class=\"pageForm\">
	    <div class=\"module\">
    		<div class=\"forms\">
    		<h2>Edit Site to be Blocked</h2>
    			<div class=\"form-row\">
    			<label for=\"url-" .$blockedSitesURL["$i"]['InstanceID']. "\" class=\"checkbox\">URL:</label>
    			<input id=\"url-" .$blockedSitesURL["$i"]['InstanceID']. "\"  type=\"text\"  value=\"" .$blockedSitesURL["$i"]['Site'] . "\"  name=\"url\" class=\"text\" size=\"50\"  />
    		    </div>" 
    		    .generateEditPart($blockedSitesURL["$i"]) .
    		  " </div> <!-- end .form -->
	       </div> <!-- end .module -->
         </form>
        </div><!-- end #content --> "
    	;
    }

    for($i=0; $i<$KeywordNums; $i++){
     //generate edit blocked Keyword part 
    	echo "<div id=\"edit-" . $blockedSitesKeyWord["$i"]['InstanceID'] . "\" class=\"edit-Keyword content\" style='display:none'>
    	<h1>Parental Control > Managed Sites > Edit Blocked Keyword </h1>
      <form id=\"pageForm-" . $blockedSitesKeyWord["$i"]['InstanceID'] . "\" class=\"pageForm\">
	    <div class=\"module\">
    		<div class=\"forms\">
    		<h2>Edit Keyword to be Blocked</h2>
    			<div class=\"form-row\">
    			<label for=\"Keyword-" .$blockedSitesKeyWord["$i"]['InstanceID']. "\" class=\"checkbox\">Keyword:</label>
    			<input id=\"Keyword-" .$blockedSitesKeyWord["$i"]['InstanceID']. "\"  type=\"text\"  value=\"" .$blockedSitesKeyWord["$i"]['Site']. "\"  name=\"Keyword\" class=\"text\" size=\"50\" maxlength='64' />
    		    </div>" 
    		    .generateEditPart($blockedSitesKeyWord["$i"]) .
    		  " </div> <!-- end .form -->
	       </div> <!-- end .module -->
         </form>
        </div><!-- end #content --> "
    	;
    }

?>


<?php include('includes/footer.php'); ?>
