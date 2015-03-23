<?php
//createDropdown() Creates a drop-down list form element dynamically from a database table
//It excepts the name of the table, the name of the field to create it from, the name of the form element,
//as well as an integer value to show which option should be selected by default if we are editing a record

//PROTOTYPE
//string createDropdown(string $table, string $field, string $name, int $selected)

function createDropdown($table, $field, $name, $selected) {
	global $db;
	
	$dropDown = "<select name=\"".$name."\" id=\"".$name."\">\n";
	
	//Values of this array will be tables that have a "sort" column so that the drop-down can be sorted
	$sortArray = array(
	"tfdb_factions",
	"tfdb_series",
	"tfdb_classes",
	"tfdb_subgroups"
	);
	
	$sortText = in_array($table,$sortArray) ? " ORDER BY sort" : "";
	
	$rows = $db->get_results("SELECT * FROM ".$table.$sortText);
	
	//Add Blank Row to start for Form Validation to work
	$dropDown .= "<option value=\"0\"></option>\n";
	
	if($db->num_rows > 0){
		foreach($rows as $row){
			$dropDown .= "	<option value=\"".$row->id."\"";
			if($selected > 0 && $selected == $row->id){
				$dropDown .= " selected";
			}
			$dropDown .= ">".$row->$field."</option>\n";
		}
	}
	
	$dropDown .= "</select>\n";
	
	return $dropDown;
}

//printLinkList() creates a bulleted list of links to the $targetPage.
//It takes all the records from $table and prints out $field from each one.
//It uses $urlLabel as the label for the id of each row. For example:
//
//If the row has an ID of 4 and the target page is view.php and the URL label is "bot" then the link would be:
//view.php?bot=4

//PROTOTYPE
//string printLinkList(string $table, string $field, string $targetPage, string $urlLabel)

function printLinkList($table, $field, $targetPage, $urlLabel) {
	global $db;
	
	$linkList = "<ul class=\"generated_link_list\">\n";
	
	$rows = $db->get_results("SELECT * FROM ".$table);
	
	//Create glue string for the query portion of the URL
	//If there is a question mark in the $targetPage string, then we don't want to add another one
	//so we'll use an ampersand instead
	$urlGlue = strpos($targetPage, "?") !== false ? "&" : "?";
	
	if($db->num_rows > 0){
		foreach($rows as $row){
			$linkList .= "	<li><a href=\"". $targetPage . $urlGlue . $urlLabel ."=". $row->id ."\">". $row->$field ."</a></li>\n";
		}
	}
	
	//Print an ALL option
	//$linkList .= "	<li><a href=\"". $targetPage . $urlGlue . $urlLabel ."=0\">ALL</a></li>\n";
	
	$linkList .= "</ul>\n";
	
	return $linkList;
}

//getVar() gets a variable out of one of the DB tables with the given $field and $id

//PROTOTYPE
//string getVar(string $table, string $field, string $id)

function getVar($table, $field, $id){
	global $db;
	
	return $db->get_var("SELECT ".$field." FROM ".$table." WHERE id = ".$id);
}


//createFilterList() creates a verical list of radio-button options for a given table/field

//PROTOTYPE
//string createFilterList(string $table, string $field, string $field2, string $name, int $checked)

//$table is the name of table which has the names to print
//$field is the name of the field to print from that table
//$field2 is the name of the field in the tfdb_bots table we want to get the data from
//$name is the name attribute to give the radio buttons
//$checked, if greater than 0, will indicate which option should be checked. The number will be the id of the option to be checked.
//$user is which user the to filter by
//$list is which list to filter by

