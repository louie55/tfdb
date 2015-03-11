	<?php require("common/connect_db.php"); ?>
	
	<?php require("common/functions.php"); ?>
	
	<link rel="icon" href="/favicon.ico">
	<link href="style.css?<?php echo date('l jS \of F Y h:i:s A'); ?>" type="text/css" rel="stylesheet" />
	<link href="jquery-ui.css" type="text/css" rel="stylesheet" />
	<link href="jquery-ui.structure.css?<?php echo date('l jS \of F Y h:i:s A'); ?>" type="text/css" rel="stylesheet" />
	<link href="jquery-ui.theme.css" type="text/css" rel="stylesheet" />
	
	<script src="jquery-1.11.1.min.js"></script>
	
	<script src="jquery.clickout.js"></script>
	
	<script src="autosize.js"></script>
	
	<script src="jquery-ui.js"></script>
	
	<script>
		//Convert all "title" text into fancy tooltips
		$(function() {
			//$( document ).tooltip();
		});
		
		//Jquery Plugin for inserting emoticons at the current cursor position
		jQuery.fn.extend({
		insertAtCaret: function(myValue){
		  return this.each(function(i) {
		    if (document.selection) {
		      //For browsers like Internet Explorer
		      this.focus();
		      var sel = document.selection.createRange();
		      sel.text = myValue;
		      this.focus();
		    }
		    else if (this.selectionStart || this.selectionStart == '0') {
		      //For browsers like Firefox and Webkit based
		      var startPos = this.selectionStart;
		      var endPos = this.selectionEnd;
		      var scrollTop = this.scrollTop;
		      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
		      this.focus();
		      this.selectionStart = startPos + myValue.length;
		      this.selectionEnd = startPos + myValue.length;
		      this.scrollTop = scrollTop;
		    } else {
		      this.value += myValue;
		      this.focus();
		    }
		  });
		}
		});
	</script>