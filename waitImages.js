$(document).ready(function() {
	var total_images = $("#accordion img").length;
	var images_loaded = 0;
	var percent = 0;
	var maxValue = total_images - total_images * .02;

	//Add progress bar to loading screen
	$("#loader").append("<div id='progressbarcontainer'><span id='botsLoading'>CREATING UPLINK TO VECTOR SIGMA...</span><br><div id='progressBar'><div class='progress-label'>Loading...</div></div></div>");
	
	$( "#progressBar" ).progressbar({
		value: false,
		max: 100,
		change: function() {
		$( ".progress-label" ).text( $( "#progressBar" ).progressbar( "value" ) + "%" );
		},
		complete: function() {
			$( "#botsLoading" ).text( "DISPLAY NODES READY. BOOTING INTERFACE..." );
		}
	});
	
	$("#accordion").find('img').each(function() {
		imgAttr = $(this).attr("src");
		$("<img/>").attr("src",imgAttr).load(function() {
			images_loaded++;
			
			percent = Math.round(images_loaded / maxValue * 100);
			
			$( "#progressBar" ).progressbar( "value" , percent);
			
			//Update Message for Coolness effect!
			switch(true){
				case percent > 40 && percent < 75:
					$( "#botsLoading" ).text( "LINK COMPLETE. FETCHING DATA..." );
					break;
				case percent > 74 && percent < 90:
					$( "#botsLoading" ).text( "ALL BOT RECORDS RETRIEVED. TELETRAAN 1 DECRYPTING DATA..." );
					break;
				case percent > 89 && percent < 100:
					$( "#botsLoading" ).text( "ALL DATA LOADED. CONFIGURING DISPLAY NODES..." );	
			}
			
			
		});

	});

});