function createFilterList($table, $field, $field2, $name, $checked, $user, $list){
	global $db;
	
	//See which ones actually exist in the database since we only want to display those
	//Don't include bots that are part of combiners
	$whichOnes = $db->get_results("SELECT DISTINCT(".$field2.") AS id FROM tfdb_bots WHERE part_of_combiner < 1 AND owner = ".$user." AND list = ".$list);
	
		
	$finishedList = "";
	
	//If there are any, create the list
	if($db->num_rows > 0){
		
		//Ok, we have which ones we need to list, but they aren't in the right sort order.
		//We want them sorted depending on the sort order from their table.
		//To do that, we'll create an associative array with the sort value as the key and the actual value as the value.
		
		foreach($whichOnes as $i){
			$sort = $db->get_var("SELECT sort FROM ".$table." WHERE id = ".$i->id);
			
			$sortedArray[$sort] = $i->id;			
		}
		
		//Now sort the array ascending by key to put them in the right sort order
		ksort($sortedArray);
		
		
		$checkedText = $checked == 0 ? " checked" : "";
		
		//Make text red and bold if it's checked
		$styleText = $checkedText != ""	? "<span class=\"selected_filter\">Show All</span>" : "Show All";
		$finishedList .= "<input type=\"radio\" name=\"".$name."\" value=\"0\"".$checkedText."> ".$styleText."<br>\n";
		
				
		foreach($sortedArray as $w){
			if($w < 1){continue;} //Don't print zero ID
			
			//Get a count of how many of these exist in the current list
			$count = $db->get_var("SELECT COUNT(*) FROM tfdb_bots WHERE part_of_combiner < 1 AND owner = ".$user." AND list = ".$list." AND ".$field2." = ".$w);
			
			$checkedText = $checked == $w ? " checked" : "";
			$n = $db->get_var("SELECT ".$field." FROM ".$table." WHERE id = ".$w);	
			
			//Make text red and bold if it's checked
			$styleText = $checkedText != ""	? "<span class=\"selected_filter\">".$n."</span>" : $n;
				
			$finishedList .= "<input type=\"radio\" name=\"".$name."\" value=\"".$w."\"".$checkedText."> ".$styleText." <span class=\"filter_category_count\">(".$count.")</span><br>\n";
		}
	}
	
	return $finishedList;
}


//insertEmoticons() takes a string and returns the same string with all emoticons replaced with graphical versions

//PROTOTYPE
//string insertEmoticons(string $text)

//Create the array for emoticon parsing
	$emoticonArray = array(
		array( array(":D",":^D","XD"),"big_smile" ),
		array( array("-_-","-__-","-___-"),"bummed" ),
		array( array(":cool","B)","B^)"),"cool" ),
		array( array(":cry",";u;",":'("),"cry" ),
		array( array("--devil",":evil"),"devil" ),
		array( array(":embarass",":$"),"embarassed" ),
		array( array(":palm",":face-palm"),"face_palm" ),
		array( array(":hmm",":thinking"),"hmm" ),
		array( array(":idea",":eureka"),"idea" ),
		array( array(":idk",":confuse"),"idk" ),
		array( array(":laugh",":'D"),"laugh" ),
		array( array("*u*",":love","8D"),"love" ),
		array( array(":mad",">:(",">:^(",">:-("),"mad" ),
		array( array(":nervous","':S","':("),"nervous" ),
		array( array(":quiet",":shh",":|",":^|","=|"),"quiet" ),
		array( array(":(","=(",":^(",":S"," :/","=S",":^S","=/","D:","D=","D^:","D^=","D-:"),"sad" ),
		array( array("--shock",":worry"),"shocked" ),
		array( array(":)","=)",":^)",":-)","8^]",":^]","c:"),"smile" ),
		array( array("D8","8O","--surprise","8^0","8^O",":0",":O"),"surprised" ),
		array( array("--suspicious",":wondering"),"suspicious" ),
		array( array(":thumbs",":thumb"),"thumbs_up" ),
		array( array(":P","=P",":^P",":-P",),"tongue" ),
		array( array(":wink",";)",";^)",";-)"),"wink" ),
		array( array(":yes",":yeah"),"yes" )	
	);	

function insertEmoticons($text){
	global $emoticonArray;
	
	foreach($emoticonArray as $e){
		$text = str_ireplace($e[0], "<img src=\"images/emoticons/".$e[1].".png\">", $text);
	}
	
	return $text;
}


//Makes URLs into Links

function formatUrlsInText($text){
            $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            preg_match_all($reg_exUrl, $text, $matches);
            $usedPatterns = array();
            foreach($matches[0] as $pattern){
                if(!array_key_exists($pattern, $usedPatterns)){
                    $usedPatterns[$pattern]=true;
                    $text = str_replace  ($pattern, "<a href='".$pattern."' target='_blank'>".$pattern."</a> ", $text);   
                }
            }
            return $text;            
}


?>