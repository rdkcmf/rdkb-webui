<?php include('includes/header.php'); ?>

<!-- $Id: managed_sites_add_domain.php 3158 2010-01-08 23:32:05Z slemoine $ -->

<div id="sub-header">
    <?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Parental Control > Managed Sites > Add Blocked Domain", "nav-sites");

    jQuery.validator.addMethod("url2", function(value, element, param) {
        if (value.indexOf('//www.') > 0) {
            value = value.replace("//www.","//");
        }

        return this.optional(element) || (value.match(".$") != '.') && /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value)
        || /^(https?|s?ftp):\/\/\[((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))]?(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$/i.test(value);  
    }, jQuery.validator.messages.url);

    $("#always_switch").radioswitch({
        id: "always-switch",
        radio_name: "block",
        id_on: "yes",
        id_off: "no",
        title_on: "Select always block",
        title_off: "Unselect always block",
        size: "small",
        label_on: "Yes",
        label_off: "No",
        revertOrder: true,
        state: "on"
    }).change(function(event, data) {
        updateBlockTimeVisibility($("#always_switch").radioswitch("getState").on ? "yes" : "no")
    });

    function updateBlockTimeVisibility(isBlocked) {
        if(isBlocked == "yes") {
            $("#block-time *").prop("disabled", true).addClass("disabled");
        } else {
            $("#block-time *").prop("disabled", false).removeClass("disabled");
        }
    }

    updateBlockTimeVisibility($("#always_switch").radioswitch("getState").on ? "yes" : "no")

    $("#weekday_select_all").click(function() {
        if(!$(this).is(".disabled")) {
            $("#weekday input").prop("checked", true);
        }
    });

    $("#weekday_select_none").click(function() {
        if(!$(this).is(".disabled")) {
            $("#weekday input").prop("checked", false);
        }
    });

    $("#pageForm").validate({
        rules: {
            url: {
                required: true,
                url2: true 
            }
            ,day: {
                required: function() {
                    return ( $('#no').is(':checked') );
                }
            }
        },
	onfocusout: false,
        messages:{
            url: "Please input a valid URL, start with 'http://' or 'https://'"
        }
    });

    $("#url").change(function(){
        $("#pageForm").validate().element('#url');
    });

    $("#url").focus().val('http://');

    $("#btn-cancel").click(function() {
        window.location.href = "managed_sites.php";
    });

    $("#pageForm").submit(function(e) {

        e.preventDefault();

        var URL = $('#url').val();
        var alwaysBlock = $("#always_switch").radioswitch("getState").on;
        //alert($('#yes').prop("checked"));  true or false

        var startTime_unit = $('#time_start_ampm').val();
        var endTime_unit   = $('#time_end_ampm').val();
        var startHour = parseInt($('#time_start_hour').val());
        var endHour   = parseInt($('#time_end_hour').val());
        var sminute   = parseInt($('#time_start_minute').val());
        var eminute   = parseInt($('#time_end_minute').val());

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
        $(".blockedDay").each(function(){ if($(this).prop("checked") == true) blockedDays += $(this).val()+','; });
        blockedDays = blockedDays.slice(0, -1); //trim the last,
        //alert(blockedDays);
        //$(".blockedDay").each(function(){ alert($(this).val());});

        if(alwaysBlock)
            var blockInfo = '{"URL": "'+URL+'", "alwaysBlock": "'+alwaysBlock+'"}';
        else
            var blockInfo = '{"URL": "'+URL+'", "alwaysBlock": "'+alwaysBlock+'", "StartTime": "'+StartTime+'", "EndTime": "'+EndTime+'", "blockedDays": "'+blockedDays+'"}';
        //alert(blockInfo);

        if($("#pageForm").valid()){

            jProgress('This may take several seconds', 60); 
            $.ajax({
                type: "POST",
                url: "actionHandler/ajaxSet_add_blockedSite.php",
                data: { BlockInfo: blockInfo },
                success: function(data){            
                    jHide();
                    if (data != "Success!") {
                        jAlert(data);
                    }else{
                        window.location.href = "managed_sites.php";
                    }
                },
                error: function(){
                    jHide();
                    jAlert("Failure, please try again.");
                }
            });
        }
        });
});

</script>

<div id="content">
	<h1>Parental Control > Managed Sites > Add Blocked Domain</h1>
   <form id="pageForm">

     <div class="module">
         <div class="forms">
         <h2>Add Site to be Blocked</h2>

         <div class="form-row">
            <label for="url" class="checkbox">URL:</label>
            <input type="text" value="" id="url" name="url" class="text" size="50" />
         </div>

         <div class="form-row">
            <label for="on">Always Block?</label>
			<span id="always_switch"></span>
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
     <input type="submit" class="btn submit" value="Save"/>
     <input type="button" id="btn-cancel" class="btn alt reset" value="Cancel"/>
</div>
</div> <!-- end .form -->
</div> <!-- end .module -->
</form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
