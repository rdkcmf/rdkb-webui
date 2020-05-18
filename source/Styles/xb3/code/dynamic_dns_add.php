<?php
/*
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2018 RDK Management

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/
?>
<?php include('includes/header.php'); ?>
<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->
<?php include('includes/nav.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
    gateway.page.init("Advanced > Dynamic DNS", "nav-Dynamic-dns");
	$('#Service_Provider1').focus();
	jQuery.validator.addMethod("noSpace", function(value, element) { 
		return value.indexOf(" ") < 0 && value != ""; 
	}, "<?php echo _('Spaces are not allowed')?>");
	$.validator.addMethod("allowed_char_new", function(value, element, param) {
	//Invalid characters are Less than (<), Greater than (>), Ampersand (&), Double quote ("), Single quote ('), Pipe (|).
	return !param || (value.match(/[\(<>&"'$`;|\)]/)==null);
	}, '<?php echo _('Special characters are not allowed.')?>');

	$("#pageForm").validate({
		rules: {
			User_name: {
				required: true,
				noSpace: true,
				maxlength: 64,
				allowed_char: true
			}
			,Password: {
				required: true,
				noSpace: true,
				maxlength: 64,
				allowed_char: true
			}
			,Host_Name: {
				required: true,
				noSpace: true,
				allowed_char: true,
				allowed_char_new: true
			}
		}
	});
	$("#btn-save").click(function() {
		if($("#pageForm").valid()){
	    	var sp = $("#Service_Provider1").val();
			var spLC = sp.toLowerCase();
			var username = $("#User_name").val();
			var password = $("#Password").val();
			var hostnames = document.getElementsByName("Host_Name");
			var hostname = hostnames[0].value;
			var hostArray=[];
			hostArray[0]=hostnames[0].value;
			for(var i=1;i<hostnames.length;i++) {
				if(hostnames[i].value==""){
					alert("<?php echo _('Please enter HostName.')?>");
					return false;
				}
				if(hostnames[i].value!=""){
					hostArray[i]=hostnames[i].value;
					hostname += ","+hostnames[i].value;
				}
			}
			var object = {};
	        hostArray.forEach(function (item) {
	          if(!object[item])
	              object[item] = 0;
	            object[item] += 1;
	        })

	        for (var prop in object) {
                   if(prop.charAt(0) == '.'){
                        alert("<?php echo _('Host Name starting with period(.) is not allowed')?>");
                        return false;
                     }
                   if(prop.charAt(prop.length-1) == '.') {
                       alert("<?php echo _('Host Name ending with period(.) is not allowed')?>");
                       return false;
                    }
                   if(prop.match((/([.])\1/ig))) {
                      alert("<?php echo _('Host Name having consecutive period(.) is not allowed')?>");
                         return false;
                     }
	           if(object[prop] >= 2) {
	               alert("<?php echo _('Host Name having Duplicate Values')?>");
	               return false;
	           }
	        }
	        if(hostname.length>64){
	        	alert("<?php echo _('Host Name having greater than 64 characters'); ?>");
	        	return false;
	        }

			if(spLC!="dyndns.org" && spLC!="tzo.com" && spLC!="changeip.com" && spLC!="freedns.afraid.org") {
				alert("<?php echo _('Service provider name should be \"DynDns.org\" or \"TZO.com\" or \"changeip.com\" or \"freedns.afraid.org\".')?>");
			} else {
				jProgress('<?php echo _('This may take several seconds')?>', 60);
				$.ajax({
					type:"POST",
					url:"actionHandler/ajax_ddns.php",
					data:{add:"true",sp:sp,username:username,password:password,hostname:hostname},
					success:function(results){
						//jAlert(results);
						jHide();
						if (results=="<?php echo _("Success!")?>") { window.location.href="dynamic_dns.php";}
					},
					error:function(){
						jHide();
						jAlert("<?php echo _('Failure, please try again.')?>");
					}
				});
			}
		}
    });
	$("#btn-cancel").click(function() {
    	window.location = "dynamic_dns.php";
    });
});
function add1()
{
	element = document.getElementById(this.id);
	element.parentNode.removeChild(element);
	var i=this.id;
	i=i-1;
	elemet = document.getElementById(i);
	elemet.parentNode.removeChild(elemet);
	i=i-1;
	elem = document.getElementById(i);
	elem.parentNode.removeChild(elem);
}
var count=0;
function add()
{
	var label = document.createElement("label");
	 var element = document.createElement("input");
	  element.type="text"
	var button=document.createElement('input');
	button.type="button";
	button.value="<?php echo _('Remove')?>";
	label.setAttribute("id",count);
	var labeltext = document.createTextNode("<?php echo _('Host Name:')?>");
	label.appendChild(labeltext);
	count=count+1;
	element.setAttribute("id",count);
	element.setAttribute("size","36");
	element.style.margin="0px 0px 20px 0px ";
	element.setAttribute("name", "<?php echo _('Host_Name')?>");
	count=count+1;
	button.setAttribute("id",count);
	button.style.margin="0px 0px 20px 0px ";
	count=count+1;
	var div1 =document.createElement("div");
	document.getElementById('foo').appendChild(div1);
	document.getElementById('foo').appendChild(label);
	document.getElementById('foo').appendChild(element);
	document.getElementById('foo').appendChild(button);
	button.onclick=add1
}
</script>
<div id="content">
   	<h1><?php echo _('Advanced > Dynamic DNS > Add')?></h1>
   	<div id="educational-tip">
   		<p class="tip"><?php echo _('You can configure a new DDNS entry by entering the following details.')?> </p>
   		<p class="hidden"><?php echo _('<strong>Service Provider:</strong>provide the registered FQDN of the Dynamic DNS Service Provider.')?></p>
   		<p class="hidden"><?php echo _('<strong>User Name:</strong> Enter the User Name you registered on the Service Provider.')?></p>
   		<p class="hidden"><?php echo _('<strong>Password:</strong> Enter the Password you registered on the Service Provider')?></p>
   		<p class="hidden"><?php echo _('<strong>Host Name:</strong> Enter the Host Name you registered on the Service Provider. In case you had registered multiple host names with the same provider, click "ADD" next to Host Name and add the additional host names.')?></p>
   	</div>
	<form id="pageForm">
	   	<div class="module forms">
	   		<h2><?php echo _('Dynamic DNS')?></h2>
	   		<div class="form-row " id="Service_Provider" >
	   			<label for="Service_Provider"><?php echo _('Service Provider:')?></label>
					<!--input type="text" value="dyndns.org" id="Service_Provider1"  name="Service_Provider" /-->
					<select name="Service_Provider1" id="Service_Provider1">
					<?php
						$ids=explode(",",getInstanceIDs("Device.X_CISCO_COM_DDNS.Service."));
						foreach ($ids as $key=>$j) {
							$spName		=getStr("Device.X_CISCO_COM_DDNS.Service.".$j.".ServiceName");
							$serviceStatus	=getStr("Device.X_CISCO_COM_DDNS.Service.".$j.".Enable");
							if(strcasecmp($serviceStatus,"false") == 0) echo '<option value="'.$spName.'">'.$spName.'</option>';
						}
					?>
					</select>
	   		</div>
	   		<div class="form-row odd" >
	   			<label for="User_name"><?php echo _('User Name:')?></label> <input type="text"  id="User_name" name="User_name" class="text" size="35"  />
	   		</div>
	   		<div class="form-row">
	   			<label for="Password"><?php echo _('Password:')?></label> <input type="password"  id="Password" name="Password" class="text" size="32"  />
	   		</div>
	   		<div class="form-row odd" id="show1">
	   			<label for="Host_Name"><?php echo _('Host Name:')?></label> <input type="text"  id="Host_Name1" name="Host_Name" class="text" size="35"  />
	   		</div>
	   		<div class="form-row odd" ><div id="foo"></div></div>
	   		<div class="form-btn">
	   			<input type="button" value="<?php echo _('ADD')?>" id="add-Dynamic-DNS" class="btn" onclick="add()"/>
	   		</div>
	   		<div class="form-btn">
	   			<input type="button" id="btn-save" value="<?php echo _('Save Settings')?>" class="btn submit" />
	   			<input type="button" id="btn-cancel" value="<?php echo _('Cancel Settings')?>" class="btn alt reset"/>
	   		</div>
	   	</div> <!-- end .module -->
	</form>
   </div><!-- end #content -->
<?php include('includes/footer.php'); ?>
