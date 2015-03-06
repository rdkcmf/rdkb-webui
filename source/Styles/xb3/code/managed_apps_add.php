<?php include('includes/header.php'); ?>

<!-- $Id: wizard_step1.php 2943 2009-08-25 20:58:43Z slemoine $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {

    comcast.page.init("Parental Control > Managed Applications > Add Blocked Application", "nav-applications");
    
    $("input:radio[name='block']").click(function() {
        if($(this).is(":checked") && $(this).val() == "no") {
            $("#block-time").find("input,label,h3,select").prop("disabled", false).removeClass("disabled");
        } else {
            $("#block-time").find("input,label,h3,select").prop("disabled", true).addClass("disabled");        
        }
    });
     $("input:radio[name='block']:checked").trigger("click");
    
	$("#weekday_select_all").click(function() {
	   $("#weekday input").prop("checked", true);
	});
	
    $("#weekday_select_none").click(function() {
	   $("#weekday input").prop("checked", false);
	});
	
	$("#pageForm").validate({
	   rules: {
	       service: {
	           required: true    
	       }
	       ,start_port: {
	           required: true
	           ,digits: true
	       }
	       ,end_port: {
	           required: true
	           ,digits: true
	       }
	   }
	});
	
	$("#cancel-btn").click(function() {
		window.location = "managed_apps.php";
	});
});
</script>

<div id="content">
	
	<h1>Parental Control > Managed Applications > Add Blocked Application</h1>

	<div id="educational-tip">
		<p class="tip"> Some useful help text needed here.</p>
		<p class="hidden">Some more useful text might be needed here.</p>
	</div>
	
	<form id="pageForm" action="managed_sites.php" method="post">

	<div class="module data">
		<div class="forms">
			<h2>Add Application to be Blocked</h2>
            <table class="data">
                <tr>
                    <th>Block</th>
                    <th>Application</th>
                    <th>Protocol</th>
                    <th>Start Port</th>
                    <th>End Port</th>
                </tr>
                <tr class="odd">
                    <td><input type="checkbox" name="blocked_WOW" id="blocked_WOW" /></td>
                    <td>World of Warcraft</td>
                    <td>TCP</td>
                    <td>3724</td>
                    <td>3724</td>                    
                </tr>
                <tr>
                    <td><input type="checkbox" name="blocked_aim" id="blocked_aim" /></td>
                    <td>AIM</td>
                    <td>TCP</td>
                    <td>5190</td>
                    <td>5190</td>                    
                </tr>
                <tr class="odd">
                    <td><input type="checkbox" name="blocked_skype" id="blocked_skype" /></td>
                    <td>Skype</td>
                    <td>TCP/UDP</td>
                    <td>36013</td>
                    <td>36013</td>                    
                </tr>
                <tr>
                    <td><input type="checkbox" name="blocked_realaudio" id="blocked_realaudio" /></td>
                    <td>Real Audio</td>
                    <td>TCP/UDP</td>
                    <td>7070</td>
                    <td>7070</td>                    
                </tr>
                <tr class="odd">
                    <td><input type="checkbox" name="blocked_directx" id="blocked_directx" /></td>
                    <td>Direct X Gaming</td>
                    <td>TCP/UDP</td>
                    <td>3724</td>
                    <td>3724</td>                    
                </tr>
                <tr>
                    <td><input type="checkbox" name="blocked_netmeeting" id="blocked_netmeeting" /></td>
                    <td>NetMeeting</td>
                    <td>TCP</td>
                    <td>1503</td>
                    <td>1503</td>                    
                </tr>                                                                
            </table>
            <h3>User Defined Service</h3>
            <div class="form-row">
				<label for="service">Service Name:</label>
				<input type="text" id="user_defined_service" name="user_defined_service" class="text" />
			</div>
			
			<div class="form-row">
				<label for="protocol">Protocol:</label>
				<select name="protocol" id="protocol">
				    <option value="tcp">TCP</option>
				    <option value="tcp_udp">TCP/UDP</option>
				</select>
			</div>
			
			<div class="form-row">
				<label for="start_port">Start Port:</label>
				<input type="text" id="start_port" name="start_port" class="text" />
			</div>

            <div class="form-row">
				<label for="end_port">End Port:</label>
				<input type="text" id="end_port" name="end_port" class="text" />
			</div>
			
			<div class="form-row">
				<label for="">Always Block?</label>
				<span class="value">
				<input type="radio" id="on" value="yes" name="block" class="radio"/> <label class="radio" for="yes">Yes</label>
				<input type="radio" checked="checked" value="no" name="block" class="radio radio-off"/> <label class="radio" for="no">No</label>
				</span>
			</div>

        	<div id="block-time">
        		<h3>Set Block Time</h3>
        
        		<div class="form-row">
        	<label for="time_start_hour">Start from:</label>
           <select id="time_start_hour" name="time_start_hour">
                <option value"12">12</option>
                <option value"1">1</option>
                <option value"2">2</option>
                <option value"3">3</option>
                <option value"4">4</option>
                <option value"5">5</option>
                <option value"6">6</option>
                <option value"7">7</option>
                <option value"8">8</option>
                <option value"9">9</option>
                <option value"10">10</option>
                <option value"11">11</option>
        </select>
         <label for="time_start_minute" class="acs-hide"></label>
        <select id="time_start_minute" name="time_start_minute">
                <option value"00">00</option>
                <option value"15">15</option>
                <option value"30">30</option>
                <option value"45">45</option>
        </select>
         <label for="time_start_ampm" class="acs-hide"></label>
        <select id="time_start_ampm" name="time_start_ampm">
                <option value"AM">AM</option>
                <option value"PM">PM</option>
        </select>
        </div>
        <div class="form-row">
           <label for="time_end_hour">End on:</label>
           <select id="time_end_hour" name="time_end_hour">
                <option value"12">12</option>
                <option value"1">1</option>
                <option value"2">2</option>
                <option value"3">3</option>
                <option value"4">4</option>
                <option value"5">5</option>
                <option value"6">6</option>
                <option value"7">7</option>
                <option value"8">8</option>
                <option value"9">9</option>
                <option value"10">10</option>
                <option value"11" selected="selected">11</option>
        </select>
        <label for="time_end_minute" class="acs-hide"></label>
        <select id="time_end_minute" name="time_end_minute">
                <option value"00">00</option>
                <option value"15">15</option>
                <option value"30">30</option>
                <option value"45">45</option>
                <option value"59" selected="selected">59</option>
        </select>
        <label for="time_end_ampm" class="acs-hide"></label>
        <select id="time_end_ampm" name="time_end_ampm">
                <option value"AM">AM</option>
                <option value"PM" selected="selected">PM</option>
        </select>
        </div>

