$(document).ready(function() {

    var pathName = $('#current_url').val();
    //---Home page member buttons--------------------------------------------------------------------------------

    $('.memberButtons input:first').attr('checked', 'checked');


    //---Styling radio and checkbox buttons--------------------------------------------------------------------------------

    $('input[type="checkbox"]').checkRadio({
        wrapperClass: 'checkboxWrapper',
        chekedClass: 'checked'
    });

    $('input[type="radio"]').checkRadio({
        wrapperClass: 'radioBoxWrapper',
        chekedClass: 'checked'
    });

    $('.memberButtons label').each(function(){
        $(this).find( '.txt' ).appendTo($(this).find('.radioBoxWrapper'));
    });

    $("#signUp").click(function(){
        var myDate = new Date();

        var UTC = myDate.getTimezoneOffset()/60*(-1);
        $.ajax({
            url: $('#signUp').attr('action'),
            type: "post",
            data: {
                "firstname" : $("#firstname").val(),
                "lastname" : $("#lastname").val(),
                "username" : $("#username").val(),
                "gender" : $('input[name=gender]:checked').val(),
                "email" : $("#email").val(),
                "password" : $("#password").val(),
                "role" : $('input[name=type]:checked').val()
            },
            success: function(response){
                    $('.error').remove();

                for (key in response.errors){
                    $("#" + key).removeClass("input-error");
                    $("#" + key).removeAttr('style');
                    if(response.errors[key].length>0){
                        $("#" + key).parent().after('<div class="error">' +response.errors[key] + '</div>');
                        $("#" + key).addClass("input-error");
                        $("#" + key).change(function() {
                            $(this).removeClass("input-error");
                            $(this).parent().next().remove();
                        });
                    }

                }

                if(response.alertFlash){
                    $('.alertBlock .alert').remove();
                    $('.alertBlock').append('<div class="alert"><div class = "flash-warning">Warning!</div>'+response.alertFlash+'<button type="button" class="close" id = "closeAlert"></button></div>');
                    $('html, body').animate({scrollTop:0}, 'fast');



                }else if(response.confirmFlash){
                    $('.alertBlock .alert').remove();
                    $('.alertBlock').append('<div class="attention alert"><div class = "flash-attention">Attention!</div>'+response.confirmFlash+'<button type="button" class="close" id = "closeAlert"></button></div>');
                    $('html, body').animate({scrollTop:0}, 'fast');
                }

            }

        });
        return false;

    });
    $("#login").click(function(){
        $('.alertBlock .alert').remove();
        $("input").removeClass("input-error");

        $.ajax({
            url: $('#signIn').attr('action'),
            type: "post",
            data: {
                "username" : $("#username-login").val(),
                "password" : $("#password-login").val()
            },
            success: function(response){
                for (key in response.errors){

                    if(response.errors[key].length>0){
                        $("#" + key +"-login").attr("placeholder", response.errors[key]);
                        $("#" + key +"-login").addClass("input-error");
                        $("#" + key +"-login").change(function() {
                            $(this).removeClass("input-error");
                        });

                    }

                }

                if(response.status == 1){

                    window.location.href = pathName + "/account/index/";

                }else if(response.status == 0){
                    $('.alertBlock').append('<div class="alert">Account is not confirmed. Please check you email and confirm registration</div>');
                }
                if(response.alertFlash){

                    $('.alertBlock').append('<div class="alert"><div class = "flash-warning">Warning!</div>'+response.alertFlash+'<button type="button" class="close" id = "closeAlert"></button></div>');

                }

            }
    });

        return false;

    });

});

/*---PIE - add css3 to ie 7 and ie8 -----------------------------------------------------*/

$(function() {
    if (window.PIE) {
        $('.boxShadow, #username-login, #password-login, .button, #tabsNavigation ul, #tabsNavigation ul li, .mainContainer').each(function() {
            PIE.attach(this);
        });//each
    }//if
});