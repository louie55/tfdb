<!doctype html>
<html>
<head>
	<title><?php include($_SERVER["DOCUMENT_ROOT"]."/database/common/page_title.php"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php include($_SERVER["DOCUMENT_ROOT"]."/database/common/includes.php"); ?>
		
</head>
<body>
	
	<div id="container"> <!--Contains all content on the page-->
		
		<div id="header_container"> <!--Contains Header Image and Navbar Divs-->
		
			<div id="header_image"> <!--Contains Header Image-->
		
				<?php include($_SERVER["DOCUMENT_ROOT"]."/database/common/header_image.php"); ?>
		
			</div>
			
			<div id="navbar"> <!--Contains Navigation Bar-->
		
				<?php include($_SERVER["DOCUMENT_ROOT"]."/database/common/navbuttons.php"); ?>
		
			</div>
				
		
		</div>
		
		<div id="bottom_container"> <!--Contains Sidebar and Content Divs-->
		
			<div id="sidebar"> <!--Contains Sidebar-->
				
				<div class="sidebar_links"> <!--Contains Sidebar-->
					
					<?php include($_SERVER["DOCUMENT_ROOT"]."/database/common/sidebar_links.php"); ?>
					
				</div>
			
			</div>
			
			<div id="content"> <!--Contains Page Content-->
		
				<div id="content_holder"> <!--Contains Page Content-->
		
					<div class="page_header bold">Louis's &amp; Cindy's Transformers Database Administration</div>
					
					<div id="edit_message">
						
						The Cybertronian Hall of Records has been updated!
						
					</div>
					
					<?php if(isset($_GET["list"])){ ?>
						<h2><a href="../view_bots.php?user=<?php echo $_GET['user']; ?>&list=<?php echo $_GET['list']; ?>">Click Here</a> to go back to the Transformer List!</h2>
					<?php } ?>
				</div>
				
			</div>
		
		</div>
		
	</div>
<?php include($_SERVER["DOCUMENT_ROOT"]."/database/common/footer.php"); ?>
</body>
</html>
