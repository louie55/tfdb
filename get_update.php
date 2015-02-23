<?php
//Processes copying an item to a wishlist



if(isset($_GET["id"])){
	require($_SERVER["DOCUMENT_ROOT"]."/database/common/connect_db.php");
		
	require($_SERVER["DOCUMENT_ROOT"]."/database/common/functions.php");
}

							if(isset($_GET["id"])){
								$id = $_GET["id"];
							}
							
							echo $db->get_var("SELECT text FROM tfdb_updates WHERE id = ".$id);
							
							
		
								
?>