$(document).ready(function() {

});

function addService(){

    $("#addService").css('display', 'block');
}

function deleteService(e) {
    var id = $(e).nextAll('input[type=hidden]:first').val();
    var answer = confirm("Delete service?");

    if (answer) {


        $.post(

            '/account/services/0/controller%3D%3Eaccount/1/action%3D%3Eservices', {'deleteService':id}

        );
    }

    return false;

}

function editService(e) {
    var id = $(e).nextAll('input[type=hidden]:first').val();
    alert(id)
    /*
     $.post(

     '/account/services/0/controller%3D%3Eaccount/1/action%3D%3Eservices', {'deleteService':id}

     );
     */

    return false;

}