<h3>Set Blocked Days</h3>
<div class="select_all_none">
   <a rel="weekday" href="#select_all" id="weekday_select_all" class="">Select All</a> | <a rel="weekday" id="weekday_select_none" href="#select_none" class="">Select None</a>
</div>
<div class="form-row" id="weekday">
   <input class="blockedDay" type="checkbox" name="day" id="monday" value="Mon" checked="checked" /><label class="checkbox" for="monday">Monday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="tuesday" value="Tue" checked="checked" /><label class="checkbox" for="tuesday">Tuesday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="wednesday" value="Wed" checked="checked" /><label class="checkbox" for="wednesday">Wednesday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="thursday" value="Thu" checked="checked" /><label class="checkbox" for="thursday">Thursday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="friday" value="Fri" checked="checked" /><label class="checkbox" for="friday">Friday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="saturday" value="Sat" checked="checked" /><label class="checkbox" for="saturday">Saturday</label><br />
   <input class="blockedDay" type="checkbox" name="day" id="sunday" value="Sun" checked="checked" /><label class="checkbox" for="sunday">Sunday</label>
</div>
</div> <!-- end #block-time -->

            <div class="form-row form-btn">
            	<input type="button" class="btn submit" value="Save"/>
            	<input type="button" id="cancel-btn" class="btn alt reset" value="Cancel"/>
            </div>
    	</div> <!-- end .form -->
	</div> <!-- end .module -->
    </form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
