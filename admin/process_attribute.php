<?php
//Processes editing or adding attributes such as Series, Factions, etc...




require($_SERVER["DOCUMENT_ROOT"]."/database/common/connect_db.php");
	
require($_SERVER["DOCUMENT_ROOT"]."/database/common/functions.php");


								
		//Only process if the correct info is passed
		//If these are present, that means we are editing via an AJAX call
		if(isset($_GET["list"]) && isset($_GET["id"]) && isset($_GET["data"])){
			
			//Get column names since we don't know them
							$columns = $db->get_results("
							SELECT COLUMN_NAME
							FROM INFORMATION_SCHEMA.COLUMNS
							WHERE table_schema = 'transformers'
  							AND table_name = 'tfdb_".
							$_GET["list"]."'", ARRAY_N);
							
							
			//Now simply save the changes in the DB
			
			//Clean up data
			$data = trim(urldecode($_GET["data"]));
			
			//Get id and column
			$arr = explode("_",$_GET["id"]);
			
			$id = $arr[0];
			$column = $arr[1];
			
			//Now enter it into the database
			$db->query("UPDATE tfdb_".$_GET["list"]." SET ".$columns[$column][0]." = '".$data."' WHERE id = ".$id);
			
			
			//Return OK
			echo "OK";
			
		}
		
		//If we are adding, then this isn't an AJAX call, but a regular call and we should add entry to database an redirect back
		if(isset($_POST["add"])){
			$list = $_POST["list"];
			
			//Create strings for DB INSERT query
			foreach($_POST as $k => $v){
				if($k != "list" && $k != "add"){ //Filter out the hidden form elements and the rest should be what to enter in the DB
					$qArr1[] = $k;
					$qArr2[] = "'".$v."'";
				}
			}
			
			
			//Now we will set the sort for this item if it is not a list.
			//By default we will place this new item at the end of the list by giving a sort value of 1 higher than the highest sort value that exists
			if($list != "lists"){
				$sortArr = $db->get_results("SELECT sort FROM tfdb_".$list,ARRAY_N);
				
				foreach($sortArr as $s){
					$sorts[] = $s[0];
				}
				
				//sort the array in ascending order
				sort($sorts,SORT_NUMERIC);
				
				//Get the highest sort number
				$sortMax = array_pop($sorts);
				
				//Add 1 to get the value to assign this added item
				$sortMax++;
				
				//Add the sort value into the array that will be entered into the database
				$qArr1[] = "sort";
				$qArr2[] = $sortMax;
				
			}			
			
			
			//Now add to the DB
			$db->query("INSERT INTO tfdb_" . $list . " (".implode(",",$qArr1).") VALUES (".implode(",",$qArr2).")");
			
			
			
			//Go back to where we started
			header("location:edit_attributes.php?list=".$list);
		}
								
								
								
?>