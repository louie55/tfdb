<?php
//THIS FILE WILL CREATE A ZIP FILE OF ALL OF THE PHOTOS OF THE PASSED BOT
//AND SEND IT BACK TO THE BROWSER AS A FILE TO DOWNLOAD.

//Make sure no file compression happens so that the browser will know the filesize
//@apache_setenv('no-gzip', 1);
//@ini_set('zlib.output_compression', 0);

//Delete old ZIP file if it exists
if(file_exists("zip/photos.zip")){
	unlink("zip/photos.zip");
}

if(isset($_GET['id'])){ //Only run if this has been called with a bot ID

	//Get the bot id
	$bot = $_GET['id'];
	
	//Connect to DB
	require("common/connect_db.php");
	
	//Get the list of images for this bot
	$images = $db->get_var("SELECT image FROM tfdb_bots WHERE id = ".$bot);
	
	//Get the bot name
	$bot_name = $db->get_var("SELECT name FROM tfdb_bots WHERE id = ".$bot);
	
	//Replace spaces with underscores just because I hate using spaces in filenames
	//Also replace other symbols
	$bot_name = str_replace(array(" ","&"),array("_","and"),$bot_name);
	
	
	//Convert to Array
	$images = unserialize($images);
	
	//Make sure $images is an array in case there are no images
	if(!is_array($images)){
		$images = array();
	}
	
	//If this is a combiner, also add all of the images of its members
	if($db->get_var("SELECT is_combiner FROM tfdb_bots WHERE id = ".$bot) > 0){ //Then this is a combiner
		//Get it's members
		$members = $db->get_results("SELECT image FROM tfdb_bots WHERE part_of_combiner = ".$bot,ARRAY_N);
		
		if($db->num_rows > 0){
			foreach($members as $m){
				$tempArr = unserialize($m[0]);
				//Merge this member's photos to the end of the images array
				$images = array_merge($images,$tempArr);
			}
		}
	}
	
	if(count($images) > 0){ //There should be at least 1 image since the download button won't show if there isn't, but putting it here just in case
		$zip = new ZipArchive();
		$filename = "zip/photos.zip";

		if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
			exit("cannot open <$filename>\n");
		}

		foreach($images as $i){ //Add each image to the zip file
			//The second $i tells it to put the file in the root of ZIP file and keep the same name.
			//The second argument tells it what path/filename to make the file in the zip file.
			//So if you wanted it the folder /images inside the ZIP file, then you would put images/$i
			//You could also change the $i to anything you want if you want to rename the file in the ZIP file
			$zip->addFile("images/tf/".$i,$i);
		}
		
		$zip->close();
	}
	
	//Send ZIP file to browser
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename='.$bot_name.'_Photos.zip');
	header('Content-Length: '.filesize("zip/photos.zip"));
	readfile("zip/photos.zip");
	
	
		
	



} //end if isset GET id
elseif(isset($_GET["all"])){
	//Connect to DB
	require("common/connect_db.php");
	
	//First unserialize the ID array
	$ids = unserialize(base64_decode($_GET["all"]));
	
		
	if(is_array($ids) && count($ids) > 0){
		$imgArray = array();
		foreach($ids as $bot){
			//Get the list of images for this bot
			$images = $db->get_var("SELECT image FROM tfdb_bots WHERE id = ".$bot);
			
			//Convert to Array
			$images = unserialize($images);
			
			//Make sure $images is an array in case there are no images
			if(!is_array($images)){
				$images = array();
			}
			
			//If this is a combiner, also add all of the images of its members
			if($db->get_var("SELECT is_combiner FROM tfdb_bots WHERE id = ".$bot) > 0){ //Then this is a combiner
				//Get it's members
				$members = $db->get_results("SELECT image FROM tfdb_bots WHERE part_of_combiner = ".$bot,ARRAY_N);
				
				if($db->num_rows > 0){
					foreach($members as $m){
						$tempArr = unserialize($m[0]);
						//Merge this member's photos to the end of the images array
						$images = array_merge($images,$tempArr);
					}
				}
			}
			
			//Add the images from this bot to the global image array
			$imgArray = array_merge($imgArray,$images);
		}
		
		//Create ZIP file
		if(count($imgArray) > 0){ //There should be at least 1 image since the download button won't show if there isn't, but putting it here just in case
			$zip = new ZipArchive();
			$filename = "zip/photos.zip";

			if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
				exit("cannot open <$filename>\n");
			}

			foreach($imgArray as $i){ //Add each image to the zip file
				//The second $i tells it to put the file in the root of ZIP file and keep the same name.
				//The second argument tells it what path/filename to make the file in the zip file.
				//So if you wanted it the folder /images inside the ZIP file, then you would put images/$i
				//You could also change the $i to anything you want if you want to rename the file in the ZIP file
				$zip->addFile("images/tf/".$i,$i); 
			}
			
			$zip->close();
		}
		
		//Send ZIP file to browser
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename=All_Photos.zip');
		header('Content-Length: '.filesize("zip/photos.zip"));
		readfile("zip/photos.zip");
	}
}
?>