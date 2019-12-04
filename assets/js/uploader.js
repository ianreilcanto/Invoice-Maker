(function($){

	$(document).ready( function(){

		// Just to be sure that the input will be called
		$("#wpim_file_upload").on("click", function(){
            alert();
		  	$('#wpim_file_input').click(function(event) {
				event.stopPropagation();
      			});
    		});

		$('#wpim_file_input').on('change', prepareUpload);

		function prepareUpload(event) { 
            alert();
            var file = event.target.files;
              var parent = $("#" + event.target.id).parent();
              var data = new FormData();
              data.append("action", "wpim_file_upload");
              $.each(file, function(key, value)
                {
                      data.append("wpim_file_upload", value);
                });
        
                $.ajax({
                      url: wpimUploader.ajax_url,
                      type: 'POST',
                      data: data,
                      cache: false,
                      dataType: 'json',
                      processData: false, // Don't process the files
                      contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                      success: function(data, textStatus, jqXHR) {	
                   
                          if( data.response == "SUCCESS" ){
                                var preview = "";
                                if( data.type === "image/jpg" 
                                  || data.type === "image/png" 
                                  || data.type === "image/gif"
                                  || data.type === "image/jpeg"
                                ) {
                                  preview = "<img src='" + data.url + "' />";
                                } else {
                                  preview = data.filename;
                                }
                  
                                var previewID = parent.attr("id") + "_preview";
                                var previewParent = $("#"+previewID);
                                previewParent.show();
                                previewParent.children(".wpim_file_preview").empty().append( preview );
                                previewParent.children( "button" ).attr("data-fileurl",data.url );
                                parent.children("input").val("");
                                parent.hide();
                            
                             } else {
                             alert( data.error );
                             }
        
                }
        
            });
        
        }

    });

})(jQuery);