// JavaScript Document

$(document).ready(function () {
    $(function() {
        $('#file_upload').uploadifive({
            'auto'         : false,
            'scriptData'     : {'test' : 'something'},
            'queueID'      : 'queue',
            'uploadScript' : '/resume/ajax',
            'onUploadComplete' : function(file, data) {
                console.log(data);
            }
        });
    });
});


function saveSkills(){

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