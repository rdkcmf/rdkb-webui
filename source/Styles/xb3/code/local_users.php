<?php include('includes/header.php'); ?>

<!-- $Id: local_users.php 3116 2009-10-15 20:19:45Z cporto $ -->

<div id="sub-header">
	<?php include('includes/userbar.php'); ?>
</div><!-- end #sub-header -->

<?php include('includes/nav.php'); ?>

<?php
	$ft = array();
	$id = array_filter(explode(",", getInstanceIds("Device.X_CISCO_COM_FileSharing.User.")));
	for ($j=0; $j<count($id); $j++)
	{
		$ft[$j][0] = $id[$j];
		$ft[$j][1] = getStr("Device.X_CISCO_COM_FileSharing.User.$id[$j].UserName");
		$ft[$j][2] = getStr("Device.X_CISCO_COM_FileSharing.User.$id[$j].Password");
	}
	$arConfig = array('ft'=>$ft);			
	$jsConfig = json_encode($arConfig);
?>

<script type="text/javascript">
$(document).ready(function() {
    comcast.page.init("Advanced > Local Users", "nav-local-users");
	
	var obj	= eval('(' + '<?php echo $jsConfig;?>' + ')'); 
	var usr	= obj.ft;

	for (var i=0; i<usr.length; i++)
	{
		$("#samba-users").append('\
			<tr id="'+usr[i][0]+'" class="'+ (i%2 ? "odd" : "") +'">\
				<td class="users">'+ usr[i][1] +'</td>\
				<td class="password">['+ (""==usr[i][2] ? "No Password Set" : "Hidden") +']<input type="hidden" value="'+ usr[i][2] +'"/></td>\
				<td class="edit"><a class="btn">Edit</a></td>\
				<td class="delete"><a class="btn" title="delete this service">x</a></td>\
			</tr>');
	}
	
	$(".edit").click(function(){
		var obj = $(this).parent("tr");
		$("[name='h_dest']").val("Edit");
		$("[name='h_idex']").val(obj.attr("id"));
		$("[name='h_name']").val(obj.find(".users").html());
		$("[name='h_pass']").val(obj.find("input").val());
		$("[name='h_form']").submit();
	});
	
	$(".add").click(function(){
		$("[name='h_dest']").val("Add");
		$("[name='h_idex']").val("");
		$("[name='h_name']").val("");
		$("[name='h_pass']").val("");
		$("[name='h_form']").submit();
	});
	
	$(".delete").click(function(){
		var idex = $(this).parent("tr").attr("id")
		jConfirm(
		'Are you sure you want to delete this service?'
		, 'Are You Sure?'
		, function(ret){
			if(ret){

				var jsConfig 	=	'{"dest":"'+"Delete"
					+'", "idex":"'+idex
					+'"}';

				jProgress('This may take several seconds...', 60);
				
				$.ajax({
					type: "POST",
					url: "actionHandler/ajaxSet_local_users.php",
					data: { configInfo: jsConfig },
					success: function(msg) {
						jHide();
						location.href = 'local_users.php';
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

<div id="content">
	<h1>Advanced > Local Users</h1>
     <div id="educational-tip">
	        <p class="tip">Manage permissions for shared folders on your network.</p>
	        <p class="hidden">Click <strong>+ ADD USER</strong> to add users who are allowed to access shared folders.</p>
	        <p class="hidden">Modify names and passwords by clicking <strong>EDIT</strong>, or click the <strong>X</strong> button to delete.</p>
    </div>

	<form method="post" action="#">
		<div class="module forms data">
			<h2>Local Users</h2>
			<p class="button add"><a class="btn">+ Add User</a></p>
			<table id="samba-users" class="data">
				<tr>
					<th class="users">User Name</th>
					<th class="password">Password</th>
					<th class="">&nbsp;</th>
					<th class="">&nbsp;</th>
				</tr>
			</table>
		</div> <!-- End Module -->
	</form>
	
	<form name="h_form" method="post" action="local_user_edit.php">
		<input type="hidden" name="h_dest" value=""/>
		<input type="hidden" name="h_idex" value=""/>
		<input type="hidden" name="h_name" value=""/>
		<input type="hidden" name="h_pass" value=""/>
	</form>
</div><!-- end #content -->

<?php include('includes/footer.php'); ?>
