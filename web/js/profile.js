$('#symfony_websitebundle_update_save').on('click', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');
        var $href = $form.attr('action');
        var $post = $form.attr('method');
        var $data = $form.serialize();
        var loc = window.location.href;
        $.ajax({
            dataType: "json",
            type: $post,
            url: loc,
            data: $data,
            success: function (data, textStatus, jqXHR) {
                if (data.success) {
                   $('#message').html(data.message);
                   $('.update').addClass('hidden');
                } else {
                   $('#message').html(data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log( errorThrown );
            }
            
        });
});

$('#buttonEdit').on('click', function() {
    $('.update').removeClass('hidden');
});

$('#cancel').on('click', function() {
    $('.update').addClass('hidden');
});