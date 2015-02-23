<?php
//Processes adding/editing/displaying comments.
//Will be called VIA AJAX



if(isset($_GET["id"])){
	require($_SERVER["DOCUMENT_ROOT"]."/database/common/connect_db.php");
		
	require($_SERVER["DOCUMENT_ROOT"]."/database/common/functions.php");
}


							if(isset($_GET["id"])){
								$id = $_GET["id"];
							}
							
							echo $db->get_var("SELECT comment FROM tfdb_comments WHERE id = ".$id);
							
		
								
?>