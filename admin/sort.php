<!doctype html>
<html>
<head>
	<title><?php include("common/page_title.php"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php include("common/includes.php"); ?>
	
	
	
	<script type="text/javascript">
						 // On load, style typical form elements
		$(function () {
			//$("select, input, button").uniform();
		});
	</script>
		
	<script type="text/javascript">
		//Create sortable list
		 $(function() {
			$( "#sortable" ).sortable();
			$( "#sortable" ).disableSelection();
		});
		
		//Shows or removes thumbnails in the list when the checkbox is changed
					$(document).ready(function(){	
						$("#thumbnail_toggle_checkbox").on("change",function(){
							if(!$("#thumbnail_toggle_checkbox").is(":checked")){ //If box isn't checked, remove thumbnails
								$(".sort_bots_image").hide(1000);
								$("#thumb_size_div").hide(1000);
							}
							else{
								$(".sort_bots_image").show(1000);
								$("#thumb_size_div").show(1000);
							}
						});
					});
		//Handle changing list thumbnail size
												
						//Create Thumbnail Size Slider
						
						function refreshThumbs() {
							var height = $( "#thumbnail_size_slider" ).slider( "value" );
							
							$(".sort_bots_image").height(height);
						}
						 
						 $(function() {
							$( "#thumbnail_size_slider" ).slider({
							orientation: "horizontal",
							range: "min",
							max: 100,
							min: 25,
							value: 60,
							slide: refreshThumbs							
							});
							
						});
	</script>
	
	<script src="../images/lightbox/js/lightbox.min.js"></script>

	<link href="../images/lightbox/css/lightbox.css" rel="stylesheet" />
</head>
<body>
	
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
		
					<div class="page_header bold">Sort</div>
					
					
					<?php
					//Display a list of choices of what to sort if they haven't chosen already
					
					if(!isset($_GET["what"])){
						?>
						<h3>What do you want to sort?</h3>
						<ul class="generated_link_list">
							<li><a href="sort.php?what=bots">Transformers</a></li>
							<li><a href="sort.php?what=attributes">Attributes</a></li>
						</ul>
						
						<?php
					}
					
					//Handle letting them choose a user/list they want to sort if they have chosen bots
					if(isset($_GET["what"]) && $_GET["what"] == "bots"){
						if(!isset($_GET["user"])){ //Print user list
							?>
							<h3>Who's Transformers do you want to sort?</h3>							
							<?php
							echo printLinkList("tfdb_users", "name", "sort.php?what=".$_GET["what"], "user");
						}
						elseif(!isset($_GET["list"])){ //Print Lists
							?>
							<h3>Which list do you want to sort?</h3>						
							<?php
							echo printLinkList("tfdb_lists", "name", "sort.php?what=".$_GET["what"]."&user=".$_GET["user"], "list");
						}
						else{ //Else everything is chosen, so start the sorting code!!
							$bots = $db->get_results("SELECT * FROM tfdb_bots WHERE owner = ".$_GET["user"]." AND list = ".$_GET["list"]." ORDER BY sort");
							
							//Hide my collection
							if($_GET["list"] == 1 && $_GET["user"] == 1 && !isset($_GET["admin"])){
								$db->num_rows = 0;
							}
							
							
							if($db->num_rows > 1){ //Only show the sorting code if there is more than 1 bot to sort
								?>
								<h3>Click and drag the bots to reorder them. Then click the Sort Button.</h3>
								<br>
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
															&nbsp;
														</td>
													</tr>
												</table>
											</div>
								
								
								<ul id="sortable">
								<?php
								//Create list items to be sortable
								foreach($bots as $bot){
									//Get the name of the first image of this bot if there is one
									$imgArr = unserialize($bot->image);
									if(count($imgArr) > 0){ //If there is at least one image for this bot
										$img = $imgArr[0];
									}
									else{
										$img = "image_preview_placeholder.png";
									}
									
									?>
									<li class="sort_bot" id="order_<?php echo $bot->id; ?>">
										<a data-lightbox="image<?php echo $bot->id; ?>" data-title="<?php echo $bot->name; ?> (<?php echo getVar("tfdb_series", "abbreviation", $bot->series); ?>)" href="../images/tf/<?php echo $img; ?>" target="_blank"><img class="sort_bots_image" src="../images/tf/thumbs/<?php echo $img; ?>"></a>
										<?php echo $bot->name." (". getVar("tfdb_series", "abbreviation", $bot->series) .")\n"; ?>
									</li>
									<?php								
								}
								
								?>
								</ul>
								<input type="button" value="Sort!" onclick="processSort('','bots');">
								<br><br>
								<div id="attribute_saved">Sort Order Saved Successfully!</div>
								<?php
							} //End if num rows > 1
							else{ //Else there are 1 or less bots in this list for this user. Display a "can't sort" message
								//Message to hide my collection from Cindy for now. Delete when Collection is ready!
								if($_GET["list"] == 1 && $_GET["user"] == 1){
									echo "<h2 style='color:red'>Nice Try!<br>But you can't see my collection here either yet! :P</h2>";
								}
								else{
								?>
								<h2>There aren't enough Transformers in this list to sort. Sorry!</h2>
								<?php
								}
							}
						}
					} //End if isset GET what and isset GET what is bots
					elseif(isset($_GET["what"]) && $_GET["what"] == "attributes"){
						if(!isset($_GET["list"])){
							?>
							
							<h3>Choose a List of Attributes You Want to Sort</h3>
							
							<ul id="edit_attribute_list">
								<li><a href="sort.php?what=attributes&list=series">Series</a></li>
								<li><a href="sort.php?what=attributes&list=subgroups">Subgroups</a></li>
								<li><a href="sort.php?what=attributes&list=classes">Size Classes</a></li>
								<li><a href="sort.php?what=attributes&list=factions">Factions</a></li>
							</ul>
							
							<?php
						}
						else{ //We know which list they want to sort, so let's do it!
							
							//Everything here says "bot" or "bots" because I copied it from the above section and was too lazy to edit it. Should probably say "items". :P
							
							$bots = $db->get_results("SELECT * FROM tfdb_".$_GET["list"]." ORDER BY sort");
							
							if($db->num_rows > 1){ //Only show the sorting code if there is more than 1 bot to sort
								?>
								<h3>Click and drag the items to reorder them. Then click the Sort Button.</h3>
								
								<ul id="sortable">
								<?php
								//Create list items to be sortable
								foreach($bots as $bot){
									//Get the name of the first image of this bot if there is one
									
									
									?>
									<li class="sort_bot" id="order_<?php echo $bot->id; ?>">
										<?php echo $bot->name; ?>
									</li>
									<?php								
								}
								
								?>
								</ul>
								<input type="button" value="Sort!" onclick="processSort('<?php echo $_GET["list"]; ?>','lists');">
								<br><br>
								<div id="attribute_saved">Sort Order Saved Successfully!</div>
								<?php
							} //End if num rows > 1
							else{ //Else there are 1 or less bots in this list for this user. Display a "can't sort" message
								?>
								<h2>There aren't enough items in this list to sort. Sorry!</h2>
								<?php
							}
						}
					}
					
					?>
					
					
					
					
										
					<script type="text/javascript">
							
						function processSort(list,what){
							//Get current sort order
							var sorted = $( "#sortable" ).sortable( "serialize");
							
							//If we are sorting a list, we need to add which list to the POST data
							if(what == "lists"){
								sorted += "&list="+list;
							}
							
							var posting = $.post( "process_sort.php?what="+what, sorted);
						    posting.done( function(data){
						      //Show the saved message for 2 seconds
						      $("#attribute_saved").fadeIn(1000);
						      setTimeout(function(){$("#attribute_saved").fadeOut(1000)},2000);
						      
						      
						    });
						      
						      
						   
						}
						
					</script>
		
				</div>
				
			</div>
		
		</div>
		
	</div>
<?php include("common/footer.php"); ?>
</body>
</html>
