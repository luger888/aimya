$(document).ready(function() {
    $("#signup").click(function(){

        $.post(

            "/user/registration/0/controller%3D%3Euser/1/action%3D%3Eregistration",{

                "firstname" : $("#firstname").val(),
                "lastname" : $("#lastname").val(),
                "username" : $("#username").val(),
                "email" : $("#email").val(),
                "password" : $("#password").val(),
                "role" : $('input[name=type]:checked').val()

            },

            function(response){
                    $('.error').remove();
                for (key in response.errors){
                    $("#" + key).removeAttr('style');
                    if(response.errors[key].length>0){
                        $("#" + key).parent().after('<div class="error">' +response.errors[key] + '</div>');
                        $("#" + key).attr('style', 'border: 1px solid red !important');
                    }

                }

                if(response.status == 1){

                    window.location.href = "/account/index/";

                }

            }

        );

        return false;

    });
    $("#login").click(function(){

        $.post(

            "/user/login/0/controller%3D%3Euser/1/action%3D%3Elogin",{

                "username" : $("#username-login").val(),
                "password" : $("#password-login").val()

            },

            function(response){
                for (key in response.errors){
                    $("#" + key +"-login").attr("placeholder", response.errors[key]);
                    $("#" + key).addClass("input-error");

                }

                if(response.status == 1){

                    window.location.href = "/account/index/";

                }

            }

        );

        return false;

    });
    $("#signUp input").focus(function(){

        $(this).removeClass('input-error');

    })
});