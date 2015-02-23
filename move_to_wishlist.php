<?php
//Processes adding/editing/displaying comments.
//Will be called VIA AJAX




	require($_SERVER["DOCUMENT_ROOT"]."/database/common/connect_db.php");
		
	require($_SERVER["DOCUMENT_ROOT"]."/database/common/functions.php");



						
							
							
				//Get the id for the bot to be copied
			$id = $_POST["id"];
			
			
			//Ok, for now, since there are only 2 users, Louis & Cindy, we will assume if the bot belongs
			//to Cindy, it will be copied to Louis's Wishlist and if the bot belongs to Louis we will copy
			//it to Cindy's wishlist. This will have to be changed if a new user is ever added!
			
			//Get the id of the user of the bot to be copied
			$user = $db->get_var("SELECT owner FROM tfdb_bots WHERE id = ".$id);
			
			//Get the name of that user
			$username = $db->get_var("SELECT name FROM tfdb_users WHERE id = ".$user);
			
			//Get the name of the bot
			$botName = $db->get_var("SELECT name FROM tfdb_bots WHERE id = ".$id);
			
			//Get the id of the list
			$list = $db->get_var("SELECT list FROM tfdb_bots WHERE id = ".$id);
			
			//Now, make copies of the the images for this bot if there are any.
			//We want to make copies of the images instead of just using the same images
			//in case one user deletes the bot, it won't delete the images of the other person's copied bot
			
			//Get the images array
			$images = $db->get_var("SELECT image FROM tfdb_bots WHERE id = ".$id);
			
			//Unserialize the image array
			$images = unserialize($images);
			
			//Create blank array to hold the new images for the copied bot
			$newImages = array();
			
			
			//Copy the images if there are any
			if(count($images) > 0){
				$count = 1;	
				foreach($images as $i){
					
					//Generate new filename
					$fn = explode("_",$i);
					//Take the timestamp off the end of the filename
					array_pop($fn);	
					//Create new filename. Since all images should have lowercase .jpg as extension, we will assume that.
					//Adds a count variable into the name that changes for each image just in case 2 images get saved in the same second
					//which would make the timestamp useless.
					$fn = implode("_",$fn);
					$fn = $fn."_copy_".$count."_".time().".jpg";	
					
					
					//Copy large image	
					if(file_exists("images/tf/".$i)){
						copy("images/tf/".$i, "images/tf/".$fn);
					}
					
					//Copy thumbnail	
					if(file_exists("images/tf/thumbs/".$i)){
						copy("images/tf/thumbs/".$i, "images/tf/thumbs/".$fn);
					}
					
					//Add filename to new image array
					$newImages[] = $fn;
					
					$count++;
				}
			}
			
			//Now copy into the database using some array magic since if I add a new field to tfdb_bots in the future, I want them all to copy over
			$row = $db->get_row("SELECT * FROM tfdb_bots WHERE id = ".$id,ARRAY_A);
			
			//Change some values before copying
			
			//Get rid of the id field
			unset($row["id"]);
			
			//Set quotes for name
			$row["name"] = "'".$row["name"]."'";
			
			//Change the date to right now
			$row["date"] = "'".date("Y-m-d H:i:s",time()-3600)."'";
			
			//Change "list" to 2 for Wishlist
			$row["list"] = 2;
			
			//Switch the owner. 1 = Louis. 2 = Cindy.
			$row["owner"] = $user == 1 ? 2 : 1;
			
			//Clear the description
			$row["comments"] = "''";
			
			//Set quotes for tfl_link
			$row["tfl_link"] = "'".$row["tfl_link"]."'";
			
			//Insert new image array
			$row["image"] = "'".serialize($newImages)."'";
			
			//If this is part of a combiner, remove it from being part of a combiner.
			//This way if somebody only wants that one member, they can extract it out of the combiner
			$row["part_of_combiner"] = 0;
			
			//Reset the sort to add it to the bottom. We will assume no list will ever have 10000 entries!
			$row["sort"] = 10000;
			
			//Create an array of just the keys
			$keys = array_keys($row);
			
			//Create an array of just the values
			$values = array_values($row);
			
			//Insert into the database
			$db->query("INSERT INTO tfdb_bots (".implode(",",$keys).") VALUES (".implode(",",$values).")");
			
		
		
								
?>