// JavaScript Document

/* create account */

$(document).ready(function () {


});

function saveExperience(){
   var experience = $('#experience').val();
    $.post(

        '/resume/ajax/0/controller%3D%3Eresume/1/action%3D%3Eajax', {

            'experience' : experience

        },

        function (response) {


            for (key in response.errors){


                $("#" + key).attr("placeholder", response.errors[key]);

            }
            if(response.success == 1){ //if success

                $('#experience').val('');
                var content = '<div class = "experienceHeader">Experience : </div>'+
                              '<div class = "experienceBody">'+ experience +
                              '<input type = "button" value ="delete" class = "btn" onclick=deleteExperience(this);>'+
                              '<input type = "button" value ="edit" class = "btn" onclick=editExperience(this);>'+
                              '<input type = "hidden" value="' + response.lastId + '">'+
                              '</div>';
                $('.experienceBody:last').after(content);
            }

        }
    );

}

function deleteExperience(e){
        var id = $(e).nextAll('input[type=hidden]:first').val();
        var answer = confirm("Delete experience?");

        if (answer) {
            $(e).parents('.service').remove();
            $.post(

                '/resume/ajax/0/controller%3D%3Eresume/1/action%3D%3Eajax', {'deleteExperience':id}

            );

        }

        return false;

}
function editExperience(){

}
