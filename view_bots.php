<?php
//Handle sessions so that a user doesn't have to keep saying who they are to leave a comment
session_start();	

						
?>

<!doctype html>
<html>
<head>
	
  	
  	
  	
	<title><?php include("common/page_title.php"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php include("common/includes.php"); ?>
	
	<!--Get Page Progress Bar Script-->
	<script src="waitImages.js"></script>
	
	 <script>
		//Preload Loading Screen Image
		$.preloadImages = function() {
		  for (var i = 0; i < arguments.length; i++) {
		    $("<img />").attr("src", arguments[i]);
		  }
		}
		
		$.preloadImages("images/loading_screen.gif");
		
		
		<?php 
	//Only show the loading screen if we are showing bots
	if(isset($_GET["user"]) && isset($_GET["list"])){
	?>
		$(window).load(function() {
			$("#loader").fadeOut("slow");
		})
		<?php
	}
	?>
		
		
		$(function() {
		$( "#accordion" ).accordion({
		collapsible: true,
		active: false,
		heightStyle: content
		});
		});
		
		
		
		function confirmDelete(name,id) {
			var c = confirm("Are you sure you want to delete " + name + "?\n\nThis can NOT be undone!");
			if(c === true){
				window.location = "admin/process.php?user=<?php echo $_GET['user'] ?>&list=<?php echo $_GET['list']; ?>&a=delete&id=" + id;
			}
			else{
				return false;
			}
		}
		
		function editBot(id){
			window.location = "admin/edit.php?user=<?php echo $_GET['user'] ?>&list=<?php echo $_GET['list'] ?>&id=" + id;
		}
		
		<?php
			//Get the minimum and maximum dates for this list
			$dates = $db->get_results("SELECT MIN(date),MAX(date) FROM tfdb_bots WHERE owner = '". $_GET["user"] ."' AND list = '". $_GET["list"]."'",ARRAY_A);
			
			//Make sure there are results just in case this is an empty list
			if($db->num_rows > 0){
				$dateMIN = date('F j, Y',strtotime($dates[0]["MIN(date)"]));
				$dateMAX = date('F j, Y',strtotime($dates[0]["MAX(date)"]));
			}
			else{
				$dateMIN = "May 4, 1984";
				$dateMAX = "May 4, 2084";
			}
			
			
		?>
		
		 $(function() {
			$( "#chooseDateFrom" ).datepicker({
				minDate: new Date("<?php echo $dateMIN; ?>"), 
				maxDate: new Date("<?php echo $dateMAX; ?>"),
				onClose: function( selectedDate ) {
					$( "#chooseDateTo" ).datepicker( "option", "minDate", selectedDate );
				} 
			});
			
			$( "#chooseDateTo" ).datepicker({
				minDate: new Date("<?php echo $dateMIN; ?>"), 
				maxDate: new Date("<?php echo $dateMAX; ?>"),
				onClose: function( selectedDate ) {
					$( "#chooseDateFrom" ).datepicker( "option", "maxDate", selectedDate );
				} 
			});
		 });
		 
		 
		 
	</script>
	
	<script type="text/javascript" src="images/slimbox/js/slimbox2.js"></script>
	<link rel="stylesheet" href="images/slimbox/css/slimbox2.css" type="text/css" media="screen" />
	
		
</head>
<body>
	<?php 
	//Only show the loading screen if we are showing bots
	if(isset($_GET["user"]) && isset($_GET["list"])){
	?>
		<div id="loader"></div>
	<?php
	}
	?>
	
	<div id="container"> <!--Contains all content on the page-->
		
		<div id="header_container"> <!--Contains Header Image and Navbar Divs-->
		
			<div id="header_image"> <!--Contains Header Image-->
		
				<?php include("common/header_image.php"); ?>
		
			</div>
			
			<div id="navbar"> <!--Contains Navigation Bar-->
		
				<?php include("common/navbuttons.php"); ?>
		
			</div>
				
		
		</div>
		
		<div id="bottom_container"> <!--Contains Sidebar and Content Divs-->
		
			<div id="sidebar"> <!--Contains Sidebar-->
				
				<div class="sidebar_links"> <!--Contains Sidebar-->
					
					<?php include("common/sidebar_links.php"); ?>
					
				</div>
			
			</div>
			
			<div id="content"> <!--Contains Page Content-->
		
				<div id="content_holder"> <!--Contains Page Content-->
		
					<div class="page_header bold">View Transformers</div>
					
					<?php
						
						//First we display the name list so we know who's TFs we want to see (if we don't already know)
						if( !isset($_GET["user"]) && !isset($_GET["bot"])){
							echo "<h2>Who's Transformers do you want to see?</h2>\n";
							echo printLinkList("tfdb_users", "name", "view_bots.php", "user");
						}
						else{ //If the user is already known
							if( !isset($_GET["list"]) && !isset($_GET["bot"])){
								echo "<h2>Which list do you want to see?</h2>\n";
							    echo printLinkList("tfdb_lists", "name", "view_bots.php?user=". $_GET["user"], "list");
							}
							else{ //Display main bot page
								
								//If we are displaying a single bot from a direct link, set user and list variables to stop errors
								if(isset($_GET["bot"])){
									$_GET["user"] = $db->get_var("SELECT owner FROM tfdb_bots WHERE id = ".$_GET["bot"]);
									$_GET["list"] = $db->get_var("SELECT list FROM tfdb_bots WHERE id = ".$_GET["bot"]);;
								}
								
								
								
								
								//Ok, the URL should have the information we need to display a list of bots
								
								//First we will display a filter section
								
									//------START FILTER SECTION------------------------------------------
									
									//Don't display filter section if only displaying single bot
									
									//
									$louisCollection = 0;
									
									
									if(!isset($_GET["bot"]) && !isset($_GET["search"]) && $louisCollection == 0){	
										//Get filtered variables if the results are already filtered
										$seriesID = isset($_GET["series"]) ? $_GET["series"] : 0;
										$subgroupID = isset($_GET["subgroup"]) ? $_GET["subgroup"] : 0;
										$classID = isset($_GET["class"]) ? $_GET["class"] : 0;
										$factionID = isset($_GET["faction"]) ? $_GET["faction"] : 0;
										
										
										//Exiting PHP mode
										?>
										<div id="filter_div">
											<form id="filterForm" action="view_bots.php" method="get">
											<h2>Filter This List</h2>
											<span class="smallText">You can mix and match any of the below options.<br>Items in <span style="font-weight:bold;color:red">red</span> are currently being shown</span>
											<table id="filter_table">
												<tr>
													<td>
														<div class="filter_header">Series</div>
														<?php echo createFilterList("tfdb_series", "abbreviation", "series","series", $seriesID, $_GET["user"], $_GET["list"]); ?>
													</td>
													
													<td>
														<div class="filter_header">Subgroup</div>
														<?php echo createFilterList("tfdb_subgroups", "name", "subgroup","subgroup", $subgroupID, $_GET["user"], $_GET["list"]); ?>
													</td>
													
													<td>
														<div class="filter_header">Size Class</div>
														<?php echo createFilterList("tfdb_classes", "name", "class","class", $classID, $_GET["user"], $_GET["list"]); ?>
													</td>
													
													<td>
														<div class="filter_header">Faction</div>
														<?php echo createFilterList("tfdb_factions", "name", "faction","faction", $factionID, $_GET["user"], $_GET["list"]); ?>
													</td>
												</tr>
											</table>
											
											<h3 style="color: blue">Only Show Bots From a Certain Date Range</h3>
											<div class="smallText">Can be useful to create a link to post in an update to show all bots added to a list on certain dates.<br>That way you don't have to list in the update all the bots you added if it was a lot.</div>
											Choose A Date Range: <input id="chooseDateFrom" type="text" size="20" name="datefrom"<?php if(isset($_GET["datefrom"]) && $_GET["datefrom"] != ""){echo " value=\"".urldecode($_GET["datefrom"])."\"";}?>>
											&nbsp;to&nbsp;
											<input id="chooseDateTo" type="text" size="20" name="dateto"<?php if(isset($_GET["dateto"]) && $_GET["dateto"] != ""){echo " value=\"".urldecode($_GET["dateto"])."\"";}?>>
											<br>(Leave Blank For All Dates)<br><input id="clearDate" type="button" value="Clear Dates">
											<script type="text/javascript">
												$("#clearDate").on("click",function(){
													$("#chooseDateFrom").val("");
													$("#chooseDateTo").val("");
												});
											</script>
											<br>
											<br>
											<?php
													//Keep Sort Intact if there is one
													if( isset($_GET["s"]) ){ 
													?>
														<input type="hidden" name="sort" value="<?php echo $_GET["sort"]; ?>">
														<input type="hidden" name="s" value="1">
													<?php
													}
													?>
											
											<input type="hidden" name="filter" value="1">
											<input type="hidden" name="user" value="<?php echo $_GET["user"]; ?>">
											<input type="hidden" name="list" value="<?php echo $_GET["list"]; ?>">
											
											
											<input type="checkbox" value="1" name="comments"<?php if(isset($_GET["comments"]) && $_GET["comments"] == 1){echo " checked";}?>> <span<?php if(isset($_GET["comments"]) && $_GET["comments"] == 1){echo " class=\"selected_filter\"";}?>>Only Show Bots/Items With Comments?</span>
											<br>
											<br>
											
											<input type="submit" value="Filter Bots!"> <input type="button" value="Show All Bots" onclick="location.href='view_bots.php?user=<?php echo $_GET["user"]; ?>&list=<?php echo $_GET["list"]; if(isset($_GET["sort"])){echo "&s=1&sort=".$_GET["sort"];}?>'">
											</form>
										</div>
										
										<br><br>
										
										<?php
										
										//START SORT SECTION
										?>
										
										<div id="sort_div">
											<form method="get" action="view_bots.php">
												<h2>Sort This List</h2>
												<div class="smallText">NOTE: If you currently have the list filtered your filter selections will remain intact.<br><br>
													
													The option in <span style="font-weight:bold;color:red">red</span> is the current sort order
												</div>
												<br>
												<div id="sort_choose"><span class="bold" style="color: blue">Choose a Sort Order:</span><br>
												
												<input type="radio" name="sort" value="default"<?php if(!isset($_GET["s"])){echo " checked";} if(isset($_GET["sort"]) && $_GET["sort"] == "default"){echo " checked";}?>> <span<?php if(!isset($_GET["s"])){echo " class=\"selected_filter\"";} if(isset($_GET["sort"]) && $_GET["sort"] == "default"){echo " class=\"selected_filter\"";}?>>Default Order (By Preferred Sort Order)</span><br>
												<input type="radio" name="sort" value="newest"<?php if(isset($_GET["s"]) && $_GET["sort"] == "newest"){echo " checked";} ?>> <span<?php if(isset($_GET["s"]) && $_GET["sort"] == "newest"){echo " class=\"selected_filter\"";} ?>>Newest First (Last ones to be Added/Edited)</span><br>
												<input type="radio" name="sort" value="oldest"<?php if(isset($_GET["s"]) && $_GET["sort"] == "oldest"){echo " checked";} ?>> <span<?php if(isset($_GET["s"]) && $_GET["sort"] == "oldest"){echo " class=\"selected_filter\"";} ?>>Oldest First</span><br>
												<input type="radio" name="sort" value="series"<?php if(isset($_GET["s"]) && $_GET["sort"] == "series"){echo " checked";} ?>> <span<?php if(isset($_GET["s"]) && $_GET["sort"] == "series"){echo " class=\"selected_filter\"";} ?>>By Series</span><br>
												<input type="radio" name="sort" value="subgroup"<?php if(isset($_GET["s"]) && $_GET["sort"] == "subgroup"){echo " checked";} ?>> <span<?php if(isset($_GET["s"]) && $_GET["sort"] == "subgroup"){echo " class=\"selected_filter\"";} ?>>By Subgroup</span><br>
												<input type="radio" name="sort" value="class"<?php if(isset($_GET["s"]) && $_GET["sort"] == "class"){echo " checked";} ?>> <span<?php if(isset($_GET["s"]) && $_GET["sort"] == "class"){echo " class=\"selected_filter\"";} ?>>By Size Class</span><br>
												<input type="radio" name="sort" value="faction"<?php if(isset($_GET["s"]) && $_GET["sort"] == "faction"){echo " checked";} ?>> <span<?php if(isset($_GET["s"]) && $_GET["sort"] == "faction"){echo " class=\"selected_filter\"";} ?>>By Faction</span><br>
												
												<?php
												//Keep Filter Intact if there is one
												if( isset($_GET["filter"]) ){ 
												?>
													<input type="hidden" name="filter" value="1">
													<input type="hidden" name="series" value="<?php echo $seriesID; ?>">
													<input type="hidden" name="subgroup" value="<?php echo $subgroupID; ?>">
													<input type="hidden" name="class" value="<?php echo $classID; ?>">
													<input type="hidden" name="faction" value="<?php echo $factionID; ?>">
													<input type="hidden" name="datefrom" value="<?php echo $_GET["datefrom"]; ?>">
													<input type="hidden" name="dateto" value="<?php echo $_GET["dateto"]; ?>">
												<?php
												}
												?>
												<input type="hidden" name="s" value="1">
												<input type="hidden" name="user" value="<?php echo $_GET["user"]; ?>">
												<input type="hidden" name="list" value="<?php echo $_GET["list"]; ?>">
												</div>
												
												<input type="submit" value="Sort Bots!">
											</form>
										</div>
										
										<?php
										
										
									} //End if not isset GET[bot]
									elseif(!isset($_GET["search"]) && $louisCollection == 0){
										?>
										<h3 style="color:red">Currently Displaying a Single Bot</h3>
										
										To get the direct link, either copy the address of this page in your browser or copy this URL:<br>
										<span class="bold"><?php echo "http://".$_SERVER["SERVER_NAME"]; ?>/view_bots.php?bot=<?php echo $_GET["bot"]; ?></span><br><br>
										<?php
										
									}
										//------END FILTER SECTION--------------------------------------------
								
								//Create the sort section
								
								//By default we will sort by the chosen sort order
								$orderBy = "sort";
								
								//If a differnt sort option was selected, then order by that
								if(isset($_GET["sort"])){
									switch($_GET["sort"]){
										case "newest":
											$orderBy = "date DESC, sort";
											break;
										case "oldest":
											$orderBy = "date, sort";
											break;
										case "series":
											$orderBy = "series, sort";
											break;
										case "subgroup":
											$orderBy = "subgroup, sort";
											break;
										case "class":
											$orderBy = "class, sort";
											break;
										case "faction":
											$orderBy = "faction, sort";
											break;
										default:
											//Do Nothing
									}
								}
								
								
								
													
								
								
								//Create the database query string
								if( isset($_GET["filter"]) ){ //Create a filtered result set
									
									//Build the DB query
									$query = "";
									
									if($_GET["series"] > 0){
										$query .= " AND series = ".$_GET["series"];
									}
									
									if($_GET["subgroup"] > 0){
										$query .= " AND subgroup = ".$_GET["subgroup"];
									}
									
									if($_GET["class"] > 0){
										$query .= " AND class = ".$_GET["class"];
									}
									
									if($_GET["faction"] > 0){
										$query .= " AND faction = ".$_GET["faction"];
									}
									
									if($_GET["datefrom"] != ""){
										//Convert date to MySQL format
										$dateFrom = date("Y-m-d",strtotime(urldecode($_GET["datefrom"])));
										$dateTo = date("Y-m-d",strtotime(urldecode($_GET["dateto"])));
										$query .= " AND date BETWEEN '".$dateFrom." 00:00:01' AND '".$dateTo." 23:59:59'";
									}
									
									
									$results = $db->get_results("SELECT * FROM tfdb_bots WHERE owner = '". $_GET["user"] ."' AND list = '". $_GET["list"] ."'".$query." ORDER BY ".$orderBy);
								}
								else{ //Display every bot for this user in the selected list
									$results = $db->get_results("SELECT * FROM tfdb_bots WHERE owner = '". $_GET["user"] ."' AND list = '". $_GET["list"] ."' ORDER BY ".$orderBy);
								}
								
								//Intercept DB query if we are searching
								if(isset($_GET["search"])){
									$list = $_GET["list"];
									$user = $_GET["user"];
									
									$search = urldecode($_GET["search"]);
									
									$results = $db->get_results("SELECT * FROM tfdb_bots WHERE list = ". $list ." AND owner = ". $user ." AND MATCH (name) AGAINST ('". $search ."')");
								}
								
								//Intercept DB query if we are supposed to be displaying only a certain bot
								if(isset($_GET["bot"])){
									$results = $db->get_results("SELECT * FROM tfdb_bots WHERE id = ".$_GET["bot"]);
								}
								
								
								
								

								
								
								//Next we will actually display the bots
								
								//First make sure there are records before displaying
								if($db->num_rows < 1){ //There are no results, so just show a message
									if(isset($_GET["search"])){
										echo "There are no Transformers that match your search. Sorry!<br><br>Use the Back button to try again.";
									}
									else{
										echo "There are no Transformers that match your criteria. Sorry!";
									}
								}
								else{ //There are results, so show them
								
									//First we will see how many combiners there are so we know how many accordions to create
									$combinerCount = 0;
									foreach($results as $bot){
										if($bot->is_combiner){
											$combinerCount++;
										}
									}
									
									?>
									<script type="text/javascript">
										<?php
										//Create accordions for combiners
										for($i = 0; $i < $combinerCount; $i++){
											?>											
												$(function() {
												$( "#accordion<?php echo $i + 2; ?>" ).accordion({
												collapsible: true,
												active: false,
												heightStyle: content
												});
												});
											<?php
										}
										?>
									</script>
									
									<div id="view_bots_currently_showing">
										
										
										
										<?php
											
										
										
										//Show a message showing what is currently being shown
										
											echo "User: ".getVar("tfdb_users", "name", $_GET["user"])." &nbsp;&nbsp;List: ".getVar("tfdb_lists", "name", $_GET["list"]);
											
											//Show the list description
											?>
											<br>
											<h3 style="color:#000000"><?php echo getVar("tfdb_lists", "description", $_GET["list"]) ?></h3>
											<?php
											if(isset($_GET["search"])){
												?>
													<h3 style="color:red;font-size:16px">Currently Showing Search Results for "<?php echo $_GET["search"]; ?>".<br>
														<?php echo count($results) > 1 ? count($results)." matches were " : "1 match was "; ?>found.<br>
														<a href='view_bots.php?user=<?php echo $_GET["user"]; ?>&list=<?php echo $_GET["list"]; ?>'>Click Here</a> to show all Transformers in this list.
													</h3>
												<?php	
											}
											
											//Get a proper count of the bots in the list
											$listCount = 0; //Won't include combiner members
											$combinerMemberCount = 0;
											foreach($results as $r){
												if($r->part_of_combiner == 0){
													$listCount++;
												}
												else{
													$combinerMemberCount++;
												}
											}
											?>
											
											<h3 style="color:green">There<?php echo $listCount > 1 ? " are ".$listCount." items that match " : " is 1 item that matches "; ?>the criteria in this list<?php if($combinerMemberCount > 0){echo " (Plus ".$combinerMemberCount." Combiner Member"; echo $combinerMemberCount > 1 ? "s)" : ")";} ?></h3>
											<br>
											<?php
												if(isset($_GET["datefrom"]) && $_GET["datefrom"] != ""){
													if($_GET["datefrom"] == $_GET["dateto"]){	
														echo "<h3 class='red_message'>Currently only displaying Bots added or edited on ".date('F j, Y',strtotime(urldecode($_GET["datefrom"])))."!</h3>";
													}
													else{
														echo "<h3 class='red_message'>Currently only displaying Bots added or edited between<br>".date('F j, Y',strtotime(urldecode($_GET["datefrom"])))." and ".date('F j, Y',strtotime(urldecode($_GET["dateto"])))."!</h3>";
													}	
												}
											?>
											<div id="toggle_thumbnails">
												<input type="checkbox" name="toggletn" id="thumbnail_toggle_checkbox" checked> Show Thumbnails in The List 
												<table id="view_bots_header_table">
													<tr>
														<td>
															<div id="thumb_size_div">
																Change Thumbnail Size:
																<div id="thumbnail_size_slider"></div>
															</div>
														</td>
													
														<td id="search_td">
															<div id="search_div">
																Search This List: <input type="text" size="30" name="search" id="search">&nbsp;<input type="button" onclick="searchBots();" value="Go">
															</div>
														</td>
													</tr>
												</table>
												<br>
												<a href="#bottomBot">Go To Bottom Bot</a>
											</div>
											
										
									</div>
									<?php
									//Print the results in a JQuery UI Accordion
																		
									//Start Accordion DIV
									echo "<div id=\"accordion\">\n";
									
									//Create a count variable
									$botCount = 0;
									//Create a variable that counts up after each combiner. This is to create the necessary accordions for them.
									$combinerCounter = 2;
									
									foreach($results as $bot){
											
										//If this bot is part of a combiner, skip it as it will be taken care of when a combiner is processed
										if($bot->part_of_combiner){
											$botCount++;
											continue;
										}

										$commentCount = $db->get_var("SELECT COUNT(*) FROM tfdb_comments WHERE type = 'bot' AND bot = ".$bot->id);
										
										//Don't show bots/items without comments if the user chose to only show items with comments
										if(isset($_GET["comments"]) && $_GET["comments"] == 1){
											if($commentCount < 1){
												continue; //Skip this item because it has no comments
											}
										}
										
										//Here's some code to create the anchor for the bottom bot.
										//Keep in mind that this isn't perfect. This code assumes that the last bot in the array is NOT part of a combiner.
										//If the last bot IS part of a combiner, then this anchor will never get created. If this becomes a problem in the future
										//then I'll have to change it.
										$botCount++;
										$h3id = "";
										if($botCount == count($results)){
											$h3id = " id=\"bottomBot\"";
										}	
										
										
										
										echo "	<h3 class=\"accordion_header\"".$h3id.">\n";
										
											//Create a table for left and right alignment in the header (tried it with DIVs, didn't work)
											//Name & icon will be on the left, action buttons on the right
											
											echo "<table class=\"accordion_header_table\">\n";
												echo "<tr>\n";
													echo "<td class=\"accordion_header_left\">\n"; //Create left DIV
														
														//Insert small thumbnail of the bot. This is experimental. Not sure if I want it or not?
														
														//Unserialize the image array from the database
														$botImages = unserialize($bot->image);
														
														if(count($botImages) > 0){ //If there are actually images for this bot
															$thumbSRC = "images/tf/thumbs/".$botImages[0];
														}
														else{
															$thumbSRC = "images/image_preview_placeholder.png";
														}
														?>
														<img src="<?php echo $thumbSRC; ?>" class="view_bots_list_thumb">
														<?php
														
														
														//Insert faction icon if there is one
														$imageName = getVar("tfdb_factions", "image", $bot->faction);
														if( trim($imageName) != "" ){
															echo "<img class=\"accordion_faction_icon\" src=\"images/tf/factions/".$imageName."?y=234\"> ";
														}
														
														//Print the bot's name and series
														echo $bot->name." (". getVar("tfdb_series", "abbreviation", $bot->series) .")\n";
														
														//Print a combiner notice if this is a combiner
														if($bot->is_combiner){
															echo " &nbsp;&nbsp;{Combiner}\n";
														}
														
														//Add the comment count if this bot has comments
																												
														if($commentCount > 0){ //Display comment balloon with count in it
														?>
															
																<div class="comment_balloon">
																	<img src="images/comment.png">
																	<div class="comment_count" title="This item has <?php echo $commentCount ." comment"; echo $commentCount > 1 ? "s" : ""; ?>"><?php echo $commentCount; ?></div>
																</div>
															
														<?php
															
														}
														
													echo "</td>\n";
													
													echo "<td class=\"accordion_header_right\">\n"; //Create right DIV
														
														if($_GET["list"] == 2){
															echo "<img title=\"Move to Collection! 1-UP!\" src=\"images/1up.png\" onclick=\"toCollection('".$bot->id."','".$bot->name."');\"> ";
														}
														$oppositeOwner = $bot->owner == 1 ? 2 : 1;
														echo "<img title=\"Copy to ".$db->get_var("SELECT name FROM tfdb_users WHERE id = ".$oppositeOwner)."'s Wishlist!\" src=\"images/heart.png\" onclick=\"toWishlist('".$bot->id."','".$bot->name."');\"> ";
														echo "<img title=\"Edit This Bot\" src=\"images/edit.png\" onclick=\"editBot(".$bot->id.");\"> ";
														echo "<img title=\"Delete This Bot\" src=\"images/delete.png?y=34\" onclick=\"confirmDelete('".$bot->name." (". getVar("tfdb_series", "abbreviation", $bot->series) .")',".$bot->id.");\"> ";
														if(!isset($_GET["bot"])){//Don't show the direct link button if we are already at a direct link!
															echo "<img title=\"Get Direct Link To This Bot\" src=\"images/link.png\" onclick=\"location.href='view_bots.php?bot=".$bot->id."&user=".$_GET["user"]."&list=".$_GET["list"]."';\"> ";
														}
														
													echo "</td>\n";
												echo "</tr>\n";
											echo "</table>\n";
										
										echo "</h3>\n";
										
										//Bot HTML
										//Exiting PHP mode to make HTML editing easier
										?>
										
										<div class="view_bot_container">
											<!--Print Bot Name-->
											<h2><?php echo $bot->name; ?></h2>
											
											<!--Print Combiner Message if this is a combiner-->
											<?php
												if($bot->is_combiner){
													echo "This bot is a Combiner. To see its members, scroll down.<br><br>\n";
												}
											?>
											
											
											<!--Print Thumbnail Image-->
											<div class="bot_thumbnail_image_div" id="bot_photos_div_<?php echo $bot->id; ?>">
												<?php
												//Unserialize the image array from the database
												$bot->image = unserialize($bot->image);
												$imgCount = count($bot->image);
												
												if($imgCount > 0){ 
													
															
														
																									
													?>	
													
															<a rel="lightbox-<?php echo $bot->id; ?>" title="<?php echo $bot->name; ?> (<?php echo getVar("tfdb_series", "abbreviation", $bot->series); ?>)" href="images/tf/<?php echo $bot->image[0]; ?>" target="_blank"><img class="image_thumb main_thumb" src="images/tf/thumbs/<?php echo $bot->image[0]; ?>"></a><br>
															
															<?php
															if($imgCount > 1){
																$imgsLeft = $imgCount-1;
																?>
																<br>
																<?php
																if($imgCount > 2){
																	echo "There are ". $imgsLeft ." more photos";
																}
																else{
																	echo "There is ". $imgsLeft ." more photo";
																}
																?>
																<br>															
																<div class="button" onclick="loadPhotos(<?php echo $bot->id; ?>)">Load All Photos</div>
																<?php
															}
															else{
																echo "There are no more photos";
															}
															?>
															
															
														
												
													<?php
												} //End if count bot image > 0
												else { //There is no image for this bot ?>
													<img src="images/image_preview_placeholder.png">																									
												<?php
												}
												?>
											</div>
											
											<!--Print Description-->
											<?php
												if(trim($bot->comments) != ""){ //If there is a description to print
													?>
													
													<div class="bot_description">
														<?php echo str_replace(array("\n"),"<br>",$bot->comments); ?>
													</div>
													
													<?php
												} //End if bot comments not = ""
											?>
											
											
											<!--Print Data Table-->
											<table class="bot_data_table">
												<tr class="bot_data_odd">
													<td class="bot_data_label">
														Owner
													</td>
													<td class="bot_data_text">
														<?php echo getVar("tfdb_users", "name", $bot->owner); ?>
													</td>
												</tr>
												
												<tr class="bot_data_even">
													<td class="bot_data_label">
														List
													</td>
													<td class="bot_data_text">
														<?php echo getVar("tfdb_lists", "name", $bot->list); ?>
													</td>
												</tr>
												
												<tr class="bot_data_odd">
													<td class="bot_data_label">
														Series
													</td>
													<td class="bot_data_text">
														<?php echo getVar("tfdb_series", "name", $bot->series);
														if(getVar("tfdb_series", "name", $bot->series) != getVar("tfdb_series", "abbreviation", $bot->series)){echo " (".getVar("tfdb_series", "abbreviation", $bot->series).")";} ?>
													</td>
												</tr>
												
												<tr class="bot_data_even">
													<td class="bot_data_label">
														Subgroup
													</td>
													<td class="bot_data_text">
														<?php echo $bot->subgroup > 0 ? getVar("tfdb_subgroups", "name", $bot->subgroup) : ""; ?>
													</td>
												</tr>
												
												<tr class="bot_data_odd">
													<td class="bot_data_label">
														Toy Size Class
													</td>
													<td class="bot_data_text">
														<?php echo $bot->class > 0 ? getVar("tfdb_classes", "name", $bot->class) : ""; ?>
													</td>
												</tr>
												
												<tr class="bot_data_even">
													<td class="bot_data_label">
														Faction
													</td>
													<td class="bot_data_text">
														<?php echo getVar("tfdb_factions", "name", $bot->faction); ?>
													</td>
												</tr>
												
												<tr class="bot_data_odd">
													<td class="bot_data_label">
														TranformerLand Link
													</td>
													<td class="bot_data_text bot_link">
														<a href="<?php echo $bot->tfl_link; ?>" target="_blank"><?php echo $bot->tfl_link; ?></a>
													</td>
												</tr>
												
																
												<tr class="bot_data_even">
													<td class="bot_data_label">
														Last Updated
													</td>
													<td class="bot_data_text">
														<?php echo date('m/j/y \a\t g:i A',strtotime($bot->date)); ?>
													</td>
												</tr>
											</table>
											
											<div class="comment_container"> <!--HOLDS ALL THE COMMENTS FOR THIS BOT-->
												<?php include("comments.php"); ?>
											</div>
											
											<!--If this is a Combiner. Print the members if there are any-->
											<?php
												if($bot->is_combiner){
													//See if there are any members to show
													$members = $db->get_results("SELECT * FROM tfdb_bots WHERE part_of_combiner = ".$bot->id);
													
													if($db->num_rows > 0){ //If there are members to show, create a nested accordion for them
													
														
														echo"<br><h2>Members of ".$bot->name."</h2>\n";
														
														echo "<div id=\"accordion".$combinerCounter."\">\n"; //Start nested accordion DIV
														
														//Increment combiner counter
														$combinerCounter++;
														
														foreach($members as $m){
															//Exit PHP Mode for easier HTML
															
															
																															
																echo "	<h3 class=\"accordion_header\">\n";
										
																//Create a table for left and right alignment in the header (tried it with DIVs, didn't work)
																//Name & icon will be on the left, action buttons on the right
																
																echo "<table class=\"accordion_header_table\">\n";
																	echo "<tr>\n";
																		echo "<td class=\"accordion_header_left\">\n"; //Create left DIV
																			
																			
																			//Print the bot's name and series
																			echo $m->name."\n";
																			
																																	
																		echo "</td>\n";
																		
																		echo "<td class=\"accordion_header_right\">\n"; //Create right DIV
																			
																			echo "<img title=\"Edit This Bot\" src=\"images/edit_small.png\" onclick=\"editBot(".$m->id.");\"> ";
																			echo "<img title=\"Delete This Bot\" src=\"images/delete_small.png\" onclick=\"confirmDelete('".$m->name." (". getVar("tfdb_series", "abbreviation", $m->series) .")',".$m->id.");\"> ";
																			
																		echo "</td>\n";
																	echo "</tr>\n";
																echo "</table>\n";
															
															echo "</h3>\n";
																					
																	?>				
																
																
																<div>
																	
																	<!--Print Thumbnail Image-->
																	<div class="bot_thumbnail_image_div">
																		<?php
																		//Unserialize the image array from the database
																		$m->image = unserialize($m->image);
																		
																		if(count($m->image) > 0){ 
																			//Loop through the array and show each image. Make the first image larger than the others and on top of them
																			for($i = 0; $i < count($m->image); $i++){
																			
																				if($i == 0){
																					
																				
																															
																			?>	
																			
																					<a data-lightbox="image<?php echo $m->id; ?>" data-title="<?php echo $m->name; ?> (<?php echo getVar("tfdb_series", "abbreviation", $m->series); ?>)" href="images/tf/<?php echo $m->image[$i]; ?>" target="_blank"><img class="image_thumb main_thumb" src="images/tf/thumbs/<?php echo $m->image[$i]; ?>"></a><br>
																				
																		<?php
																				} //End if $i == 0
																				else{?>
																					<a data-lightbox="image<?php echo $m->id; ?>" data-title="<?php echo $m->name; ?> (<?php echo getVar("tfdb_series", "abbreviation", $m->series); ?>)" href="images/tf/<?php echo $m->image[$i]; ?>" target="_blank"><img class="image_thumb smaller_thumb" src="images/tf/thumbs/<?php echo $m->image[$i]; ?>"></a>
																				<?php
																					//Only show 5 thumbnails per line
																					if( $i % 5 == 0 ){
																						echo "<br>\n";
																					}
																				} //End else
																			} //End for loop
																		} //End if count bot image > 0
																		else { //There is no image for this bot ?>
																			<img src="images/image_preview_placeholder.png">																									
																		<?php
																		}
																		?>
																	</div>
																	
																	<!--Print Description-->
																	<?php
																		if(trim($m->comments) != ""){ //If there is a description to print
																			?>
																			
																			<div class="bot_description">
																				<?php echo str_replace(array("\n"),"<br>",$m->comments); ?>
																			</div>
																			
																			<?php
																		} //End if bot comments not = ""
																	?>
																	
																	
																	<!--Print Data Table-->
																	<table class="bot_data_table">
												<tr class="bot_data_odd">
													<td class="bot_data_label">
														Owner
													</td>
													<td class="bot_data_text">
														<?php echo getVar("tfdb_users", "name", $m->owner); ?>
													</td>
												</tr>
												
												<tr class="bot_data_even">
													<td class="bot_data_label">
														List
													</td>
													<td class="bot_data_text">
														<?php echo getVar("tfdb_lists", "name", $m->list); ?>
													</td>
												</tr>
												
												<tr class="bot_data_odd">
													<td class="bot_data_label">
														Series
													</td>
													<td class="bot_data_text">
														<?php echo getVar("tfdb_series", "name", $m->series). " (".getVar("tfdb_series", "abbreviation", $m->series).")"; ?>
													</td>
												</tr>
												
												<tr class="bot_data_even">
													<td class="bot_data_label">
														Subgroup
													</td>
													<td class="bot_data_text">
														<?php echo $m->subgroup > 0 ? getVar("tfdb_subgroups", "name", $m->subgroup) : ""; ?>
													</td>
												</tr>
												
												<tr class="bot_data_odd">
													<td class="bot_data_label">
														Toy Size Class
													</td>
													<td class="bot_data_text">
														<?php echo $m->class > 0 ? getVar("tfdb_classes", "name", $m->class) : ""; ?>
													</td>
												</tr>
												
												<tr class="bot_data_even">
													<td class="bot_data_label">
														Faction
													</td>
													<td class="bot_data_text">
														<?php echo getVar("tfdb_factions", "name", $m->faction); ?>
													</td>
												</tr>
												
												<tr class="bot_data_odd">
													<td class="bot_data_label">
														TranformerLand Link
													</td>
													<td class="bot_data_text bot_link">
														<a href="<?php echo $m->tfl_link; ?>" target="_blank"><?php echo $m->tfl_link; ?></a>
													</td>
												</tr>
												
																
												<tr class="bot_data_even">
													<td class="bot_data_label">
														Last Updated
													</td>
													<td class="bot_data_text">
														<?php echo date('m/j/y \a\t g:i A',strtotime($m->date)); ?>
													</td>
												</tr>
											</table>
											
											<div class="comment_container"> <!--HOLDS ALL THE COMMENTS FOR THIS BOT-->
												<?php include("comments.php"); ?>
											</div>
																	
																</div>
																
															
															
															<?php
														} //End foreach $members
														
														//Unset the $m variable so that we don't mess up the comments of bots after this
														unset($m);
														
														echo "</div>\n"; //End Nested Accordion DIV
													} //End if num_rows != 0
													else{ //There are no members added yet
														echo "<br><span class=\"bold\">There are no members added yet</span>";
													} //End else there are no members added yet
												}
											?>
											
										</div>
									
										<?php
									}
									
									//End Accordion DIV
									echo "</div>\n";
								}
							}
						}
						
					?>
					
					<div class="footer_text">Website Design, Graphics, and Code Created By Louis Chanady (2014)</div>
					
				</div>

		
			</div>
		
		</div>
		
	</div>
	
	<script type="text/javascript">
		//Comment AJAX scripting and functions
		
						//Cookie function from w3schools.com
						//Need to use cookies to fill in the name field of the comment form
						function getCookie(cname) {
						    var name = cname + "=";
						    var ca = document.cookie.split(';');
						    for(var i=0; i<ca.length; i++) {
						        var c = ca[i];
						        while (c.charAt(0)==' ') c = c.substring(1);
						        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
						    }
						    return "";
						}
						
						
						
						//Function to add a comment
						function addComment(id){
								//Get the comment text
								var text = encodeURIComponent($("#text_"+id).val());
								var who = $("select[name='user_"+id+"']").val();
								
								//Abort if user or comment are blank
								if(text == ""){
									alert("You didn't type anything in the comment box!");
									return;
								}
								if(who == 0){
									alert("You must choose a user for this comment!");
									return;
								}
								
								httpRequest = new XMLHttpRequest();
							
								httpRequest.onreadystatechange = function(){
									if (httpRequest.readyState === 4) {
								      if (httpRequest.status === 200) { //This runs after a response is received
								        //Redraw the comment section for this bot after adding the comment
								        $(".comment_holder_"+id).html(httpRequest.responseText);
								        
								        //If a user cookie was set, lets set all the rest of the user drop-down boxes to that user
								        //so that they won't have to do it for every comment.
								        //I'm just setting every <select> element on the page to this value since
								        //comment users are the only <select> elements being used on this page.
								        if(getCookie("user") != ""){
								        	$("select").val(getCookie("user"));
								        }
								        
								       								        
								        
								      } else {
								        alert('There was a problem with the request. Error Code: ' + httpRequest.status);
								      }
								    }
								};
	    						httpRequest.open('POST', "/comments.php?add=1&id="+id+"&r=" +  Math.random());
	    						httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    						httpRequest.send("who="+who+"&comment="+text);
						}
						
						//Create a global variable to show if we are editing so only 1 comment can be edited at a time
						var editing = 0;
						
						function editComment(id,bot){
							if(editing == 1){
								return; //Don't do anything if we are already editing a comment
							}
							editing = 1;
							var field = $("#comment_"+id);
							var text ="";
							//Get Actual Text from Database Without Emoticons
							httpRequest2 = new XMLHttpRequest();
							
								httpRequest2.onreadystatechange = function(){
									if (httpRequest2.readyState === 4) {
								      if (httpRequest2.status === 200) {
								        field.html("<textarea rows=\"4\" cols=\"55\" id=\"editing_"+id+"\">"+httpRequest2.responseText+"</textarea>");	
								        
										//Auto size textarea
										autosize($("#editing_"+id));
										
								        //Insert Save Button and emoticon button
										field.after("<input type='button' value='Save' id='save_edit'> <img src='images/emoticon.png' title='Add Emoticons! (Or you can use text emoticons and they will be automatically converted :D )' id='emoticon_image_add' onclick='openEmoticons(\""+id+"\",\"edit\");'>");



										
										var editBox = $("#editing_"+id);
										
										editBox.focus;
										
										//Bind Save Button
										$("#save_edit").on("click", function(){
											var newText = encodeURIComponent(editBox.val());
											
											httpRequest = new XMLHttpRequest();
										
											httpRequest.onreadystatechange = function(){
												if (httpRequest.readyState === 4) {
											      if (httpRequest.status === 200) {  //This runs after the response is received
											        //Redraw the comment section for this bot after editing the comment
											        $(".comment_holder_"+bot).html(httpRequest.responseText);
											        
											        
											        //Remove the Save Button
											        $("#save_edit").remove();
											         											        
											        //Reset the editing flag so another comment can be edited
											        editing = 0;
											        
											       
											      } else {
											        alert('There was a problem with the request. Error Code: ' + httpRequest.status);
											      }
											    }
											};
				    						httpRequest.open('POST', "/comments.php?edit=1&id="+bot+"&r=" +  Math.random());
				    						httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				    						httpRequest.send("id="+id+"&comment="+newText);
										});
														        
								      } else {
								        alert('There was a problem with the request. Error Code: ' + httpRequest2.status);
								      }
								    }
								};
	    						httpRequest2.open('GET', "/get_comment.php?id="+id+"&r=" +  Math.random());
	    						httpRequest2.send();	
							
						}
						
						//Some global variables for emoticons since they will be used in 2 different functions
						var emotType = "";
						var emotID = "";
						
						//Opens the Emoticon Dialog
						function openEmoticons(id,type){
							//Set where we are using the emoticons
							emotType = type;
							
							//Set which ID this is. If emotType is "add" we will assume this ID is a BOT ID.
							//If emotType is "edit" we will assume this ID is a COMMENT ID
							emotID = id;
							
							//Show the translucent background
							$("#choose_emoticon_background").fadeIn(500);
							
							//Show the emoticons DIV
							$("#choose_emoticon").fadeIn(500);
							
							//Set the event of the "I'm Done" button
							$("#close_emoticons_button").on("click",function(){
								//Hide the translucent background
								$("#choose_emoticon_background").fadeOut(500);
							
								//Hide the emoticons DIV
								$("#choose_emoticon").fadeOut(500);
							});
							
						}
						
						//Function that inserts the emoticons into the text box
						function insertEmoticon(code){
							//code is the actual code that is inserted into the textbox
							
							//Now we perform different actions depending on whether we are adding or editing
							if(emotType == "add"){
								$("#text_"+emotID).insertAtCaret(code);
							}
							else{ //We must be editing
								$("#editing_"+emotID).insertAtCaret(code);
							}
						}
						
						//Shows or removes thumbnails in the list when the checkbox is changed
						$("#thumbnail_toggle_checkbox").on("change",function(){
							if(!$("#thumbnail_toggle_checkbox").is(":checked")){ //If box isn't checked, remove thumbnails
								$(".view_bots_list_thumb").hide(1000);
								$("#thumb_size_div").hide(1000);
							}
							else{
								$(".view_bots_list_thumb").show(1000);
								$("#thumb_size_div").show(1000);
							}
						});
						
						//Handle changing list thumbnail size
												
						//Create Thumbnail Size Slider
						
						function refreshThumbs() {
							var width = $( "#thumbnail_size_slider" ).slider( "value" );
							
							$(".view_bots_list_thumb").width(width);
						}
						 
						 $(function() {
							$( "#thumbnail_size_slider" ).slider({
							orientation: "horizontal",
							range: "min",
							max: 150,
							min: 40,
							value: 80,
							slide: refreshThumbs							
							});
							
						});
						
						//Submit Bot Search
						function searchBots(){
							var text = $("#search").val();
							
							//Make sure no illegal characters exist.
							//We only want letters,numbers,spaces, or dashes
							if(/^[a-zA-Z0-9- ]*$/.test(text) == false) {
    							alert("Your Search Contains Illegal Characters!\n\nPlease only use Letters, Numbers, Spaces, and Dashes in your search terms!");
    							return;
							}
							
							text = encodeURIComponent(text);
							
							location.href="view_bots.php?user=<?php echo $_GET["user"]; ?>&list=<?php echo $_GET["list"]; ?>&search=" + text;
						}
						
						//Move Wishlist entry to Collection
						function toCollection(id,name){
							//Make sure
							var sure = confirm("Are you sure you want to add "+name+" to your Collection?")
							
							if(sure == true){
								//Send AJAX request with data
								var add = $.post("to_collection.php", { id: id });
								
								add.done(function(data){
									alert(name + " has been moved to your Collection! Refresh the page and it should be removed from your Wishlist.");
								});
							}
						}
						
						//Copy bot to wishlist
						function toWishlist(id,name){
							//Make sure
							var sure = confirm("Are you sure you want to copy "+name+" to your Wishlist?")
							
							if(sure == true){							
								//Send AJAX request with data
								var add = $.post("move_to_wishlist.php", { id: id });
								
								add.done(function(data){
									alert(name + " has been copied to your Wishlist!");
								});
							}
						}
						
						//Validate Filter Form
						$("#filterForm").on("submit",function(){
						 	if($.trim($("#chooseDateFrom").val()) == "" && $.trim($("#chooseDateTo").val()) != ""){
						 		alert("You chose a TO date but not a FROM date! If you want to see only a single day, just pick the same date for both boxes.");
						 		return false;
						 	}
						 	if($.trim($("#chooseDateTo").val()) == "" && $.trim($("#chooseDateFrom").val()) != ""){
						 		alert("You chose a FROM date but not a TO date! If you want to see only a single day, just pick the same date for both boxes.");
						 		return false;
						 	}
						 	return true;
						 });
						 
						 
						 //Load all photos for a bot via AJAX
						 function loadPhotos(i){
							var div = $("#bot_photos_div_" + i);
							
							$.get("load_images.php",{id: i}).done(function(data){
								div.html(data); //Show all images
								jQuery("#bot_photos_div_" + i + " a").slimbox(); //Reset Slimbox on the new images
							});
						 }
		
	</script>
	
	<!--CREATE THE CHOOSE EMOTICON DIV WHICH WILL BE HIDDEN UNTIL NEEDED-->
	<div id="choose_emoticon">
		<?php
			foreach($emoticonArray as $e){
				?>
				<img title="<?php echo $e[1] ?>" src="images/emoticons/<?php echo $e[1] ?>.png" onclick="insertEmoticon('<?php echo $e[0][0] ?>')">
				<?php
			}
		?>
		<br>
		<input type="button" id="close_emoticons_button" value="I'm Done">
	</div>
	
	<div id="choose_emoticon_background">$nbsp;</div>
	
	
	
<?php include("common/footer.php"); ?>

</body>
</html>
