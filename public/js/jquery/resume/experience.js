$(document).ready(function () {

});

function saveResumeItem(tab) {
    var id = $(this).nextAll('input[type=hidden]:first').val();
    var dataObject = {};

//    $('#file_upload').uploadifive('upload');
    var experience = $('#' + tab).val();
    var baseUrl = $('#current_url').val();
    dataObject[tab] = experience;
    $.ajax({
            url:baseUrl + "/resume/ajax",
            type:"post",
            data:dataObject,
            success:function (response) {


                for (key in response.errors) {


                    $("#" + key).attr("placeholder", response.errors[key]);

                }
                if (response.success == 1) { //if success

                    $('#experience').val('');
                    var content = '  <div class="experienceItem clearfix">' +
                        '<div class="headRow clearfix">' +
                        '<h1>experience:</h1>' +
                        '<div class="buttonRow clearfix">' +
                        '<input type = "button" value ="edit" class="button-2 edit" onclick=editExperience(this);>' +
                        '<input type = "button" value ="delete" class="button-2 delete" onclick=deleteExperience(this);>' +
                        '<input type = "hidden" value="' + response.lastId + '">' +
                        '</div>' +
                        '</div>' +
                        '<div class = "experienceBody">' + experience + '</div>' +
                        '</div>';
                    $('.resumeList').append(content);
                }

            }
        }
    );

}

function deleteResumeItem(e, tab) {
    var id = $(e).nextAll('input[type=hidden]:first').val();
    var answer = confirm("Delete " + tab + " ?");
    var baseUrl = $('#current_url').val();
    var dataObject = {};
    dataObject['delete'+tab] = id;
    if (answer) {

        $.ajax({
                url:baseUrl + "/resume/ajax",
                type:"post",
                data:dataObject,
                success:function (response) {
                    $(e).parents('.resumeItem').remove();
                }
            }
        );

    }

    return false;

}
function editResumeItem(e, tab) {
    var experienceWrapper = $(e).parents('.resumeItem'); //parent div
    var experience = experienceWrapper.children('.experienceBody:first').text();
    var content = '<textarea rows="2" class = "resumeEditInput">';
    experienceWrapper.children('.experienceBody').html(content);
    experienceWrapper.children('.experienceBody').children('.resumeEditInput').val($.trim(experience));
    $(e).after('<input type = "button" value ="save" class = "button-2 save" onclick="updateResumeItem(this, '+tab+');">');
    $(e).remove();
}

function updateResumeItem(e, tab) {
    var id = $(e).nextAll('input[type=hidden]:first').val();
    var experience = $(e).prev().children().val();
    var baseUrl = $('#current_url').val();
    var dataObject = {};
    dataObject['update' + tab] = experience;
    $.ajax({
            url:baseUrl + "/resume/ajax",
            type:"post",
            data:dataObject,
            success:function (response) {
                window.location.reload();
            }
        }

    );


}

function uploadExperienceFile() {

    $('#file_upload').click();

}

function updateObjective(){

    var objective = $('#objective').val();
    $.post(

        '/resume/ajax/0/controller%3D%3Eresume/1/action%3D%3Eajax', {

            'objective' : objective

        },

        function (response) {


            for (key in response.errors){


                $("#" + key).attr("placeholder", response.errors[key]);

            }
        }
    )
};