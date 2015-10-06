    var loc = window.location.href;
    var url = loc.split('=');
    var idUrl = url[1];
   
    $('.messages').addClass('current');
    row = $('#' + idUrl);
   
    row.addClass('clicked');

    $('.conversationWithUser').on('click', function(e) {
        e.preventDefault();
        var id = $(this).attr('id');
        var newUrl = url[0] + '=' + id;
        window.history.pushState("object or string", "Title", newUrl);
        $('.conversationWithUser').not(this).removeClass('clicked');
        $(this).addClass('clicked'); 
       
        $.ajax({
            dataType: "json",
            type: "POST",
            url: loc,
            data: {idOfUser: id},
            success: function (data, textStatus, jqXHR) {
                if (data.success) {
                    console.log(data.numberOfMessagesRemainToShow);
                    if (data.numberOfMessagesRemainToShow > 0) {
                        $('#showMoreButton').removeClass('hidden');
                        $('#number').text(data.numberOfMessagesRemainToShow);
                        $('.allMessages').html(data.template);
                        $('#nameOfTheUser').text(data.nameOfUserInConversation);
                    } else {
                        $('#showMoreButton').addClass('hidden');
                        $('.allMessages').html(data.template);
                        $('#nameOfTheUser').text(data.nameOfUserInConversation); 
                    }
                } else {
                   console.log('not success', data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log( errorThrown );
            }
            
        });

    });
    

$('.message').keypress(function(e) {
        var key = e.which;
        var $form = $(this).closest('form');
        var $href = $form.attr('action');
        var $post = $form.attr('method');
        var $data = $form.serialize();
        var text = $('#message_text').val();

    if(key == 13)  // the enter key code
    {
        $.ajax({
            dataType: "json",
            type: $post,
            url: $href,
            data: $data,
            success: function (data, textStatus, jqXHR) {
                if (data.success) {
                    $('.box2').last().after(data.template);
                    $('#message_text').val('');
                    
                } else {
                   console.log('not success', data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log( errorThrown );
            }
            
        });
    }
});

$('#newMessage').on('click', function() {
    $('#container').show();
    $('#newMessagePopUp').show();
    $('#new_message_usernameMessageSend').val("");
    $('#new_message_text').val("");
});

$('#closeButtonOfPopUp').on('click', function() {
    $('#new_message_usernameMessageSend').val("");
    $('#new_message_text').val("");
    $('#container').hide();
    $('#newMessagePopUp').hide();
    $('.popUpWithSearchedUsersForNewMessage').hide();
});

$('#new_message_usernameMessageSend').keyup(function() {
        var receiver = $('#new_message_usernameMessageSend').val();
        console.log(receiver);

        $.ajax({
            dataType: "json",
            type: "POST",
            url: loc,
            data: {searched: receiver},
            success: function (data, textStatus, jqXHR) {
                    if (data.success) {
                        $('.popUpWithSearchedUsersForNewMessage').show();
                        $('.popUpWithSearchedUsersForNewMessage').html(data.template);
                    } else {
                        $('.popUpWithSearchedUsersForNewMessage').hide();
                        console.log('not success');
                    }
                },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.popUpWithSearchedUsersForNewMessage').hide();
                console.log( errorThrown );
            }
            
        });
    });

$('#new_message_save').on('click', function(e) {
        e.preventDefault();
        var $form = $(this).closest('form');
        var $href = $form.attr('action');
        var $post = $form.attr('method');
        var $data = $form.serialize();

        $.ajax({
            dataType: "json",
            type: $post,
            url: loc,
            data: $data,
            success: function (data, textStatus, jqXHR) {
                    if (data.success) {
                        var newUrl = url[0] + '=' + data.userReceiverId;
                        window.history.pushState("object or string", "Title", newUrl);
                        $('body').load(newUrl);
                        $('#container').hide();
                        $('#newMessagePopUp').hide();
                        $('.popUpWithSearchedUsersForNewMessage').hide();
                        console.log('success');
                    } else {
                        console.log('not success');
                    }
                },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log( errorThrown );
            }
            
        });
    });

$('#showMoreButton').on('click', function(e) {
    e.preventDefault();
    console.log('hello');
    var lastId = $('.box2').first().attr('id');
    var NUMBER_OF_MESSAGES_TO_SHOW = 4;
    var x = $('#number').html();
    x -= NUMBER_OF_MESSAGES_TO_SHOW;
    if (x > 0) {
        $('#number').html(x);
    } else {
        $(this).addClass('hidden');
    }
    console.log(lastId);
    $.ajax({
            type: "POST",
            url: loc,
            data: {idd: lastId},
            success: function (data, textStatus, jqXHR) {
                    if (data.success) {
                       $('.box2').first().before(data.message);
                    } else {

                        $('#showMoreButton').addClass('hidden');
                        console.log('not success');
                    }
                },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log( errorThrown );
            }
            
        });
});
