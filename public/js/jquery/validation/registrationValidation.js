$(document).ready(function() {
    $("#signup").click(function(){

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
                        console.log(key);
                        $("#" + key).parent().after('<div class="error">' +response.errors[key] + '</div>');
                        $("#" + key).addClass("input-error");
                    }

                }

                if(response.confirmFlash){
                    $('.alertBlock .alert').remove();
                    $('.alertBlock').append('<div class="alert">'+response.confirmFlash+'</div>');

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
                    }

                }

                if(response.status == 1){

                    window.location.href = "/account/index/";

                }else if(response.status == 0){
                    $('.alertBlock').append('<div class="alert">Account is not confirmed. Please check you email and confirm registration</div>');
                }
                if(response.confirmFlash){

                    $('.alertBlock').append('<div class="alert">'+response.confirmFlash+'</div>');

                }

            }

        );

        return false;

    });

});