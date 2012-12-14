$(document).ready(function() {

});

function addService(){

    $("#serviceForm").css('display', 'block');
    $('.button-2:not(".save")').addClass("disable");
    $('#addMoreServices').remove();
}

function addRequestService(){

    $("#serviceRequestForm").css('display', 'block');
    $('.button-2:not(".save")').addClass("disable");
    $('#addMoreRequestServices').remove();
}
/* Picking ID of service from hidden input and sending on controller to DELETE service*/
function deleteService(e, url) {
    var id = $(e).nextAll('input[type=hidden]:first').val();
    var answer = confirm("Delete service?");

    if (answer) {
        $(e).parents('.service').remove();
        $.ajax({
            url: url,
            type: "post",
            data: {
                'deleteService':id
            }

        });

    }

    return false;

}
/* Picking ID of service from hidden input and sending on controller to EDIT service*/
function editService(e, service_type, url){
    //taking values for populating from DOM
    $.ajax({
        url: url,
        type: "post",
        data: {
            'getServiceCategories':1
        },
        success: function(response){
            url = "'" + url + "'";
            var lesson_categories = response.categories;//lesson categories from DB
            var options = [];
            for(var key in lesson_categories) {
                options += '<option value ="'+lesson_categories[key]["title"]+'">'+lesson_categories[key]['title']+'</option>';
            }

            var serviceWrapper  = $(e).parents('.shadowSeparatorBox'); //parent div
            var id = $(e).nextAll('input[type=hidden]:first').val();//id of service
            /* Getting values from elements */
            var categoriesDropdown =  '<select id="lesson_categoryEditInput">'+options+'</select>';//Lesson categories select
            var durationDropdown =  '<select id="durationEditInput"><option value="15 min">15 min</option><option value="45 min">45 min</option><option value="hour">hour</option><option value="lesson">lesson</option></select>';//Duration categories select
            var lesson_category = serviceWrapper.find('#lesson_category').html();
            var subcategory = serviceWrapper.find('#subcategory').html().substr(0, serviceWrapper.find('#subcategory').html().length - 1);
            var rate = serviceWrapper.find('#rate').html();
            var duration = serviceWrapper.find('#duration').html();
            var description = serviceWrapper.find('#description').html();
            /*   Service block, Poplulating   */
            if(service_type == 1){ //SERVICE TYPE = OFFERED
                var serviceItem =
                    '<div class = "serviceForm">'+
                        '<div class="formRow clearfix">'+
                            '<span class = "s-116">' + categoriesDropdown + '</span>' +
                            '<span class = "field-116"> <input id="subcategoryEditInput" value ="'+subcategory+'"></span>' +
                            '<span class = "field-66"><input id="rateEditInput" value ="'+rate+'"></span><span class = "txt"> per </span>' +
                            '<span class = "s-86">' + durationDropdown + '</span>' +
                        '</div>'+
                        '<div class="serviceDesc clearfix">' +
                            '<h2>DESCRIBE YOUR SERVICE DETAILS:</h2>' +
                            '<textarea  id="descriptionEditInput">'+description+'</textarea>'+
                        '</div>' +
                        '<div class="buttonsRow clearfix">'+
                            '<input type = "button" value="save" class="updateService button-2 save"  onclick="updateService(this, 1, ' + url + ')";>'+
                            '<input type = "hidden" value="'+ id +'">'+
                        '</div>'+
                    '</div>';
            }else if(service_type == 2){ //SERVICE TYPE = REQUESTED
                 subcategory = serviceWrapper.find('#subcategory').html();
                 serviceItem =
                    '<div class = "serviceForm">'+
                        '<div class="formRow clearfix">'+
                            '<span class = "s-116">' + categoriesDropdown + '</span>' +
                            '<span class = "field-116"> <input id="subcategoryEditInput" value ="'+subcategory+'"></span>' +
                        '</div>'+
                        '<div class="buttonsRow clearfix">'+
                            '<input type = "button" value="save" class="updateService button-2 save" onclick="updateService(this, 2, ' + url + ')";>'+
                            '<input type = "hidden" value="'+ id +'">'+
                        '</div>'+
                    '</div>';
            }



            /*   END -- Service block    */
            $('.button-2:not(".save")').addClass("disable");

            serviceWrapper.empty();//clean the div
            serviceWrapper.html(serviceItem);//insert edit form with populated values
            serviceWrapper.find('#lesson_categoryEditInput').val(lesson_category);
            serviceWrapper.find('#durationEditInput').val($.trim(duration));
        }
    });



}

function updateService(e, service_type, url){
    var id = $(e).nextAll('input[type=hidden]:first').val();//id of service
    var serviceWrapper  = $(e).parents('.shadowSeparatorBox'); //parent div
    /* getting values to post */
    var lesson_category = serviceWrapper.find('#lesson_categoryEditInput').val();
    var subcategory = serviceWrapper.find('#subcategoryEditInput').val();
    var rate = serviceWrapper.find('#rateEditInput').val();
    var duration = serviceWrapper.find('#durationEditInput').val();
    var description = serviceWrapper.find('#descriptionEditInput').val();
    if(service_type == 1){
        $.ajax({
            url: url,
            type: "post",
            data: {
                'updateService':id,
                'lesson_category': lesson_category,
                'subcategory': subcategory,
                'rate': rate,
                'duration': duration,
                'description': description,
                'service_type': 1
            },
            success: function (response){
                window.location.reload();
            }

        });
    }else{
        $.ajax({
            url: url,
            type: "post",
            data: {
                'updateService':id,
                'lesson_category': lesson_category,
                'subcategory': subcategory,
                'rate': '',
                'duration': '',
                'description': '',
                'service_type': 2
            },
            succes: function (response){
                window.location.reload();
            }
        });
    }



}