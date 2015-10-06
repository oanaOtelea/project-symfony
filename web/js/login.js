$('#login').on('click', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');
        var $href = $form.attr('action');
        var $post = $form.attr('method');
        var $data = $form.serialize();

        $.ajax({
            dataType: "json",
            type: $post,
            url: $href,
            data: $data,
            success: function (data, textStatus, jqXHR) {
                if (data.success) {
                  window.location.href = "show";
                } else {
                    $('<p id="error"></p>').insertAfter('#signIn');
                   $('#error').html(data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log( errorThrown );
            }
            
        });
    });