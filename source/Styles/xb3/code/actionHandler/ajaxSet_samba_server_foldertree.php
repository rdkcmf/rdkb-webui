<!--
 If not stated otherwise in this file or this component's Licenses.txt file the
 following copyright and licenses apply:

 Copyright 2015 RDK Management

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
-->
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
