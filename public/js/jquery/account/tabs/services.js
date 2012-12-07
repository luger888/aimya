$(document).ready(function() {

});

function addService(){

    $("#addService").css('display', 'block');
    $('.buttonService').addClass("disabled");
    $('#addMoreServices').remove();
}

function addRequestService(){

    $("#addRequestService").css('display', 'block');
    $('.buttonService').addClass("disabled");
    $('#addMoreRequestServices').remove();
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
function editService(e, service_type){
    //taking values for populating from DOM
    $.post(

        '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {'getServiceCategories':1},
        function(response){
            var lesson_categories = response.categories;//lesson categories from DB
            var options = [];
            for(var key in lesson_categories) {
                options += '<option value ="'+lesson_categories[key]["title"]+'">'+lesson_categories[key]['title']+'</option>';
            }

            var serviceWrapper  = $(e).parent(); //parent div
            var id = $(e).nextAll('input[type=hidden]:first').val();//id of service
            /* Getting values from elements */
            var categoriesDropdown =  '<select id="lesson_categoryEditInput">'+options+'</select>';//Lesson categories select
            var durationDropdown =  '<select id="durationEditInput"><option value="15 min">15 min</option><option value="45 min">45 min</option><option value="hour">hour</option><option value="lesson">lesson</option></select>';//Duration categories select
            var lesson_category = serviceWrapper.find('#lesson_category').html();
            var subcategory = serviceWrapper.find('#subcategory').html();
            var rate = serviceWrapper.find('#rate').html();
            var duration = serviceWrapper.find('#duration').html();
            var description = serviceWrapper.find('#description').html();
            /*   Service block, Poplulating   */
            if(service_type == 1){
                var serviceItem = categoriesDropdown +
                    '<input id="subcategoryEditInput" value ="'+subcategory+'">' +
                    '<input id="rateEditInput" value ="'+rate+'"> per ' +
                    durationDropdown+
                    '<textarea  id="descriptionEditInput">'+description+'</textarea>'+
                    '<input type = "button" value ="save" class="updateService"  onclick="updateService(this, 1)";>'+
                    '<input type = "hidden" value ="'+ id +'">'+
                    '<div class="middleSeparator"></div>';
            }else{

                var serviceItem = categoriesDropdown +
                    '<input id="subcategoryEditInput" value ="'+subcategory+'">' +
                    '<input type = "button" value ="save" class="updateService"  onclick="updateService(this, 2)";>'+
                    '<input type = "hidden" value ="'+ id +'">'+
                    '<div class="middleSeparator"></div>';
            }



            /*   END -- Service block    */
            $('.buttonService').addClass("disabled");

            serviceWrapper.empty();//clean the div
            serviceWrapper.html(serviceItem);//insert edit form with populated values
            serviceWrapper.find('#lesson_categoryEditInput').val(lesson_category);
            serviceWrapper.find('#durationEditInput').val($.trim(duration));
        });



}

function updateService(e, service_type){
    var id = $(e).nextAll('input[type=hidden]:first').val();//id of service
    var serviceWrapper  = $(e).parent(); //parent div
    /* getting values to post */
    var lesson_category = serviceWrapper.find('#lesson_categoryEditInput').val();
    var subcategory = serviceWrapper.find('#subcategoryEditInput').val();
    var rate = serviceWrapper.find('#rateEditInput').val();
    var duration = serviceWrapper.find('#durationEditInput').val();
    var description = serviceWrapper.find('#descriptionEditInput').val();
    if(service_type == 1){
        $.post(

            '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {
                'updateService':id,
                'lesson_category': lesson_category,
                'subcategory': subcategory,
                'rate': rate,
                'duration': duration,
                'description': description,
                'service_type': 1
            },
            function (response){
                window.location.reload();
            }

        );
    }else{
        $.post(

            '/account/edit/0/controller%3D%3Eaccount/1/action%3D%3Eedit', {
                'updateService':id,
                'lesson_category': lesson_category,
                'subcategory': subcategory,
                'rate': '',
                'duration': '',
                'description': '',
                'service_type': 2
            },
            function (response){
                window.location.reload();
            }

        );
    }



}