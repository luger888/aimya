$(document).ready(function () {
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

            "/friends/send/0/controller%3D%3Efriends/1/action%3D%3Esend", {

                "friend_id": id

            },

            function (response) {

                if(response.alertFlash){
                    $('.alertBlock .alert').remove();
                    $('.alertBlock').append('<div class="alert"><div class = "flash-warning">Warning!</div>'+response.alertFlash+'<button type="button" class="close" data-dismiss="alert"></button></div>');
                    $('html, body').animate({scrollTop:0}, 'fast');



                }else if(response.successFlash){
                    $('.alertBlock .alert').remove();
                    $('.alertBlock').append('<div class="success alert"><div class = "flash-success">Success!</div>'+response.successFlash+'<button type="button" class="close" data-dismiss="alert"></button></div>');
                    $('html, body').animate({scrollTop:0}, 'fast');
                }

            })


    });

});