<?php

//Include necessary files
require($_SERVER["DOCUMENT_ROOT"]."/database/common/connect_db.php");
	
require($_SERVER["DOCUMENT_ROOT"]."/database/common/functions.php");

//Include the upload handler to handle image uploads
include('class.upload.php');

//error_reporting(E_ALL);

//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//die();


//Create an array that the upload handler can use

/*
SAMPLE:
 Array
        (
            [name] => Buzzsaw_G1_04.jpg
            [type] => image/jpeg
            [tmp_name] => /tmp/phpKbDBlh
            [error] => 0
            [size] => 686456
        )
*/

//Get the number of files uploaded

//First see if any files were uploaded
if( trim($_FILES["image"]["name"][0]) != "" ){
	//Then there was at least 1 file uploaded
	$uploadCount = count($_FILES["image"]["name"]);
}
else{
	//No Files Were Uploaded
	$uploadCount = 0;
}

//Now create the new files array which the upload handler can use
if($uploadCount > 0){ //Only do it if there were files uploaded
	
	//Loop through each file uploaded and add it to the new array
	for($i = 0; $i < $uploadCount; $i++){
		$uploadedArray[] = array("name" => $_FILES["image"]["name"][$i], "type" => $_FILES["image"]["type"][$i], "tmp_name" => $_FILES["image"]["tmp_name"][$i], "error" => $_FILES["image"]["error"][$i], "size" => $_FILES["image"]["size"][$i]);
	}
	
		
}


//Create an array of symbols to replace in image names
$symbols = array(" ","%","\t","&","!","@","#","^","*","+","=","?",">","<","(",")",";","'","\"","/","\\","[","]","{","}",",","`","~");


