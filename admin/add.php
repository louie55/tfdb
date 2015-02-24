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
		//Preload Loading Screen Image
		$.preloadImages = function() {
		  for (var i = 0; i < arguments.length; i++) {
		    $("<img />").attr("src", arguments[i]);
		  }
		}
		
		$.preloadImages("../images/loading_screen.gif");
		
		
		
		 $(function() {
			$( document ).tooltip();
		 });
		 
		 //Validate Form
		 
						function validateForm(){
							
							if($("#user").val() == 0){
								alert("You didn't choose a person for this Transformer!");
								return false;
							}
							if($("#series").val() == 0){
								alert("You didn't choose a series for this Transformer!");
								return false;
							}
							if($("#faction").val() == 0){
								alert("You didn't choose a faction for this Transformer!");
								return false;
							}
							if($("#list").val() == 0){
								alert("You didn't choose a list for this Transformer!");
								return false;
							}
							
							
						}
	</script>
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
		
					<div class="page_header bold">Add A Transformer</div>
					
					<form enctype="multipart/form-data" action="process.php?a=add" method="post" class="jqtransform centered" id="tfform" onsubmit="return validateForm();">
					
						  Name: <input size="70" type="text" id="name" name="name" required autofocus><br>
						  
						  <?php
						  //If the user cookie is set, choose that user so they don't have to choose themselves everytime
						  $userSelected = isset($_COOKIE["user"]) ? $_COOKIE["user"] : 0;
						  ?>
						  
						  
						  Who Is This For?: <?php echo createDropdown("tfdb_users","name","owner",$userSelected); ?><br><br>
						  
						  Series: <?php echo createDropdown("tfdb_series","name","series",0); ?><br><br>
						  
						  Subgroup (Optional): <?php echo createDropdown("tfdb_subgroups","name","subgroup",0); ?><br><br>
						  
						  <span title="Size Classes were only given to toys after Generation 2. Leave blank for G1 or G2.">Toy Size Class:</span> <?php echo createDropdown("tfdb_classes","name","class",0); ?><br><br>
						  
						  Faction: <?php echo createDropdown("tfdb_factions","name","faction",0); ?><br><br>
						  
						  Which List Does This Go On?: <?php echo createDropdown("tfdb_lists","name", "list",0); ?><br>
						  
						  <span title="Meaning is this the name of the bot AFTER it's combined. i.e. Devastator">Is this a Combiner?</span> &nbsp; &nbsp;<input type="radio" name="is_combiner" value="1"> Yes &nbsp; &nbsp;<input type="radio" name="is_combiner" value="0" checked> No <br>
						  
						  <div id="choose_combiner">
						  <span title="NOTE: You must create the Combiner before you can add bots to it">Is this TF Part of a Combiner? If so, which one: </span>
						  
						  <?php
						  	//Create Combiner Drop-Down box
						  	$com = $db->get_results("SELECT * FROM tfdb_bots WHERE is_combiner = 1");
						  	
						  	if($db->num_rows > 0){ //If any combiners exist in the database
						  		echo "<select name=\"combiner\" id=\"combiner\">\n";
								
								echo "<option value=\"0\">None</option>\n";
								
								foreach($com as $c){
									echo "<option value=\"".$c->id."\">".$c->name." (".getVar("tfdb_series", "abbreviation", $c->series).") (".getVar("tfdb_users", "name", $c->owner).")</option>\n";
								}
								
								echo "</select>\n";
						  	}
							else{ //No Combiners exist
								echo " There are no Combiners in the Database";
							}
						  ?>
						  </div> <!--End of choose_combiner DIV-->
						  <br>
						  
						  <a href="http://www.transformerland.com" title="Go To TransformerLand!" target="_blank">TransformerLand</a> Collectors Guide Link (Optional): <input size="40" type="text" name="tfl_link"><br>	

						  <!--DON'T NEED ANYMORE  Photo Gallery Link (Optional) (For Louis): <input size="40" type="text" name="gallery_link"><br> -->
						  
						  
						  <label title="This is the main desciption for this Transformer. You can tell about the condition, what accessories it has or are missing, or even just how or where you got it. Anything you want to say about it, put here. It's optional, but recommended." for="comments">Description (Optional):</label> <textarea id="comments" name="comments" rows="5" cols="50" maxlength="700"></textarea><br>
						  <div id="commentCount">700 Characters Remaining</div>
						  
						  <br><br>
						  <div>
							  <h3>Choose an Image (Optional)</h3>
							  Can be an actual picture of your Transformer or can be a stock image from the internet<br><br>
							  
							   <span class="bold">Upload Image(s) From Your Computer or from online URL<br>(at lease 800PX wide recommended)</span><br><br>
							   
							   <span class="bold" style="color:green;font-size:20px">Images must be less than 3MB in size!</span><br><br>
							 
							  
							  <span style="color:blue">NOTE: You may mix & match uploaded images and images from URLs. However, if one URL fails to load, then all images will fail.</span>
							  <br>
							  <br>
							  Click Browse to choose photos on your computer<br>(JPG Recommended. PNG and GIF will work too.):<br>
							  <input id="uploadImage" type="file" name="image[]" onchange="PreviewImage();" multiple><br>
							  
							  <span class="bold">Load Image(s) From Online URL(s) (provide direct link to the image)</span><br>(Images of at least 800PX wide recommended)<br>
							  If you want to enter more than 1 URL, click the button below to add as many boxes as you need.<br>
							  
							  <div id="image_urls">
							  	<input type="text" name="image_url[]" class="image_url">
							  </div>
							  <input type="button" value="Add Another Image URL Box" onclick="addUrlBox();">
						  </div>
						  <br><br>
						
						<button type="submit">Add Transformer!</button>
					
					</form>
					
										
					<script type="text/javascript">
						
						
						$("#comments").keyup(function(){
						
							$("#commentCount").html(700 - $(this).val().length + " Characters Remaining");
						
						}
						);
						
						$( document ).ready(function(){
						
							
								$("#commentCount").html(700 - $("#comments").val().length + " Characters Remaining");
						
						}
						
						);
						
						$("input[name=is_combiner]").change(function(){
						
							if($("input[name=is_combiner]:checked", "#tfform").val() == 1){
								$("#choose_combiner").hide(200);
							}
							if($("input[name=is_combiner]:checked", "#tfform").val() == 0){
								$("#choose_combiner").show(200);
							}
						
						}
						
						);

						//Adds a URL box to the form
						function addUrlBox(){
							var new_box = $("<input type='text' name='image_url[]' class='image_url'>");
							new_box.hide();
							$("#image_urls").append(new_box);
							new_box.fadeIn(1000);
						}
										
						
					</script>
		
				</div>
				
			</div>
		
		</div>
		
	</div>
<?php include("common/footer.php"); ?>
</body>
</html>
