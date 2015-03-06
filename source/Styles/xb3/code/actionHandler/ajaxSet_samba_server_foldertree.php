<?php

$dir	= $_REQUEST['dir'];
$id		= $_GET["id"];

setStr("Device.X_CISCO_COM_FileSharing.Sharing.$id.LocalPath", $dir, false);
$files	= array_filter(explode(",", getStr("Device.X_CISCO_COM_FileSharing.Sharing.$id.SubDirectories")));

echo '<ul class="jqueryFolderTree" style="display: none;" >'; //style="display: none;"
// All dirs start with "+", for simple coding...
foreach($files as $file){
	echo '<li><a href="#" rel="'.$dir.$file.'/" class="collapsed"></a><a href="#" ref="'.$dir.$file.'/" class="folder">'.$file.'</a></li>';
}
echo '</ul>';	

sleep(1);





/*
//-------------- CONFIG VARS ---------------------------------//

$basefolder = 'uploads'; //just the name
$base = $_SERVER['DOCUMENT_ROOT'].'/jquery_folder_tree/'.$basefolder.'';

//-------------- END FILE BROWSER CONFIG VARS-----------------//


if(isset($_REQUEST['dir'])){
	$dir=urldecode($_REQUEST['dir']);
}else{
	$dir='';
}

//sleep(1);

if( file_exists($base.$dir) ) {
	$files = scandir($base.$dir);
	natcasesort($files);
	if( count($files) > 2){ // The 2 accounts for . and .. 
		echo '<ul class="jqueryFolderTree" style="display: none;" >'; //style="display: none;"
		// All dirs
		foreach($files as $file){
			if( file_exists($base.$dir.$file) && $file != '.' && $file != '..' && is_dir($base.$dir.$file)){
				if(check_for_subdirs($base.$dir.$file)==true){
					echo '<li><a href="#" class="collapsed" rel="'.$dir.$file.'/"></a><a href="'.$dir.$file.'/" class="folder">'.$file.'</a></li>';
				}else{
					echo '<li><a href="#" class="nosubs" rel="'.$dir.$file.'/"></a><a href="'.$dir.$file.'/" class="folder">'.$file.'</a></li>';
				}
			}
		}
		echo '</ul>';	
	}
}


function check_for_subdirs($path){
	$found = false;
	$items = scandir($path);
	foreach($items as $item){
		if($item != '.' && $item != '..' && is_dir($path.'/'.$item) ){
			$found=true;
			break;
		}else{
			$found=false;
		}
	}
	return $found;
}
*/

?>