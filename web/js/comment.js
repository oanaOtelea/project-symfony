var reviewObject = {

    init : function() {
            this.initEvents();

            $('body').css('overflow-y', 'hidden');
        },

    initEvents : function() {
        $('#nextButton').on('click', function(e) {
        
            var loc = window.location.href;
            var array = [];
            var urlSlider = loc.split('='); 
            var start = urlSlider[1];
            $('.image').each(function() {
                array.push($(this).attr('id'));
            });
           
            var next = array[($.inArray(start, array) + 1) % array.length]; 
            var newUrlSlider = urlSlider[0] + '=' + next;
            window.history.pushState( "object or string", "Title", newUrlSlider );
            $('body').load(newUrlSlider);
        });

        $('#prevButton').on('click', function(e) {
            var loc = window.location.href;
            var array = [];
            var urlSlider = loc.split('='); 
            var start = urlSlider[1];
            $('.image').each(function() {
                array.push($(this).attr('id'));
            });
           
            var prev = array[($.inArray(start, array) - 1 + array.length) % array.length]; 
            var newUrlSlider = urlSlider[0] + '=' + prev;
            window.history.pushState( "object or string", "Title", newUrlSlider );
            $('body').load(newUrlSlider);
        });

        $('#closeButton').on('click', function() {
            window.location.href = newUrl;
        });

        $('#numberOfLikes').mouseenter(function() {
            $('#listWithNames').show();
        });

        $('#numberOfLikes').mouseleave(function() {
            $('#listWithNames').hide();
        });

        $("#comment_comment").keyup(function(event){
            if(event.keyCode == 13){
                var id = $('.picture').attr('id');
                var loc = window.location.href;
                
                var $form = $(this).closest('form');
                var $post = $form.attr('method');
                var $data = $form.serialize();

                var text = $('#comment_comment').val();

                 $.ajax({
                    dataType: "json",
                    type: $post,
                    url: loc,
                    data: $data,
                    success: function (data, textStatus, jqXHR) {
                        if (data.success) {
                            $('.comment').last().after(data.template);
                            $('#comment_comment').val('');
                        } else {
                           console.log('not success');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log( errorThrown );
                    }
                    
                });

            }
        });

        $('#profile_picture_send').on('click', function(e) {
            e.preventDefault();
            
            var $form = $(this).closest('form');
            var $post = $form.attr('method');
            var $data = $form.serialize();
            var loc = window.location.href;
            console.log($data);
            $.ajax({
                dataType: "json",
                type: $post,
                url: loc,
                data: $data,
                success: function (data, textStatus, jqXHR) {
                    if (data.success) {
                        $('#profile_picture_send').html("Profile Picture");
                    } else {
                        console.log('not success');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log( errorThrown );
                }
                    
            });
        });

        $('#showMoreButton').on('click', function() {
       
        var loc = window.location.href;
        var lastId = $('.comment').first().attr('id');
        var NUMBER_OF_MESSAGES_TO_SHOW = 4;
        var x = $('#number').html();
        x -= NUMBER_OF_MESSAGES_TO_SHOW;
        if (x > 0) {
            $('#number').html(x);
        } else {
            $(this).addClass('hidden');
        }
        console.log(x);
        $.ajax({
                type: "POST",
                url: loc,
                data: {idd: lastId},
                success: function (data, textStatus, jqXHR) {
                        if (data.success) {
                           $('.comment').first().before(data.template);
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

    }

}

$( document ).ready(function() {
    reviewObject.init();
});