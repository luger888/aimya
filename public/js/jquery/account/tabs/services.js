$(document).ready(function() {

});

function addService(){

    $("#addService").css('display', 'block');
}
/* Picking ID of service from hidden input and sending on controller to DELETE service*/
function deleteService(e) {
    var id = $(e).nextAll('input[type=hidden]:first').val();
    var answer = confirm("Delete service?");

    if (answer) {
        $(e).parents('.service').remove();
        $.post(

            '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {'deleteService':id}

        );

    }

    return false;

}
/* Picking ID of service from hidden input and sending on controller to EDIT service*/
function editService(e) {

    var id = $(e).nextAll('input[type=hidden]:first').val();

     $.post(

     '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {'editService':id},
         function(response){

             $("#lesson_category").val(response.editForm['lesson_category']);
             $("#subcategory").val(response.editForm['subcategory']);
             $("#rate").val(response.editForm['rate']);
             $("#duration").val(response.editForm['duration']);
             $("#description").val(response.editForm['description']);
             $("#saveService").attr('name', 'updateService');
             $("#serviceForm").append('<input name="hiddenId" type="hidden" value="'+id+'">')
             $("#addService").css('display', 'block');
         }

     );


    return false;

}
