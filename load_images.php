<?php
//THIS FILE WILL BE CALLED BY AJAX ONLY. IT WILL RETURN THE FULL SET OF IMAGES FOR THE BOT FROM WHICH IT WAS CALLED.


if(!function_exists("createDropdown")){ //Only include these if this is being called by AJAX

	require("common/connect_db.php");
		
	require("common/functions.php");
}

?>

<?php
//Get the bot ID
$id = $_GET["id"];

//Get the image array from the database
$bot = $db->get_row("SELECT * FROM tfdb_bots WHERE id = ".$id);


												//Unserialize the image array from the database
												$bot->image = unserialize($bot->image);
												
												if(count($bot->image) > 0){ 
													//Loop through the array and show each image. Make the first image larger than the others and on top of them
													for($i = 0; $i < count($bot->image); $i++){
													
														if($i == 0){
															
														
																									
													?>	
													
															<a rel="lightbox-<?php echo $bot->id; ?>" title="<?php echo $bot->name; ?> (<?php echo getVar("tfdb_series", "abbreviation", $bot->series); ?>)" href="images/tf/<?php echo $bot->image[$i]; ?>" target="_blank"><img class="image_thumb main_thumb" src="images/tf/thumbs/<?php echo $bot->image[$i]; ?>"></a><br>
														
												<?php
														} //End if $i == 0
														else{?>
															<a rel="lightbox-<?php echo $bot->id; ?>" title="<?php echo $bot->name; ?> (<?php echo getVar("tfdb_series", "abbreviation", $bot->series); ?>)" href="images/tf/<?php echo $bot->image[$i]; ?>" target="_blank"><img class="image_thumb smaller_thumb" src="images/tf/thumbs/<?php echo $bot->image[$i]; ?>"></a>
														<?php
															//Only show 5 thumbnails per line
															if( $i % 5 == 0 ){
																echo "<br>\n";
															}
														} //End else
													} //End for loop
												} //End if count bot image > 0
												


?>