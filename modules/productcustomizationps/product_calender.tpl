<div id="calendar_preview">
	<div id="calender_navigation">
		<a id="prev_button" href='#' class="minibutton btn-download" style="float:left;">Previous Month</a>
		<a id="next_button" href='#' class="minibutton btn-download" style="float:right;">Next Month</a>
	</div>
	<br><br>
	<div id="active_content" class="loading">
		<br><br>
	</div>
	<br>
</div>

<script>
$(document).ready(function(){

    var start_month =  parseInt('{$month}');
    var start_year = '{$year}';

    var current_month = start_month;

    preview_calender('#active_content', start_month, start_year);

	$("#next_button").bind("click", function() { 
		$('#active_content').empty();

		current_month = current_month + 1

        if (current_month == 13){
            current_month = 1;
        } 
        
	    preview_calender("#active_content", current_month, start_year);
	});

	$("#prev_button").bind("click", function() { 
		$('#active_content').empty();

		current_month = current_month - 1

        if ( current_month <= 0){
            current_month = 12;
        } 

	    preview_calender("#active_content", current_month, start_year); 
	});

	// when the DOM is ready
	function preview_calender(element, month, year) {
	  var img = new Image();

	   $(element).addClass('loading');

	  	// wrap our new image in jQuery, then:
	  	$(img)

	    // once the image has loaded, execute this code
	    .load(function () {
	      // set the image hidden by default    
	      $(this).hide();
	    
	      // with the holding div #loader, apply:
	      $(element)
	        // remove the loading class (so no background spinner), 
	        .removeClass('loading')
	        // then insert our image
	        .append(this);
	    
	      // fade our image in to create a nice effect
	      $(this).fadeIn();
	    })
	    
	    
	    // if there was an error loading the image, react accordingly
	    .error(function () {
	      // notify the user that the image could not be loaded
	    })
	    
	    // *finally*, set the src attribute of the new image to our image
	    .attr('src',  '{$preview_url}?guid={$project_guid}&month=' + month + '&year=' + year);

	};

});
</script>