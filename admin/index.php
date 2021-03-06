<!doctype html>
<html>
<head>
	<title><?php include("common/page_title.php"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php include("common/includes.php"); ?>
		
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
		
					<div class="page_header bold">Louis's &amp; Cindy's Transformers Database Administration</div>
					
					<div id="admin_index_list_container">
						
						<!--CREATE LIST OF NAV ITEMS FOR THE ADMIN SECTION-->
						<ul id="admin_index_list">
							<li><a href="add.php">Add Transformer</a></li>
							<li><a href="../view_bots.php">Edit Transformer</a></li>
							<li><a href="add.php">Add/Edit Toy Series</a></li>
							<li><a href="add.php">Add/Edit Lists</a></li>
							<li><a href="add.php">Add/Edit Factions</a></li>
						</ul>
						
						<script type="text/javascript">
							
						</script>
						
					</div>
		
				</div>
				
			</div>
		
		</div>
		
	</div>
<?php include("common/footer.php"); ?>
</body>
</html>
