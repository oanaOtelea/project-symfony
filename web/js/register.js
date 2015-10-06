$('#symfony_websitebundle_register_Register').on('click', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');
        var $href = $form.attr('action');
        var $post = $form.attr('method');
        var $data = $form.serialize();
        $('.error').remove();
        console.log($data);
        $.ajax({
            dataType: "json",
            type: $post,
            url: $href,
            data: $data,
            success: function (data, textStatus, jqXHR) {
                if (data.success) {
                    $("<p id='success' class='error'></p>").insertAfter("h1");
                    $('#success').html(data.message);
                } else {
                    console.log(data.message);
                    $("<p id='error1' class='error'></p>").insertAfter(".email");
                    $('#error1').html(data.message.emptyEmail);
                    $('#error1').html();

                    $("<p id='error2' class='error'></p>").insertAfter(".email");
                    $('#error2').html(data.message.emailNotValid);
                    $('#error2').val('');

                    $("<p id='error3' class='error'></p>").insertAfter(".username");
                    $('#error3').html(data.message.emptyUsername);

                    $("<p id='error4' class='error'></p>").insertAfter(".password");
                    $('#error4').html(data.message.emptyPassword);

                    $("<p id='error5' class='error'></p>").insertAfter(".password");
                    $('#error5').html(data.message.passwordMatch);

                    $("<p id='error6' class='error'></p>").insertAfter(".repeatPassword");
                    $('#error6').html(data.message.emptyRepeatedPassword);

                    $("<p id='error7' class='error'></p>").insertAfter("#symfony_websitebundle_register_agree");
                    $('#error7').html(data.message.agree);

                    $("<p id='error9' class='error'></p>").insertAfter(".lastname");
                    $('#error9').html(data.message.emptyLastname);

                    $("<p id='error10' class='error'></p>").insertAfter(".firstname");
                    $('#error10').html(data.message.emptyFirstname);

                    $("<p id='error8' class='error'></p>").insertBefore(".username");
                    $('#error8').html(data.message.user);

                    $('.main-col').css('height', '900px');
                   console.log(data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log( errorThrown );
            }
            
        });
    }); 