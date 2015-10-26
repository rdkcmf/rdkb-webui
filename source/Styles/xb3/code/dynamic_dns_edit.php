<?php include('includes/header.php'); ?>
<?php include('includes/utility.php'); ?>

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
	$i=$_GET['id'];
	$dns_param = array(
	  	"sp" 		=> "Device.X_CISCO_COM_DDNS.Service."."$i".".ServiceName",
		"username" 	=> "Device.X_CISCO_COM_DDNS.Service."."$i".".Username",
		"password" 	=> "Device.X_CISCO_COM_DDNS.Service."."$i".".Password",
		"hostname" 	=> "Device.X_CISCO_COM_DDNS.Service."."$i".".Domain",      
		);
	$dns_value = KeyExtGet("Device.X_CISCO_COM_DDNS.Service.", $dns_param);
//	echo "<script>var ID=".$i.";</script>";
	$sp = $dns_value["sp"];
	$username = $dns_value["username"];
	$password = $dns_value["password"];
	$hostname = $dns_value["hostname"];
	
?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > Dynamic DNS", "nav-Dynamic-dns");

	var ID = <?php echo $i ?>;
	var jsSP = "<?php echo $sp ?>";
	var jsUser = "<?php echo $username ?>";
	var jsPwd = "<?php echo $password ?>";
	var jsHost = "<?php echo $hostname ?>";
	var hostArr = jsHost.split(",");
	var cnt = hostArr.length;
	
	$("#Service_Provider1").val(jsSP);
	$("#User_name").val(jsUser);
	$("#Password").val(jsPwd);
	$("#Host_Name1").val(hostArr[0]);

	for(var i=1;i<hostArr.length;i++) {
		add(hostArr[i]);
	}
	
	// If Enable UPnP is not checked, disable the next two form fields


    $("#btn-save").click(function() {
    	var sp = $("#Service_Provider1").val();
		var spLC = sp.toLowerCase();
		var username = $("#User_name").val();
		var password = $("#Password").val();
		
		var hostnames = document.getElementsByName("Host_Name");
		var hostname = hostnames[0].value;
		for(var i=1;i<hostnames.length;i++) {
			hostname += ","+hostnames[i].value
		}
		
		if(spLC!="dyndns.org" && spLC!="tzo.com" && spLC!="changeip.com" && spLC!="freedns.afraid.org") {
			alert("Service provider name should be \"DynDns.org\" or \"TZO.com\" or \"changeip.com\" or \"freedns.afraid.org\".");
		} else {
			jProgress('This may take several seconds', 60);
			$.ajax({
				type:"POST",
				url:"actionHandler/ajax_ddns.php",
				data:{edit:"true",ID:ID,sp:sp,username:username,password:password,hostname:hostname},
				success:function(results){
					//jAlert(results);
					jHide();
					if (results=="Success!") { window.location.href="dynamic_dns.php";}
				},
				error:function(){
					jHide();
					jAlert("Failure, please try again.");
				}
			});
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
function add(hostName)
{
var label = document.createElement("label");

 var element = document.createElement("input");

  element.type="text"

var button=document.createElement('input');
button.type="button";
button.value="Remove";

label.setAttribute("id",count);

var labeltext = document.createTextNode("Host Name:");
label.appendChild(labeltext);

count=count+1;
element.setAttribute("id",count);
element.setAttribute("size","36");
element.style.margin="0px 0px 20px 0px ";
element.setAttribute("name", "Host_Name");
if(hostName!=null) 
	element.value = hostName;

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
	<h1>Advanced > Dynamic DNS > Edit</h1>

	<div id="educational-tip">
		<p class="tip">You can edit this existing DDNS entry, by changing the service provider name/host name/user name/password combination.</p>

	</div>

	<div class="module forms">
		<h2>Dynamic DNS</h2>

		<div class="form-row " id="Service_Provider" >

			<div class="form-row "  style="float:left">
				<label for="Service_Provider">Service Provider:</label>
				<!--input type="text" value="dyndns.org" id="Service_Provider1"  name="Service_Provider" /-->
				<select name="Service_Provider1" id="Service_Provider1">
				<?php
					$ids=explode(",",getInstanceIDs("Device.X_CISCO_COM_DDNS.Service."));
					foreach ($ids as $key=>$j) {
						$spName		=getStr("Device.X_CISCO_COM_DDNS.Service.".$j.".ServiceName");
						$serviceStatus	=getStr("Device.X_CISCO_COM_DDNS.Service.".$j.".Enable");
						if(strcasecmp($sp,$spName) == 0) echo '<option value="'.$spName.'">'.$spName.'</option>';
						else if(strcasecmp($serviceStatus,"false") == 0) echo '<option value="'.$spName.'">'.$spName.'</option>';
					}
				?>
				</select>
			</div>

		</div>

		<div class="form-row odd" >
			<label for="User_name"> User Name:</label> <input type="text"  id="User_name" name="User_name" class="text" size="35"  value="USG"/>
		</div>
		<div class="form-row">
			<label for="Password">Password:</label> <input type="password"  id="Password" name="Password" class="text" size="32"  value="Comcast123"/>
		</div>
		<div class="form-row odd" id="show1">
			<label for="Host_Name">Host Name:</label> <input type="text"  id="Host_Name1" name="Host_Name" class="text" size="35"  value="App1"/>
		</div>


		<div class="form-row odd" ><div id="foo"></div></div>

		<div class="form-btn">
			<input type="button" value="ADD" id="add-Dynamic-DNS" class="btn" onclick="add()"/>

		</div>

		<div class="form-btn">
			<input type="button" id="btn-save" value="Save Settings" class="btn submit" />
			<input type="button" id="btn-cancel" value="Cancel Settings" class="btn alt reset"/>

		</div>


	</div> <!-- end .module -->

</div><!-- end #content -->


<?php include('includes/footer.php'); ?>
