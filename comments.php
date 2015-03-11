
				
<?php
//Processes adding/editing/displaying comments.
//Will be called VIA AJAX



if(isset($_GET["id"])){
	require("common/connect_db.php");
		
	require("common/functions.php");
}


							//First get the id of the current bot from the query string
							//Or, if this is the initial load, we get it from the current bot in view_bots.php where this file is included
							if(isset($_GET["id"])){
								$id = $_GET["id"];
								$botName = $db->get_var("SELECT name FROM tfdb_bots WHERE id = ".$id);
							}
							elseif(isset($bot->id)){ //Handle Normal Bots
								$id = $bot->id;
								$botName = $bot->name;
							}
							
							
							if(isset($m->id)){ //Handle Combiner Members
								$id = $m->id;
								$botName = $m->name;
							}
							
						//Start container DIV
						echo "<div class=\"comment_holder_".$id."\">\n";
							
							
							//START ADDING SECTION------------------------------------------------------------
							if(isset($_GET["add"])){
																		
								$text = addslashes(htmlspecialchars($_POST["comment"]));
								$who = $_POST["who"];
								
								
								
								//Set Session variable
								$_SESSION["user"] = $who;
								
								//Set Cookie. Expires in 30 Days.
								?>
								
								<script type="text/javascript">
									var now = new Date();
									var time = now.getTime();
									time += 2592000;
									now.setTime(time);
									document.cookie = 'user=<?php echo $who ?>; expires=' + now.toUTCString();
								</script>
								
								<?php
								
								
								//Insert into database
								$db->query("INSERT INTO tfdb_comments (type,comment,bot,user,date) VALUES ('bot','".$text."',".$id.",".$who.",'".date("Y-m-d H:i:s",time()-3600)."')");
								
								
								//Send Louis a Message if Cindy comments
								
								if($who != 1){ //If Louis didn't comment
									$to      = 'lchanady@gmail.com';
									$subject = 'Cindy Commented on ' .$botName. '!';
									$message = "Cindy has left a comment on the following bot: ".$botName."\n\nYou can see it by going to the following link:\n\nhttp://robotsindisguise.grintfarmsupply.com/view_bots.php?bot=".$id."&user=".$_GET["user"]."&list=".$_GET["list"];
									$headers = 'From: Vector Sigma <vector_sigma@cybertrons-core.com>' . "\r\n" .
									    	    'X-Mailer: PHP/' . phpversion();
									
									mail($to, $subject, $message, $headers);
								}
								
								//Send Cindy a Message if Louis comments
								
								if($who != 2){ //If Cindy didn't comment
									//$to      = 'cynthiachanady5@gmail.com';
									$subject = 'Louis Commented on ' .$botName. '!';
									$message = "Louis has left a comment on the following bot: ".$botName."\n\nYou can see it by going to the following link:\n\nhttp://robotsindisguise.grintfarmsupply.com/view_bots.php?bot=".$id."&user=".$_GET["user"]."&list=".$_GET["list"];;
									$headers = 'From: Vector Sigma <vector_sigma@cybertrons-core.com>' . "\r\n" .
									    	    'X-Mailer: PHP/' . phpversion();
									
									//mail($to, $subject, $message, $headers);
								}

								
							}
							
							
							
							
							//START EDITING SECTION------------------------------------------------------------							
							if(isset($_GET["edit"])){
							
								$text = addslashes(htmlspecialchars($_POST["comment"]));
								$commentID = $_POST["id"];
								
								
								//Edit comment in the database
								$db->query("UPDATE tfdb_comments SET comment = '".$text."', date = '".date("Y-m-d H:i:s",time()-3600)."' WHERE id = ".$commentID);
							}
							
							
							
							
							//START DISPLAYING SECTION----------------------------------------------------------
							echo "<h2>Comments</h2>\n";
							
							
							
							//See if there are any comments for this bot
							$comments = $db->get_results("SELECT * FROM tfdb_comments WHERE bot = ".$id);
							
							if($db->num_rows < 1){ //Then we have no comments. Display message.
								?>
								<div class="no_comments">There are no comments for this Transformer yet</div>
								<?php
							}
							else{ //There are comments! Display them!
								
								
																
								
								?>
								<table class="comment_table">
								<?php
								$c = 1;
								foreach($comments as $comment){
									?>
									<tr>
										<td class="comment_<?php if($c % 2 != 0){echo "odd";} else{echo "even";} ?>">
											<div class="comment_username"><?php echo getVar("tfdb_users", "name", $comment->user); ?></div>
											<div class="comment_date"><?php echo date('l, M j, Y - g:i A',strtotime($comment->date)); ?></div>
											<div class="comment_text" id="comment_<?php echo $comment->id; ?>"><?php echo insertEmoticons(str_replace(array("\n","\r"),"<br>",formatUrlsInText(stripslashes($comment->comment)))); ?></div>
											<img title="Edit This Comment" src="images/edit_small.png" class="comment_edit_button" onclick="editComment(<?php echo $comment->id ?>,<?php echo $comment->bot ?>);">
										</td>
									</tr>
									<?php
								$c++;
								}
								
								?>
								</table>
								<?php
							
							}
							
							//Display Add Comment Form
							
							//If the user session variable exists, it means this user has added a comment already
							//in this session and we want to remember who they are so they don't have to choose their
							//name every time they add a comment.
							$userCommentSelected = isset($_COOKIE["user"]) ? $_COOKIE["user"] : 0;
							
							?>
							<div class="comment_add_form">
								<h3>Add a Comment</h3>
								Who Are You: <?php echo createDropdown("tfdb_users","name","user_".$id,$userCommentSelected); ?><br>
								<textarea class="comment_add_textarea" id="text_<?php echo $id; ?>"></textarea><br>
								<input type="button" value="Add Comment" onclick="addComment('<?php echo $id ?>')">
								<img src="images/emoticon.png" title="Add Emoticons! (Or you can use text emoticons and they will be automatically converted :D )" id="emoticon_image_add" onclick="openEmoticons('<?php echo $id ?>','add');">
							</div>
						</div>
							<?php
							
		
								
?>