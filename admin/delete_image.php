<?php
//Deletes an image via an AJAX call.

//THIS IS MY FIRST EVER ATTEMPT AT USING AJAX REQUESTS!!!


require($_SERVER["DOCUMENT_ROOT"]."/database/common/connect_db.php");
	
require($_SERVER["DOCUMENT_ROOT"]."/database/common/functions.php");


								$imageArr = unserialize( $db->get_var("SELECT image FROM tfdb_bots WHERE id = ".$_GET['id']) );
								
								//Delete an image if we are deleting an image
								if(isset($_GET["delete"])){
									$img = 	$_GET["delete"];
									//Loop through the image array and find the image
									
									//Am not adding check for 0 images here since this should never run if there are no images
									
									//Delete the image from the image array
									$key = array_search($img, $imageArr);
									unset($imageArr[$key]);
									//Reset Array Keys
									$imageArr = array_values($imageArr);
									
									//Delete the images from the server
									if(file_exists("../images/tf/".$img)){
										unlink("../images/tf/".$img);
									}
									if(file_exists("../images/tf/thumbs/".$img)){
										unlink("../images/tf/thumbs/".$img);
									}
									
									//Rewrite the new image array back to the database
									$db->query("UPDATE tfdb_bots SET image = '".serialize($imageArr)."' WHERE id = ".$_GET['id']);
								}
								
								
								//Set main image if that's what we are doing
								if(isset($_GET["main"])){
									if(array_search($_GET["main"],$imageArr) !== false){
										$key = array_search($_GET["main"],$imageArr); //Find the image in the array
										unset($imageArr[$key]); //Delete that image out of the array
										array_unshift($imageArr,$_GET["main"]); //Add that image back to the beginning of the array
										//Rewrite the new image array back to the database
										$db->query("UPDATE tfdb_bots SET image = '".serialize($imageArr)."' WHERE id = ".$_GET['id']);
									}
								}
								
								
								
								//Print the list of images if there are any
								
								
								if(count($imageArr) < 1){
									echo "<img src=\"images/image_preview_placeholder.png\">\n";
								}
								else{ //There are images, so process them
									//Loop Through The Images
									$c = 1;
									foreach($imageArr as $i){
										echo "<div class=\"edit_image_thumbnail\"><img class=\"edit_image_img\" src=\"../images/tf/thumbs/".$i."\">\n";
										echo "	<img onclick=\"deleteImage('".$i."')\" title=\"Delete This Image\" class=\"delete_image_icon\" src=\"../images/delete_tiny.png\">\n";
										if($c == 1){ //Print text for choosing the main image
											echo "<br><span class=\"smallText\">Main Image</span>\n";
										}
										else{
											echo "<br><span onclick=\"makeMain('".$i."');\" id=\"make_main\" class=\"smallText\">Make Main</span>\n";
										}
										echo "</div>\n";
										if($c % 5 == 0){ //Place a line break after every 5 images
											echo "<br>\n";
										}
										
										$c++;
									}
								}


?>