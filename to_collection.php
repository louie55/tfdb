<?php
//Moves a Wishlist Entry into the Collection of the same person




require("common/connect_db.php");
	
require("common/functions.php");


		//Get the ID of the bot that was passed to this page
		$id = $_POST["id"];
		
		//Move to collection
		$db->query("UPDATE tfdb_bots SET list = 1 , sort = 10000 WHERE id = ".$id);	
		
		
		
		//Go back to the page we were on
		
		
								
								
?>