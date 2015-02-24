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
				
		 $(function() {
			$( document ).tooltip();
		 });
		 
		 
		 
						
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
		
					<div class="page_header bold">Edit Transformer Attributes</div>
					
					<div id="attribute_form_container">
						
						<?php
						
						if(!isset($_GET["list"])){ ?>
							
							<h2>Choose an attribute to add or edit:</h2>
							
							<ul id="edit_attribute_list">
								<li><a href="edit_attributes.php?list=series">Series</a></li>
								<li><a href="edit_attributes.php?list=subgroups">Subgroups</a></li>
								<li><a href="edit_attributes.php?list=classes">Size Classes</a></li>
								<li><a href="edit_attributes.php?list=factions">Factions</a></li>
								<li><a href="edit_attributes.php?list=lists">Lists</a></li>
							</ul>
						
						<?php
						}
						else{ //The user has chosen a DB to edit, so display it
								
							
							
							$headerArray = array(
							"series" => "Series",
							"subgroups" => "Subgroups",
							"classes" => "Size Classes",
							"factions" => "Factions",
							"lists" => "Lists"
							);
							
							echo "<h2>Now Viewing/Editing " . $headerArray[$_GET["list"]] . "</h2>\n";
							
							//First get the info from the database
							$rows = $db->get_results("SELECT * FROM tfdb_".$_GET["list"], ARRAY_N);
							
							
							$columnCount = $db->get_var("
							SELECT COUNT(*)
							FROM INFORMATION_SCHEMA.COLUMNS
							WHERE table_schema = 'transformers'
  							AND table_name = 'tfdb_".
							$_GET["list"]."'");
							
							//Subtract 1 since we won't be displaying the id column
							$columnCount--;
							
							//Get column names since we don't know them
							$columns = $db->get_results("
							SELECT COLUMN_NAME
							FROM INFORMATION_SCHEMA.COLUMNS
							WHERE table_schema = 'transformers'
  							AND table_name = 'tfdb_".
							$_GET["list"]."'", ARRAY_N);
							
							//Print the table with some looping magic!
							
							?>
							<a href="edit_attributes.php">Edit a Different Attribute</a>
							<br>
							<br>
							<span style="line-height: 20px"><h3>Click on any field to edit it!</h3>Or Add A New One at the Bottom in the Blank Field(s).<br>Sorry, you can't delete any. If you need to delete one, ask Louis!<br></span>
							<br>
							<?php
							
								if($_GET["list"] == "lists"){
									?>
									<span class="bold" style="color:red;font-size:14px">
										You are editing lists! Please don't edit "Collection" or "Wishlist" as those are the main lists used.<br>
										Any lists you add here will be available to all users, but each user's items on that list will be kept separate.<br>
										For example, "Wishlist" is usable by both Cindy and Louis, but Louis's Wishlist items won't be mixed up with Cindy's.
									</span><br><br>
									<?php
								}
								
								if($_GET["list"] == "factions"){
									?>
									<span class="bold" style="color:red;font-size:14px">
										You can add Factions here, but just leave the IMAGE field blank.<br>
										That is just for the small icon next to the TFs in the listing.<br>
										Louis can add the image manually. If you have an image for it, just email it to him.
									</span><br><br>
									<?php
								}
								
								if($_GET["list"] == "subgroups"){
									?>
									<span class="bold" style="color:red;font-size:14px">
										Subgroups are groups of toys within a single toy series or generation.<br>
										G1 had a lot of Subgroups and so do many of the newer toy lines.<br>
										For example, the newer Generations toy line has a &quot;Thrilling 30&quot; group that celebrates Transformers&apos; 30th Anniversary.
									</span><br><br>
									<?php
								}
							
							?>
							<table id="edit_attributes_table">
								
								<?php
								//Echo the column names
								?>
								<tr>
									<?php
									$x = 0;
									//Create variable to hold which column is the sort column (if there is one)
									$sortCol = 0;
									foreach($columns as $column){
										//Skip the first one
										if($x == 0){$x++; continue;}
										//Skip the sort column
										if($column[0] == "sort"){$sortCol = $x; continue;}	
										?>
										<td class="attribute_header"><?php echo strtoupper($column[0]); ?></td>
										<?php
										$x++;
									}
									?>
								</tr>
								<?php
								
								$c = 1;
								foreach($rows as $row){
									
									?>
									
									<tr>
										<?php										
										for($i = 1; $i <= $columnCount; $i++){
											//Skip this column if this is the sort column
											if($i == $columnCount && $_GET["list"] != "lists"){continue;}
												
											//Order of numbers below are id_column-number
											$fieldID = $row[0]."_".$i;
											?>
											
											<td id="<?php echo $fieldID; ?>" onclick="editMode('<?php echo $fieldID; ?>');"><?php echo $row[$i]."\n"; ?></td>
											
											<?php											
										}
										?>
										<td class="right_cell">&nbsp;</td>
									</tr>
									
									<?php
									$c++;
								}

								//Now create the ADD row allowing you to add a new entry					

								?>
								<tr>
									<form action="process_attribute.php" method="post">
										<?php
											$x = 0;
											foreach($columns as $column){
												//Skip the first one
												if($x == 0){$x++; continue;}	
												//Skip the sort column
												if($x == $sortCol){continue;}
												?>
												<td><input type="text" name="<?php echo $column[0]; ?>" size="35"></td>
												<?php
												$x++;
											}
										?>
										<td class="right_cell"><input type="submit" value="Add Entry"></td>
										<input type="hidden" name="list" value="<?php echo $_GET["list"]; ?>">
										<input type="hidden" name="add" value="1">
									</form>
								</tr>
								
							</table><br>
							<input style="display:none" type="button" id="action_button" value="Click To Save!">
							<br>
							<br>
							<div id="attribute_saved">
								Your Changes Have Been Saved!!
							</div>
							<br><br>
							<a href="sort.php?what=attributes&list=<?php echo $_GET["list"]; ?>">SORT THIS LIST</a>
							
							<?php							
							
							//Add required fields when adding entries
							if($_GET["list"] == "series" || $_GET["list"] == "subgroups" || $_GET["list"] == "factions" || $_GET["list"] == "classes" || $_GET["list"] == "lists"){
								?>
								
								<script type="text/javascript">
									$("input[name='name']").attr("required","required");
								</script>
								
								<?php
							}
							
							if($_GET["list"] == "series"){
								?>
								
								<script type="text/javascript">
									$("input[name='abbreviation']").attr("required","required");
								</script>
								
								<?php
							}
							
							
							
							
							
						}
						
						?>
						
					</div>
					
										
					<script type="text/javascript">
						var editing = 0;
						
						//Change a field to edit mode
						function editMode(id){
							if(editing == 1){return;} //Don't do anything if there is currently a field being edited
							editing = 1;
							var field = $("#" + id);
							
							//remove onclick attribute because it causes problems
							field.attr("onclick","");
							
							//Show the save button
							$("#action_button").on("click",function(){
								var content = $("#currentEdit").val();
								//Make sure the field isn't empty
								if(content == ""){
									alert("You must enter a value!");
									$("#currentEdit").focus();
									return;
								}
								
								
								field.empty();
								field.html(content);
								
								//Do the actual saving
								httpRequest = new XMLHttpRequest();
							
								httpRequest.onreadystatechange = function(){
									if (httpRequest.readyState === 4) {
								      if (httpRequest.status === 200) {
								        if(httpRequest.responseText == "OK"){
									        $("#attribute_saved").fadeIn(1000);
									        setTimeout(function(){$("#attribute_saved").fadeOut(1000);}, 2000)
								        }
								        else{
								        	//alert("There was a problem processing the edit! Tell Louis!");
								        	alert(httpRequest.responseText);
								        }
								      } else {
								        alert('There was a problem with the request. Error Code: ' + httpRequest.status);
								      }
								    }
								};
	    						httpRequest.open('GET', "http://robotsindisguise.grintfarmsupply.com/admin/process_attribute.php?list=<?php echo $_GET["list"]; ?>&data="+content+"&id="+id+"&r=" +  Math.random());
	    						httpRequest.send();
								
								
								//Add onclick event
								field.attr("onclick","editMode('"+id+"')");
								editing = 0;
								
								//Remove Click Event
								$("#action_button").off("click");
								//Hide it
								$("#action_button").hide();
							});
							
							$("#action_button").show();
							
							
							var content = field.html();
							field.html("");
							field.append("<input id=\"currentEdit\" type=\"text\" value=\""+content+"\" size=\""+content.length+"\">");
							
							$("#currentEdit").focus();
						}
						
						
						
						
						
										
						
					</script>
		
				</div>
				
			</div>
		
		</div>
		
	</div>
<?php include("common/footer.php"); ?>
</body>
</html>
