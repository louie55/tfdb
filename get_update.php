<?php
//Processes copying an item to a wishlist



if(isset($_GET["id"])){
	require("common/connect_db.php");
		
	require("common/functions.php");
}

							if(isset($_GET["id"])){
								$id = $_GET["id"];
							}
							
							$text = $db->get_var("SELECT text FROM tfdb_updates WHERE id = ".$id);
							
							echo stripslashes($text);
							
							
		
								
?>