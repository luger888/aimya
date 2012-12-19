$(document).ready(function () {
    var baseUrl = $('#current_url').val();

    /*  FILTER SYSTEM   */
    $.urlParam = function (name) {
        var results = new RegExp('[\\?\&]' + name + '=([^\&#]*)').exec(window.location.href);//getting params from url
        if (results)
            return results[1] || 0;
    };
    var user = $.urlParam('user');
    var category = $.urlParam('category');
    $('#author').val(user);
    $('#lesson_category').val(category);
    $('#author, #lesson_category').change(function () {
        window.location.href = 'features?user=' + $('#author').val() + '&category=' + $('#lesson_category').val();//reload url after selecting category

    });
    /*  END -- FILTER SYSTEM    */

    $('.featuresTab .addAccount').click(function () {
        var id = $(this).nextAll('input[type=hidden]:first').val();//id of service

        $.post(

            baseUrl + "/friends/send/0/controller%3D%3Efriends/1/action%3D%3Esend", {

                "friend_id": id

            },

            function (response) {

                if(response.alertFlash){
                    $('.alertBlock .alert').remove();


                    $('.alertBlock').append('<div class="alert"><div class = "flash-warning">Warning!</div>'+response.alertFlash+'<button type="button" class="close" data-dismiss="alert"></button></div>');
                    $('html, body').animate({scrollTop:0}, 'fast');




                }else if(response.successFlash){
                    $('.alertBlock .alert').remove();
                    $('.modal').css('visibility', 'visible');
                    $('.alertBlock').append('<div class="success alert"><div class = "flash-success">Success!</div>'+response.successFlash+'<button type="button" class="close" data-dismiss="alert"></button></div>');
                    $('.modal').fadeIn(1000).delay(800).fadeOut(1000);
                }

            })


    });

    jQuery.fn.shorten = function(settings) {
        var config = {
            showChars : 100,
            ellipsesText : "...",
            moreText : "more",
            lessText : "less"
        };

        if (settings) {
            $.extend(config, settings);
        }

        $('.morelink').live('click', function() {
            var $this = $(this);
            if ($this.hasClass('less')) {
                $this.removeClass('less');
                $this.html(config.moreText);
            } else {
                $this.addClass('less');
                $this.html(config.lessText);
            }
            $this.parent().prev().toggle();
            $this.prev().toggle();
            return false;
        });

        return this.each(function() {
            var $this = $(this);

            var content = $this.html();
            if (content.length > config.showChars) {
                var c = content.substr(0, config.showChars);
                var h = content.substr(config.showChars , content.length - config.showChars);
                var html = c + '<span class="moreellipses">' + config.ellipsesText + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="javascript:void(0)" class="morelink">' + config.moreText + '</a></span>';
                $this.html(html);
                $(".morecontent span").hide();
            }
        });
    };

    $('.showMore').click(function () {
        var baseUrl = $('#current_url').val();
        var elementCount = $('.shadowSeparator').length;
        jQuery("body").append('<div class="loadingIcon"></div>');



        $.ajax({
            url: baseUrl +"/account/features",
            type: "post",
            data: {
                'offset': elementCount,
                'count': 5

            },
            success: function(result) {
                var viewMorButton = $('.feauteresButtons');
                $('.feauteresButtons').remove();
                $('.messageContent').append(result.featuredHtml);
                $('.messageContent').append(viewMorButton);
                jQuery('.loadingIcon').remove();
            }
        });

    });

});