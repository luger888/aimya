$(document).ready(function () {

});
function addResumeItem(){

    $(".resumeItemForm").css('display', 'block');
    $('.button-2:not(".save, .upload")').addClass("disable");
    $('.addResumeItem').remove();
}
function saveResumeItem(tab) {
    var id = $(this).nextAll('input[type=hidden]:first').val();
    var dataObject = {};

//    $('#file_upload').uploadifive('upload');
    var resumeContent = $('#' + tab).val();
    var baseUrl = $('#current_url').val();
    dataObject[tab] = resumeContent;
    $.ajax({
            url:baseUrl + "/resume/ajax",
            type:"post",
            data:dataObject,
            success:function (response) {


                for (key in response.errors) {


                    $("#" + key).attr("placeholder", response.errors[key]);

                }
                if (response.success == 1) { //if success
                    window.location.reload();
//                    $('#experience').val('');
//                    var content = '  <div class="experienceItem clearfix">' +
//                        '<div class="headRow clearfix">' +
//                        '<h1>experience:</h1>' +
//                        '<div class="buttonRow clearfix">' +
//                        '<input type = "button" value ="edit" class="button-2 edit" onclick="editResumeItem(this, '+tab+');">' +
//                        '<input type = "button" value ="delete" class="button-2 delete" onclick="deleteResumeItem(this, '+tab+');">' +
//                        '<input type = "hidden" value="' + response.lastId + '">' +
//                        '</div>' +
//                        '</div>' +
//                        '<div class = "resumeItemBody">' + resumeContent + '</div>' +
//                        '</div>';
//                    $('.resumeList').append(content);
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
                    $(e).parents('.shadowSeparator').remove();
                }
            }
        );

    }

    return false;

}
function editResumeItem(e, tab) {
    var experienceWrapper = $(e).parents('.resumeItem'); //parent div
    var resumeContent = experienceWrapper.children('.resumeItemBody:first').text();
    var content = '<div class="formRow clearfix"><textarea rows="2" class = "resumeEditInput"></textarea></div>';
    experienceWrapper.children('.resumeItemBody').html(content);
    experienceWrapper.children('.resumeItemBody').children('.formRow').children('.resumeEditInput').val($.trim(resumeContent));
    experienceWrapper.children('.resumeItemBody').after('<div class = "uploadWrapper">'+
        '<div id="queue"></div>'+
        '<input id="file_upload" name="file_upload" type="file" multiple="true">'+
            '<input id="uploadExperience" class="button-2 upload" type="button"  value="upload file" onclick=uploadExperienceFile();>'+
                '<input type="button" value="save" class="button-2 save floatRight" onclick=updateResumeItem(this,"' + tab + '");>'+
                '</div>');
}

function updateResumeItem(e, tab) {
    var experienceWrapper = $(e).parents('.resumeItem'); //parent div
    var id = experienceWrapper.children('.headRow').children('.buttonRow').children('input[type=hidden]').val();
    var resumeContent = experienceWrapper.children('.resumeItemBody').children('.formRow').children('.resumeEditInput').val();
    var baseUrl = $('#current_url').val();
    var dataObject = {};
    dataObject['update' + tab] = resumeContent;
    dataObject['resumeItemId'] = id;
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