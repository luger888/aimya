$(document).ready(function() {
    $("#signup").click(function(){
        var myDate = new Date();

        var UTC = myDate.getTimezoneOffset()/60*(-1);
        $.post(

            "/user/registration/0/controller%3D%3Euser/1/action%3D%3Eregistration",{

                "firstname" : $("#firstname").val(),
                "lastname" : $("#lastname").val(),
                "username" : $("#username").val(),
                "gender" : $('input[name=gender]:checked').val(),
                "email" : $("#email").val(),
                "password" : $("#password").val(),
                "role" : $('input[name=type]:checked').val()

            },

            function(response){
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
                    $('.alertBlock').append('<div class="alert"><div class = "flash-warning">Warning!</div>'+response.alertFlash+'<button type="button" class="close" data-dismiss="alert"></button></div>');
                    $('html, body').animate({scrollTop:0}, 'fast');



                }else if(response.confirmFlash){
                    $('.alertBlock .alert').remove();
                    $('.alertBlock').append('<div class="attention alert"><div class = "flash-attention">Attention!</div>'+response.confirmFlash+'<button type="button" class="close" data-dismiss="attention"></button></div>');
                    $('html, body').animate({scrollTop:0}, 'fast');
                }

            }

        );

        return false;

    });
    $("#login").click(function(){
        $('.alertBlock .alert').remove();
        $("input").removeClass("input-error");

        $.post(

            "/user/login/0/controller%3D%3Euser/1/action%3D%3Elogin",{

                "username" : $("#username-login").val(),
                "password" : $("#password-login").val()

            },

            function(response){
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

                    window.location.href = "/account/index/";

                }else if(response.status == 0){
                    $('.alertBlock').append('<div class="alert">Account is not confirmed. Please check you email and confirm registration</div>');
                }
                if(response.alertFlash){

                    $('.alertBlock').append('<div class="alert"><div class = "flash-warning">Warning!</div>'+response.alertFlash+'<button type="button" class="close" data-dismiss="alert"></button></div>');

                }

            }

        );

        return false;

    });

});