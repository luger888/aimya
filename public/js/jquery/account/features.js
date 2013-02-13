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

    /*$('.showMore').click(function () {
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
                totalCount = $('.shadowSeparator').length + viewMorButton.children('.shadowSeparator').length;
                if(totalCount != $('#featured_count').val() ) {
                    $('.messageContent').append(viewMorButton);
                }
                jQuery('.loadingIcon').remove();
            }
        });

    });*/

});

function showMoreUsers (){
    var baseUrl = $('#current_url').val();
    var elementCount = $('.shadowSeparator').length;
    jQuery("body").append('<div class="loadingIcon"></div>');



    var postData = {
        'offset': elementCount,
        'count': 5
    };
    if($.getUrlVar('category')) {
        postData['category'] = $.getUrlVar('category');
    }
    if($.getUrlVar('user')) {
        postData['user'] = $.getUrlVar('user');
    }

    $.ajax({
        url: baseUrl +"/account/features",
        type: "post",
        data: postData,
        success: function(result) {
            var viewMorButton = $('.feauteresButtons');
            $('.feauteresButtons').remove();
            $('.messageContent').append(result.featuredHtml);
            totalCount = $('.shadowSeparator').length + viewMorButton.children('.shadowSeparator').length;
            if(totalCount < $('#featured_count').val() ) {
                $('.messageContent').append(viewMorButton);
            }
            jQuery('.loadingIcon').remove();
            $(".more").trunc(30);
        }
    });
}

function getParameters() {
    var searchString = window.location.search.substring(1)
        , params = searchString.split("&")
        , hash = {}
        ;

    for (var i = 0; i < params.length; i++) {
        var val = params[i].split("=");
        hash[unescape(val[0])] = unescape(val[1]);
    }
    return hash;
}

function addToFriend(id, obj) {
    element = $(obj);
    var baseUrl = $('#current_url').val();
    jQuery("body").append('<div class="loadingIcon"></div>');
    $.post(

        baseUrl + "/friends/send/0/controller%3D%3Efriends/1/action%3D%3Esend", {

            "friend_id": id

        },

        function (response) {
            jQuery('.loadingIcon').remove();
            if(response.alertFlash){
                $('.alertBlock .alert').remove();
                $('.modal').css('visibility', 'visible');

                $('.alertBlock').append('<div class="alert"><div class = "flash-warning">Warning!</div>'+response.alertFlash+'<button type="button" class="close" data-dismiss="alert"></button></div>');
                $('.modal').fadeIn(1000).delay(800).fadeOut(1000);
                //$('html, body').animate({scrollTop:0}, 'fast');




            }else if(response.successFlash){
                $('.alertBlock .alert').remove();
                $('.modal').css('visibility', 'visible');
                $('.alertBlock').append('<div class="success alert"><div class = "flash-success">Success!</div>'+response.successFlash+'<button type="button" class="close" data-dismiss="alert"></button></div>');
                $('.modal').fadeIn(1000).delay(800).fadeOut(1000);
                if(response.result == 'request') {
                    element.parent().append('<span class="request_sent">REQUEST SENT</span>');
                    element.remove();
                } else if(response.result == 'friend') {
                    element.parent().append('<a class="sendMessage" href="javascript:void(1)" onclick="sendMessage(' + id + ')">SEND MESSAGE</a>');
                    element.remove();
                }

            }

        })


};

function sendMessage(id, element) {
    var baseUrl = $('#current_url').val();

    alert('in development');
    return false;
}

$.extend({
    getUrlVars: function(){
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    },
    getUrlVar: function(name){
        return $.getUrlVars()[name];
    }
});