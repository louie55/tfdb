<!doctype html>
<html>
<head>
	<title><?php include("common/page_title.php"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php include("common/includes.php"); ?>
	
	<script>
		//Preload Loading Screen Image
		$.preloadImages = function() {
		  for (var i = 0; i < arguments.length; i++) {
		    $("<img />").attr("src", arguments[i]);
		  }
		}
		
		$.preloadImages("images/loading_screen.gif");
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
		
				<div id="content_holder" style="position:relative;"> <!--Contains Page Content-->
		
					<?php
					$firefox = strpos($_SERVER["HTTP_USER_AGENT"], 'Firefox') ? true : false;
					
					if($firefox){ //Only show logo flipper if we are in Firefox since that's the only place it works :(
						?>					
					<div class="flip-container">
						<div class="flipper">
							<div class="front" style="background: url('images/large_autobot.png') 0 0 no-repeat;">
								
							</div>
							<div class="back" style="background: url('images/large_decepticon.png') 0 0 no-repeat;">
								
							</div>
						</div>
					</div>
					<?php
					}
					else{ //If not Firefox, just show logo fader
						?> 	
										
					<div id="cycler" title="To See A Cooler Logo Flipping Animation, view this page in Firefox!">
						<img class="active" src="images/large_autobot.png">
						<img src="images/large_decepticon.png">				
					</div>
					
					<?php
					}
					?>
					
					<div id="home_text"<?php if(!$firefox){echo " style=\"margin-top:350px\"";} ?>>
						This is the official Transformers Database for Louis and Cindy!! It is a place to catalog, organize, and display Transformers toys and other items
						that they own or that they wish they owned. It can hold multiple lists for each person as well as allowing multiple images for each of those Transformers.
						Use the links at the top of the page to get started.
					</div>
					<br><br>
					<h1>Updates</h1>
					<span class="smallText">To create a new update, use the form below.</span><br>
					<br>
					<div class="comment_container"> <!--HOLDS ALL THE UPDATES-->
						<div class="updates_holder">
							<?php include("updates.php"); ?>
						</div>
					</div>
					
					<script type="text/javascript">
						
						//Updates AJAX scripting and functions
						//The reason everything says "Comments" or "Comment" instead of Updates is because I re-used the comment code for updates. It was faster that way.
		
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
						function addUpdate(){
								//Get the comment text
								var text = encodeURIComponent($("#update_text").val());
								var who = $("select[name='user']").val();
								
								//Abort if user or comment are blank
								if(text == ""){
									alert("You didn't type anything in the text box!");
									return;
								}
								if(who == 0){
									alert("You must choose a user for this update!");
									return;
								}
								
								httpRequest = new XMLHttpRequest();
							
								httpRequest.onreadystatechange = function(){
									if (httpRequest.readyState === 4) {
								      if (httpRequest.status === 200) { //This runs after a response is received
								        //Redraw the comment section for this bot after adding the comment
								        $(".updates_holder").html(httpRequest.responseText);
								        
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
	    						httpRequest.open('POST', "/updates.php?add=1&r=" +  Math.random());
	    						httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    						httpRequest.send("who="+who+"&update="+text);
						}
						
						//Create a global variable to show if we are editing so only 1 comment can be edited at a time
						var editing = 0;
						
						function editUpdate(id){
							if(editing == 1){
								return; //Don't do anything if we are already editing an comment
							}
							editing = 1;
							var field = $("#comment_"+id);
							var text ="";
							//Get Actual Text from Database Without Emoticons
							httpRequest2 = new XMLHttpRequest();
							
								httpRequest2.onreadystatechange = function(){
									if (httpRequest2.readyState === 4) {
								      if (httpRequest2.status === 200) {
								        field.html("<textarea rows=\"4\" cols=\"45\" id=\"editing_"+id+"\">"+httpRequest2.responseText+"</textarea>");	
								        
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
											        $(".updates_holder").html(httpRequest.responseText);
											        
											        
											        //Remove the Save Button
											        $("#save_edit").remove();
											         											        
											        //Reset the editing flag so another comment can be edited
											        editing = 0;
											        
											       
											      } else {
											        alert('There was a problem with the request. Error Code: ' + httpRequest.status);
											      }
											    }
											};
				    						httpRequest.open('POST', "/updates.php?edit=1&id="+id+"&r=" +  Math.random());
				    						httpRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				    						httpRequest.send("id="+id+"&update="+newText);
										});
														        
								      } else {
								        alert('There was a problem with the request. Error Code: ' + httpRequest2.status);
								      }
								    }
								};
	    						httpRequest2.open('GET', "/get_update.php?id="+id+"&r=" +  Math.random());
	    						httpRequest2.send();	
							
						}
						
						//This function will display more updates if the user wishes to display them
						function getMoreUpdates(c){
							$.get( "updates.php", {count:c, r: Math.random()}).done(function( data ) {
								$( ".updates_holder" ).html( data );
							});
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
								$("#update_text").val($("#update_text").val() + code);
							}
							else{ //We must be editing
								$("#editing_"+emotID).val($("#editing_"+emotID).val() + code);
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
							var height = $( "#thumbnail_size_slider" ).slider( "value" );
							
							$(".view_bots_list_thumb").height(height);
						}
						 
						 $(function() {
							$( "#thumbnail_size_slider" ).slider({
							orientation: "horizontal",
							range: "min",
							max: 100,
							min: 25,
							value: 50,
							slide: refreshThumbs							
							});
							
						});
						
						/*
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
							//Send AJAX request with data
							var add = $.post("to_collection.php", { id: id });
							
							add.done(function(data){
								alert(name + " has been moved to your Collection! Refresh the page and it should be removed from your Wishlist.");
							});
						}
						
						*/
						
						<?php
						if($firefox){ //Only show logo flipper if we are in Firefox since that's the only place it works :(
						?>	
						//Some TF Logo Flipping Magic					
												
						var c = 1;						
						function flipLogos(){
							
							if(c){
								$(".flipper").css("transform","rotateY(180deg)");
								c = 0;
							}
							else{
								$(".flipper").css("transform","rotateY(0deg)");
								c = 1;
							}
							
						}
						
						setInterval(function(){flipLogos();},2000);
						<?php
						}
						else{ //If not Firefox, just show logo fader
							?> 
						//Boring Crossfade for Non-Firefox Browsers
						function cycleImages(){
						      var $active = $('#cycler .active');
						      var $next = ($active.next().length > 0) ? $active.next() : $('#cycler img:first');
						      $next.css('z-index',2);//move the next image up the pile
						      $active.fadeOut(1500,function(){//fade out the top image
							  $active.css('z-index',1).show().removeClass('active');//reset the z-index and unhide the image
						          $next.css('z-index',3).addClass('active');//make the next image the top one
						      });
						    }
						
						$(document).ready(function(){
						// run every 7s
						setInterval('cycleImages()', 3000);
						});
						<?php
						}
						?>
						
						
						
						
						
					</script>
		
		
					<div class="footer_text">Website Design, Graphics, and Code Created By Louis Chanady (2014)</div>
				</div>

		
			</div>
		
		</div>
		
	</div>
	
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
