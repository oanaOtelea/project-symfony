var $form = $(this).closest('form');
var loc = window.location.href;
		
	$('#upload_save').on('click', function(e) {
		e.preventDefault();

		var fd = new FormData(document.forms.namedItem("upload"));
        console.log(fd);
		$.ajax({
	    url: loc,
	    type: "POST",
	    data: fd,
	    processData: false,
    	contentType: false,
	     success: function (data, textStatus, jqXHR) {
                if (data.success) {
                  console.log(data, 'success');
                  location.reload();
                  $('#upload_file').val('');
                } else {
                   console.log('not success');
                   $('#upload_file').val('');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log( errorThrown );
            }
	  });

	});