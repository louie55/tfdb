<?php
//Processes Sorting Bots and Attributes




require($_SERVER["DOCUMENT_ROOT"]."/database/common/connect_db.php");
	
require($_SERVER["DOCUMENT_ROOT"]."/database/common/functions.php");


		//If we are sorting bots
		if($_GET["what"] == "bots"){
			//enter the sorts into the database
			foreach($_POST["order"] as $k => $v){
				$sort = $k + 1; //Array starts at key 0. I want sort to start at 1 so am adding 1 to each key.
				$db->query("UPDATE tfdb_bots SET sort = ".$sort." WHERE id = ".$v);
			}
			
			
		}
		elseif($_GET["what"] == "lists"){
			$list = $_POST["list"];	
			
			//enter the sorts into the database
			foreach($_POST["order"] as $k => $v){
				$sort = $k + 1; //Array starts at key 0. I want sort to start at 1 so am adding 1 to each key.
				$db->query("UPDATE tfdb_".$list." SET sort = ".$sort." WHERE id = ".$v);
			}
		}
								
								
?>