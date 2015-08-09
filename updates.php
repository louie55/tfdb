
				
<?php
//Processes adding/editing/displaying updates.
//Will be called VIA AJAX

if(!function_exists("createDropdown")){ //Only include these if this is being called by AJAX

	require("common/connect_db.php");
		
	require("common/functions.php");
}


				
							
						
							
							
							//START ADDING SECTION------------------------------------------------------------
							if(isset($_GET["add"])){
																		
								$text = addslashes(htmlspecialchars($_POST["update"]));
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
								$db->query("INSERT INTO tfdb_updates (text,who,date) VALUES ('".$text."',".$who.",'".date("Y-m-d H:i:s",time()-3600)."')");
								
								
								//Send Louis a Message if Cindy Posts an Update
								
								if($who != 1 && strpos($_SERVER["SERVER_NAME"],"robotsindisguise") !== false){ //If Louis didn't post an update
									$to      = 'lchanady@gmail.com';
									$subject = 'Cindy Posted an Update!';
									$message = "Cindy has a new update!\n\nYou can see it by going to the following link:\n\nhttp://robotsindisguise.grintfarmsupply.com";
									$headers = 'From: Vector Sigma <vector_sigma@cybertrons-core.com>' . "\r\n" .
									    	    'X-Mailer: PHP/' . phpversion();
									
									mail($to, $subject, $message, $headers);
								}
								
								//Send Cindy a Message if Louis Posts an Update
								
								if($who != 2 && strpos($_SERVER["SERVER_NAME"],"robotsindisguise") !== false){ //If Cindy didn't post an update
									$to      = 'cynthiachanady5@gmail.com,lchanady@gmail';
									$subject = 'Louis Posted an Update!';
									$message = "Louis has a new update!\n\nYou can see it by going to the following link:\n\nhttp://robotsindisguise.grintfarmsupply.com";
									$headers = 'From: Vector Sigma <vector_sigma@cybertrons-core.com>' . "\r\n" .
									    	    'X-Mailer: PHP/' . phpversion();
									
									mail($to, $subject, $message, $headers);
								}

								
							}
							
							
							
							
							//START EDITING SECTION------------------------------------------------------------							
							if(isset($_GET["edit"])){
							
								$text = addslashes(htmlspecialchars($_POST["update"]));
								$updateID = $_POST["id"];
								
								
								//Edit comment in the database
								$db->query("UPDATE tfdb_updates SET text = '".$text."', date = '".date("Y-m-d H:i:s",time()-3600)."' WHERE id = ".$updateID);
							}
							
							
							//START COMMENT SECTION FOR UPDATE COMMENTS
							if(isset($_POST["comment"])){
								switch($_POST["comment"]){
									case "add":
										$commentText = addslashes(htmlspecialchars($_POST["text"]));
										
										$db->query("INSERT INTO tfdb_comments (type,comment,bot,user,date) VALUES ('update','". $commentText ."',".$_POST["id"].",".$_POST["user"].",'".date("Y-m-d H:i:s",time()-3600)."')");
										
										//Send Louis a Message if Cindy Posts a comment
								
										if($_POST["user"] != 1){ //If Louis didn't post a comment
											$to      = 'lchanady@gmail.com';
											$subject = 'Cindy Posted a Comment on an Update!';
											$message = "Cindy has a posted a comment on an update!\n\nThe comment was:\n\n".$commentText."\n\nYou can see it by going to the following link:\n\nhttp://robotsindisguise.grintfarmsupply.com\n\nNOTE: If the comment was on an older update, you may have to show more updates to see it.";
											$headers = 'From: Vector Sigma <vector_sigma@cybertrons-core.com>' . "\r\n" .
														'X-Mailer: PHP/' . phpversion();
											
											mail($to, $subject, $message, $headers);
										}
										
										//Send Cindy a Message if Louis Posts a comment
										
										if($_POST["user"] != 2){ //If Cindy didn't post a comment
											//$to      = 'cynthiachanady5@gmail.com';
											$subject = 'Louis Posted a Comment on an Update!';
											$message = "Louis has a posted a comment on an update!\n\nThe comment was:\n\n".$commentText."\n\nYou can see it by going to the following link:\n\nhttp://robotsindisguise.grintfarmsupply.com\n\nNOTE: If the comment was on an older update, you may have to show more updates to see it.";
											$headers = 'From: Vector Sigma <vector_sigma@cybertrons-core.com>' . "\r\n" .
														'X-Mailer: PHP/' . phpversion();
											
											//mail($to, $subject, $message, $headers);
										}
										
										//Set Session variable
										$_SESSION["user"] = $_POST["user"];
										//Set Cookie. Expires in 30 Days.
										?>
										
										<script type="text/javascript">
											var now = new Date();
											var time = now.getTime();
											time += 2592000;
											now.setTime(time);
											document.cookie = 'user=<?php echo $_POST["user"]; ?>; expires=' + now.toUTCString();
										</script>
										<?php
										
										break;
									
									case "edit":
										$commentText = addslashes(htmlspecialchars($_POST["text"]));
										
										$db->query("UPDATE tfdb_comments SET comment = '".$commentText."' WHERE id = ".$_POST["id"]);
										break;
								}
							}
							
							
							
							
							
							//START DISPLAYING SECTION----------------------------------------------------------
							
							//Display Add Comment Form
							
							//If the user session variable exists, it means this user has added a comment already
							//in this session and we want to remember who they are so they don't have to choose their
							//name every time they add a comment.
							$userCommentSelected = isset($_COOKIE["user"]) ? $_COOKIE["user"] : 0;
							
							?>
							<div class="comment_add_form">
								<h3>Add an Update</h3>
								Who Are You: <?php echo createDropdown("tfdb_users","name","user",$userCommentSelected); ?><br>
								<textarea class="comment_add_textarea" id="update_text"></textarea><br>
								<input type="button" value="Add Update" onclick="addUpdate()">
								<img src="images/emoticon.png" title="Add Emoticons! (Or you can use text emoticons and they will be automatically converted :D )" id="emoticon_image_add" onclick="openEmoticons('#update_text');">
							</div>
							<br>
							
							<?php
							
							//Control how many updates are shown
							if(isset($_GET["count"])){
								$shownCount = $_GET["count"];  //This means the "Show X More" button was pressed
							}
							elseif(isset($_POST["updateCount"])){
								$shownCount = $_POST["updateCount"]; //This means a comment was added or edited and we want to keep the same amount shown
							}
							else{
								$shownCount = 5; //Show only 5 updates by default
							}
							
							//Display the Updates. The reason everything says "comments" below is because I reused the comment code for updates and didn't feel like changing it :P
							$comments = $db->get_results("SELECT * FROM tfdb_updates ORDER BY id DESC LIMIT ".$shownCount);
							
							if($db->num_rows < 1){ //Then we have no comments. Display message.
								?>
								<div class="no_comments">There are no updates yet</div>
								<?php
							}
							else{ //There are comments! Display them!
								
								?>
								<table class="comment_table">
								<?php
								$y = 1;
								foreach($comments as $comment){
									?>
									<tr>
										<td class="comment_<?php if($y % 2 != 0){echo "odd";} else{echo "even";} ?>">
											<div class="comment_username"><?php echo getVar("tfdb_users", "name", $comment->who); ?></div>
											<div class="comment_date"><?php echo date('l, M j, Y - g:i A',strtotime($comment->date)); ?></div>
											<div class="comment_text" id="comment_<?php echo $comment->id; ?>"><?php echo insertEmoticons(str_replace(array("\n","\r"),"<br>",formatUrlsInText(stripslashes($comment->text)))); ?></div>
											<img title="Edit This Update" src="images/edit_small.png" class="comment_edit_button" onclick="editUpdate(<?php echo $comment->id ?>);">
											<br>
											<div class="updateComment">
												<span style="font-weight:bold;font-size:14px">Comments:</span>
												
												<?php
													//First see if there are any comments for this update
													$commentCount = $db->get_var("SELECT COUNT(*) FROM tfdb_comments WHERE bot = ".$comment->id." AND type = 'update'");
													
													if($commentCount < 1){
												?>
													<blockquote>No Comments Yet</blockquote>
												<?php
													} //End if
													else{
												?>
												<table>
													<?php
													
													$cmnts = $db->get_results("SELECT * FROM tfdb_comments WHERE type = 'update' AND bot = ".$comment->id." ORDER BY date ASC");
													
													$x = 1;
													foreach($cmnts as $c){ ?>
														<tr>
															<td class="update_comment_<?php if($x % 2 != 0){echo "odd";} else{echo "even";} ?>">
																<div class="updateCommentHeader"><?php echo getVar("tfdb_users", "name", $c->user); ?> - <span class="updatCommentDate"><?php echo date('l, M j, Y - g:i A',strtotime($c->date)-18000); ?></span></div>
																<div class="updateCommentDisplay" id="updateCommentDisplay<?php echo $c->id; ?>"><?php echo nl2br(insertEmoticons(stripslashes($c->comment))); ?></div>
																<img class="updateCommentEditButton" src="images/comment_edit.png" onclick="editUpdateComment(<?php echo $c->id; ?>)" title="Click to Edit this Comment">
															</td>
														</tr>
													<?php
													$x++;
													}
													
													
													
													?>
												</table>
												<?php
													} //End else
												?>
												
												<div class="updateCommentContainer">
													Add Comment:
													<textarea class="updateCommentAddText" id="updateCommentTextBox<?php echo $comment->id; ?>"></textarea><br>
													<div class="whoAreYouUpdateComment">Who Are You?: <?php echo createDropdown("tfdb_users","name","user_".$comment->id,$userCommentSelected); ?><img src="images/emoticon.png" title="Add Emoticons! (Or you can use text emoticons and they will be automatically converted :D )" id="emoticon_image_add_update_comments" onclick="openEmoticons('#updateCommentTextBox<?php echo $comment->id; ?>');"></div><br>
													<div class="addUpdateCommentButton" onclick="addUpdateComment(<?php echo $comment->id; ?>)">Add Comment</div>
												</div>
											</div>
										</td>
									</tr>
									<?php
								$y++;
								}
								
								?>
								</table>
								<?php
							
							}
							
							//Start code for displaying a link to show more updates
							$updateCount = $db->get_var("SELECT COUNT(*) FROM tfdb_updates"); //Get how many updates are in the database
							
							$getCount = $updateCount - $shownCount - 5 >= 0 ? 5 : $updateCount - $shownCount;
							
							if($updateCount - $shownCount > 0){
								?>
								<div class="moreUpdates">
									Currently Showing <?php echo $shownCount;?> out of <?php echo $updateCount;?> Updates<br>
									<input type="button" value="Show <?php echo $getCount;?> More" id="showUpdatesButton" onclick="getMoreUpdates(<?php echo $shownCount + $getCount;?>)">
								</div>
								<?php
							}
							else{
								?>
								<div class="moreUpdates">
									Currently Showing All Updates
								</div>
								<?php
							}
							?>
							
							
						