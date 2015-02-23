
				
<?php
//Processes adding/editing/displaying updates.
//Will be called VIA AJAX



if(isset($_GET["add"]) || isset($_GET["edit"])){
	require($_SERVER["DOCUMENT_ROOT"]."/database/common/connect_db.php");
		
	require($_SERVER["DOCUMENT_ROOT"]."/database/common/functions.php");
}


							
							
							
						//Start container DIV
						echo "<div class=\"updates_holder\">\n";
							
							
							//START ADDING SECTION------------------------------------------------------------
							if(isset($_GET["add"])){
																		
								$text = htmlspecialchars($_POST["update"]);
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
								
								if($who != 1){ //If Louis didn't post an update
									$to      = 'lchanady@gmail.com';
									$subject = 'Cindy Posted an Update!';
									$message = "Cindy has a new update!\n\nYou can see it by going to the following link:\n\nhttp://robotsindisguise.grintfarmsupply.com/database/";
									$headers = 'From: Vector Sigma <vector_sigma@cybertrons-core.com>' . "\r\n" .
									    	    'X-Mailer: PHP/' . phpversion();
									
									mail($to, $subject, $message, $headers);
								}
								
								//Send Cindy a Message if Louis Posts an Update
								
								if($who != 2){ //If Cindy didn't post an update
									$to      = 'cynthiachanady5@gmail.com';
									$subject = 'Louis Posted an Update!';
									$message = "Louis has a new update!\n\nYou can see it by going to the following link:\n\nhttp://robotsindisguise.grintfarmsupply.com/database/";
									$headers = 'From: Vector Sigma <vector_sigma@cybertrons-core.com>' . "\r\n" .
									    	    'X-Mailer: PHP/' . phpversion();
									
									mail($to, $subject, $message, $headers);
								}

								
							}
							
							
							
							
							//START EDITING SECTION------------------------------------------------------------							
							if(isset($_GET["edit"])){
							
								$text = htmlspecialchars($_POST["update"]);
								$updateID = $_POST["id"];
								
								
								//Edit comment in the database
								$db->query("UPDATE tfdb_updates SET text = '".$text."', date = '".date("Y-m-d H:i:s",time()-3600)."' WHERE id = ".$updateID);
							}
							
							
							
							
							//START DISPLAYING SECTION----------------------------------------------------------
							
							
							
							
							//Display the Updates. The reason everything says "comments" below is because I reused the comment code for updates and didn't feel like changing it :P
							$comments = $db->get_results("SELECT * FROM tfdb_updates ORDER BY id DESC");
							
							if($db->num_rows < 1){ //Then we have no comments. Display message.
								?>
								<div class="no_comments">There are no updates yet</div>
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
											<div class="comment_username"><?php echo getVar("tfdb_users", "name", $comment->who); ?></div>
											<div class="comment_date"><?php echo date('l, M j, Y - g:i A',strtotime($comment->date)); ?></div>
											<div class="comment_text" id="comment_<?php echo $comment->id; ?>"><?php echo insertEmoticons(str_replace(array("\n","\r"),"<br>",formatUrlsInText($comment->text))); ?></div>
											<img title="Edit This Update" src="images/edit_small.png" class="comment_edit_button" onclick="editUpdate(<?php echo $comment->id ?>);">
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
								<h3>Add an Update</h3>
								Who Are You: <?php echo createDropdown("tfdb_users","name","user",$userCommentSelected); ?><br>
								<textarea class="comment_add_textarea" id="update_text"></textarea><br>
								<input type="button" value="Add Update" onclick="addUpdate()">
								<img src="images/emoticon.png" title="Add Emoticons! (Or you can use text emoticons and they will be automatically converted :D )" id="emoticon_image_add" onclick="openEmoticons('','add');">
							</div>
						</div>
							<?php
							
		
								
?>