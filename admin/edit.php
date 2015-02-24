<!doctype html>
<html>
<head>
	<title><?php include("common/page_title.php"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<?php include("common/includes.php"); ?>

	
	
	<script type="text/javascript">
						 // On load, style typical form elements
		/*
		$(function () {
			$("select, input, button").uniform();
		});
		*/
	</script>
		
	<script type="text/javascript">
		//Preload Loading Screen Image
		$.preloadImages = function() {
		  for (var i = 0; i < arguments.length; i++) {
		    $("<img />").attr("src", arguments[i]);
		  }
		}
		
		$.preloadImages("../images/loading_screen.gif");
		
		 //Activate Tooltips
		 $(function() {
			$( document ).tooltip();
		 });
		 
		 //Validate Form
		 
						function validateForm(){
							//Only checking Dropdown boxes here since HTML5 takes care of the rest
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
		
					<div class="page_header bold">Edit A Transformer</div>
					
					<?php if(!isset($_GET['id'])){die("Sorry, you can only access this page after choosing a Transformer to edit</div></div></div></div></body></html>");}?>
					
					<?php
					if(isset($_GET["list"])){ //If we are editing, get the user/list that the user was editing so we can pass it on
						$queryString = "a=edit&list=".$_GET['list']."&user=".$_GET['user'];
					}
					else{
						$queryString = "a=edit";
					}
					?>
					
					<form enctype="multipart/form-data" action="process.php?<?php echo $queryString; ?>&id=<?php echo $_GET['id']; ?>" method="post" class="jqtransform centered" id="tfform" onsubmit="return validateForm();">
					
						  Name: <input size="80" value="<?php echo $db->get_var("SELECT name FROM tfdb_bots WHERE id = ".$_GET['id']);?>" type="text" id="name" name="name" required autofocus><br>
						  
						  Who Is This For?: <?php echo createDropdown("tfdb_users","name","owner",$db->get_var("SELECT owner FROM tfdb_bots WHERE id = ".$_GET['id'])); ?><br><br>
						  
						  Series: <?php echo createDropdown("tfdb_series","name","series",$db->get_var("SELECT series FROM tfdb_bots WHERE id = ".$_GET['id'])); ?><br><br>
						  
						  Subgroup (Optional): <?php echo createDropdown("tfdb_subgroups","name","subgroup",$db->get_var("SELECT subgroup FROM tfdb_bots WHERE id = ".$_GET['id'])); ?><br><br>
						  
						  <span title="Size Classes were only given to toys after Generation 2. Leave blank for G1 or G2.">Toy Size Class:</span> <?php echo createDropdown("tfdb_classes","name","class",$db->get_var("SELECT class FROM tfdb_bots WHERE id = ".$_GET['id'])); ?><br><br>
						  
						  Faction: <?php echo createDropdown("tfdb_factions","name","faction",$db->get_var("SELECT faction FROM tfdb_bots WHERE id = ".$_GET['id'])); ?><br><br>
						  
						  Which List Does This Go On?: <?php echo createDropdown("tfdb_lists","name", "list",$db->get_var("SELECT list FROM tfdb_bots WHERE id = ".$_GET['id'])); ?><br>
						  
						  <?php
						  	//Setup the checked text for the yes/no radio buttons below
						  	$yesChecked = "";
							$noChecked = "";
							if( getVar("tfdb_bots", "is_combiner", $_GET['id']) ){
								$yesChecked = " checked";
							}
							else{
								$noChecked = " checked";
							}
						  ?>
						  
						  <span title="Meaning is this the name of the bot AFTER it's combined. i.e. Devastator">Is this a Combiner?</span> &nbsp; &nbsp;<input type="radio" name="is_combiner" value="1"<?php echo $yesChecked; ?>> Yes &nbsp; &nbsp;<input type="radio" name="is_combiner" value="0"<?php echo $noChecked; ?>> No <br>
						  
						  <div id="choose_combiner">
						  <span title="NOTE: You must create the Combiner before you can add bots to it">Is this TF Part of a Combiner? If so, which one: </span>
						  
						  <?php
						  	//Create Combiner Drop-Down box
						  	$com = $db->get_results("SELECT * FROM tfdb_bots WHERE is_combiner = 1");
						  	
						  	if($db->num_rows > 0){ //If any combiners exist in the database
						  		echo "<select name=\"combiner\" id=\"combiner\">\n";
								
								echo "<option value=\"0\">None</option>\n";
								
								foreach($com as $c){
									$selectedText = "";	
									//See if this one should be selected
									if(getVar("tfdb_bots", "part_of_combiner", $_GET['id']) == $c->id){
										$selectedText = " selected";
									}	
										
									echo "<option value=\"".$c->id."\"".$selectedText.">".$c->name." (".getVar("tfdb_series", "abbreviation", $c->series).") (".getVar("tfdb_users", "name", $c->owner).")</option>\n";
								}
								
								echo "</select>\n";
						  	}
							else{ //No Combiners exist
								echo " There are no Combiners in the Database";
							}
						  ?>
						  </div> <!--End of choose_combiner DIV-->
						  <br>
						  
						  
						  <a href="http://www.transformerland.com" title="Go To TransformerLand!" target="_blank">TransformerLand</a> Collectors Guide Link (Optional): <input size="40" type="text" name="tfl_link" value="<?php echo $db->get_var("SELECT tfl_link FROM tfdb_bots WHERE id = ".$_GET['id']);?>"><br>	

						  <!--DON'T NEED ANYMORE  Photo Gallery Link (Optional) (For Louis): <input size="40" type="text" name="gallery_link" value="<?php echo $db->get_var("SELECT gallery_link FROM tfdb_bots WHERE id = ".$_GET['id']);?>"><br>-->
						  
						  
						  <label  title="This is the main desciption for this Transformer. You can tell about the condition, what accessories it has or are missing, or even just how or where you got it. Anything you want to say about it, put here. It's optional, but recommended." for="comments">Description (Optional):</label> <textarea id="comments" name="comments" rows="10" cols="50" maxlength="700"><?php echo $db->get_var("SELECT comments FROM tfdb_bots WHERE id = ".$_GET['id']);?></textarea><br>
						  <div id="commentCount">700 Characters Remaining</div>
						  
						  <br><br>
						  <div>
							  <h3>Add/Edit Images</h3>
							  If you don't wish to make any changes to the current TF's images,<br>just leave this part alone and the images will not be changed.<br><br>
							  Can be an actual picture of your Transformer or can be a stock image from the internet<br><br>
							  <span class="bold">Current Images:</span><br>
								<div id="edit_images_container">
							  <?php
								//This DIV will get populated via AJAX
							  ?>
							 </div>
							  <br><br><br>
							  <span class="bold">Upload Image(s) From Your Computer or from online URL<br>(at lease 800PX wide recommended)</span><br><br>
							  
							   <span class="bold" style="color:green;font-size:20px">Images must be less than 3MB in size!</span><br><br>
							  
							  <span class="bold">If you upload new images or give image URLs, what do you want to happen?</span><br>
							  
							  <div class="alignLeft">
							  	<input type="radio" name="upload_action" value="add" checked>I want to add them to the ones that are already there (if there are any)<br>
							  	<input type="radio" name="upload_action" value="replace" style="margin-top:5px;">I want them to replace the ones that are already there (<span style="color:red">all existing images will be deleted!</span>)
							  </div>
							  <br>
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
						
						<button type="submit">Save Changes!</button>
					
					</form>
					
					
					<div id="dialog-confirm" title="Delete This Image?" style="display:none;">
					<img class="center" id="confirm_delete_image" src="">
					</div>
					
					<div id="deleted-message" title="Action Completed" style="display:none;">
					The Image Has Been Deleted
					</div>
					
										
					<script type="text/javascript">
						
						
						//Handle the character count in the desription text area after something is typed/deleted
						$("#comments").keyup(function(){
						
							$("#commentCount").html(700 - $(this).val().length + " Characters Remaining");
						
						}
						);
						
						//Handle the character count of the description text area after document load
						$( document ).ready(function(){
						
							
								$("#commentCount").html(700 - $("#comments").val().length + " Characters Remaining");
						
						}
						
						);
						
						//Handle AJAX Image Processing
						
						//First, on document load, create the image HTML with an AJAX request
						$( document ).ready(function(){
						
							httpRequest = new XMLHttpRequest();
							
							httpRequest.onreadystatechange = showImages;
    						httpRequest.open('GET', "http://robotsindisguise.grintfarmsupply.com/admin/delete_image.php?id=<?php echo $_GET["id"]; ?>&r=" +  Math.random());
    						httpRequest.send();
    						
    						//Hide the combiner drop-down box if this is a combiner
							if($("input[name=is_combiner]:checked", "#tfform").val() == 1){
								$("#choose_combiner").hide();
							}
							
						
						}
						
						);
						
						//Function to display the current images for this bot via AJAX when the page first loads
						function showImages(){
							if (httpRequest.readyState === 4) {
						      if (httpRequest.status === 200) {
						        $("#edit_images_container").html(httpRequest.responseText);
						      } else {
						        alert('There was a problem with the request. Error Code: ' + httpRequest.status);
						      }
						    }
						}
						
						//Function that actually deletes the image
						function deleteImage(imgName){
							
							 	$("#confirm_delete_image").attr("src","../images/tf/thumbs/" + imgName);
							 	
							 	$( "#dialog-confirm" ).dialog({
								resizable: false,
								height:"auto",
								modal: true,
								buttons: {
								"Delete Image": function() {
									httpRequest = new XMLHttpRequest();
							
									httpRequest.onreadystatechange = deletedMessage;
		    						httpRequest.open('GET', "http://robotsindisguise.grintfarmsupply.com/admin/delete_image.php?id=<?php echo $_GET["id"]; ?>&delete=" + imgName + "&r=" +  Math.random());
		    						httpRequest.send();
		    						
		    						$( this ).dialog( "close" );
		    						
								},
								Cancel: function() {
									$( this ).dialog( "close" );
								}
								}
								});
						}
						
						//Function to show deleted message and re-show the images after deletion
						function deletedMessage(){
							if (httpRequest.readyState === 4) {
						      if (httpRequest.status === 200) {
						        $("#edit_images_container").html(httpRequest.responseText);
						         $( "#deleted-message" ).dialog({
									modal: true,
									buttons: {
									Ok: function() {
									$( this ).dialog( "close" );
									}
									}
									});
						      } else {
						        alert('There was a problem with the request. Error Code: ' + httpRequest.status);
						      }
						    }							
						}			
						
						
						//Makes sure a combiner can't be added as a member of itself or another combiner
						$("input[name=is_combiner]").change(function(){
						
							if($("input[name=is_combiner]:checked", "#tfform").val() == 1){
								$("#choose_combiner").hide(200);
							}
							if($("input[name=is_combiner]:checked", "#tfform").val() == 0){
								$("#choose_combiner").show(200);
							}
						
						}
						
						);
						
						//Handle setting the main image
						function makeMain(img){
							$("#edit_images_container").fadeOut(1);
							
							//Process the AJAX request
							httpRequest = new XMLHttpRequest();
							
							httpRequest.onreadystatechange = function(){
								$("#edit_images_container").html(httpRequest.responseText);
								$("#edit_images_container").fadeIn(2000);
							};
		    				httpRequest.open('GET', "http://robotsindisguise.grintfarmsupply.com/admin/delete_image.php?id=<?php echo $_GET["id"]; ?>&main=" + img + "&r=" +  Math.random());
		    				httpRequest.send();
						}
						
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