//Create a function for image processing since we will be using it many times
//Returns a string with the filename of the processed image.
//PROTOTYPE: string processTFImage(string (or Array) $image)
function processTFImage($image,$fn){
	global $db;	
	global $series;
	global $imageUser;
	global $symbols;
		
	//Holds the name of the image file.
	//Starts as blank in case no image was selected
	$image_file_name = "";
	
	$image = new Upload($image);
	
	if ($image->uploaded) {
	  
	  
	  //Save filename into a variable for 
	  $image_filename = str_replace($symbols,"_",$_POST['name'])."_".$series."_".$imageUser."_".time();
	  
	  $image->file_new_name_body = $image_filename;
	
		  
	//Resize image if it's bigger than 1000 pixels
	$image_width = 0;	
	if(file_exists($image->file_src_pathname)){
		$img_arr = getimagesize($image->file_src_pathname);
		$image_width = $img_arr[0];
	} 
	  if($image_width > 1000){
	  	$image->image_resize          = true;
	  	$image->image_ratio_y         = true;
	  	$image->image_x               = 1000;
	  }
	  else{
	  	$image->image_resize          = false;
	  }
	  
	  $image->image_convert         = 'jpg';
	  $image->jpeg_quality          = 90;
	  $image->Process('../images/tf/');
	  
	  $image->file_new_name_body = $image_filename;
	  $image->image_resize          = true;
	  $image->image_ratio_y         = true;
	  $image->image_x               = 200;
	  $image->image_convert         = 'jpg';
	  $image->jpeg_quality          = 75;
	  $image->Process('../images/tf/thumbs');
	  
	  //Set the image filename to be entered into the database
	  $image_file_name = $image_filename . ".jpg";
	  
	  if ($image->processed) {
		 $image->Clean();
	  }
	  else {
		die('There was an error processing '.$fn.' <br><br> 
		
		If the error message below says "File too big", it means the image is too large to process. Images can\'t be larger than 3MB. You will have to pick a different image or download the image to your computer and shrink it.<br><br>
		
		If it is a different error message, email Louis with the error message and attach the image (or send the URL of the image) you were trying to upload and he\'ll try to figure it out!<br><br>
		
		<span style="font-weight: bold">Error Message:</span> ' . $image->error);
	  }
	}
	
	return $image_file_name;
}


//Create a variable to hold the image filename
$picFN = Array();

//Create a boolean variable to hold whether an image was processed or not
$imageProcessed = 0;

//Create another variable to tell us if the image that was processed was a URL upload.
//This is NOT a boolean variable as it can have 3 values. 0=No Image Processed. 1=URL Processed. 2=Uploaded Images Processed.
$urlUploaded = 0;


//Create some variables for image names
$series = ""; //Create blank variable just to make sure it doesn't mess up during deletion

	if($_GET["a"] == "add" || $_GET["a"] == "edit"){
	$series = $db->get_var("SELECT name FROM tfdb_series WHERE id = ".$_POST['series']);
	$series = str_replace($symbols,"_",$series);
	
	$imageUser = $db->get_var("SELECT name FROM tfdb_users WHERE id = ".$_POST['owner']);

}


//Now to figure out if any image URLs were uploaded. This is a little tricky since they are being passed in an array of unknown size
$urls_exist = 0;
if(isset($_POST['image_url'])){
	foreach($_POST['image_url'] as $u){
		if(trim($u) != ""){
			$urls_exist = 1;
			break;
		}
	} 
}


//Process Saving image(s) from URL
if($urls_exist == 1){
	
	foreach($_POST['image_url'] as $u){
		if(trim($u) != ""){		
			//Save temporary copy of image at the url
			$url = $u;
			$file_extension = explode(".",$u);
			$file_extension = $file_extension[count($file_extension) - 1];
			
			$filename = str_replace($symbols,"_",$_POST['name'])."_".$series."_".$imageUser."_".time().".".$file_extension;
			
			$img = '../images/tf/temp/'.$filename;
			if(!file_put_contents($img, @file_get_contents($url))){
				//Delete temporary file if something went wrong
				if(file_exists($img)){
					unlink($img);
				}
				//Delete any files that have already been processed if something went wrong so they don't end up as orphan files
					foreach($picFN as $i){
						if(file_exists("../images/tf/".$i)){
							unlink("../images/tf/".$i);
						}
						if(file_exists("../images/tf/thumbs/".$i)){
							unlink("../images/tf/thumbs/".$i);
						}
					}
					
				die("<span style='color:red;font-weight:bold;font-size:20px'>
				SORRY! Something went wrong with trying to save ".$url." from a URL!<br>
				This has been known to happen if you are trying to use an image URL from seibertron.com or other sites<br>
				that don't allow their images to be harvested by other servers.<br><br>
				
				Hit the Back button and remove this image URL from the box and try again. If you really want this image,<br>
				save it to your computer and upload it to the site instead of using the URL.<br><br>
				
				NOTE: If you were trying to upload multiple images from your computer or from URLs, you must try them all again<br>
				as they have all been erased due to this error!
				</span>");
			}
			
			//Now process it
			$picFN[] = processTFImage($img,$u);
			$imageProcessed = 1;
			$urlUploaded = 1;
			
			//Delete temporary file
			if(file_exists($img)){
				unlink($img);
			}
		}
	}
}


if($uploadCount > 0){ //Only process uploaded image(s) if we have to
	//Process the image(s)
	foreach($uploadedArray as $image){
		//Skip if there was an error with the file
		if($image["error"] > 0){
			continue;
		}
			
		$picFN[] = processTFImage($image,$image["name"]);
	}
	
	$imageProcessed = 1;
	$urlUploaded = 2;
}


//Prepare data for the database
$name = trim(htmlspecialchars($_POST['name'], ENT_QUOTES));

$comments = trim(htmlspecialchars($_POST['comments'], ENT_QUOTES));

$_POST['tfl_link'] = trim($_POST['tfl_link']);

$_POST['gallery_link'] = trim($_POST['gallery_link']);



//Insert data into the database

//Since this file processes adds, edits, and deletions, we need to create different routines

switch($_GET['a']){


	case "add":
		
		$combinerText1 = "";
		$combinerText2 = "";
		
		//Add Combiner Info if there is any
		if(isset($_POST["combiner"])){
			$combinerText1 = ",part_of_combiner";
			$combinerText2 = ",".$_POST["combiner"];
		}
		
		//Set the user cookie. Expires in 30 days.
		setcookie("user",$_POST["owner"],time() + 2592000);
		
		//Add data to the database
		//Adds it in with a sort value of 10000 to put it at the bottom of the list by default
		$db->query("INSERT INTO tfdb_bots (sort,name,series,subgroup,class,list,owner,date,tfl_link,comments,image,faction,is_combiner".$combinerText1.") VALUES (10000,'".$name."',".$_POST['series'].",".$_POST['subgroup'].",".$_POST['class'].",".$_POST['list'].",".$_POST['owner'].",'".date("Y-m-d H:i:s",time()-3600)."','".$_POST['tfl_link']."','".$comments."','".serialize($picFN)."',".$_POST['faction'].",".$_POST['is_combiner'].$combinerText2.")");
		
		
		
		break;
	
	
	
	case "edit":
		
		//Don't process without id
		if(!isset($_GET["id"])){
			die("No ID given in URL");
		}
		
		//Set part_of_combiner value if one doesn't exist so the MYSQL query doesn't break
		if(!isset($_POST["combiner"])){
			$_POST["combiner"] = 0;
		}
				
		//Enter everything EXCEPT the image
		$db->query("UPDATE tfdb_bots SET name = '".$name."', series = ".$_POST['series'].", subgroup = ".$_POST['subgroup'].", list = ".$_POST['list'].", owner = ".$_POST['owner'].", date = '".date("Y-m-d H:i:s",time()-3600)."', tfl_link = '".$_POST['tfl_link']."', comments = '".$comments."', faction = ".$_POST['faction'].", class = ".$_POST['class'].", is_combiner = ".$_POST['is_combiner'].", part_of_combiner = ".$_POST['combiner']." WHERE id = ".$_GET['id']);
		
		//If images were processed, we need to update the image array in the database
		
		if($imageProcessed){
			
			//Get the current image array
			$imgArr = unserialize($db->get_var("SELECT image FROM tfdb_bots WHERE id = ".$_GET['id']));	
					
						
			//If images were uploaded via upload or URL, then find out what to do with them depending on the user's preference
			
				
				//See if the user opted to add images to existing images or replace them
				if($_POST["upload_action"] == "add"){
					$imgArr = array_merge($imgArr,$picFN);
				}
				elseif($_POST["upload_action"] == "replace"){
					//First we need to delete all the existing images
					foreach($imgArr as $i){
						if(file_exists("../images/tf/".$i)){
							unlink("../images/tf/".$i);
						}
						if(file_exists("../images/tf/thumbs/".$i)){
							unlink("../images/tf/thumbs/".$i);
						}
					}
					
					//Now Replace the existing Image array with the new one
					$imgArr = $picFN;
				}
				
			
					
				
			//Put the new image array into the database	
			$db->query("UPDATE tfdb_bots SET image = '".serialize($imgArr)."' WHERE id = ".$_GET['id']);
			
		} //End if image processed
		
		break;
		
	
	
	case "delete":
		
		//Don't process without id
		if(!isset($_GET["id"])){
			die("No ID given in URL");
		}
		
		
		//First delete the images if there are any
		
		//Get the current image array
			$imgArr = unserialize($db->get_var("SELECT image FROM tfdb_bots WHERE id = ".$_GET['id']));
			
				if(count($imgArr) > 0){	
					//Delete All Images
					foreach($imgArr as $i){
						if(file_exists("../images/tf/".$i)){
							unlink("../images/tf/".$i);
						}
						if(file_exists("../images/tf/thumbs/".$i)){
							unlink("../images/tf/thumbs/".$i);
						}
					}
				}
			
		//Now delete record from the database
		$db->query("DELETE FROM tfdb_bots WHERE id = ".$_GET['id']);
		
		break;

}

if(isset($_GET["list"])){ //If we are editing, get the user/list that the user was editing so we can pass it on
	$queryString = "?list=".$_GET['list']."&user=".$_GET['user'];
}
else{
	$queryString = "";
}

header("location:message.php".$queryString);

?>
