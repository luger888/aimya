// JavaScript Document

/* create account */

$(document).ready(function () {


});

function saveEducation(){
   var education = $('#education').val();
    $.post(

        '/resume/ajax/0/controller%3D%3Eresume/1/action%3D%3Eajax', {

            'education' : education

        },

        function (response) {


            for (key in response.errors){


                $("#" + key).attr("placeholder", response.errors[key]);

            }
            if(response.success == 1){ //if success

                $('#education').val('');
                var content = '<div class = "educationItem">'+
                              '<div class = "educationHeader">Education : </div>'+
                              '<div class = "educationBody">'+ education + '</div>'+
                              '<input type = "button" value ="edit" class = "btn" onclick=editEducation(this);>'+
                              '<input type = "button" value ="delete" class = "btn" onclick=deleteEducation(this);>'+
                              '<input type = "hidden" value="' + response.lastId + '">'+
                              '</div>';
                $('.educationItem:last').after(content);
            }

        }
    );

}

function deleteEducation(e){
        var id = $(e).nextAll('input[type=hidden]:first').val();
        var answer = confirm("Delete education?");

        if (answer) {
            $(e).parents('.service').remove();
            $.post(

                '/resume/ajax/0/controller%3D%3Eresume/1/action%3D%3Eajax', {'deleteEducation':id}


            );

        }

        return false;

}
function editEducation(e){

    var education = $(e).prev().text();
    var content = '<textarea rows="2" class = "educationEditInput">';
    $(e).prev().html(content);
    $(e).prev().children().val($.trim(education));
    $(e).after('<input type = "button" value ="save" class = "btn" onclick=updateEducation(this);>');
    $(e).remove();
}

function updateEducation(e){
   var id = $(e).nextAll('input[type=hidden]:first').val();
   var education = $(e).prev().children().val();
    $.post(

        '/resume/ajax/0/controller%3D%3Eresume/1/action%3D%3Eajax', {'updateEducation':id, 'content': education},
        function (response){
            window.location.reload();
        }

    );


}