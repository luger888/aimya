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

                for (key in response.errors){

                    $("#" + key).attr("placeholder", response.errors[key]);
                    $("#" + key).addClass("input-error");

                }

                if(response.status == 1){

                    alert(1);

                }

            }

        );

        return false;

    })

    $("#signUp input").focus(function(){

        $(this).removeClass('input-error');

    })
});