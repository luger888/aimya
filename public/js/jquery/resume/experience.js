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
                var content = '<div class = "experienceItem">'+
                              '<div class = "experienceHeader">Experience : </div>'+
                              '<div class = "experienceBody">'+ experience + '</div>'+
                              '<input type = "button" value ="edit" class = "btn" onclick=editExperience(this);>'+
                              '<input type = "button" value ="delete" class = "btn" onclick=deleteExperience(this);>'+
                              '<input type = "hidden" value="' + response.lastId + '">'+
                              '</div>';
                $('.experienceItem:last').after(content);
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

                '/resume/ajax/0/controller%3D%3Eresume/1/action%3D%3Eajax', {'deleteExperience':id},

            function (response){
                window.location.reload();
            }
            );

        }

        return false;

}
function editExperience(e){

    var experience = $(e).prev().text();
    var content = '<textarea rows="2" class = "experienceEditInput">';
    $(e).prev().html(content);
    $(e).prev().children().val($.trim(experience));
    $(e).after('<input type = "button" value ="save" class = "btn" onclick=updateExperience(this);>');
    $(e).remove();
}

function updateExperience(e){
   var id = $(e).nextAll('input[type=hidden]:first').val();
   var experience = $(e).prev().children().val();
    $.post(

        '/resume/ajax/0/controller%3D%3Eresume/1/action%3D%3Eajax', {'updateExperience':id, 'content': experience},
        function (response){
            window.location.reload();
        }

    );


}